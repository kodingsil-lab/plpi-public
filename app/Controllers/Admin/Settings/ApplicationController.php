<?php

namespace App\Controllers\Admin\Settings;

use App\Controllers\BaseController;
use App\Models\AppSettingModel;

class ApplicationController extends BaseController
{
    protected $helpers = ['url', 'app_settings'];

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        $row = [];
        $databaseError = null;

        if ($this->tableReady()) {
            $fetched = (new AppSettingModel())->first();
            $row = is_array($fetched) ? $fetched : [];
        } else {
            $databaseError = 'Tabel app_settings belum tersedia. Jalankan migrasi terlebih dahulu.';
        }

        return view('admin/settings/application/index', [
            'title'           => 'Aplikasi',
            'activeMenu'      => 'settings_application',
            'eyebrow'         => 'Pengaturan',
            'pageTitle'       => 'Aplikasi',
            'adminName'       => session()->get('admin_name'),
            'adminEmail'      => session()->get('admin_email'),
            'adminRole'       => session()->get('admin_role'),
            'row'             => $row,
            'databaseError'   => $databaseError,
            'timezoneOptions' => plpi_timezone_options(),
        ]);
    }

    public function update()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to(site_url('login'));
        }

        if (! $this->tableReady()) {
            return redirect()->back()->withInput()->with('error', 'Tabel app_settings belum tersedia. Jalankan migrasi terlebih dahulu.');
        }

        $timezone = (string) $this->request->getPost('app_timezone');
        if (! array_key_exists($timezone, plpi_timezone_options())) {
            return redirect()->back()->withInput()->with('error', 'Zona waktu yang dipilih tidak valid.');
        }

        $payload = [
            'app_timezone'      => $timezone,
            'statcounter_code'  => trim((string) $this->request->getPost('statcounter_code')),
        ];

        $fileMap = [
            'app_logo' => ['header_logo_path', 'login_logo_path', 'public_logo_path'],
            'favicon'  => ['favicon_path'],
        ];

        foreach ($fileMap as $inputName => $fieldNames) {
            $storedPath = $this->storeSettingFile($inputName);
            if ($storedPath === null) {
                continue;
            }

            foreach ($fieldNames as $fieldName) {
                $payload[$fieldName] = $storedPath;
            }

            if ($inputName === 'favicon') {
                $this->syncBrowserFavicon($storedPath);
            }
        }

        $model = new AppSettingModel();
        $row = $model->first();
        if (! empty($row['id'])) {
            $model->update((int) $row['id'], $payload);
        } else {
            $model->insert($payload);
        }

        return redirect()->to(site_url('dashboard/settings/application'))->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }

    private function storeSettingFile(string $inputName): ?string
    {
        $file = $this->request->getFile($inputName);
        if (! $file || ! $file->isValid() || $file->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower((string) $file->getExtension());
        $allowed = $inputName === 'favicon'
            ? ['ico', 'png', 'webp']
            : ['png', 'jpg', 'jpeg', 'webp'];

        if (! in_array($ext, $allowed, true)) {
            return null;
        }

        $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'app-settings';
        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        $newName = $inputName . '-' . $file->getRandomName();
        $file->move($targetDir, $newName, true);

        return 'uploads/app-settings/' . $newName;
    }

    private function syncBrowserFavicon(string $storedPath): void
    {
        $source = realpath(FCPATH . ltrim($storedPath, '/\\'));
        $uploadRoot = realpath(FCPATH . 'uploads');

        if (
            $source === false
            || $uploadRoot === false
            || ! str_starts_with($source, $uploadRoot . DIRECTORY_SEPARATOR)
            || ! is_file($source)
            || ! is_readable($source)
        ) {
            return;
        }

        @copy($source, FCPATH . 'favicon.ico');
    }

    private function tableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('app_settings');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('admin_logged_in');
    }

}
