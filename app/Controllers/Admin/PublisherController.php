<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PublisherModel;

class PublisherController extends BaseController
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
            $model = new PublisherModel();
            $rows = $model->orderBy('id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel publishers belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/publishers/index', $this->viewData('Publisher') + [
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
        if (! $this->tableReady()) {
            return redirect()->to(site_url('dashboard/publishers'))->with('error', 'Tabel publishers belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        return view('admin/publishers/form', $this->viewData('Tambah Publisher') + ['row' => null]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $rules = [
            'code'    => 'required|max_length[50]|is_unique[publishers.code]',
            'name'    => 'required|max_length[255]',
            'email'   => 'permit_empty|valid_email|max_length[191]',
            'phone'   => 'permit_empty|max_length[50]',
            'address' => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form publisher.');
        }

        $data = $this->validator->getValidated();
        $payload = [
            'code'    => strtoupper(trim((string) $data['code'])),
            'name'    => trim((string) $data['name']),
            'email'   => $data['email'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ];

        try {
            $logoPath = $this->storeImage('logo', 'publishers');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        if ($logoPath !== null) {
            $payload['logo_path'] = $logoPath;
        }

        (new PublisherModel())->insert($payload);

        return redirect()->to(site_url('dashboard/publishers'))->with('success', 'Publisher berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new PublisherModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/publishers'))->with('error', 'Publisher tidak ditemukan.');
        }

        return view('admin/publishers/form', $this->viewData('Edit Publisher') + ['row' => $row]);
    }

    public function logo(int $id)
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(403);
        }

        $row = (new PublisherModel())->find($id);
        if (! $row) {
            return $this->response->setStatusCode(404);
        }

        return $this->serveUploadImage((string) ($row['logo_path'] ?? ''));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new PublisherModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/publishers'))->with('error', 'Publisher tidak ditemukan.');
        }

        $rules = [
            'code'    => 'required|max_length[50]|is_unique[publishers.code,id,' . $id . ']',
            'name'    => 'required|max_length[255]',
            'email'   => 'permit_empty|valid_email|max_length[191]',
            'phone'   => 'permit_empty|max_length[50]',
            'address' => 'permit_empty|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa form publisher.');
        }

        $data = $this->validator->getValidated();
        $payload = [
            'code'    => strtoupper(trim((string) $data['code'])),
            'name'    => trim((string) $data['name']),
            'email'   => $data['email'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ];

        try {
            $logoPath = $this->storeImage('logo', 'publishers');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        if ($logoPath !== null) {
            $payload['logo_path'] = $logoPath;
        }

        $model->update($id, $payload);

        return redirect()->to(site_url('dashboard/publishers'))->with('success', 'Publisher berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new PublisherModel())->delete($id);

        return redirect()->to(site_url('dashboard/publishers'))->with('success', 'Publisher berhasil dihapus.');
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

        $publisherIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($publisherIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new PublisherModel())->delete($publisherIds);

        return redirect()->to(site_url('dashboard/publishers'))->with('success', 'Publisher terpilih berhasil dihapus.');
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'publishers',
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
            throw new \RuntimeException('Upload logo gagal. Periksa ukuran file dan coba lagi.');
        }

        $ext = strtolower((string) $file->getExtension());
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            throw new \RuntimeException('Format logo tidak didukung. Gunakan PNG, JPG, JPEG, atau WEBP.');
        }

        $targetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName, true)) {
            throw new \RuntimeException('Logo gagal disimpan ke server.');
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

    private function tableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('publishers');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
