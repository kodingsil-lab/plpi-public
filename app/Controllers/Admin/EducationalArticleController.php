<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleCategoryModel;
use App\Models\EducationalArticleModel;

class EducationalArticleController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    private const STATUSES = ['draft', 'published'];

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
        $status = trim((string) $this->request->getGet('status'));
        $rows = [];
        $pager = null;
        $databaseError = null;

        if ($this->tableReady()) {
            $model = $this->articleQuery();
            if ($search !== '') {
                $model->groupStart()
                    ->like('educational_articles.title', $search)
                    ->orLike('educational_articles.slug', $search)
                    ->orLike('educational_articles.summary', $search)
                    ->orLike('article_categories.name', $search)
                    ->groupEnd();
            }
            if (in_array($status, self::STATUSES, true)) {
                $model->where('educational_articles.status', $status);
            } else {
                $status = '';
            }

            $rows = $model->orderBy('educational_articles.published_at', 'DESC')
                ->orderBy('educational_articles.id', 'DESC')
                ->paginate($perPage);
            $pager = $model->pager;
        } else {
            $databaseError = 'Tabel artikel edukatif belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/articles/index', $this->viewData('Semua Artikel', 'articles') + [
            'rows'          => $rows,
            'pager'         => $pager,
            'startNumber'   => (($page - 1) * $perPage) + 1,
            'perPage'       => $perPage,
            'search'        => $search,
            'status'        => $status,
            'statuses'      => self::STATUSES,
            'databaseError' => $databaseError,
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tableReady()) {
            return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('error', 'Tabel artikel edukatif belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        return view('admin/articles/form', $this->viewData('Tulis Artikel', 'article_create') + [
            'row'        => null,
            'categories' => $this->categories(),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('error', 'Periksa form artikel.');
        }

        $payload = $this->payload();
        try {
            $coverPath = $this->storeImage('cover', 'articles');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        if ($coverPath !== null) {
            $payload['cover_path'] = $coverPath;
        }

        (new EducationalArticleModel())->insert($payload);

        return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('success', 'Artikel berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = (new EducationalArticleModel())->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('error', 'Artikel tidak ditemukan.');
        }

        return view('admin/articles/form', $this->viewData('Edit Artikel', 'articles') + [
            'row'        => $row,
            'categories' => $this->categories(),
        ]);
    }

    public function cover(int $id)
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(403);
        }

        $row = (new EducationalArticleModel())->find($id);
        if (! $row) {
            return $this->response->setStatusCode(404);
        }

        return $this->serveUploadImage((string) ($row['cover_path'] ?? ''));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $model = new EducationalArticleModel();
        $row = $model->find($id);
        if (! $row) {
            return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('error', 'Artikel tidak ditemukan.');
        }

        if (! $this->validate($this->rules($id))) {
            return redirect()->back()->withInput()->with('error', 'Periksa form artikel.');
        }

        $payload = $this->payload();
        try {
            $coverPath = $this->storeImage('cover', 'articles');
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
        if ($coverPath !== null) {
            $payload['cover_path'] = $coverPath;
        }

        $model->update($id, $payload);

        return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        (new EducationalArticleModel())->delete($id);

        return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('success', 'Artikel berhasil dihapus.');
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

        $articleIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($articleIds === []) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dipilih.');
        }

        (new EducationalArticleModel())->delete($articleIds);

        return redirect()->to(site_url('dashboard/artikel-edukatif'))->with('success', 'Artikel terpilih berhasil dihapus.');
    }

    private function articleQuery(): EducationalArticleModel
    {
        return (new EducationalArticleModel())
            ->select('educational_articles.*, article_categories.name AS category_name')
            ->join('article_categories', 'article_categories.id = educational_articles.category_id', 'left');
    }

    private function categories(): array
    {
        if (! $this->categoryTableReady()) {
            return [];
        }

        return (new ArticleCategoryModel())
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    private function rules(?int $id = null): array
    {
        $slugRule = $id
            ? 'permit_empty|max_length[255]|is_unique[educational_articles.slug,id,' . $id . ']'
            : 'permit_empty|max_length[255]|is_unique[educational_articles.slug]';

        return [
            'category_id'  => 'permit_empty|integer',
            'title'        => 'required|max_length[255]',
            'slug'         => $slugRule,
            'summary'      => 'permit_empty|max_length[2000]',
            'content'      => 'permit_empty',
            'image_alt'    => 'permit_empty|max_length[255]',
            'status'       => 'required|in_list[draft,published]',
            'published_at' => 'permit_empty|max_length[20]',
            'sort_order'   => 'permit_empty|integer',
        ];
    }

    private function payload(): array
    {
        $title = trim((string) $this->request->getPost('title'));
        $slug = trim((string) $this->request->getPost('slug'));
        $status = in_array((string) $this->request->getPost('status'), self::STATUSES, true)
            ? (string) $this->request->getPost('status')
            : 'draft';
        $publishedAt = trim((string) $this->request->getPost('published_at'));
        if ($publishedAt !== '') {
            $publishedAt = str_replace('T', ' ', $publishedAt) . ':00';
        } elseif ($status === 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        } else {
            $publishedAt = null;
        }

        return [
            'category_id'  => (int) $this->request->getPost('category_id') > 0 ? (int) $this->request->getPost('category_id') : null,
            'title'        => $title,
            'slug'         => $this->slugify($slug !== '' ? $slug : $title),
            'summary'      => trim((string) $this->request->getPost('summary')) ?: null,
            'content'      => (string) ($this->request->getPost('content') ?? ''),
            'image_alt'    => trim((string) $this->request->getPost('image_alt')) ?: null,
            'status'       => $status,
            'published_at' => $publishedAt,
            'sort_order'   => (int) ($this->request->getPost('sort_order') ?? 0),
        ];
    }

    private function storeImage(string $field, string $folder): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload cover gagal. Periksa ukuran file dan coba lagi.');
        }

        $ext = strtolower((string) $file->getExtension());
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            throw new \RuntimeException('Format cover tidak didukung. Gunakan PNG, JPG, JPEG, atau WEBP.');
        }

        $targetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $folder;
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        if (! $file->move($targetDir, $newName, true)) {
            throw new \RuntimeException('Cover gagal disimpan ke server.');
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

    private function viewData(string $title, string $activeMenu): array
    {
        return [
            'title'      => $title,
            'activeMenu' => $activeMenu,
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

        return $slug !== '' ? $slug : 'artikel-' . date('YmdHis');
    }

    private function tableReady(): bool
    {
        try {
            $db = \Config\Database::connect();
            return $db->tableExists('educational_articles') && $db->tableExists('article_categories');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function categoryTableReady(): bool
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
