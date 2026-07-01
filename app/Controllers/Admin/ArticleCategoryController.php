<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleCategoryModel;

class ArticleCategoryController extends BaseController
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
        $search = trim((string) $this->request->getGet('q'));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->tableReady()) {
            $model = new ArticleCategoryModel();
            if ($search !== '') {
                $model->groupStart()
                    ->like('name', $search)
                    ->orLike('slug', $search)
                    ->orLike('description', $search)
                    ->groupEnd();
            }

            $rows = $model->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC')->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel kategori artikel belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/article_categories/index', $this->viewData('Kategori Artikel') + [
            'rows'          => $rows,
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'search'        => $search,
            'editRow'       => $this->editRow(),
            'databaseError' => $databaseError,
        ]);
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        return redirect()->to(site_url('dashboard/artikel-edukatif/kategori?edit=' . $id));
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', 'Periksa form kategori artikel.');
        }

        (new ArticleCategoryModel())->insert($this->payload());

        return redirect()->to(site_url('dashboard/artikel-edukatif/kategori'))->with('success', 'Kategori artikel berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new ArticleCategoryModel();
        if (! $model->find($id)) {
            return redirect()->to(site_url('dashboard/artikel-edukatif/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        if (! $this->validate($this->rules($id))) {
            return redirect()->back()->withInput()->with('error', 'Periksa form kategori artikel.');
        }

        $model->update($id, $this->payload());

        return redirect()->to(site_url('dashboard/artikel-edukatif/kategori'))->with('success', 'Kategori artikel berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new ArticleCategoryModel())->delete($id);

        return redirect()->to(site_url('dashboard/artikel-edukatif/kategori'))->with('success', 'Kategori artikel berhasil dihapus.');
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

        $categoryIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($categoryIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new ArticleCategoryModel())->delete($categoryIds);

        return redirect()->to(site_url('dashboard/artikel-edukatif/kategori'))->with('success', 'Kategori terpilih berhasil dihapus.');
    }

    private function rules(?int $id = null): array
    {
        $slugRule = $id
            ? 'permit_empty|max_length[191]|is_unique[article_categories.slug,id,' . $id . ']'
            : 'permit_empty|max_length[191]|is_unique[article_categories.slug]';

        return [
            'name'        => 'required|max_length[191]',
            'slug'        => $slugRule,
            'description' => 'permit_empty|max_length[2000]',
            'is_active'   => 'permit_empty|in_list[0,1]',
            'sort_order'  => 'permit_empty|integer',
        ];
    }

    private function payload(): array
    {
        $name = trim((string) $this->request->getPost('name'));
        $slug = trim((string) $this->request->getPost('slug'));

        return [
            'name'        => $name,
            'slug'        => $this->slugify($slug !== '' ? $slug : $name),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'is_active'   => (int) $this->request->getPost('is_active') === 0 ? 0 : 1,
            'sort_order'  => (int) ($this->request->getPost('sort_order') ?? 0),
        ];
    }

    private function editRow(): ?array
    {
        $id = (int) ($this->request->getGet('edit') ?? 0);
        if ($id < 1 || ! $this->tableReady()) {
            return null;
        }

        return (new ArticleCategoryModel())->find($id) ?: null;
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'article_categories',
            'eyebrow'    => 'Artikel Edukatif',
            'pageTitle'  => $title,
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
        ];
    }

    private function slugify(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?: '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'kategori-' . date('YmdHis');
    }

    private function tableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('article_categories');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
