<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($row); ?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" action="<?= $isEdit ? site_url('dashboard/messages/templates/' . (int) $row['id'] . '/update') : site_url('dashboard/messages/templates') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:message-text-outline"></iconify-icon>Template Pesan</h3>
            <div class="form-grid">
                <label>
                    <span>Nama Template</span>
                    <input name="name" value="<?= esc((string) old('name', $row['name'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Kode</span>
                    <input name="code" value="<?= esc((string) old('code', $row['code'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Tipe Pesan</span>
                    <?php $typeValue = (string) old('type', $row['type'] ?? 'whatsapp'); ?>
                    <select name="type" id="templateTypeSelect" required>
                        <option value="whatsapp" <?= $typeValue === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
                        <option value="email" <?= $typeValue === 'email' ? 'selected' : '' ?>>Email</option>
                    </select>
                </label>
                <label>
                    <span>Status</span>
                    <?php $activeValue = (string) old('is_active', (string) ($row['is_active'] ?? '1')); ?>
                    <select name="is_active">
                        <option value="1" <?= $activeValue === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $activeValue === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </label>
                <label class="span-2" id="subjectField">
                    <span>Subjek Email</span>
                    <input name="subject" value="<?= esc((string) old('subject', $row['subject'] ?? ''), 'attr') ?>" placeholder="Contoh: Notifikasi LoA - {judul_artikel}">
                    <small>Dipakai hanya untuk template email.</small>
                </label>
                <label class="span-2">
                    <span>Isi Pesan</span>
                    <textarea name="message" rows="14" data-editor="plain" required><?= esc((string) old('message', $row['message'] ?? '')) ?></textarea>
                    <small>Placeholder: {nama_penerima}, {judul_artikel}, {nama_jurnal}, {tanggal}, {nama_admin}.</small>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/messages/templates') ?>"><iconify-icon icon="mdi:arrow-left"></iconify-icon>Kembali</a>
            <button class="admin-btn primary" type="submit"><iconify-icon icon="mdi:content-save-outline"></iconify-icon>Simpan</button>
        </div>
    </form>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('templateTypeSelect');
    const subjectField = document.getElementById('subjectField');
    if (!typeSelect || !subjectField) return;

    function syncSubjectField() {
        subjectField.style.display = typeSelect.value === 'email' ? '' : 'none';
    }

    typeSelect.addEventListener('change', syncSubjectField);
    syncSubjectField();
});
</script>
<?= $this->endSection() ?>
