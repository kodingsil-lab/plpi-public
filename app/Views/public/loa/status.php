<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$status = strtolower((string) ($loaRequest['status'] ?? 'pending'));
$statusLabel = match ($status) {
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    'revision' => 'Revisi',
    default => 'Menunggu',
};
$statusClass = match ($status) {
    'approved' => 'success',
    'rejected', 'revision' => 'danger',
    default => 'process',
};
?>

<section class="loa-status-section">
    <div class="container">
        <?php if (session('success')): ?>
            <div class="public-alert success"><?= esc((string) session('success')) ?></div>
        <?php endif; ?>

        <article class="loa-status-card">
            <div class="loa-status-head">
                <div>
                    <span class="eyebrow">Status Permohonan</span>
                    <h1><?= esc((string) ($loaRequest['request_code'] ?? '-')) ?></h1>
                </div>
                <span class="status-badge <?= esc($statusClass, 'attr') ?>"><?= esc($statusLabel) ?></span>
            </div>

            <div class="loa-status-grid">
                <div>
                    <span>Jurnal</span>
                    <strong><?= esc((string) ($loaRequest['journal_name'] ?? '-')) ?></strong>
                </div>
                <div>
                    <span>Tanggal Pengajuan</span>
                    <strong><?= esc(date('d M Y H:i', strtotime((string) ($loaRequest['created_at'] ?? 'now')))) ?></strong>
                </div>
                <div class="span-2">
                    <span>Judul Artikel</span>
                    <strong><?= esc((string) ($loaRequest['title'] ?? '-')) ?></strong>
                </div>
                <div>
                    <span>Email</span>
                    <strong><?= esc((string) ($loaRequest['corresponding_email'] ?? '-')) ?></strong>
                </div>
                <div>
                    <span>WhatsApp</span>
                    <strong><?= esc((string) ($loaRequest['whatsapp_number'] ?? '-')) ?></strong>
                </div>
            </div>

            <div class="loa-form-actions">
                <a href="<?= site_url('/') ?>" class="btn-secondary">Beranda</a>
                <a href="<?= site_url('ajukan-loa') ?>" class="btn-primary">Ajukan Lagi</a>
            </div>
        </article>
    </div>
</section>

<?= $this->endSection() ?>
