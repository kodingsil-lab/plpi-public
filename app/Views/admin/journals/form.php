<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($row); ?>
<?php
$logoPreviewUrl = $isEdit && ! empty($row['logo_path']) ? site_url('dashboard/journals/' . (int) $row['id'] . '/logo?v=' . rawurlencode((string) ($row['updated_at'] ?? time()))) : '';
$signaturePreviewUrl = $isEdit && ! empty($row['default_signature_path']) ? site_url('dashboard/journals/' . (int) $row['id'] . '/signature?v=' . rawurlencode((string) ($row['updated_at'] ?? time()))) : '';
?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" enctype="multipart/form-data" action="<?= $isEdit ? site_url('dashboard/journals/' . (int) $row['id'] . '/update') : site_url('dashboard/journals') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:book-open-page-variant-outline"></iconify-icon>Identitas Jurnal</h3>
            <div class="form-grid">
                <label>
                    <span>Publisher</span>
                    <select name="publisher_id" required>
                        <option value="">Pilih publisher</option>
                        <?php foreach (($publishers ?? []) as $publisher): ?>
                            <?php $selectedPublisher = (int) old('publisher_id', $row['publisher_id'] ?? 0); ?>
                            <option value="<?= (int) $publisher['id'] ?>" <?= $selectedPublisher === (int) $publisher['id'] ? 'selected' : '' ?>>
                                <?= esc((string) $publisher['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Nama Jurnal</span>
                    <input name="name" value="<?= esc((string) old('name', $row['name'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Kode Jurnal</span>
                    <input name="code" value="<?= esc((string) old('code', $row['code'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>URL Website</span>
                    <input name="website_url" value="<?= esc((string) old('website_url', $row['website_url'] ?? ''), 'attr') ?>" placeholder="https://...">
                </label>
                <label>
                    <span>URL Pernyataan Komitmen</span>
                    <input name="commitment_statement_url" value="<?= esc((string) old('commitment_statement_url', $row['commitment_statement_url'] ?? ''), 'attr') ?>" placeholder="https://...">
                </label>
                <label>
                    <span>ISSN</span>
                    <input name="issn" value="<?= esc((string) old('issn', $row['issn'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>E-ISSN</span>
                    <input name="e_issn" value="<?= esc((string) old('e_issn', $row['e_issn'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>P-ISSN</span>
                    <input name="p_issn" value="<?= esc((string) old('p_issn', $row['p_issn'] ?? ''), 'attr') ?>">
                </label>
                <label class="span-2">
                    <span>Intro Rekrutmen</span>
                    <textarea name="recruitment_intro" rows="6" data-editor="rich"><?= esc((string) old('recruitment_intro', $row['recruitment_intro'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:account-tie-outline"></iconify-icon>Pimpinan Redaksi</h3>
            <div class="form-grid">
                <label>
                    <span>Nama Penandatangan</span>
                    <input name="default_signer_name" value="<?= esc((string) old('default_signer_name', $row['default_signer_name'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Jabatan</span>
                    <input name="default_signer_title" value="<?= esc((string) old('default_signer_title', $row['default_signer_title'] ?? ''), 'attr') ?>">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:file-pdf-box"></iconify-icon>Pengaturan PDF</h3>
            <div class="form-grid four-cols">
                <label>
                    <span>TTD Kiri/Kanan</span>
                    <input type="number" name="pdf_sig_left_px" value="<?= esc((string) old('pdf_sig_left_px', $row['pdf_sig_left_px'] ?? '20'), 'attr') ?>">
                </label>
                <label>
                    <span>TTD Atas/Bawah</span>
                    <input type="number" name="pdf_sig_top_px" value="<?= esc((string) old('pdf_sig_top_px', $row['pdf_sig_top_px'] ?? '10'), 'attr') ?>">
                </label>
                <label>
                    <span>Tinggi TTD</span>
                    <input type="number" name="pdf_sig_height_px" value="<?= esc((string) old('pdf_sig_height_px', $row['pdf_sig_height_px'] ?? '85'), 'attr') ?>">
                </label>
                <label>
                    <span>Skala TTD (%)</span>
                    <input type="number" min="50" max="250" step="5" name="pdf_sig_scale_percent" value="<?= esc((string) old('pdf_sig_scale_percent', $row['pdf_sig_scale_percent'] ?? '100'), 'attr') ?>">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:image-multiple-outline"></iconify-icon>Berkas Jurnal</h3>
            <div class="form-grid">
                <label>
                    <span>Logo Jurnal</span>
                    <input type="file" name="logo" id="journalLogoInput" accept="image/png,image/jpeg,image/webp">
                    <small>Format PNG, JPG, JPEG, atau WEBP.</small>
                    <div class="settings-preview">
                        <img id="journalLogoPreview" class="<?= $logoPreviewUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($logoPreviewUrl, 'attr') ?>" alt="Preview logo jurnal">
                        <em>Belum ada logo jurnal.</em>
                    </div>
                </label>
                <label>
                    <span>Cap + Tanda Tangan Digital</span>
                    <input type="file" name="signature" id="journalSignatureInput" accept="image/png,image/jpeg,image/webp">
                    <small>Format PNG, JPG, JPEG, atau WEBP.</small>
                    <div class="settings-preview">
                        <img id="journalSignaturePreview" class="<?= $signaturePreviewUrl !== '' ? '' : 'is-hidden' ?>" src="<?= esc($signaturePreviewUrl, 'attr') ?>" alt="Preview cap dan tanda tangan">
                        <em>Belum ada cap atau tanda tangan.</em>
                    </div>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/journals') ?>">Kembali</a>
            <button class="admin-btn primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<script>
    (function () {
        function bindPreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
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
        }

        bindPreview('journalLogoInput', 'journalLogoPreview');
        bindPreview('journalSignatureInput', 'journalSignaturePreview');
    })();
</script>
<?= $this->endSection() ?>
