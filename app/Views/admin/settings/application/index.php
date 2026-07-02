<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
helper('app_settings');
$row = is_array($row ?? null) ? $row : [];
$timezoneOptions = is_array($timezoneOptions ?? null) ? $timezoneOptions : [];
$currentTimezone = (string) ($row['app_timezone'] ?? 'Asia/Jakarta');
$appLogoPath = (string) ($row['header_logo_path'] ?? $row['login_logo_path'] ?? $row['public_logo_path'] ?? '');
$appLogoUrl = $appLogoPath !== '' ? plpi_asset_url($appLogoPath) : '';
$faviconUrl = ! empty($row['favicon_path']) ? plpi_asset_url((string) $row['favicon_path']) : '';
$smtpLocked = ! empty($smtpEnvLocked);
?>
<section class="admin-panel user-form-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <form class="admin-form" method="post" enctype="multipart/form-data" action="<?= site_url('dashboard/settings/application') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:image-multiple-outline"></iconify-icon>Identitas Visual</h3>
            <div class="settings-upload-grid">
                <label class="settings-upload">
                    <span>Logo Aplikasi</span>
                    <input type="file" name="app_logo" id="appLogoInput" accept="image/png,image/jpeg,image/webp">
                    <small>Dipakai untuk header admin, halaman login, dan beranda publik.</small>
                    <div class="settings-preview">
                        <img id="appLogoPreview" class="<?= $appLogoUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($appLogoUrl) ?>" alt="Preview logo aplikasi">
                        <em>Belum ada logo aplikasi.</em>
                    </div>
                </label>

                <label class="settings-upload">
                    <span>Favicon</span>
                    <input type="file" name="favicon" id="faviconInput" accept=".ico,image/png,image/webp">
                    <small>Digunakan pada tab browser dan bookmark aplikasi.</small>
                    <div class="settings-preview">
                        <img id="faviconPreview" class="<?= $faviconUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($faviconUrl) ?>" alt="Preview favicon">
                        <em>Belum ada favicon.</em>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:clock-outline"></iconify-icon>Konfigurasi Sistem</h3>
            <div class="form-grid">
                <label>
                    <span>Zona Waktu</span>
                    <select name="app_timezone" required>
                        <?php foreach ($timezoneOptions as $value => $label): ?>
                            <option value="<?= esc($value, 'attr') ?>" <?= $currentTimezone === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:email-cog-outline"></iconify-icon>Email Resmi / SMTP</h3>
            <?php if ($smtpLocked): ?>
                <div class="admin-alert info">
                    Konfigurasi SMTP produksi dibaca dari file <strong>.env</strong> hosting. Field di bawah dikunci agar kredensial email tidak disimpan dari dashboard.
                </div>
            <?php endif; ?>
            <div class="form-grid">
                <label>
                    <span>Email Pengirim</span>
                    <input type="email" name="mail_from_email" value="<?= esc((string) old('mail_from_email', $row['mail_from_email'] ?? ''), 'attr') ?>" placeholder="plpi@domain.ac.id" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>Nama Pengirim</span>
                    <input name="mail_from_name" value="<?= esc((string) old('mail_from_name', $row['mail_from_name'] ?? ''), 'attr') ?>" placeholder="PLPI" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>SMTP Host</span>
                    <input name="smtp_host" value="<?= esc((string) old('smtp_host', $row['smtp_host'] ?? ''), 'attr') ?>" placeholder="mail.domain.ac.id" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>SMTP Port</span>
                    <input type="number" name="smtp_port" value="<?= esc((string) old('smtp_port', $row['smtp_port'] ?? ''), 'attr') ?>" placeholder="465 / 587" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>SMTP Username</span>
                    <input name="smtp_user" value="<?= esc((string) old('smtp_user', $row['smtp_user'] ?? ''), 'attr') ?>" placeholder="plpi@domain.ac.id" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>SMTP Password</span>
                    <input type="password" name="smtp_pass" value="" placeholder="<?= $smtpLocked ? 'Dikelola dari .env hosting' : (! empty($row['smtp_pass']) ? 'Sudah tersimpan, isi untuk mengganti' : 'Password email resmi') ?>" <?= $smtpLocked ? 'disabled' : '' ?>>
                </label>
                <label>
                    <span>Enkripsi</span>
                    <?php $smtpCrypto = (string) old('smtp_crypto', $row['smtp_crypto'] ?? 'tls'); ?>
                    <select name="smtp_crypto" <?= $smtpLocked ? 'disabled' : '' ?>>
                        <option value="tls" <?= $smtpCrypto === 'tls' ? 'selected' : '' ?>>TLS</option>
                        <option value="ssl" <?= $smtpCrypto === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        <option value="" <?= $smtpCrypto === '' ? 'selected' : '' ?>>Tanpa enkripsi</option>
                    </select>
                    <small>Sesuaikan dengan layanan email resmi. Umumnya port 587/TLS atau 465/SSL.</small>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard') ?>"><iconify-icon icon="mdi:arrow-left"></iconify-icon>Kembali</a>
            <button class="admin-btn primary" type="submit"><iconify-icon icon="mdi:content-save-outline"></iconify-icon>Simpan</button>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function bindPreview(inputId, imageId) {
        const input = document.getElementById(inputId);
        const image = document.getElementById(imageId);
        if (!input || !image) return;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0] ? input.files[0] : null;
            if (!file || (file.type && !file.type.startsWith('image/'))) {
                image.removeAttribute('src');
                image.classList.add('is-hidden');
                return;
            }

            image.src = URL.createObjectURL(file);
            image.classList.remove('is-hidden');
        });
    }

    bindPreview('appLogoInput', 'appLogoPreview');
    bindPreview('faviconInput', 'faviconPreview');
});
</script>
<?= $this->endSection() ?>
