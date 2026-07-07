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
