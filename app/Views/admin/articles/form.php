<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$isEdit = ! empty($row);
$coverPreviewUrl = $isEdit && ! empty($row['cover_path'])
    ? site_url('dashboard/artikel-edukatif/' . (int) $row['id'] . '/cover?v=' . rawurlencode((string) ($row['updated_at'] ?? time())))
    : '';
$publishedValue = old('published_at');
if ($publishedValue === null && ! empty($row['published_at'])) {
    $publishedValue = date('Y-m-d\TH:i', strtotime((string) $row['published_at']));
}
?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" enctype="multipart/form-data" action="<?= $isEdit ? site_url('dashboard/artikel-edukatif/' . (int) $row['id'] . '/update') : site_url('dashboard/artikel-edukatif') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon>Identitas Artikel</h3>
            <div class="form-grid">
                <label>
                    <span>Judul Artikel</span>
                    <input name="title" value="<?= esc((string) old('title', $row['title'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Slug</span>
                    <input name="slug" value="<?= esc((string) old('slug', $row['slug'] ?? ''), 'attr') ?>" placeholder="Otomatis jika dikosongkan">
                </label>
                <label>
                    <span>Kategori</span>
                    <select name="category_id">
                        <option value="">Tanpa kategori</option>
                        <?php foreach (($categories ?? []) as $category): ?>
                            <?php $selectedCategory = (string) old('category_id', $row['category_id'] ?? ''); ?>
                            <option value="<?= (int) $category['id'] ?>" <?= $selectedCategory === (string) $category['id'] ? 'selected' : '' ?>>
                                <?= esc((string) $category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Status</span>
                    <select name="status">
                        <?php $selectedStatus = (string) old('status', $row['status'] ?? 'draft'); ?>
                        <option value="draft" <?= $selectedStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $selectedStatus === 'published' ? 'selected' : '' ?>>Terbit</option>
                    </select>
                </label>
                <label>
                    <span>Tanggal Terbit</span>
                    <input type="datetime-local" name="published_at" value="<?= esc((string) $publishedValue, 'attr') ?>">
                </label>
                <label>
                    <span>Urutan</span>
                    <input type="number" name="sort_order" value="<?= esc((string) old('sort_order', $row['sort_order'] ?? 0), 'attr') ?>">
                </label>
                <label class="span-2">
                    <span>Ringkasan</span>
                    <textarea name="summary" rows="3"><?= esc((string) old('summary', $row['summary'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:image-outline"></iconify-icon>Cover Artikel</h3>
            <div class="form-grid">
                <label>
                    <span>Cover</span>
                    <input type="file" name="cover" id="articleCoverInput" accept="image/png,image/jpeg,image/webp">
                    <small>Format PNG, JPG, JPEG, atau WEBP.</small>
                    <div class="settings-preview">
                        <img id="articleCoverPreview" class="<?= $coverPreviewUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($coverPreviewUrl, 'attr') ?>" alt="Preview cover artikel">
                        <em>Belum ada cover artikel.</em>
                    </div>
                </label>
                <label>
                    <span>Alt Gambar</span>
                    <input name="image_alt" value="<?= esc((string) old('image_alt', $row['image_alt'] ?? ''), 'attr') ?>" placeholder="Deskripsi singkat gambar">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:text-box-edit-outline"></iconify-icon>Isi Artikel</h3>
            <div class="form-grid">
                <label class="span-2">
                    <span>Konten</span>
                    <textarea name="content" rows="12" data-editor="rich"><?= esc((string) old('content', $row['content'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/artikel-edukatif') ?>">Kembali</a>
            <button class="admin-btn primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<script>
    (function () {
        const input = document.getElementById('articleCoverInput');
        const preview = document.getElementById('articleCoverPreview');
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
