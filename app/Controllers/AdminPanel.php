<?php

namespace App\Controllers;

class AdminPanel extends BaseController
{
    protected $helpers = ['url'];

    public function dashboard()
    {
        if (! session()->get('admin_logged_in')) {
            return redirect()->to(site_url('login'));
        }

        return view('admin/dashboard', [
            'title'      => 'Dashboard Admin',
            'activeMenu' => 'dashboard',
            'eyebrow'    => 'Dashboard',
            'pageTitle'  => 'Panel Admin PLPI',
            'adminName'  => session()->get('admin_name'),
            'adminEmail' => session()->get('admin_email'),
            'adminRole'  => session()->get('admin_role'),
            'stats'      => $this->dashboardStats(),
            'loaRows'    => $this->latestLoaRequests(),
        ]);
    }

    private function dashboardStats(): array
    {
        return [
            'loa_requests'       => $this->safeCount('loa_requests'),
            'loa_requests_week'  => $this->safeCountThisWeek('loa_requests'),
            'loa_letters'        => $this->safeCount('loa_letters', ['status' => 'published']),
            'journals'           => $this->safeCount('journals'),
            'educative_articles' => $this->safeArticleCount(),
        ];
    }

    private function latestLoaRequests(): array
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists('loa_requests')) {
                return [];
            }

            $builder = $db->table('loa_requests')
                ->select("loa_requests.id, loa_requests.request_code, loa_requests.title, loa_requests.status, loa_requests.created_at, loa_requests.approved_at, EXISTS(SELECT 1 FROM loa_letters ll WHERE ll.loa_request_id = loa_requests.id AND ll.status = 'published') as has_published_letter")
                ->orderBy('loa_requests.id', 'DESC')
                ->limit(15);

            return $builder->get()->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function safeCount(string $table, array $where = []): int
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists($table)) {
                return 0;
            }

            $builder = $db->table($table);
            foreach ($where as $field => $value) {
                $builder->where($field, $value);
            }

            return (int) $builder->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeCountThisWeek(string $table): int
    {
        try {
            $db = \Config\Database::connect();
            if (! $db->tableExists($table)) {
                return 0;
            }

            return (int) $db->table($table)
                ->where('created_at >=', date('Y-m-d 00:00:00', strtotime('monday this week')))
                ->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeArticleCount(): int
    {
        foreach (['educational_articles', 'articles', 'educative_articles', 'artikel_edukatif'] as $table) {
            $count = $this->safeCount($table);
            if ($count > 0) {
                return $count;
            }
        }

        return 0;
    }
}
