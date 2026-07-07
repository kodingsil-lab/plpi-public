<?php

namespace App\Controllers;

class AppAssetController extends BaseController
{
    protected $helpers = ['app_settings'];

    public function favicon()
    {
        $settings = plpi_app_settings();
        $faviconPath = plpi_favicon_path($settings);

        return $this->servePublicUpload($faviconPath);
    }

    private function servePublicUpload(string $path)
    {
        $relativePath = ltrim(trim($path), '/\\');
        $absolutePath = realpath(FCPATH . $relativePath);
        $uploadRoot = realpath(FCPATH . 'uploads');

        if (
            $relativePath === ''
            || $absolutePath === false
            || $uploadRoot === false
            || ! str_starts_with($absolutePath, $uploadRoot . DIRECTORY_SEPARATOR)
            || ! is_file($absolutePath)
            || ! is_readable($absolutePath)
        ) {
            return $this->response->setStatusCode(404);
        }

        $binary = @file_get_contents($absolutePath);
        if (! is_string($binary) || $binary === '') {
            return $this->response->setStatusCode(404);
        }

        $mime = plpi_favicon_mime($absolutePath);
        if ($mime === '') {
            $mime = @mime_content_type($absolutePath) ?: 'image/png';
        }

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Cache-Control', 'public, max-age=86400, must-revalidate')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($binary);
    }
}
