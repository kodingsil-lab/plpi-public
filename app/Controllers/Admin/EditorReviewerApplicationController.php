<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EditorReviewerApplicationModel;
use App\Models\JournalModel;

class EditorReviewerApplicationController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $journalId = (int) $this->request->getGet('journal_id');
        $role = (string) $this->request->getGet('role');
        $status = (string) $this->request->getGet('status');
        $q = trim((string) $this->request->getGet('q'));
        $allowedPerPage = [10, 25, 50];
        $perPage = in_array((int) $this->request->getGet('perPage'), $allowedPerPage, true)
            ? (int) $this->request->getGet('perPage')
            : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $rows = [];
        $journals = [];
        $pager = null;
        $databaseError = null;

        if ($this->tablesReady()) {
            $model = new EditorReviewerApplicationModel();
            $builder = $model
                ->select('editor_reviewer_applications.*, journals.name as journal_name')
                ->join('journals', 'journals.id = editor_reviewer_applications.journal_id', 'left');

            if ($journalId > 0) {
                $builder->where('editor_reviewer_applications.journal_id', $journalId);
            }
            if (in_array($role, ['Reviewer', 'Editor'], true)) {
                $builder->where('editor_reviewer_applications.role_requested', $role);
            }
            if ($status !== '') {
                $builder->where('editor_reviewer_applications.status', $status);
            }
            if ($q !== '') {
                $builder->groupStart()
                    ->like('editor_reviewer_applications.application_code', $q)
                    ->orLike('editor_reviewer_applications.full_name', $q)
                    ->orLike('editor_reviewer_applications.email', $q)
                    ->orLike('editor_reviewer_applications.institution', $q)
                    ->orLike('editor_reviewer_applications.expertise', $q)
                    ->groupEnd();
            }

            $rows = $builder->orderBy('editor_reviewer_applications.id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
            $journals = (new JournalModel())->orderBy('name', 'ASC')->findAll();
        } else {
            $databaseError = 'Tabel rekrutmen atau jurnal belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/editor_reviewer_applications/index', [
            'title'         => 'Rekrutmen Editor & Reviewer',
            'activeMenu'    => 'editor_reviewer',
            'eyebrow'       => 'Manajemen Rekrutmen',
            'pageTitle'     => 'Rekrutmen Editor & Reviewer',
            'adminName'     => session()->get('admin_name'),
            'adminEmail'    => session()->get('admin_email'),
            'adminRole'     => session()->get('admin_role'),
            'rows'          => $rows,
            'journals'      => $journals,
            'filters'       => ['journal_id' => $journalId, 'role' => $role, 'status' => $status, 'q' => $q],
            'statuses'      => $this->statuses(),
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'databaseError' => $databaseError,
        ]);
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new EditorReviewerApplicationModel())->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        (new EditorReviewerApplicationModel())->delete($id);

        return redirect()->back()->with('success', 'Data pendaftaran berhasil dihapus.');
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

        $applicationIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($applicationIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new EditorReviewerApplicationModel())->delete($applicationIds);

        return redirect()->to(site_url('dashboard/rekrutmen-editor-reviewer'))->with('success', 'Data pendaftaran terpilih berhasil dihapus.');
    }

    public function exportCsv()
    {
        return $this->exportExcel();
    }

    public function exportExcel()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $rows = (new EditorReviewerApplicationModel())
            ->select('editor_reviewer_applications.*, journals.name as journal_name')
            ->join('journals', 'journals.id = editor_reviewer_applications.journal_id', 'left')
            ->orderBy('editor_reviewer_applications.id', 'DESC')
            ->findAll(10000);

        $columns = [
            'No',
            'Kode Pendaftaran',
            'Jurnal',
            'Nama Lengkap dan Gelar',
            'Asal Institusi',
            'Bergabung Sebagai',
            'Alamat Email',
            'Nomor Handphone/WA',
            'Google Scholar ID',
            'SINTA ID',
            'Scopus ID',
            'ORCID ID',
            'Bidang Keahlian',
            'Status',
            'Tanggal Daftar',
        ];

        $html = '<!doctype html><html><head><meta charset="UTF-8">';
        $html .= '<style>';
        $html .= 'table{border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:11pt;}';
        $html .= 'th{background:#0f766e;color:#fff;font-weight:bold;text-align:left;}';
        $html .= 'th,td{border:1px solid #cbd5e1;padding:8px;vertical-align:top;mso-number-format:"\@";}';
        $html .= '.title{font-size:16pt;font-weight:bold;color:#10233d;}';
        $html .= '.meta{color:#475569;}';
        $html .= '</style>';
        $html .= '</head><body>';
        $html .= '<table>';
        $html .= '<tr><td class="title" colspan="' . count($columns) . '">Data Rekrutmen Editor & Reviewer</td></tr>';
        $html .= '<tr><td class="meta" colspan="' . count($columns) . '">Diekspor pada ' . $this->excelValue(date('d-m-Y H:i:s')) . '</td></tr>';
        $html .= '<tr>';

        foreach ($columns as $column) {
            $html .= '<th>' . $this->excelValue($column) . '</th>';
        }

        $html .= '</tr>';

        foreach ($rows as $index => $row) {
            $values = [
                (string) ($index + 1),
                $row['application_code'] ?? '',
                $row['journal_name'] ?? '',
                $row['full_name'] ?? '',
                $row['institution'] ?? '',
                $row['role_requested'] ?? '',
                $row['email'] ?? '',
                $row['phone'] ?? '',
                $row['google_scholar_id'] ?? '',
                $row['sinta_id'] ?? '',
                $row['scopus_id'] ?? '',
                $row['orcid_id'] ?? '',
                $row['expertise'] ?? '',
                ucfirst((string) ($row['status'] ?? '')),
                ! empty($row['created_at']) ? date('d-m-Y H:i', strtotime((string) $row['created_at'])) : '',
            ];

            $html .= '<tr>';
            foreach ($values as $value) {
                $html .= '<td>' . $this->excelValue((string) $value) . '</td>';
            }
            $html .= '</tr>';
        }

        if ($rows === []) {
            $html .= '<tr><td colspan="' . count($columns) . '">Belum ada data pendaftaran.</td></tr>';
        }

        $html .= '</table></body></html>';

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="rekrutmen-editor-reviewer-' . date('Ymd-His') . '.xls"')
            ->setBody($html);
    }

    private function excelValue(string $value): string
    {
        $value = trim($value);
        if ($value !== '' && preg_match('/^[=+\-@]/', $value) === 1) {
            $value = "'" . $value;
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function tablesReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('editor_reviewer_applications') && $db->tableExists('journals');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function statuses(): array
    {
        if (! $this->tablesReady()) {
            return [];
        }

        try {
            $rows = \Config\Database::connect()
                ->table('editor_reviewer_applications')
                ->select('status')
                ->where('status IS NOT NULL')
                ->groupBy('status')
                ->orderBy('status', 'ASC')
                ->get()
                ->getResultArray();

            return array_values(array_filter(array_map(static fn ($row) => (string) ($row['status'] ?? ''), $rows)));
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
