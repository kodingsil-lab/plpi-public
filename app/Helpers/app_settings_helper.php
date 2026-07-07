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

if (! function_exists('plpi_favicon_path')) {
    function plpi_favicon_path(?array $settings = null): string
    {
        $settings ??= plpi_app_settings();

        return trim((string) ($settings['favicon_path'] ?? ''));
    }
}

if (! function_exists('plpi_favicon_mime')) {
    function plpi_favicon_mime(string $path): string
    {
        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_EXTENSION));

        return match ($extension) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            default => '',
        };
    }
}

if (! function_exists('plpi_favicon_url')) {
    function plpi_favicon_url(?array $settings = null): string
    {
        $settings ??= plpi_app_settings();
        $path = plpi_favicon_path($settings);

        if ($path === '') {
            return '';
        }

        $version = rawurlencode((string) ($settings['updated_at'] ?? time()));

        return plpi_asset_url($path) . '?v=' . $version;
    }
}

if (! function_exists('plpi_favicon_tags')) {
    function plpi_favicon_tags(?array $settings = null): string
    {
        $settings ??= plpi_app_settings();
        $url = plpi_favicon_url($settings);
        $path = plpi_favicon_path($settings);

        if ($url === '') {
            return '';
        }

        $href = esc($url, 'attr');
        $mime = plpi_favicon_mime($path);
        $type = $mime !== '' ? ' type="' . esc($mime, 'attr') . '"' : '';

        return implode("\n", [
            '<link rel="icon" href="' . $href . '"' . $type . '>',
            '<link rel="shortcut icon" href="' . $href . '"' . $type . '>',
            '<link rel="apple-touch-icon" href="' . $href . '">',
            '<meta name="msapplication-TileImage" content="' . $href . '">',
        ]);
    }
}

if (! function_exists('plpi_statcounter_code')) {
    function plpi_statcounter_code(?array $settings = null): string
    {
        $settings ??= plpi_app_settings();

        return trim((string) ($settings['statcounter_code'] ?? ''));
    }
}

if (! function_exists('plpi_social_image_url')) {
    function plpi_social_image_url(?array $settings = null): string
    {
        $settings ??= plpi_app_settings();

        foreach (['public_logo_path', 'header_logo_path', 'login_logo_path', 'favicon_path'] as $key) {
            $path = trim((string) ($settings[$key] ?? ''));
            if ($path !== '') {
                return plpi_asset_url($path);
            }
        }

        return base_url('plpi/images/hero1.png');
    }
}

if (! function_exists('plpi_social_meta_tags')) {
    function plpi_social_meta_tags(?array $settings = null, ?string $title = null, ?string $description = null, ?string $url = null): string
    {
        $settings ??= plpi_app_settings();
        $title = trim((string) ($title ?: 'PLPI - Pusat Layanan Publikasi Ilmiah'));
        $description = trim((string) ($description ?: 'Layanan pengajuan LoA, informasi jurnal, pendampingan publikasi, dan edukasi literasi ilmiah Universitas San Pedro.'));
        $url = trim((string) ($url ?: current_url()));
        $imageUrl = plpi_social_image_url($settings);

        return implode("\n", [
            '<meta name="description" content="' . esc($description, 'attr') . '">',
            '<meta property="og:type" content="website">',
            '<meta property="og:site_name" content="PLPI">',
            '<meta property="og:title" content="' . esc($title, 'attr') . '">',
            '<meta property="og:description" content="' . esc($description, 'attr') . '">',
            '<meta property="og:url" content="' . esc($url, 'attr') . '">',
            '<meta property="og:image" content="' . esc($imageUrl, 'attr') . '">',
            '<meta property="og:image:secure_url" content="' . esc($imageUrl, 'attr') . '">',
            '<meta property="og:image:type" content="image/png">',
            '<meta property="og:image:width" content="512">',
            '<meta property="og:image:height" content="512">',
            '<meta name="twitter:card" content="summary_large_image">',
            '<meta name="twitter:title" content="' . esc($title, 'attr') . '">',
            '<meta name="twitter:description" content="' . esc($description, 'attr') . '">',
            '<meta name="twitter:image" content="' . esc($imageUrl, 'attr') . '">',
        ]);
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
