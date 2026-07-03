<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($row); ?>
<?php $logoPreviewUrl = $isEdit && ! empty($row['logo_path']) ? site_url('dashboard/publishers/' . (int) $row['id'] . '/logo?v=' . rawurlencode((string) ($row['updated_at'] ?? time()))) : ''; ?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" enctype="multipart/form-data" action="<?= $isEdit ? site_url('dashboard/publishers/' . (int) $row['id'] . '/update') : site_url('dashboard/publishers') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:office-building-outline"></iconify-icon>Identitas Publisher</h3>
            <div class="form-grid">
                <label>
                    <span>Kode</span>
                    <input name="code" value="<?= esc((string) old('code', $row['code'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Nama Publisher</span>
                    <input name="name" value="<?= esc((string) old('name', $row['name'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="<?= esc((string) old('email', $row['email'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Nomor WhatsApp</span>
                    <input name="phone" value="<?= esc((string) old('phone', $row['phone'] ?? ''), 'attr') ?>">
                </label>
                <label class="span-2">
                    <span>Alamat</span>
                    <textarea name="address" rows="4"><?= esc((string) old('address', $row['address'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:image-outline"></iconify-icon>Logo Publisher</h3>
            <div class="form-grid">
                <label>
                    <span>Logo</span>
                    <input type="file" name="logo" id="publisherLogoInput" accept="image/png,image/jpeg,image/webp">
                    <small>Format PNG, JPG, JPEG, atau WEBP.</small>
                    <div class="settings-preview">
                        <img id="publisherLogoPreview" class="<?= $logoPreviewUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($logoPreviewUrl, 'attr') ?>" alt="Preview logo publisher">
                        <em>Belum ada logo publisher.</em>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/publishers') ?>">Kembali</a>
            <button class="admin-btn primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<script>
    (function () {
        const input = document.getElementById('publisherLogoInput');
        const preview = document.getElementById('publisherLogoPreview');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = input.files && input.files[0] ? input.files[0] : null;
            if (!file || (file.type && !file.type.startsWith('image/'))) {
                preview.removeAttribute('src');
                preview.classList.add('is-hidden');
                return;
            }
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('is-hidden');
        });
    })();
</script>
<?= $this->endSection() ?>
