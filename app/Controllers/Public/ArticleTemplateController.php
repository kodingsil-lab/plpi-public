<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\JournalArticleTemplateModel;
use App\Models\JournalModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArticleTemplateController extends BaseController
{
    protected $helpers = ['url'];

    public function index()
    {
        if (! $this->tablesReady()) {
            throw PageNotFoundException::forPageNotFound('Template artikel belum tersedia.');
        }

        $rows = (new JournalModel())
            ->select('journals.id, journals.name, journals.code, journals.e_issn, journals.p_issn, journals.website_url, journals.article_template_slug, journal_article_templates.id AS template_id, journal_article_templates.original_name, journal_article_templates.file_ext, journal_article_templates.file_size, journal_article_templates.updated_at AS template_updated_at')
            ->join('journal_article_templates', 'journal_article_templates.journal_id = journals.id', 'left')
            ->orderBy('journals.name', 'ASC')
            ->findAll();

        return view('public/templates/index', [
            'title' => 'Template Artikel Jurnal | PLPI',
            'rows'  => $rows,
        ]);
    }

    public function download(string $slug)
    {
        $template = $this->findTemplateBySlug($slug);

        return $this->serveTemplate($template);
    }

    private function findTemplateBySlug(string $slug): array
    {
        if (! $this->tablesReady()) {
            throw PageNotFoundException::forPageNotFound('Template artikel tidak ditemukan.');
        }

        $template = (new JournalArticleTemplateModel())
            ->select('journal_article_templates.*')
            ->join('journals', 'journals.id = journal_article_templates.journal_id', 'inner')
            ->where('journals.article_template_slug', trim($slug))
            ->first();

        if (! is_array($template)) {
            throw PageNotFoundException::forPageNotFound('Template artikel belum tersedia.');
        }

        return $template;
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

    private function serveTemplate(array $template)
    {
        $path = trim((string) ($template['file_path'] ?? ''));
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($path, '/\\');
        if ($path === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            throw PageNotFoundException::forPageNotFound('File template tidak ditemukan.');
        }

        return $this->response->download($absolutePath, null)->setFileName((string) ($template['original_name'] ?? basename($absolutePath)));
    }
}
