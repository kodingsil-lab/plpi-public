<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalModel;
use App\Models\PublisherModel;

class JournalController extends BaseController
{
    protected $helpers = ['url', 'text', 'admin_table'];

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

        if ($this->tablesReady()) {
            $model = new JournalModel();
            $model->select('journals.*, publishers.name AS publisher_name')
                ->join('publishers', 'publishers.id = journals.publisher_id', 'left');
            $rows = $model->orderBy('journals.id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel journals/publishers belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/journals/index', $this->viewData('Data Jurnal') + [
            'rows'          => $rows,
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'databaseError' => $databaseError,
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tablesReady()) {
            return redirect()->to(site_url('dashboard/journals'))->with('error', 'Tabel journals/publishers belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        return view('admin/journals/form', $this->viewData('Tambah Jurnal') + [
            'row'        => null,
            'publishers' => (new PublisherModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $rules = $this->rules('store');
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form jurnal.');
        }

        $data = $this->validator->getValidated();
        $model = new JournalModel();
        $payload = $this->payload($data, $model);

        try {
            $logoPath = $this->storeImage('logo', 'journals/logos');
            if ($logoPath !== null) {
                $payload['logo_path'] = $logoPath;
            }
            $signaturePath = $this->storeImage('signature', 'journals/signatures');
            if ($signaturePath !== null) {
                $payload['default_signature_path'] = $signaturePath;
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $model->insert($payload);

        return redirect()->to(site_url('dashboard/journals'))->with('success', 'Data jurnal berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new JournalModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/journals'))->with('error', 'Jurnal tidak ditemukan.');
        }

        return view('admin/journals/form', $this->viewData('Edit Jurnal') + [
            'row'        => $row,
            'publishers' => (new PublisherModel())->orderBy('name', 'ASC')->findAll(),
        ]);
    }

    public function logo(int $id)
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(403);
        }

        $row = (new JournalModel())->find($id);
        if (! $row) {
            return $this->response->setStatusCode(404);
        }

        return $this->serveUploadImage((string) ($row['logo_path'] ?? ''));
    }

    public function signature(int $id)
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(403);
        }

        $row = (new JournalModel())->find($id);
        if (! $row) {
            return $this->response->setStatusCode(404);
        }

        return $this->serveUploadImage((string) ($row['default_signature_path'] ?? ''));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new JournalModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/journals'))->with('error', 'Jurnal tidak ditemukan.');
        }

        if (! $this->validate($this->rules('update', $id))) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form jurnal.');
        }

        $data = $this->validator->getValidated();
        $payload = $this->payload($data, $model, $id);

        try {
            $logoPath = $this->storeImage('logo', 'journals/logos');
            if ($logoPath !== null) {
                $payload['logo_path'] = $logoPath;
            }
            $signaturePath = $this->storeImage('signature', 'journals/signatures');
            if ($signaturePath !== null) {
                $payload['default_signature_path'] = $signaturePath;
            }
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $model->update($id, $payload);

        return redirect()->to(site_url('dashboard/journals'))->with('success', 'Data jurnal berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new JournalModel())->delete($id);

        return redirect()->to(site_url('dashboard/journals'))->with('success', 'Jurnal berhasil dihapus.');
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

        $journalIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($journalIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new JournalModel())->delete($journalIds);

        return redirect()->to(site_url('dashboard/journals'))->with('success', 'Jurnal terpilih berhasil dihapus.');
    }

    private function rules(string $mode, ?int $id = null): array
    {
        $codeRule = $mode === 'update' && $id
            ? 'required|max_length[80]|is_unique[journals.code,id,' . $id . ']'
            : 'required|max_length[80]|is_unique[journals.code]';

        return [
            'publisher_id'          => 'required|is_natural_no_zero',
            'name'                  => 'required|max_length[255]',
            'code'                  => $codeRule,
            'e_issn'                => 'permit_empty|max_length[50]',
            'p_issn'                => 'permit_empty|max_length[50]',
            'issn'                  => 'permit_empty|max_length[50]',
            'website_url'           => 'permit_empty|valid_url|max_length[255]',
            'commitment_statement_url' => 'permit_empty|valid_url|max_length[255]',
            'recruitment_intro'     => 'permit_empty|max_length[20000]',
            'default_signer_name'   => 'permit_empty|max_length[191]',
            'default_signer_title'  => 'permit_empty|max_length[191]',
            'pdf_sig_left_px'       => 'permit_empty|integer',
            'pdf_sig_top_px'        => 'permit_empty|integer',
            'pdf_sig_height_px'     => 'permit_empty|integer',
            'pdf_sig_scale_percent' => 'permit_empty|integer|greater_than_equal_to[50]|less_than_equal_to[250]',
        ];
    }

    private function payload(array $data, JournalModel $model, ?int $ignoreId = null): array
    {
        $baseSlug = url_title(trim((string) $data['name']), '-', true) ?: url_title(trim((string) $data['code']), '-', true);
        $baseSlug = $baseSlug ?: 'jurnal';
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->slugExists($model, $slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return [
            'publisher_id'           => (int) $data['publisher_id'],
            'name'                   => trim((string) $data['name']),
            'code'                   => strtoupper(trim((string) $data['code'])),
            'slug'                   => $slug,
            'e_issn'                 => $data['e_issn'] ?? null,
            'p_issn'                 => $data['p_issn'] ?? null,
            'issn'                   => $data['issn'] ?? null,
            'website_url'            => $data['website_url'] ?? null,
            'commitment_statement_url' => $data['commitment_statement_url'] ?? null,
            'recruitment_intro'      => $data['recruitment_intro'] ?? null,
            'default_signer_name'    => $data['default_signer_name'] ?? null,
            'default_signer_title'   => $data['default_signer_title'] ?? null,
            'pdf_sig_left_px'        => (int) ($data['pdf_sig_left_px'] ?: 20),
            'pdf_sig_top_px'         => (int) ($data['pdf_sig_top_px'] ?: 10),
            'pdf_sig_height_px'      => (int) ($data['pdf_sig_height_px'] ?: 85),
            'pdf_sig_scale_percent'  => (int) ($data['pdf_sig_scale_percent'] ?: 100),
        ];
    }

    private function slugExists(JournalModel $model, string $slug, ?int $ignoreId): bool
    {
        $builder = $model->where('slug', $slug);
        if ($ignoreId !== null) {
            $builder->where('id !=', $ignoreId);
        }

        return (bool) $builder->first();
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'journals',
            'eyebrow'    => 'Manajemen Jurnal',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
        ];
    }

    private function storeImage(string $field, string $folder): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload berkas jurnal gagal. Periksa ukuran file dan coba lagi.');
        }

        $ext = strtolower((string) $file->getExtension());
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            throw new \RuntimeException('Format berkas jurnal tidak didukung. Gunakan PNG, JPG, JPEG, atau WEBP.');
        }

        $targetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder);
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName, true)) {
            throw new \RuntimeException('Berkas jurnal gagal disimpan ke server.');
        }

        return $folder . '/' . $newName;
    }

    private function serveUploadImage(string $path)
    {
        $path = trim($path);
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($path, '/\\');
        if ($path === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return $this->response->setStatusCode(404);
        }

        $binary = @file_get_contents($absolutePath);
        if (! is_string($binary) || $binary === '') {
            return $this->response->setStatusCode(404);
        }

        return $this->response
            ->setHeader('Content-Type', @mime_content_type($absolutePath) ?: 'image/png')
            ->setHeader('Cache-Control', 'no-store')
            ->setBody($binary);
    }

    private function tablesReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('publishers') && $db->tableExists('journals');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
