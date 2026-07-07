<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JournalArticleTemplateModel;
use App\Models\JournalModel;

class JournalTemplateController extends BaseController
{
    protected $helpers = ['url', 'admin_table'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $databaseError = null;
        $rows = [];

        if ($this->tablesReady()) {
            $this->ensureJournalLinks();
            $rows = (new JournalModel())
                ->select('journals.id, journals.name, journals.code, journals.article_template_slug, journals.updated_at, journal_article_templates.id AS template_id, journal_article_templates.original_name, journal_article_templates.file_path, journal_article_templates.file_ext, journal_article_templates.file_size, journal_article_templates.updated_at AS template_updated_at')
                ->join('journal_article_templates', 'journal_article_templates.journal_id = journals.id', 'left')
                ->orderBy('journals.name', 'ASC')
                ->findAll();
        } else {
            $databaseError = 'Tabel jurnal/template belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/journal_templates/index', $this->viewData('Template Artikel') + [
            'rows'          => $rows,
            'databaseError' => $databaseError,
        ]);
    }

    public function upload(int $journalId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tablesReady()) {
            return redirect()->to(site_url('dashboard/journal-templates'))->with('error', 'Tabel jurnal/template belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $journal = (new JournalModel())->find($journalId);
        if (! is_array($journal)) {
            return redirect()->to(site_url('dashboard/journal-templates'))->with('error', 'Jurnal tidak ditemukan.');
        }

        try {
            $payload = $this->storeTemplateFile($journalId);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $model = new JournalArticleTemplateModel();
        $existing = $model->where('journal_id', $journalId)->first();
        if (is_array($existing)) {
            $this->deleteStoredFile((string) ($existing['file_path'] ?? ''));
            $model->update((int) $existing['id'], $payload);
        } else {
            $model->insert($payload);
        }

        return redirect()->to(site_url('dashboard/journal-templates'))->with('success', 'Template artikel berhasil diupload.');
    }

    public function download(int $templateId)
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(403);
        }

        $template = (new JournalArticleTemplateModel())->find($templateId);
        if (! is_array($template)) {
            return $this->response->setStatusCode(404);
        }

        return $this->serveTemplate($template);
    }

    public function regenerateLink(int $journalId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }
        if (! $this->tablesReady()) {
            return redirect()->to(site_url('dashboard/journal-templates'))->with('error', 'Tabel jurnal/template belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $model = new JournalModel();
        $journal = $model->find($journalId);
        if (! is_array($journal)) {
            return redirect()->to(site_url('dashboard/journal-templates'))->with('error', 'Jurnal tidak ditemukan.');
        }

        $model->update($journalId, [
            'article_template_slug' => $this->uniqueJournalSlug((string) ($journal['code'] ?? ''), (string) ($journal['name'] ?? 'jurnal'), $journalId),
        ]);

        return redirect()->to(site_url('dashboard/journal-templates'))->with('success', 'Link template artikel berhasil digenerate ulang.');
    }

    private function storeTemplateFile(int $journalId): array
    {
        $file = $this->request->getFile('template_file');
        if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            throw new \RuntimeException('Pilih file template artikel terlebih dahulu.');
        }
        if (! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload template gagal. Periksa ukuran file dan coba lagi.');
        }

        $ext = strtolower((string) $file->getExtension());
        if (! in_array($ext, ['doc', 'docx'], true)) {
            throw new \RuntimeException('Format template tidak didukung. Gunakan file .doc atau .docx.');
        }

        $targetDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'journal_templates';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $newName = 'journal-' . $journalId . '-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (! $file->move($targetDir, $newName, true)) {
            throw new \RuntimeException('Template gagal disimpan ke server.');
        }

        return [
            'journal_id'    => $journalId,
            'original_name' => $file->getClientName(),
            'file_path'     => 'journal_templates/' . $newName,
            'file_ext'      => $ext,
            'file_size'     => (int) $file->getSize(),
        ];
    }

    private function serveTemplate(array $template)
    {
        $path = trim((string) ($template['file_path'] ?? ''));
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($path, '/\\');
        if ($path === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return $this->response->setStatusCode(404);
        }

        return $this->response->download($absolutePath, null)->setFileName((string) ($template['original_name'] ?? basename($absolutePath)));
    }

    private function ensureJournalLinks(): void
    {
        $model = new JournalModel();
        $journals = $model->select('id, name, code, article_template_slug')->findAll();
        foreach ($journals as $journal) {
            if (trim((string) ($journal['article_template_slug'] ?? '')) !== '') {
                continue;
            }

            $journalId = (int) ($journal['id'] ?? 0);
            if ($journalId <= 0) {
                continue;
            }

            $model->update($journalId, [
                'article_template_slug' => $this->uniqueJournalSlug((string) ($journal['code'] ?? ''), (string) ($journal['name'] ?? 'jurnal'), $journalId),
            ]);
        }
    }

    private function uniqueJournalSlug(string $code, string $name, int $journalId): string
    {
        $source = trim($code) !== '' ? trim($code) : trim($name);
        $base = url_title($source, '-', true) ?: 'jurnal';
        $base = implode('-', array_map(static fn ($part) => ucfirst($part), explode('-', $base)));
        $slug = $base;
        $suffix = 2;
        $model = new JournalModel();

        while ($this->journalSlugExists($model, $slug, $journalId)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function journalSlugExists(JournalModel $model, string $slug, int $ignoreId): bool
    {
        return (bool) $model
            ->where('article_template_slug', $slug)
            ->where('id !=', $ignoreId)
            ->first();
    }

    private function deleteStoredFile(string $path): void
    {
        $path = trim($path);
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($path, '/\\');
        if ($path !== '' && is_file($absolutePath)) {
            @unlink($absolutePath);
        }
    }

    private function viewData(string $title): array
    {
        return [
            'title'      => $title,
            'activeMenu' => 'journal_templates',
            'eyebrow'    => 'Manajemen Jurnal',
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
            return $db->tableExists('journals')
                && $db->fieldExists('article_template_slug', 'journals')
                && $db->tableExists('journal_article_templates');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }
}
