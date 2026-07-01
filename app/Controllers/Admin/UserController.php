<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $allowedPerPage = [10, 25, 50];
        $requestedPerPage = (int) ($this->request->getGet('perPage') ?? 10);
        $perPage = in_array($requestedPerPage, $allowedPerPage, true) ? $requestedPerPage : 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));

        $search = trim((string) $this->request->getGet('q'));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->usersTableReady()) {
            $model = new UserModel();
            if ($search !== '') {
                $model->groupStart()
                    ->like('username', $search)
                    ->orLike('name', $search)
                    ->orLike('email', $search)
                    ->orLike('role', $search)
                    ->groupEnd();
            }

            $rows = $model->orderBy('id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel users belum tersedia. Jalankan migrasi dan pastikan koneksi database sudah diatur.';
        }

        return view('admin/users/index', [
            'title'       => 'Pengguna',
            'activeMenu'  => 'users',
            'eyebrow'     => 'Pengaturan',
            'pageTitle'   => 'Pengguna Admin',
            'adminName'   => session()->get('admin_name'),
            'adminEmail'  => session()->get('admin_email'),
            'adminRole'   => session()->get('admin_role'),
            'rows'        => $rows,
            'pager'       => $pager,
            'startNumber' => (($page - 1) * $perPage) + 1,
            'perPage'     => $perPage,
            'search'      => $search,
            'databaseError' => $databaseError,
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        return view('admin/users/form', $this->formData('Tambah Pengguna'));
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $rules = [
            'username'  => 'required|max_length[80]|is_unique[users.username]',
            'name'      => 'required|max_length[191]',
            'email'     => 'required|valid_email|max_length[191]|is_unique[users.email]',
            'role'      => 'required|in_list[superadmin,admin]',
            'password'  => 'required|min_length[8]|max_length[100]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form pengguna.');
        }

        $data = $this->validator->getValidated();
        (new UserModel())->insert([
            'username'  => trim((string) $data['username']),
            'name'      => trim((string) $data['name']),
            'email'     => trim((string) $data['email']),
            'role'      => (string) $data['role'],
            'password'  => password_hash((string) $data['password'], PASSWORD_BCRYPT),
            'is_active' => (int) ($data['is_active'] ?? 1),
        ]);

        return redirect()->to(site_url('dashboard/users'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $row = (new UserModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        return view('admin/users/form', $this->formData('Edit Pengguna', $row));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $model = new UserModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $rules = [
            'username'  => 'required|max_length[80]|is_unique[users.username,id,' . $id . ']',
            'name'      => 'required|max_length[191]',
            'email'     => 'required|valid_email|max_length[191]|is_unique[users.email,id,' . $id . ']',
            'role'      => 'required|in_list[superadmin,admin]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali form pengguna.');
        }

        $data = $this->validator->getValidated();
        $payload = [
            'username'  => trim((string) $data['username']),
            'name'      => trim((string) $data['name']),
            'email'     => trim((string) $data['email']),
            'role'      => (string) $data['role'],
            'is_active' => (int) ($data['is_active'] ?? 1),
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            if (mb_strlen($password) < 8 || mb_strlen($password) > 100) {
                return redirect()->back()->withInput()->with('error', 'Password minimal 8 karakter.');
            }
            $payload['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $model->update($id, $payload);

        return redirect()->to(site_url('dashboard/users'))->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        if ($id === (int) session('admin_user_id')) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Akun yang sedang login tidak bisa dihapus.');
        }

        (new UserModel())->delete($id);

        return redirect()->to(site_url('dashboard/users'))->with('success', 'Pengguna berhasil dihapus.');
    }

    public function bulkDelete()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->usersTableReady()) {
            return redirect()->to(site_url('dashboard/users'))->with('error', 'Tabel users belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $currentUserId = (int) session('admin_user_id');
        $userIds = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0 && $id !== $currentUserId) {
                $userIds[] = $id;
            }
        }

        $userIds = array_values(array_unique($userIds));
        if ($userIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new UserModel())->delete($userIds);

        return redirect()->to(site_url('dashboard/users'))->with('success', 'Pengguna terpilih berhasil dihapus.');
    }

    private function formData(string $title, ?array $row = null): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'users',
            'eyebrow'    => 'Pengaturan',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
            'row'        => $row,
        ];
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }

    private function usersTableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('users');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
