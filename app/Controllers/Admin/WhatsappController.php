<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AppSettingModel;
use App\Models\EmailMessageModel;
use App\Models\JournalModel;
use App\Models\WhatsappMessageModel;
use App\Models\WhatsappTemplateModel;

class WhatsappController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    public function compose()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $templates = [];
        $recentMessages = [];
        $journals = [];
        $databaseError = null;

        if ($this->tablesReady()) {
            $templates = (new WhatsappTemplateModel())
                ->where('type', 'whatsapp')
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();
            $recentMessages = (new WhatsappMessageModel())
                ->orderBy('id', 'DESC')
                ->findAll(10);

            try {
                $db = \Config\Database::connect();
                if ($db->tableExists('journals')) {
                    $journals = (new JournalModel())
                        ->select('name, website_url')
                        ->orderBy('name', 'ASC')
                        ->findAll();
                }
            } catch (\Throwable $e) {
                $journals = [];
            }
        } else {
            $databaseError = 'Tabel WhatsApp belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/whatsapp/compose', $this->viewData('Kirim Pesan WhatsApp', 'message_whatsapp') + [
            'templates'      => $templates,
            'recentMessages' => $recentMessages,
            'journals'       => $journals,
            'databaseError'  => $databaseError,
        ]);
    }

    public function send()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tablesReady()) {
            return redirect()->back()->withInput()->with('error', 'Tabel WhatsApp belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $rules = [
            'recipient_name' => 'permit_empty|max_length[191]',
            'phone_number'   => 'required|max_length[40]',
            'message'        => 'required|max_length[5000]',
            'template_id'    => 'permit_empty|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form pesan WhatsApp.');
        }

        $data = $this->validator->getValidated();
        $normalizedNumber = $this->normalizeWhatsappNumber((string) $data['phone_number']);
        if ($normalizedNumber === '') {
            return redirect()->back()->withInput()->with('error', 'Nomor WhatsApp tidak valid.');
        }

        $message = trim((string) $data['message']);
        $waUrl = 'https://wa.me/' . $normalizedNumber . '?text=' . rawurlencode($message);

        (new WhatsappMessageModel())->insert([
            'recipient_name' => trim((string) ($data['recipient_name'] ?? '')),
            'phone_number'   => $normalizedNumber,
            'message'        => $message,
            'template_id'    => ! empty($data['template_id']) ? (int) $data['template_id'] : null,
            'wa_url'         => $waUrl,
            'sent_by'        => (string) session('admin_email'),
        ]);

        return redirect()->to($waUrl);
    }

    public function composeEmail()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $templates = [];
        $recentMessages = [];
        $journals = [];
        $databaseError = null;
        $mailReady = false;

        if ($this->messageTablesReady()) {
            $templates = (new WhatsappTemplateModel())
                ->where('type', 'email')
                ->where('is_active', 1)
                ->orderBy('name', 'ASC')
                ->findAll();
            $recentMessages = (new EmailMessageModel())
                ->orderBy('id', 'DESC')
                ->findAll(10);
            $journals = (new JournalModel())->orderBy('name', 'ASC')->findAll();
            $mailReady = $this->mailSettingsReady();
        } else {
            $databaseError = 'Tabel pesan email belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/whatsapp/email_compose', $this->viewData('Kirim Pesan Email', 'message_email') + [
            'templates'      => $templates,
            'recentMessages' => $recentMessages,
            'journals'       => $journals,
            'databaseError'  => $databaseError,
            'mailReady'      => $mailReady,
        ]);
    }

    public function sendEmail()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->messageTablesReady()) {
            return redirect()->back()->withInput()->with('error', 'Tabel pesan email belum tersedia. Jalankan migrasi terlebih dahulu.');
        }
        if (! $this->mailSettingsReady()) {
            return redirect()->back()->withInput()->with('error', 'Konfigurasi SMTP email resmi belum lengkap. Isi di Pengaturan > Aplikasi.');
        }

        $rules = [
            'recipient_name'  => 'permit_empty|max_length[191]',
            'recipient_email' => 'required|valid_email|max_length[191]',
            'subject'         => 'required|max_length[191]',
            'message'         => 'required|max_length[10000]',
            'template_id'     => 'permit_empty|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form email.');
        }

        $data = $this->validator->getValidated();
        $settings = $this->mailSettings();
        $email = \Config\Services::email(null, false);
        $email->initialize([
            'protocol'    => 'smtp',
            'SMTPHost'    => (string) ($settings['smtp_host'] ?? ''),
            'SMTPUser'    => (string) ($settings['smtp_user'] ?? ''),
            'SMTPPass'    => (string) ($settings['smtp_pass'] ?? ''),
            'SMTPPort'    => (int) ($settings['smtp_port'] ?? 587),
            'SMTPCrypto'  => (string) ($settings['smtp_crypto'] ?? 'tls'),
            'mailType'    => 'html',
            'charset'     => 'UTF-8',
            'wordWrap'    => true,
            'SMTPTimeout' => 15,
        ]);

        $recipientEmail = strtolower(trim((string) $data['recipient_email']));
        $recipientName = trim((string) ($data['recipient_name'] ?? ''));
        $subject = trim((string) $data['subject']);
        $message = trim((string) $data['message']);
        $htmlMessage = nl2br(esc($message));

        $email->clear(true);
        $email->setFrom((string) ($settings['mail_from_email'] ?: $settings['smtp_user']), (string) ($settings['mail_from_name'] ?: 'PLPI'));
        $email->setTo($recipientEmail);
        $email->setSubject($subject);
        $email->setMailType('html');
        $email->setMessage('<div style="font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#10233d">' . $htmlMessage . '</div>');

        $sent = $email->send(false);
        (new EmailMessageModel())->insert([
            'recipient_name'  => $recipientName,
            'recipient_email' => $recipientEmail,
            'subject'         => $subject,
            'message'         => $message,
            'template_id'     => ! empty($data['template_id']) ? (int) $data['template_id'] : null,
            'sent_by'         => (string) session('admin_email'),
            'status'          => $sent ? 'sent' : 'failed',
            'error_message'   => $sent ? null : strip_tags($email->printDebugger(['headers'])),
        ]);

        if (! $sent) {
            return redirect()->back()->withInput()->with('error', 'Email gagal dikirim. Periksa konfigurasi SMTP/email resmi.');
        }

        return redirect()->to(site_url('dashboard/messages/email/send'))->with('success', 'Email berhasil dikirim.');
    }

    public function templates()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $allowedPerPage = [10, 25, 50];
        $perPage = in_array((int) $this->request->getGet('perPage'), $allowedPerPage, true)
            ? (int) $this->request->getGet('perPage')
            : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $search = trim((string) $this->request->getGet('q'));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->templatesTableReady()) {
            $model = new WhatsappTemplateModel();
            if ($search !== '') {
                $model->groupStart()
                    ->like('name', $search)
                    ->orLike('code', $search)
                    ->orLike('type', $search)
                    ->orLike('subject', $search)
                    ->orLike('message', $search)
                    ->groupEnd();
            }
            $rows = $model->orderBy('id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel whatsapp_templates belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/whatsapp/templates/index', $this->viewData('Template Pesan', 'message_templates') + [
            'rows'          => $rows,
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'search'        => $search,
            'databaseError' => $databaseError,
        ]);
    }

    public function createTemplate()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        return view('admin/whatsapp/templates/form', $this->viewData('Tambah Template Pesan', 'message_templates') + [
            'row' => null,
        ]);
    }

    public function storeTemplate()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if (! $this->validate($this->templateRules())) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form template pesan.');
        }

        $data = $this->validator->getValidated();
        (new WhatsappTemplateModel())->insert([
            'name'      => trim((string) $data['name']),
            'code'      => strtolower(trim((string) $data['code'])),
            'type'      => (string) $data['type'],
            'subject'   => trim((string) ($data['subject'] ?? '')),
            'message'   => trim((string) $data['message']),
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);

        return redirect()->to(site_url('dashboard/messages/templates'))->with('success', 'Template pesan berhasil ditambahkan.');
    }

    public function editTemplate(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new WhatsappTemplateModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/messages/templates'))->with('error', 'Template tidak ditemukan.');
        }

        return view('admin/whatsapp/templates/form', $this->viewData('Edit Template Pesan', 'message_templates') + [
            'row' => $row,
        ]);
    }

    public function updateTemplate(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new WhatsappTemplateModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('dashboard/messages/templates'))->with('error', 'Template tidak ditemukan.');
        }

        if (! $this->validate($this->templateRules($id))) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form template pesan.');
        }

        $data = $this->validator->getValidated();
        $model->update($id, [
            'name'      => trim((string) $data['name']),
            'code'      => strtolower(trim((string) $data['code'])),
            'type'      => (string) $data['type'],
            'subject'   => trim((string) ($data['subject'] ?? '')),
            'message'   => trim((string) $data['message']),
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);

        return redirect()->to(site_url('dashboard/messages/templates'))->with('success', 'Template pesan berhasil diperbarui.');
    }

    public function deleteTemplate(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new WhatsappTemplateModel())->delete($id);

        return redirect()->to(site_url('dashboard/messages/templates'))->with('success', 'Template pesan berhasil dihapus.');
    }

    public function bulkDeleteTemplates()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $templateIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($templateIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new WhatsappTemplateModel())->delete($templateIds);

        return redirect()->to(site_url('dashboard/messages/templates'))->with('success', 'Template terpilih berhasil dihapus.');
    }

    private function templateRules(?int $id = null): array
    {
        $codeRule = $id
            ? 'required|max_length[80]|is_unique[whatsapp_templates.code,id,' . $id . ']'
            : 'required|max_length[80]|is_unique[whatsapp_templates.code]';

        return [
            'name'      => 'required|max_length[191]',
            'code'      => $codeRule,
            'type'      => 'required|in_list[whatsapp,email]',
            'subject'   => 'permit_empty|max_length[191]',
            'message'   => 'required|max_length[5000]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];
    }

    private function normalizeWhatsappNumber(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return strlen($digits) >= 8 ? $digits : '';
    }

    private function viewData(string $title, string $activeMenu): array
    {
        return [
            'title'      => $title,
            'activeMenu' => $activeMenu,
            'eyebrow'    => 'Manajemen Pesan',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
        ];
    }

    private function tablesReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('whatsapp_templates') && $db->tableExists('whatsapp_messages');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function messageTablesReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('whatsapp_templates') && $db->tableExists('email_messages');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function mailSettingsReady(): bool
    {
        $settings = $this->mailSettings();

        return trim((string) ($settings['smtp_host'] ?? '')) !== ''
            && trim((string) ($settings['smtp_user'] ?? '')) !== ''
            && trim((string) ($settings['smtp_pass'] ?? '')) !== ''
            && (int) ($settings['smtp_port'] ?? 0) > 0
            && trim((string) ($settings['mail_from_email'] ?? $settings['smtp_user'] ?? '')) !== '';
    }

    private function mailSettings(): array
    {
        $database = (new AppSettingModel())->first() ?: [];
        $env = [
            'smtp_host'       => trim((string) env('plpi.smtp.host', '')),
            'smtp_user'       => trim((string) env('plpi.smtp.user', '')),
            'smtp_pass'       => (string) env('plpi.smtp.password', ''),
            'smtp_port'       => (int) env('plpi.smtp.port', 0),
            'smtp_crypto'     => trim((string) env('plpi.smtp.crypto', '')),
            'mail_from_email' => trim((string) env('plpi.mail.fromEmail', '')),
            'mail_from_name'  => trim((string) env('plpi.mail.fromName', '')),
        ];

        $hasEnvSmtp = $env['smtp_host'] !== '' || $env['smtp_user'] !== '' || $env['smtp_pass'] !== '';
        if ($hasEnvSmtp) {
            return [
                'smtp_host'       => $env['smtp_host'],
                'smtp_user'       => $env['smtp_user'],
                'smtp_pass'       => $env['smtp_pass'],
                'smtp_port'       => $env['smtp_port'] > 0 ? $env['smtp_port'] : 587,
                'smtp_crypto'     => $env['smtp_crypto'] !== '' ? $env['smtp_crypto'] : 'tls',
                'mail_from_email' => $env['mail_from_email'] !== '' ? $env['mail_from_email'] : $env['smtp_user'],
                'mail_from_name'  => $env['mail_from_name'] !== '' ? $env['mail_from_name'] : 'PLPI',
            ];
        }

        return [
            'smtp_host'       => (string) ($database['smtp_host'] ?? ''),
            'smtp_user'       => (string) ($database['smtp_user'] ?? ''),
            'smtp_pass'       => $this->decryptSecret((string) ($database['smtp_pass'] ?? '')),
            'smtp_port'       => (int) ($database['smtp_port'] ?? 587),
            'smtp_crypto'     => (string) ($database['smtp_crypto'] ?? 'tls'),
            'mail_from_email' => (string) ($database['mail_from_email'] ?? $database['smtp_user'] ?? ''),
            'mail_from_name'  => (string) ($database['mail_from_name'] ?? 'PLPI'),
        ];
    }

    private function decryptSecret(string $value): string
    {
        $stored = trim($value);
        if ($stored === '') {
            return '';
        }

        try {
            $decoded = base64_decode($stored, true);
            if ($decoded !== false) {
                return (string) service('encrypter')->decrypt($decoded);
            }
        } catch (\Throwable $e) {
            // Fall through to raw/plaintext compatibility.
        }

        return $stored;
    }

    private function templatesTableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('whatsapp_templates');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
