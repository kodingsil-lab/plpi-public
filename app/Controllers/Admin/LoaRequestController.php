<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\LoaPdfService;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaNotificationModel;
use App\Models\LoaRequestModel;

class LoaRequestController extends BaseController
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
        $status = trim((string) $this->request->getGet('status'));
        $journalId = (int) $this->request->getGet('journal_id');
        $search = trim((string) $this->request->getGet('q'));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->tableReady()) {
            $model = new LoaRequestModel();
            $builder = $model
                ->select("loa_requests.*, journals.name as journal_name, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
                ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
                ->orderBy('loa_requests.id', 'DESC');

            if ($status !== '') {
                if ($status === 'menunggu') {
                    $builder->whereIn('loa_requests.status', ['pending', 'revision']);
                } elseif ($status === 'disetujui') {
                    $builder->where('loa_requests.status', 'approved');
                    $builder->where("NOT EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false);
                } elseif ($status === 'terbit') {
                    $builder->where("EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false);
                } elseif ($status === 'ditolak') {
                    $builder->where('loa_requests.status', 'rejected');
                } else {
                    $builder->where('loa_requests.status', $status);
                }
            }
            if ($journalId > 0) {
                $builder->where('loa_requests.journal_id', $journalId);
            }
            if ($search !== '') {
                $builder->groupStart()
                    ->like('loa_requests.request_code', $search)
                    ->orLike('loa_requests.title', $search)
                    ->orLike('loa_requests.corresponding_email', $search)
                    ->orLike('loa_requests.whatsapp_number', $search)
                    ->groupEnd();
            }

            $rows = $builder->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel LoA belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/loa_requests/index', $this->viewData('Permohonan LoA') + [
            'rows'          => $rows,
            'journals'      => $this->journals(),
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'filters'       => ['status' => $status, 'journal_id' => $journalId, 'q' => $search],
            'databaseError' => $databaseError,
        ]);
    }

    public function show(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new LoaRequestModel())
            ->select("loa_requests.*, journals.name as journal_name, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->where('loa_requests.id', $id)
            ->first();

        if (! $row) {
            return redirect()->to(site_url('dashboard/loa-requests'))->with('error', 'Data permohonan tidak ditemukan.');
        }

        return view('admin/loa_requests/show', $this->viewData('Detail Permohonan LoA') + [
            'row' => $this->normalizeRequest($row),
        ]);
    }

    public function approve(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $requestModel = new LoaRequestModel();
        $letterModel = new LoaLetterModel();
        $row = $requestModel->find($id);

        if (! $row) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
        if (! in_array((string) $row['status'], ['pending', 'revision'], true)) {
            return redirect()->back()->with('error', 'Status permohonan tidak valid untuk disetujui.');
        }

        $now = date('Y-m-d H:i:s');
        $loaNumber = $this->generateLoaNumber((int) $row['journal_id']);

        $letterModel->insert([
            'journal_id' => $row['journal_id'],
            'loa_request_id' => $row['id'],
            'loa_number' => $loaNumber,
            'article_url' => $row['article_url'] ?? '',
            'article_id_external' => $row['article_id_external'] ?? null,
            'title' => $row['title'],
            'authors_json' => $row['authors_json'] ?? '[]',
            'corresponding_email' => $row['corresponding_email'] ?? '',
            'affiliations_json' => $row['affiliations_json'] ?? null,
            'volume' => $row['volume'] ?? null,
            'issue_number' => $row['issue_number'] ?? null,
            'published_year' => $row['published_year'] ?? null,
            'status' => 'published',
            'verification_hash' => hash('sha256', $loaNumber . '|' . bin2hex(random_bytes(8))),
            'public_token' => bin2hex(random_bytes(16)),
            'published_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $letterId = (int) $letterModel->getInsertID();

        if ($letterId > 0) {
            $letter = $letterModel->find($letterId);
            if (is_array($letter)) {
                $pdfPath = (new LoaPdfService())->generate($letter);
                $letterModel->update($letterId, [
                    'pdf_path' => $pdfPath,
                    'updated_at' => $now,
                ]);
            }

            (new LoaNotificationModel())->insert([
                'loa_letter_id' => $letterId,
                'status' => 'menunggu',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $requestModel->update($id, [
            'status' => 'approved',
            'approved_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->to(site_url('dashboard/loa-letters'))->with('success', 'Permohonan berhasil disetujui dan LoA diterbitkan.');
    }

    public function reject(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaRequestModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Permohonan tidak ditemukan.');
        }
        if ((string) $row['status'] === 'approved') {
            return redirect()->back()->with('error', 'Permohonan yang sudah disetujui tidak bisa langsung ditolak.');
        }

        $model->update($id, [
            'status' => 'rejected',
            'rejection_reason' => trim((string) ($this->request->getPost('rejection_reason') ?: 'Ditolak oleh admin.')),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard/loa-requests'))->with('success', 'Permohonan berhasil ditolak.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new LoaRequestModel())->delete($id);

        return redirect()->to(site_url('dashboard/loa-requests'))->with('success', 'Permohonan berhasil dihapus.');
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

        $requestIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($requestIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new LoaRequestModel())->delete($requestIds);

        return redirect()->to(site_url('dashboard/loa-requests'))->with('success', 'Permohonan terpilih berhasil dihapus.');
    }

    private function normalizeRequest(array $row): array
    {
        foreach (['authors_json', 'affiliations_json'] as $field) {
            $decoded = json_decode((string) ($row[$field] ?? '[]'), true);
            $row[$field] = is_array($decoded) ? $decoded : [];
        }

        return $row;
    }

    private function generateLoaNumber(int $journalId): string
    {
        $db = \Config\Database::connect();
        $journal = $db->table('journals j')
            ->select('j.code as journal_code, p.code as publisher_code')
            ->join('publishers p', 'p.id = j.publisher_id', 'left')
            ->where('j.id', $journalId)
            ->get()
            ->getRowArray();

        $journalCode = $this->normalizeCodeSegment((string) ($journal['journal_code'] ?? ('JRN-' . $journalId)));
        $publisherCode = $this->normalizeCodeSegment((string) ($journal['publisher_code'] ?? 'PLPI'));
        $suffix = '/LOA/' . $journalCode . '/' . $publisherCode . '/' . $this->monthToRoman((int) date('n')) . '/' . date('Y');
        $rows = (new LoaLetterModel())
            ->select('loa_number')
            ->like('loa_number', '/LOA/' . $journalCode . '/')
            ->like('loa_number', '/' . date('Y'), 'before')
            ->findAll();

        $maxSeq = 0;
        foreach ($rows as $row) {
            $parts = explode('/', (string) ($row['loa_number'] ?? ''));
            if (ctype_digit((string) ($parts[0] ?? ''))) {
                $maxSeq = max($maxSeq, (int) $parts[0]);
            }
        }

        return str_pad((string) ($maxSeq + 1), 3, '0', STR_PAD_LEFT) . $suffix;
    }

    private function monthToRoman(int $month): string
    {
        return [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'][$month] ?? 'I';
    }

    private function normalizeCodeSegment(string $raw): string
    {
        $value = strtoupper(trim($raw));
        $value = preg_replace('/[^A-Z0-9-]+/', '-', $value) ?? '';
        $value = trim((preg_replace('/-+/', '-', $value) ?? ''), '-');

        return $value !== '' ? $value : 'NA';
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'loa_requests',
            'eyebrow'    => 'Manajemen LoA',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
        ];
    }

    private function journals(): array
    {
        try {
            return \Config\Database::connect()->tableExists('journals')
                ? (new JournalModel())->orderBy('name', 'ASC')->findAll()
                : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function tableReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('loa_requests') && $db->tableExists('loa_letters');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
