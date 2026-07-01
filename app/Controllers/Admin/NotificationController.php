<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\EmailService;
use App\Libraries\LoaPdfService;
use App\Models\LoaLetterModel;
use App\Models\LoaNotificationModel;

class NotificationController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $allowedPerPage = [10, 25, 50];
        $perPage = in_array((int) $this->request->getGet('perPage'), $allowedPerPage, true)
            ? (int) $this->request->getGet('perPage')
            : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->tableReady()) {
            $this->syncPublishedLetters();

            $model = new LoaNotificationModel();
            $builder = $model
                ->select('loa_notifications.*, loa_letters.loa_number, loa_letters.title, loa_letters.authors_json, loa_letters.public_token, loa_letters.published_at, loa_letters.corresponding_email, loa_requests.whatsapp_number, journals.name as journal_name, journals.website_url as journal_url')
                ->join('loa_letters', 'loa_letters.id = loa_notifications.loa_letter_id', 'left')
                ->join('loa_requests', 'loa_requests.id = loa_letters.loa_request_id', 'left')
                ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
                ->orderBy('loa_notifications.id', 'DESC');

            $rows = $builder->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel notifikasi belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/notifications/index', $this->viewData('Notifikasi LoA') + [
            'rows' => $rows,
            'pager' => $pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage' => $perPage,
            'databaseError' => $databaseError,
        ]);
    }

    public function sendEmail(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaNotificationModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }

        $letterModel = new LoaLetterModel();
        $letter = $letterModel->find((int) $row['loa_letter_id']);
        if (! $letter) {
            return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Data LoA tidak ditemukan.');
        }

        $email = trim((string) ($letter['corresponding_email'] ?? ''));
        if ($email === '') {
            return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Email penulis/pengaju LoA belum tersedia.');
        }

        try {
            $newPdfPath = (new LoaPdfService())->generate($letter);
            $letterModel->update((int) $letter['id'], [
                'pdf_path' => $newPdfPath,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $letter['pdf_path'] = $newPdfPath;

            $pdfFullPath = WRITEPATH . 'uploads/' . ltrim($newPdfPath, '/\\');
            if (! is_file($pdfFullPath)) {
                return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Gagal menyiapkan file PDF terbaru untuk notifikasi.');
            }

            $journalData = \Config\Database::connect()->table('journals')
                ->select('journals.name, journals.website_url, publishers.name as publisher_name, publishers.email, publishers.phone, publishers.address, journals.default_signer_name, journals.default_signer_title')
                ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
                ->where('journals.id', $letter['journal_id'])
                ->get()
                ->getRowArray();

            $sent = (new EmailService())->sendLoaApprovedNotification($email, $letter, $pdfFullPath, [
                'journal_name' => $journalData['name'] ?? 'Jurnal',
                'journal_url' => $journalData['website_url'] ?? '',
                'name' => $journalData['publisher_name'] ?? 'Penerbit',
                'email' => $journalData['email'] ?? '',
                'phone' => $journalData['phone'] ?? '',
                'address' => $journalData['address'] ?? '',
                'editor_name' => $journalData['default_signer_name'] ?? 'Pimpinan Redaksi',
                'signer_name' => $journalData['default_signer_title'] ?? 'Pimpinan Redaksi',
            ]);

            if (! $sent) {
                return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Gagal mengirim email notifikasi. Periksa konfigurasi email/SMTP.');
            }

            $model->update($id, [
                'status' => 'notifikasi terkirim',
                'sent_to_email' => $email,
                'sent_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to(site_url('dashboard/notifikasi'))->with('success', 'Email notifikasi berhasil dikirim ke penulis.');
        } catch (\Throwable $e) {
            log_message('error', 'Notification email error: ' . $e->getMessage());

            return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaNotificationModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('dashboard/notifikasi'))->with('error', 'Notifikasi tidak ditemukan.');
        }

        $model->delete($id);

        return redirect()->to(site_url('dashboard/notifikasi'))->with('success', 'Item notifikasi berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $notificationIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($notificationIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new LoaNotificationModel())->delete($notificationIds);

        return redirect()->to(site_url('dashboard/notifikasi'))->with('success', 'Item notifikasi terpilih berhasil dihapus.');
    }

    private function syncPublishedLetters(): void
    {
        $db = \Config\Database::connect();
        $rows = $db->table('loa_letters')
            ->select('loa_letters.id')
            ->join('loa_notifications', 'loa_notifications.loa_letter_id = loa_letters.id', 'left')
            ->where('loa_letters.status', 'published')
            ->where('loa_notifications.id IS NULL', null, false)
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $payload = array_map(static fn(array $row): array => [
            'loa_letter_id' => (int) $row['id'],
            'status' => 'menunggu',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows);

        $db->table('loa_notifications')->insertBatch($payload);
    }

    private function tableReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('loa_notifications')
                && $db->tableExists('loa_letters')
                && $db->tableExists('loa_requests');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function viewData(string $title): array
    {
        return [
            'title' => $title,
            'activeMenu' => 'loa_notifications',
            'eyebrow' => 'Manajemen LoA',
            'pageTitle' => $title,
            'adminName' => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole' => session()->get('admin_role'),
        ];
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
