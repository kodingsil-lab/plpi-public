<?php

if (! function_exists('plpi_app_settings')) {
    function plpi_app_settings(): array
    {
        static $cached = null;

        if (is_array($cached)) {
            return $cached;
        }

        $cached = [];
        try {
            $db = db_connect();
            if (! $db || ! $db->tableExists('app_settings')) {
                return $cached;
            }

            $row = $db->table('app_settings')->limit(1)->get()->getRowArray();
            $cached = is_array($row) ? $row : [];
        } catch (\Throwable $e) {
            $cached = [];
        }

        return $cached;
    }
}

if (! function_exists('plpi_app_setting')) {
    function plpi_app_setting(string $key, $default = null)
    {
        $settings = plpi_app_settings();

        return $settings[$key] ?? $default;
    }
}

if (! function_exists('plpi_asset_url')) {
    function plpi_asset_url(?string $path, string $fallback = ''): string
    {
        $target = trim((string) $path);
        if ($target === '') {
            $target = $fallback;
        }

        return $target !== '' ? base_url($target) : '';
    }
}

if (! function_exists('plpi_timezone_options')) {
    function plpi_timezone_options(): array
    {
        return [
            'Asia/Jakarta'  => 'Asia/Jakarta (WIB)',
            'Asia/Makassar' => 'Asia/Makassar (WITA)',
            'Asia/Jayapura' => 'Asia/Jayapura (WIT)',
        ];
    }
}
