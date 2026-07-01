<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$isValid = ! empty($letter) && is_array($letter);
$authors = [];
$affiliations = [];
if ($isValid) {
    $decodedAuthors = json_decode((string) ($letter['authors_json'] ?? '[]'), true);
    $decodedAffiliations = json_decode((string) ($letter['affiliations_json'] ?? '[]'), true);
    $authors = is_array($decodedAuthors) ? $decodedAuthors : [];
    $affiliations = is_array($decodedAffiliations) ? $decodedAffiliations : [];
}
$normalizeText = static function ($value): string {
    if (is_array($value)) {
        $value = (string) ($value['name'] ?? $value['affiliation'] ?? '');
    }

    return trim(preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*:\s*/iu', '', (string) $value) ?? '');
};
$authorText = implode(', ', array_filter(array_map($normalizeText, $authors)));
$affiliationText = implode(', ', array_filter(array_map($normalizeText, $affiliations)));
?>

<section class="loa-status-section">
    <div class="container">
        <article class="loa-status-card verify-result-card">
            <div class="loa-status-head">
                <div>
                    <span class="eyebrow">Hasil Verifikasi</span>
                    <h1><?= $isValid ? 'LoA Valid' : 'LoA Tidak Ditemukan' ?></h1>
                    <p class="verify-checked-number">Nomor yang dicek: <strong><?= esc((string) ($number ?? '-')) ?></strong></p>
                </div>
                <span class="status-badge <?= $isValid ? 'success' : 'danger' ?>"><?= $isValid ? 'VALID' : 'TIDAK VALID' ?></span>
            </div>

            <?php if ($isValid): ?>
                <div class="verify-result-message success">
                    Nomor LoA ditemukan dan berstatus terbit di sistem PLPI.
                </div>

                <div class="loa-status-grid">
                    <div class="span-2">
                        <span>Nomor LoA</span>
                        <strong><?= esc((string) ($letter['loa_number'] ?? '-')) ?></strong>
                    </div>
                    <div class="span-2">
                        <span>Judul Artikel</span>
                        <strong><?= esc((string) ($letter['title'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Jurnal</span>
                        <strong><?= esc((string) ($journal['name'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Diterbitkan</span>
                        <strong><?= esc(! empty($letter['published_at']) ? date('d M Y H:i', strtotime((string) $letter['published_at'])) : '-') ?></strong>
                    </div>
                    <div>
                        <span>Volume</span>
                        <strong><?= esc((string) ($letter['volume'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Nomor Edisi</span>
                        <strong><?= esc((string) ($letter['issue_number'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Tahun Terbit</span>
                        <strong><?= esc((string) ($letter['published_year'] ?? '-')) ?></strong>
                    </div>
                    <div>
                        <span>Status</span>
                        <strong>Terbit</strong>
                    </div>
                    <?php if ($authorText !== ''): ?>
                        <div class="span-2">
                            <span>Penulis</span>
                            <strong><?= esc($authorText) ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if ($affiliationText !== ''): ?>
                        <div class="span-2">
                            <span>Afiliasi</span>
                            <strong><?= esc($affiliationText) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="verify-result-message danger">
                    Nomor LoA tidak ditemukan pada data LoA terbit. Pastikan nomor ditulis lengkap sesuai dokumen.
                </div>
            <?php endif; ?>

            <div class="loa-form-actions">
                <?php if ($isValid && ! empty($letter['public_token'])): ?>
                    <a href="<?= site_url('loa/v/' . (string) $letter['public_token'] . '/preview') ?>" class="btn-primary" target="_blank" rel="noopener">Lihat PDF</a>
                    <a href="<?= site_url('loa/v/' . (string) $letter['public_token'] . '/download') ?>" class="btn-secondary">Unduh PDF</a>
                <?php endif; ?>
                <a href="<?= site_url('verifikasi-loa') ?>" class="btn-primary">Cek Lagi</a>
                <a href="<?= site_url('/') ?>" class="btn-secondary">Beranda</a>
            </div>
        </article>
    </div>
</section>

<?= $this->endSection() ?>
