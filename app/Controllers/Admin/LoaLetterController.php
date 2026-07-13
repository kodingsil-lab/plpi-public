<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\LoaPdfService;
use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;

class LoaLetterController extends BaseController
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
            $model = new LoaLetterModel();
            $builder = $model
                ->select('loa_letters.*, journals.name as journal_name')
                ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
                ->orderBy('loa_letters.id', 'DESC');

            if ($status !== '') {
                $builder->where('loa_letters.status', $status);
            }
            if ($journalId > 0) {
                $builder->where('loa_letters.journal_id', $journalId);
            }
            if ($search !== '') {
                $builder->groupStart()
                    ->like('loa_letters.loa_number', $search)
                    ->orLike('loa_letters.title', $search)
                    ->groupEnd();
            }

            $rows = $builder->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel LoA belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/loa_letters/index', $this->viewData('LoA Terbit') + [
            'rows'          => $rows,
            'journals'      => $this->journals(),
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'filters'       => ['status' => $status, 'journal_id' => $journalId, 'q' => $search],
            'databaseError' => $databaseError,
        ]);
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new LoaLetterModel())
            ->select('loa_letters.*, journals.name as journal_name')
            ->join('journals', 'journals.id = loa_letters.journal_id', 'left')
            ->where('loa_letters.id', $id)
            ->first();

        if (! $row) {
            return redirect()->to(site_url('dashboard/loa-letters'))->with('error', 'LoA tidak ditemukan.');
        }

        return view('admin/loa_letters/edit', $this->viewData('Edit LoA') + [
            'row' => $this->normalizeLetter($row),
            'journals' => $this->journals(),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaLetterModel();
        $current = $model->find($id);
        if (! $current) {
            return redirect()->to(site_url('dashboard/loa-letters'))->with('error', 'LoA tidak ditemukan.');
        }

        $rules = [
            'journal_id' => 'required|is_natural_no_zero',
            'title' => 'required|max_length[5000]',
            'article_url' => 'permit_empty|valid_url|max_length[255]',
            'corresponding_email' => 'required|valid_email|max_length[191]',
            'volume' => 'permit_empty|max_length[20]',
            'issue_number' => 'permit_empty|max_length[20]',
            'published_year' => 'permit_empty|regex_match[/^[0-9]{4}$/]',
            'authors_text' => 'required|min_length[3]|max_length[5000]',
            'affiliations_text' => 'permit_empty|max_length[5000]',
            'status' => 'required|in_list[published,revoked]',
            'revoked_reason' => 'permit_empty|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form LoA.');
        }

        $data = $this->validator->getValidated();
        $journalId = (int) $data['journal_id'];
        if (! (new JournalModel())->find($journalId)) {
            return redirect()->back()->withInput()->with('error', 'Jurnal yang dipilih tidak valid.');
        }

        $journalChanged = (int) ($current['journal_id'] ?? 0) !== $journalId;
        $loaNumber = $journalChanged
            ? $this->generateLoaNumber($journalId, (string) ($current['published_at'] ?? ''))
            : (string) ($current['loa_number'] ?? '');
        $authors = $this->lines((string) $data['authors_text']);
        $affiliations = $this->lines((string) ($data['affiliations_text'] ?? ''));
        $status = (string) $data['status'];

        $model->update($id, [
            'journal_id' => $journalId,
            'loa_number' => $loaNumber,
            'title' => trim((string) $data['title']),
            'article_url' => trim((string) ($data['article_url'] ?? '')),
            'corresponding_email' => trim((string) $data['corresponding_email']),
            'volume' => $this->nullable((string) ($data['volume'] ?? '')),
            'issue_number' => $this->nullable((string) ($data['issue_number'] ?? '')),
            'published_year' => $this->nullable((string) ($data['published_year'] ?? '')),
            'authors_json' => json_encode(array_map(static fn($name) => ['name' => $name], $authors), JSON_UNESCAPED_UNICODE),
            'affiliations_json' => $affiliations === [] ? null : json_encode($affiliations, JSON_UNESCAPED_UNICODE),
            'status' => $status,
            'revoked_at' => $status === 'revoked' ? date('Y-m-d H:i:s') : null,
            'revoked_reason' => $this->nullable((string) ($data['revoked_reason'] ?? '')),
        ]);

        $letter = $model->find($id);
        if (is_array($letter) && (string) ($letter['status'] ?? '') === 'published') {
            $pdfPath = (new LoaPdfService())->generate($letter);
            $model->update($id, [
                'pdf_path' => $pdfPath,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if ($journalChanged && (string) ($current['pdf_path'] ?? '') !== $pdfPath) {
                $this->deletePdfFile((string) ($current['pdf_path'] ?? ''));
            }
        }

        $message = $journalChanged
            ? 'Jurnal dan nomor LoA berhasil diperbarui menjadi ' . $loaNumber . '.'
            : 'LoA berhasil diperbarui.';

        return redirect()->to(site_url('dashboard/loa-letters'))->with('success', $message);
    }

    public function regenerate(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaLetterModel();
        $letter = $model->find($id);
        if (! $letter) {
            return redirect()->back()->with('error', 'LoA tidak ditemukan.');
        }
        if ((string) ($letter['status'] ?? '') !== 'published') {
            return redirect()->back()->with('error', 'PDF hanya dapat dibuat untuk LoA terbit.');
        }

        $pdfPath = (new LoaPdfService())->generate($letter);
        $model->update($id, [
            'pdf_path' => $pdfPath,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'PDF LoA berhasil dibuat ulang.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new LoaLetterModel();
        $letter = $model->find($id);
        if (! $letter) {
            return redirect()->back()->with('error', 'LoA tidak ditemukan.');
        }

        if (! empty($letter['loa_request_id'])) {
            (new LoaRequestModel())->update((int) $letter['loa_request_id'], [
                'status' => 'pending',
                'approved_at' => null,
            ]);
        }
        $this->deletePdfFile((string) ($letter['pdf_path'] ?? ''));

        $model->delete($id);

        return redirect()->to(site_url('dashboard/loa-letters'))->with('success', 'LoA berhasil dihapus.');
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

        $letterIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        $model = new LoaLetterModel();
        foreach ($letterIds as $id) {
            $letter = $model->find($id);
            if ($letter && ! empty($letter['loa_request_id'])) {
                (new LoaRequestModel())->update((int) $letter['loa_request_id'], [
                    'status' => 'pending',
                    'approved_at' => null,
                ]);
            }
            if ($letter) {
                $this->deletePdfFile((string) ($letter['pdf_path'] ?? ''));
            }
        }
        if ($letterIds !== []) {
            $model->delete($letterIds);
        }

        return redirect()->to(site_url('dashboard/loa-letters'))->with('success', 'LoA terpilih berhasil dihapus.');
    }

    private function normalizeLetter(array $row): array
    {
        $authors = json_decode((string) ($row['authors_json'] ?? '[]'), true);
        $affiliations = json_decode((string) ($row['affiliations_json'] ?? '[]'), true);
        $row['authors_text'] = implode("\n", array_filter(array_map(static function ($author): string {
            return is_array($author) ? trim((string) ($author['name'] ?? '')) : trim((string) $author);
        }, is_array($authors) ? $authors : [])));
        $row['affiliations_text'] = implode("\n", array_filter(array_map(static function ($affiliation): string {
            return is_array($affiliation) ? trim((string) ($affiliation['affiliation'] ?? '')) : trim((string) $affiliation);
        }, is_array($affiliations) ? $affiliations : [])));

        return $row;
    }

    private function lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $value) ?: []), static fn($line) => $line !== ''));
    }

    private function nullable(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function generateLoaNumber(int $journalId, string $issuedAt): string
    {
        $db = \Config\Database::connect();
        $journal = $db->table('journals j')
            ->select('j.code AS journal_code, p.code AS publisher_code')
            ->join('publishers p', 'p.id = j.publisher_id', 'left')
            ->where('j.id', $journalId)
            ->get()
            ->getRowArray();

        if (! is_array($journal)) {
            throw new \RuntimeException('Jurnal tidak ditemukan untuk membuat nomor LoA.');
        }

        $timestamp = strtotime($issuedAt);
        if ($timestamp === false) {
            $timestamp = time();
        }

        $journalCode = $this->normalizeCodeSegment((string) ($journal['journal_code'] ?? ''));
        $publisherCode = $this->normalizeCodeSegment((string) ($journal['publisher_code'] ?? 'PLPI'));
        $year = date('Y', $timestamp);
        $monthRoman = $this->monthToRoman((int) date('n', $timestamp));

        $rows = (new LoaLetterModel())
            ->select('loa_number')
            ->like('loa_number', '/LOA/' . $journalCode . '/')
            ->like('loa_number', '/' . $year, 'before')
            ->findAll();

        $maxSequence = 0;
        foreach ($rows as $row) {
            $parts = explode('/', trim((string) ($row['loa_number'] ?? '')));
            if (count($parts) < 6
                || strcasecmp((string) ($parts[1] ?? ''), 'LOA') !== 0
                || strcasecmp((string) ($parts[2] ?? ''), $journalCode) !== 0
                || (string) ($parts[5] ?? '') !== $year
                || ! ctype_digit((string) ($parts[0] ?? ''))) {
                continue;
            }

            $maxSequence = max($maxSequence, (int) $parts[0]);
        }

        return sprintf(
            '%03d/LOA/%s/%s/%s/%s',
            $maxSequence + 1,
            $journalCode,
            $publisherCode,
            $monthRoman,
            $year
        );
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

    private function deletePdfFile(string $path): void
    {
        $path = ltrim(str_replace('\\', '/', trim($path)), '/');
        if ($path === '') {
            return;
        }

        $absolute = WRITEPATH . 'uploads/' . $path;
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'loa_letters',
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
            return \Config\Database::connect()->tableExists('loa_letters');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
