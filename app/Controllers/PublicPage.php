<?php

namespace App\Controllers;

use App\Models\JournalModel;
use App\Models\LoaLetterModel;
use App\Models\LoaRequestModel;
use App\Models\UserModel;
use App\Models\EducationalArticleModel;
use App\Models\EditorReviewerApplicationModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use DOMDocument;
use DOMElement;
use DOMNode;

class PublicPage extends BaseController
{
    protected $helpers = ['url', 'text'];

    public function index()
    {
        $articles = $this->getArticles();

        $data = [
            'title'           => 'Pusat Layanan Publikasi Ilmiah',
            'stats'           => $this->getStats(),
            'latestRequests'  => $this->getLatestRequests(),
            'journals'        => $this->getJournals(),
            'articles'        => array_slice($articles, 0, 3),
        ];

        return view('public/home', $data);
    }

    public function artikel()
    {
        $data = [
            'title'    => 'Artikel Edukatif',
            'articles' => $this->getArticles(),
        ];

        return view('public/articles/index', $data);
    }

    public function detailArtikel(string $slug)
    {
        $articles = $this->getArticles(false);

        $article = null;

        foreach ($articles as $item) {
            if ($item['slug'] === $slug) {
                $article = $this->getArticleBySlug($slug) ?? $item;
                break;
            }
        }

        if (! $article) {
            throw PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }

        $relatedArticles = array_values(array_filter($articles, function ($item) use ($slug) {
            return $item['slug'] !== $slug;
        }));

        $data = [
            'title'           => $article['title'],
            'article'         => $article,
            'relatedArticles' => array_slice($relatedArticles, 0, 3),
        ];

        return view('public/articles/detail', $data);
    }

    public function rekrutmenEditorReviewer(string $slug)
    {
        $journal = $this->findJournalBySlug($slug);
        if (! $journal) {
            throw PageNotFoundException::forPageNotFound('Jurnal tidak ditemukan.');
        }

        $journal['recruitment_intro_html'] = $this->sanitizeRichHtml((string) ($journal['recruitment_intro'] ?? ''));

        return view('public/recruitment/form', [
            'title' => 'Rekrutmen Editor & Reviewer',
            'journal' => $journal,
        ]);
    }

    public function storeRekrutmenEditorReviewer(string $slug)
    {
        if ($this->isRateLimited('recruitment-submit', 5, 900)) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan pengiriman. Coba lagi beberapa menit lagi.');
        }

        $journal = $this->findJournalBySlug($slug);
        if (! $journal) {
            throw PageNotFoundException::forPageNotFound('Jurnal tidak ditemukan.');
        }

        $rules = [
            'full_name'         => 'required|string|min_length[3]|max_length[191]',
            'institution'       => 'required|string|min_length[3]|max_length[191]',
            'role_requested'    => 'required|in_list[Reviewer,Editor]',
            'email'             => 'required|valid_email|max_length[191]',
            'phone'             => 'required|regex_match[/^[0-9+\\-\\s]{8,30}$/]|max_length[50]',
            'google_scholar_id' => 'required|string|max_length[100]',
            'sinta_id'          => 'required|string|max_length[100]',
            'scopus_id'         => 'permit_empty|string|max_length[100]',
            'orcid_id'          => 'permit_empty|string|max_length[100]',
            'expertise'         => 'required|string|min_length[5]|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Periksa kembali data rekrutmen yang dikirim.');
        }

        $validated = $this->validator->getValidated();
        $model = new EditorReviewerApplicationModel();
        $model->insert([
            'journal_id' => (int) $journal['id'],
            'application_code' => $this->generateRecruitmentCode($model),
            'full_name' => trim((string) $validated['full_name']),
            'institution' => trim((string) $validated['institution']),
            'role_requested' => (string) $validated['role_requested'],
            'email' => strtolower(trim((string) $validated['email'])),
            'phone' => trim((string) $validated['phone']),
            'google_scholar_id' => trim((string) $validated['google_scholar_id']),
            'sinta_id' => trim((string) $validated['sinta_id']),
            'scopus_id' => $this->nullable((string) ($validated['scopus_id'] ?? '')),
            'orcid_id' => $this->nullable((string) ($validated['orcid_id'] ?? '')),
            'expertise' => trim((string) $validated['expertise']),
            'status' => 'baru',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to(site_url('rekrutmen-editor-reviewer/jurnal/' . (string) $journal['slug']))
            ->with('success', 'Pendaftaran berhasil dikirim. Tim pengelola jurnal akan meninjau data Anda.');
    }

    public function ajukanLoa()
    {
        return view('public/loa/request', [
            'title' => 'Ajukan LoA',
            'journals' => $this->getJournalOptions(),
        ]);
    }

    public function storeLoaRequest()
    {
        if ($this->isRateLimited('loa-request-submit', 5, 900)) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan pengiriman. Coba lagi beberapa menit lagi.');
        }

        $rules = [
            'article_url'         => 'permit_empty|valid_url|max_length[500]',
            'journal_id'          => 'required|is_natural_no_zero',
            'title'               => 'required|string|max_length[255]',
            'corresponding_email' => 'required|valid_email|max_length[255]',
            'whatsapp_number'     => 'required|regex_match[/^[0-9+\\-\\s]{8,20}$/]|max_length[30]',
            'volume'              => 'permit_empty|max_length[50]',
            'issue_number'        => 'permit_empty|max_length[50]',
            'published_year'      => 'permit_empty|regex_match[/^[0-9]{4}$/]',
            'authors_text'        => 'required|string|min_length[3]|max_length[5000]',
            'affiliations_text'   => 'permit_empty|string|max_length[5000]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Periksa kembali data permohonan LoA.');
        }

        $validated = $this->validator->getValidated();
        $email = strtolower(trim((string) ($validated['corresponding_email'] ?? '')));

        $journalId = (int) ($validated['journal_id'] ?? 0);
        $journal = null;
        try {
            $journal = (new JournalModel())->find($journalId);
        } catch (\Throwable $e) {
            $journal = null;
        }

        if (! is_array($journal)) {
            return redirect()->back()->withInput()->with('error', 'Silakan pilih jurnal yang tersedia.');
        }

        [, $articleId] = $this->parseOjsArticleUrl((string) ($validated['article_url'] ?? ''));
        $authors = $this->lines((string) $validated['authors_text']);
        $affiliations = $this->lines((string) ($validated['affiliations_text'] ?? ''));
        $requestModel = new LoaRequestModel();
        $requestCode = $this->generateRequestCode($requestModel);
        $now = date('Y-m-d H:i:s');

        $requestModel->insert([
            'journal_id'          => $journalId,
            'request_code'        => $requestCode,
            'article_url'         => trim((string) ($validated['article_url'] ?? '')),
            'article_id_external' => $articleId,
            'title'               => trim((string) $validated['title']),
            'authors_json'        => json_encode(array_map(static fn($name) => ['name' => $name], $authors), JSON_UNESCAPED_UNICODE),
            'corresponding_email' => $email,
            'whatsapp_number'     => trim((string) $validated['whatsapp_number']),
            'affiliations_json'   => $affiliations === [] ? null : json_encode($affiliations, JSON_UNESCAPED_UNICODE),
            'volume'              => $this->nullable((string) ($validated['volume'] ?? '')),
            'issue_number'        => $this->nullable((string) ($validated['issue_number'] ?? '')),
            'published_year'      => $this->nullable((string) ($validated['published_year'] ?? '')),
            'status'              => 'pending',
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        return redirect()
            ->to(site_url('ajukan-loa/status/' . $requestCode))
            ->with('success', 'Permohonan LoA berhasil dikirim.');
    }

    public function statusLoa(string $requestCode)
    {
        $row = (new LoaRequestModel())
            ->select('loa_requests.*, journals.name as journal_name')
            ->join('journals', 'journals.id = loa_requests.journal_id', 'left')
            ->where('loa_requests.request_code', $requestCode)
            ->first();

        if (! is_array($row)) {
            throw PageNotFoundException::forPageNotFound('Permohonan LoA tidak ditemukan.');
        }

        return view('public/loa/status', [
            'title' => 'Status Permohonan LoA',
            'loaRequest' => $row,
        ]);
    }

    public function verifikasiLoa()
    {
        return view('public/loa/verify', [
            'title' => 'Verifikasi LoA',
        ]);
    }

    public function submitVerifikasiLoa()
    {
        if ($this->isRateLimited('loa-verify-submit', 8, 600)) {
            return redirect()->to(site_url('verifikasi-loa'))->with('error', 'Terlalu banyak percobaan verifikasi. Coba lagi beberapa menit lagi.');
        }

        $rules = [
            'number' => 'required|string|min_length[5]|max_length[120]|regex_match[/^[A-Za-z0-9\\/\\-. ]+$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->to(site_url('verifikasi-loa'))
                ->withInput()
                ->with('error', 'Format nomor LoA tidak valid.');
        }

        $number = strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->request->getPost('number'))));

        return redirect()->to(site_url('verifikasi-loa/hasil') . '?number=' . rawurlencode($number));
    }

    public function hasilVerifikasiLoa()
    {
        $number = strtoupper(preg_replace('/\s+/', ' ', trim((string) $this->request->getGet('number'))));
        if ($number === '') {
            return redirect()->to(site_url('verifikasi-loa'))->with('error', 'Nomor LoA wajib diisi.');
        }

        if (! preg_match('/^[A-Z0-9\/\-. ]{5,120}$/', $number)) {
            return redirect()->to(site_url('verifikasi-loa'))->with('error', 'Format nomor LoA tidak valid.');
        }

        $normalized = str_replace(' ', '', $number);
        $letter = (new LoaLetterModel())
            ->where("REPLACE(UPPER(loa_number), ' ', '') =", $normalized)
            ->where('status', 'published')
            ->first();

        $journal = null;
        if (is_array($letter) && ! empty($letter['journal_id'])) {
            $journal = (new JournalModel())->find((int) $letter['journal_id']);
        }

        return view('public/loa/verify_result', [
            'title' => 'Hasil Verifikasi LoA',
            'number' => $number,
            'letter' => $letter,
            'journal' => $journal,
        ]);
    }

    public function journalLogo(int $id)
    {
        try {
            if (! \Config\Database::connect()->tableExists('journals')) {
                return $this->response->setStatusCode(404);
            }

            $journal = (new JournalModel())->select('id, logo_path')->find($id);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(404);
        }

        if (! is_array($journal)) {
            return $this->response->setStatusCode(404);
        }

        $logoPath = trim((string) ($journal['logo_path'] ?? ''));
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($logoPath, '/\\');
        if ($logoPath === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return $this->response->setStatusCode(404);
        }

        $binary = @file_get_contents($absolutePath);
        if (! is_string($binary) || $binary === '') {
            return $this->response->setStatusCode(404);
        }

        $mime = @mime_content_type($absolutePath) ?: 'image/png';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody($binary);
    }

    public function articleCover(int $id)
    {
        try {
            if (! \Config\Database::connect()->tableExists('educational_articles')) {
                return $this->response->setStatusCode(404);
            }

            $article = (new EducationalArticleModel())->select('id, cover_path, status')->find($id);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(404);
        }

        if (! is_array($article) || (string) ($article['status'] ?? '') !== 'published') {
            return $this->response->setStatusCode(404);
        }

        $coverPath = trim((string) ($article['cover_path'] ?? ''));
        $absolutePath = WRITEPATH . 'uploads/' . ltrim($coverPath, '/\\');
        if ($coverPath === '' || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return $this->response->setStatusCode(404);
        }

        $binary = @file_get_contents($absolutePath);
        if (! is_string($binary) || $binary === '') {
            return $this->response->setStatusCode(404);
        }

        return $this->response
            ->setHeader('Content-Type', @mime_content_type($absolutePath) ?: 'image/png')
            ->setHeader('Cache-Control', 'public, max-age=86400')
            ->setBody($binary);
    }

    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(site_url('dashboard'));
        }

        return view('public/auth/login', [
            'title' => 'Masuk Dashboard',
        ]);
    }

    public function authenticate()
    {
        $identity = trim((string) $this->request->getPost('identity'));
        $password = (string) $this->request->getPost('password');
        $loginBucket = 'login|' . strtolower($identity);

        if ($this->isRateLimitedPeek($loginBucket, 10, 300)) {
            return redirect()->back()->withInput()->with('login_error', 'Terlalu banyak percobaan masuk. Coba lagi beberapa menit lagi.');
        }

        $databaseUser = $this->findDatabaseUser($identity);

        if ($databaseUser && (int) ($databaseUser['is_active'] ?? 0) === 1 && password_verify($password, (string) $databaseUser['password'])) {
            $this->clearRateLimit($loginBucket);
            session()->set([
                'admin_logged_in' => true,
                'admin_user_id'   => (int) $databaseUser['id'],
                'admin_name'      => $databaseUser['name'],
                'admin_email'     => $databaseUser['email'],
                'admin_role'      => $databaseUser['role'],
            ]);

            return redirect()->to(site_url('dashboard'));
        }

        $this->hitRateLimit($loginBucket, 300);

        return redirect()
            ->back()
            ->withInput()
            ->with('login_error', 'Email/username atau kata sandi tidak sesuai.');
    }

    public function logout()
    {
        session()->remove([
            'admin_logged_in',
            'admin_user_id',
            'admin_name',
            'admin_email',
            'admin_role',
        ]);

        return redirect()->to(site_url('login'));
    }

    private function findDatabaseUser(string $identity): ?array
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('users')) {
                return null;
            }

            return (new UserModel())
                ->groupStart()
                ->where('email', $identity)
                ->orWhere('username', $identity)
                ->groupEnd()
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function getJournalOptions(): array
    {
        try {
            if (! \Config\Database::connect()->tableExists('journals')) {
                return [];
            }

            return (new JournalModel())->orderBy('name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function findJournalBySlug(string $slug): ?array
    {
        $slug = trim($slug);
        if ($slug === '') {
            return null;
        }

        try {
            if (! \Config\Database::connect()->tableExists('journals')) {
                return null;
            }

            $journal = (new JournalModel())->where('slug', $slug)->first();

            return is_array($journal) ? $journal : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function generateRecruitmentCode(EditorReviewerApplicationModel $model): string
    {
        $prefix = 'ER-' . date('Ymd') . '-';
        $rows = $model
            ->select('application_code')
            ->like('application_code', $prefix, 'after')
            ->findAll();
        $maxSeq = 0;

        foreach ($rows as $row) {
            $code = (string) ($row['application_code'] ?? '');
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', $code, $matches)) {
                $maxSeq = max($maxSeq, (int) $matches[1]);
            }
        }

        return $prefix . str_pad((string) ($maxSeq + 1), 4, '0', STR_PAD_LEFT);
    }

    private function parseOjsArticleUrl(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if (preg_match('#/index\.php/([^/]+)/article/view/(\d+)#', $path, $matches)) {
            return [$matches[1], $matches[2]];
        }

        if (preg_match('#/([^/]+)/article/view/(\d+)#', $path, $matches)) {
            return [$matches[1], $matches[2]];
        }

        return [null, null];
    }

    private function lines(string $value): array
    {
        return array_values(array_filter(array_map(
            static fn($line) => trim((string) $line),
            preg_split("/\r\n|\n|\r/", trim($value)) ?: []
        ), static fn($line) => $line !== ''));
    }

    private function nullable(string $value): ?string
    {
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function generateRequestCode(LoaRequestModel $requestModel): string
    {
        $rows = $requestModel->select('request_code')->findAll();
        $maxSeq = 0;

        foreach ($rows as $row) {
            $code = trim((string) ($row['request_code'] ?? ''));
            if (preg_match('/^(?:PLPI|IMP|MPL|REQ)-(\d+)$/', $code, $matches)) {
                $maxSeq = max($maxSeq, (int) $matches[1]);
            }
        }

        return 'PLPI-' . str_pad((string) ($maxSeq + 1), 5, '0', STR_PAD_LEFT);
    }

    private function getStats(): array
    {
        $totalRequests = 0;
        $publishedLetters = 0;
        $waitingRequests = 0;
        $processedRequests = 0;

        try {
            $db = \Config\Database::connect();

            if ($db->tableExists('loa_requests')) {
                $totalRequests = (int) $db->table('loa_requests')->countAllResults();

                if ($db->tableExists('loa_letters')) {
                    $publishedLetters = (int) $db->table('loa_letters')
                        ->where('status', 'published')
                        ->countAllResults();

                    $waitingRequests = (int) $db->table('loa_requests')
                        ->whereIn('status', ['pending', 'revision'])
                        ->where("NOT EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false)
                        ->countAllResults();

                    $processedRequests = (int) $db->table('loa_requests')
                        ->where('status', 'approved')
                        ->where("NOT EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published')", null, false)
                        ->countAllResults();
                } else {
                    $waitingRequests = (int) $db->table('loa_requests')
                        ->whereIn('status', ['pending', 'revision'])
                        ->countAllResults();

                    $processedRequests = (int) $db->table('loa_requests')
                        ->where('status', 'approved')
                        ->countAllResults();
                }
            }
        } catch (\Throwable $e) {
            $totalRequests = 0;
            $publishedLetters = 0;
            $waitingRequests = 0;
            $processedRequests = 0;
        }

        return [
            [
                'label' => 'Permohonan',
                'value' => $totalRequests,
                'note'  => 'Total pengajuan masuk',
            ],
            [
                'label' => 'LoA Terbit',
                'value' => $publishedLetters,
                'note'  => 'Sudah disetujui',
            ],
            [
                'label' => 'Menunggu',
                'value' => $waitingRequests,
                'note'  => 'Menunggu tinjauan admin',
            ],
            [
                'label' => 'Diproses',
                'value' => $processedRequests,
                'note'  => 'Sedang ditinjau',
            ],
        ];
    }

    private function getLatestRequests(): array
    {
        return [
            [
                'code'   => 'PLPI-00064',
                'title'  => 'Pelatihan Desain Grafis CorelDraw Untuk Membuat Poster di SMK Ibrahimy 1 Sukorejo Situbondo',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00063',
                'title'  => 'Persepsi Guru terhadap Pemberian Insentif dan Tunjangan dalam Meningkatkan Kinerja Guru di Sekolah Swasta Kecamatan Ciputat, Kota Tangerang Selatan',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00061',
                'title'  => 'Pendampingan Pelayanan Administrasi dalam Meningkatkan Kualitas Layanan bagi Guru dan Tenaga Kependidikan',
                'status' => 'Disetujui',
                'date'   => '27-06-2026',
            ],
            [
                'code'   => 'PLPI-00060',
                'title'  => 'Media Cakra Math untuk Menstimulasi Kemampuan Logika Matematika Anak Usia 5–6 Tahun',
                'status' => 'Diproses',
                'date'   => '26-06-2026',
            ],
            [
                'code'   => 'PLPI-00059',
                'title'  => 'Edukasi Teknik Nonfarmakologi untuk Mengatasi Ketidaknyamanan pada Penderita Hipertensi',
                'status' => 'Disetujui',
                'date'   => '26-06-2026',
            ],
        ];
    }

    private function getJournals(): array
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('journals') && $db->tableExists('publishers')) {
                $rows = (new JournalModel())
                    ->select('journals.id, journals.name, journals.code, journals.slug, journals.issn, journals.e_issn, journals.p_issn, journals.logo_path, journals.website_url, journals.updated_at, publishers.name as publisher_name')
                    ->join('publishers', 'publishers.id = journals.publisher_id', 'left')
                    ->orderBy('journals.id', 'ASC')
                    ->findAll(8);

                if ($rows !== []) {
                    return array_map(static function (array $journal): array {
                        $issnParts = array_filter([
                            trim((string) ($journal['e_issn'] ?? '')) !== '' ? 'E-ISSN ' . trim((string) $journal['e_issn']) : '',
                            trim((string) ($journal['p_issn'] ?? '')) !== '' ? 'P-ISSN ' . trim((string) $journal['p_issn']) : '',
                            trim((string) ($journal['issn'] ?? '')) !== '' ? 'ISSN ' . trim((string) $journal['issn']) : '',
                        ]);

                        return [
                            'id' => (int) ($journal['id'] ?? 0),
                            'name' => (string) ($journal['name'] ?? ''),
                            'category' => (string) ($journal['publisher_name'] ?? 'Jurnal PLPI'),
                            'issn' => $issnParts !== [] ? implode(' / ', $issnParts) : 'ISSN belum tersedia',
                            'url' => (string) ($journal['website_url'] ?? '#'),
                            'recruitment_url' => trim((string) ($journal['slug'] ?? '')) !== ''
                                ? site_url('rekrutmen-editor-reviewer/jurnal/' . (string) $journal['slug'])
                                : '#',
                            'logo_url' => trim((string) ($journal['logo_path'] ?? '')) !== ''
                                ? site_url('journal-logo/' . (int) ($journal['id'] ?? 0) . '?v=' . rawurlencode((string) ($journal['updated_at'] ?? '')))
                                : null,
                        ];
                    }, $rows);
                }
            }
        } catch (\Throwable $e) {
            // Fall back to static public content below.
        }

        return [
            [
                'id'       => 0,
                'name'     => 'Leibniz: Jurnal Matematika',
                'category' => 'Matematika',
                'issn'     => 'E-ISSN 2775-2356',
                'url'      => '#',
                'recruitment_url' => '#',
                'logo_url' => null,
            ],
            [
                'id'       => 0,
                'name'     => 'Edukasi Tematik: Jurnal Pendidikan Sekolah Dasar',
                'category' => 'Pendidikan Dasar',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
                'recruitment_url' => '#',
                'logo_url' => null,
            ],
            [
                'id'       => 0,
                'name'     => 'Leksikon: Jurnal Pendidikan Bahasa, Sastra, & Budaya',
                'category' => 'Bahasa dan Sastra',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
                'recruitment_url' => '#',
                'logo_url' => null,
            ],
            [
                'id'       => 0,
                'name'     => 'Sibernetik: Jurnal Pendidikan dan Pembelajaran',
                'category' => 'Pendidikan dan Pembelajaran',
                'issn'     => 'E-ISSN -',
                'url'      => '#',
                'recruitment_url' => '#',
                'logo_url' => null,
            ],
        ];
    }

    private function getArticles(bool $includeContent = false): array
    {
        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('educational_articles') && $db->tableExists('article_categories')) {
                $select = $includeContent
                    ? 'educational_articles.*, article_categories.name AS category_name'
                    : 'educational_articles.id, educational_articles.slug, educational_articles.title, educational_articles.summary, educational_articles.cover_path, educational_articles.image_alt, educational_articles.published_at, educational_articles.created_at, educational_articles.updated_at, educational_articles.sort_order, article_categories.name AS category_name';

                $rows = (new EducationalArticleModel())
                    ->select($select)
                    ->join('article_categories', 'article_categories.id = educational_articles.category_id', 'left')
                    ->where('educational_articles.status', 'published')
                    ->orderBy('educational_articles.sort_order', 'ASC')
                    ->orderBy('educational_articles.published_at', 'DESC')
                    ->orderBy('educational_articles.id', 'DESC')
                    ->findAll();

                if ($rows !== []) {
                    return array_map(fn (array $row): array => $this->mapArticleRow($row, $includeContent), $rows);
                }
            }
        } catch (\Throwable $e) {
            // Fall back to static public content below.
        }

        return [
            [
                'slug'      => 'cara-menulis-artikel-ilmiah-yang-baik',
                'title'     => 'Cara Menulis Artikel Ilmiah yang Baik dan Sistematis',
                'category'  => 'Penulisan Ilmiah',
                'date'      => '27 Juni 2026',
                'read_time' => '5 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Meja belajar dengan buku dan catatan untuk menulis artikel ilmiah',
                'summary'   => 'Panduan ringkas memahami tahapan awal dalam menyusun artikel ilmiah, mulai dari pemilihan topik hingga penyusunan struktur naskah.',
                'content'   => [
                    'Artikel ilmiah merupakan karya tulis akademik yang disusun berdasarkan kaidah ilmiah, data, argumentasi, dan rujukan yang dapat dipertanggungjawabkan.',
                    'Langkah pertama dalam menulis artikel ilmiah adalah menentukan topik yang jelas, spesifik, dan relevan dengan bidang kajian. Topik yang terlalu luas akan menyulitkan penulis dalam membangun fokus pembahasan.',
                    'Setelah topik ditentukan, penulis perlu menyusun kerangka artikel. Struktur umum artikel ilmiah biasanya meliputi judul, abstrak, pendahuluan, metode, hasil dan pembahasan, kesimpulan, serta daftar pustaka.',
                    'Artikel yang baik tidak hanya menyajikan informasi, tetapi juga menunjukkan hubungan logis antara masalah, teori, metode, temuan, dan simpulan.',
                ],
            ],
            [
                'slug'      => 'memahami-struktur-imrad-dalam-artikel-ilmiah',
                'title'     => 'Memahami Struktur IMRAD dalam Artikel Ilmiah',
                'category'  => 'Struktur Artikel',
                'date'      => '27 Juni 2026',
                'read_time' => '4 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Laptop terbuka untuk menyusun struktur artikel ilmiah',
                'summary'   => 'Mengenal struktur Introduction, Methods, Results, and Discussion sebagai format umum dalam penulisan artikel ilmiah.',
                'content'   => [
                    'IMRAD merupakan singkatan dari Introduction, Methods, Results, and Discussion. Struktur ini banyak digunakan dalam artikel ilmiah karena memudahkan pembaca memahami alur penelitian.',
                    'Bagian Introduction berisi latar belakang, masalah, gap penelitian, dan tujuan. Bagian Methods menjelaskan pendekatan, subjek, instrumen, prosedur, dan teknik analisis data.',
                    'Bagian Results menyajikan temuan penelitian secara objektif, sedangkan Discussion menafsirkan temuan tersebut dengan mengaitkannya pada teori atau hasil penelitian terdahulu.',
                    'Dengan struktur IMRAD, artikel menjadi lebih runtut, sistematis, dan mudah dievaluasi oleh editor maupun reviewer.',
                ],
            ],
            [
                'slug'      => 'tips-memilih-jurnal-yang-tepat',
                'title'     => 'Tips Memilih Jurnal yang Tepat untuk Publikasi',
                'category'  => 'Publikasi',
                'date'      => '26 Juni 2026',
                'read_time' => '5 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Ruang kerja penulis dengan laptop dan buku referensi',
                'summary'   => 'Hal-hal penting yang perlu diperhatikan penulis sebelum mengirimkan artikel ke jurnal ilmiah.',
                'content'   => [
                    'Memilih jurnal yang tepat merupakan langkah penting dalam proses publikasi ilmiah. Penulis perlu memastikan bahwa ruang lingkup artikel sesuai dengan fokus dan cakupan jurnal.',
                    'Sebelum submit, baca terlebih dahulu template, pedoman penulis, frekuensi terbit, informasi biaya, dan etika publikasi yang berlaku pada jurnal tersebut.',
                    'Hindari mengirim artikel yang sama ke lebih dari satu jurnal secara bersamaan karena hal tersebut bertentangan dengan etika publikasi.',
                    'Jurnal yang tepat akan membantu artikel diproses secara lebih relevan, baik dari sisi editor, reviewer, maupun pembaca sasaran.',
                ],
            ],
            [
                'slug'      => 'etika-publikasi-ilmiah-yang-perlu-dipahami-penulis',
                'title'     => 'Etika Publikasi Ilmiah yang Perlu Dipahami Penulis',
                'category'  => 'Etika Publikasi',
                'date'      => '26 Juni 2026',
                'read_time' => '6 menit baca',
                'image'     => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1200&q=80',
                'image_alt' => 'Diskusi tim akademik tentang etika publikasi ilmiah',
                'summary'   => 'Membahas prinsip orisinalitas, sitasi, plagiarisme, konflik kepentingan, dan tanggung jawab penulis dalam publikasi ilmiah.',
                'content'   => [
                    'Etika publikasi ilmiah menjadi fondasi penting dalam menjaga kualitas dan integritas akademik.',
                    'Penulis wajib memastikan bahwa naskah yang dikirim merupakan karya orisinal, belum pernah diterbitkan, dan tidak sedang diproses pada jurnal lain.',
                    'Setiap gagasan, data, atau kutipan dari sumber lain harus dicantumkan secara benar melalui sitasi dan daftar pustaka.',
                    'Pemahaman terhadap etika publikasi membantu mencegah plagiarisme, duplikasi publikasi, manipulasi data, dan konflik kepentingan.',
                ],
            ],
        ];
    }

    private function getArticleBySlug(string $slug): ?array
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('educational_articles') || ! $db->tableExists('article_categories')) {
                return null;
            }

            $row = (new EducationalArticleModel())
                ->select('educational_articles.*, article_categories.name AS category_name')
                ->join('article_categories', 'article_categories.id = educational_articles.category_id', 'left')
                ->where('educational_articles.status', 'published')
                ->where('educational_articles.slug', $slug)
                ->first();

            return is_array($row) ? $this->mapArticleRow($row, true) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function mapArticleRow(array $row, bool $includeContent): array
    {
        $contentHtml = '';
        $plainContent = '';
        if ($includeContent) {
            $contentHtml = $this->cleanArticleHtml((string) ($row['content'] ?? ''));
            $plainContent = trim(strip_tags($contentHtml));
        }

        $summary = trim((string) ($row['summary'] ?? ''));
        if ($summary === '' && $plainContent !== '') {
            $summary = word_limiter($plainContent, 28);
        }

        $publishedAt = (string) ($row['published_at'] ?? $row['created_at'] ?? '');
        $date = $publishedAt !== '' ? date('d F Y', strtotime($publishedAt)) : date('d F Y');
        $image = trim((string) ($row['cover_path'] ?? '')) !== ''
            ? site_url('article-cover/' . (int) ($row['id'] ?? 0) . '?v=' . rawurlencode((string) ($row['updated_at'] ?? '')))
            : 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=1200&q=80';

        $article = [
            'id'           => (int) ($row['id'] ?? 0),
            'slug'         => (string) ($row['slug'] ?? ''),
            'title'        => (string) ($row['title'] ?? ''),
            'category'     => (string) ($row['category_name'] ?? 'Artikel Edukatif'),
            'date'         => $date,
            'read_time'    => $this->estimateArticleReadTime($includeContent ? $plainContent : $summary),
            'image'        => $image,
            'image_alt'    => (string) ($row['image_alt'] ?? $row['title'] ?? 'Cover artikel edukatif'),
            'summary'      => $summary,
            'content'      => [],
            'content_html' => '',
        ];

        if ($includeContent) {
            $article['content'] = $this->htmlToParagraphs($contentHtml);
            $article['content_html'] = $contentHtml;
        }

        return $article;
    }

    private function cleanArticleHtml(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $allowedAttrs = [
            'a' => ['href', 'title', 'target', 'rel'],
            'iframe' => ['src', 'title', 'width', 'height', 'allow', 'allowfullscreen', 'frameborder', 'loading', 'referrerpolicy'],
        ];

        $previous = libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $this->sanitizeDomNode($dom, $allowedAttrs);
        $clean = $dom->saveHTML() ?: '';
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $clean = preg_replace('#^<div>|</div>$#', '', $clean) ?: $clean;

        return trim($clean);
    }

    private function htmlToParagraphs(string $html): array
    {
        $text = trim(strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $html)));
        $paragraphs = array_values(array_filter(array_map('trim', preg_split('/\n+/', $text) ?: [])));

        return $paragraphs !== [] ? $paragraphs : ['Artikel belum memiliki isi.'];
    }

    private function estimateArticleReadTime(string $content): string
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = max(1, (int) ceil($wordCount / 180));

        return $minutes . ' menit baca';
    }

    private function sanitizeRichHtml(string $html): string
    {
        return $this->cleanArticleHtml($html);
    }

    private function sanitizeDomNode(DOMNode $node, array $allowedAttrs): void
    {
        if (! $node->hasChildNodes()) {
            return;
        }

        $allowedTags = ['p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'a', 'table', 'thead', 'tbody', 'tr', 'td', 'th', 'div', 'iframe'];

        for ($i = $node->childNodes->length - 1; $i >= 0; $i--) {
            $child = $node->childNodes->item($i);
            if (! $child) {
                continue;
            }

            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                if (! in_array($tag, $allowedTags, true)) {
                    $child->parentNode?->removeChild($child);
                    continue;
                }

                if ($child->hasAttributes()) {
                    $attrs = [];
                    foreach ($child->attributes as $attr) {
                        $attrs[] = strtolower($attr->nodeName);
                    }

                    foreach ($attrs as $attrName) {
                        $keep = in_array($attrName, $allowedAttrs[$tag] ?? [], true);
                        if (! $keep) {
                            $child->removeAttribute($attrName);
                            continue;
                        }

                        if ($tag === 'a' && $attrName === 'href') {
                            $href = trim((string) $child->attributes->getNamedItem('href')?->nodeValue);
                            if ($href === '' || preg_match('/^\s*javascript:/i', $href) === 1) {
                                $child->setAttribute('href', '#');
                            }
                        }
                    }
                }

                if ($tag === 'iframe') {
                    if (! $child instanceof DOMElement || ! $this->sanitizeYoutubeIframe($child)) {
                        $child->parentNode?->removeChild($child);
                        continue;
                    }
                }
            }

            $this->sanitizeDomNode($child, $allowedAttrs);
        }
    }

    private function sanitizeYoutubeIframe(DOMElement $iframe): bool
    {
        $src = trim($iframe->getAttribute('src'));
        $embedUrl = $this->normalizeYoutubeEmbedUrl($src);
        if ($embedUrl === '') {
            return false;
        }

        $iframe->setAttribute('src', $embedUrl);
        $iframe->setAttribute('loading', 'lazy');
        $iframe->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
        $iframe->setAttribute('allowfullscreen', 'allowfullscreen');

        if (trim($iframe->getAttribute('title')) === '') {
            $iframe->setAttribute('title', 'Video YouTube');
        }

        if (trim($iframe->getAttribute('allow')) === '') {
            $iframe->setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
        }

        return true;
    }

    private function normalizeYoutubeEmbedUrl(string $src): string
    {
        if ($src === '' || preg_match('/^\s*javascript:/i', $src) === 1) {
            return '';
        }

        $parts = parse_url($src);
        if (! is_array($parts)) {
            return '';
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');
        $query = (string) ($parts['query'] ?? '');

        if ($scheme !== 'https') {
            return '';
        }

        $youtubeHosts = ['youtube.com', 'www.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'];
        if (in_array($host, $youtubeHosts, true) && preg_match('#^/embed/[-_A-Za-z0-9]+#', $path) === 1) {
            return 'https://' . $host . $path . ($query !== '' ? '?' . $query : '');
        }

        if (in_array($host, ['youtube.com', 'www.youtube.com'], true) && $path === '/watch') {
            parse_str($query, $queryParams);
            $videoId = (string) ($queryParams['v'] ?? '');
            if (preg_match('/^[-_A-Za-z0-9]{6,}$/', $videoId) === 1) {
                return 'https://www.youtube.com/embed/' . $videoId;
            }
        }

        if ($host === 'youtu.be') {
            $videoId = trim($path, '/');
            if (preg_match('/^[-_A-Za-z0-9]{6,}$/', $videoId) === 1) {
                return 'https://www.youtube.com/embed/' . $videoId;
            }
        }

        return '';
    }

    private function isRateLimited(string $bucket, int $maxAttempts, int $windowSeconds): bool
    {
        $cache = $this->rateLimitCache();
        if (! $cache) {
            return false;
        }

        $key = $this->rateLimitKey($bucket);
        $state = $cache->get($key);
        $now = time();
        if (! is_array($state) || (int) ($state['expires_at'] ?? 0) <= $now) {
            $state = ['count' => 0, 'expires_at' => $now + $windowSeconds];
        }

        $state['count'] = (int) ($state['count'] ?? 0) + 1;
        $cache->save($key, $state, $windowSeconds);

        return $state['count'] > $maxAttempts;
    }

    private function isRateLimitedPeek(string $bucket, int $maxAttempts, int $windowSeconds): bool
    {
        $cache = $this->rateLimitCache();
        if (! $cache) {
            return false;
        }

        $state = $cache->get($this->rateLimitKey($bucket));
        if (! is_array($state) || (int) ($state['expires_at'] ?? 0) <= time()) {
            return false;
        }

        return (int) ($state['count'] ?? 0) >= $maxAttempts;
    }

    private function hitRateLimit(string $bucket, int $windowSeconds): void
    {
        $cache = $this->rateLimitCache();
        if (! $cache) {
            return;
        }

        $key = $this->rateLimitKey($bucket);
        $state = $cache->get($key);
        $now = time();
        if (! is_array($state) || (int) ($state['expires_at'] ?? 0) <= $now) {
            $state = ['count' => 0, 'expires_at' => $now + $windowSeconds];
        }

        $state['count'] = (int) ($state['count'] ?? 0) + 1;
        $cache->save($key, $state, $windowSeconds);
    }

    private function clearRateLimit(string $bucket): void
    {
        $cache = $this->rateLimitCache();
        if ($cache && method_exists($cache, 'delete')) {
            $cache->delete($this->rateLimitKey($bucket));
        }
    }

    private function rateLimitCache(): ?object
    {
        try {
            $cache = service('cache');
        } catch (\Throwable $e) {
            return null;
        }

        return is_object($cache) && method_exists($cache, 'get') && method_exists($cache, 'save') ? $cache : null;
    }

    private function rateLimitKey(string $bucket): string
    {
        return 'plpi_rl_' . md5($bucket . '|' . (string) $this->request->getIPAddress());
    }
}
