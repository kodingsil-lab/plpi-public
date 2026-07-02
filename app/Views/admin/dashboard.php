<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$stats = $stats ?? [];
$loaRows = $loaRows ?? [];
$statusMeta = static function (array $row): array {
    $status = (string) ($row['status'] ?? 'pending');
    if (! empty($row['has_published_letter'])) {
        return ['label' => 'LoA Terbit', 'class' => 'done'];
    }

    return match ($status) {
        'approved' => ['label' => 'Disetujui', 'class' => 'info'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'danger'],
        'revision' => ['label' => 'Revisi', 'class' => 'info'],
        default => ['label' => 'Menunggu', 'class' => 'warning'],
    };
};
?>
<section class="summary-grid">
    <article class="summary-card tone-teal">
        <div class="summary-card-head">
            <span>Permohonan LoA</span>
            <i><iconify-icon icon="mdi:file-document-edit-outline"></iconify-icon></i>
        </div>
        <strong><?= esc((string) (int) ($stats['loa_requests'] ?? 0)) ?></strong>
        <small><iconify-icon icon="mdi:calendar-week-outline"></iconify-icon><?= esc((string) (int) ($stats['loa_requests_week'] ?? 0)) ?> permohonan baru minggu ini</small>
    </article>

    <article class="summary-card tone-green">
        <div class="summary-card-head">
            <span>LoA Terbit</span>
            <i><iconify-icon icon="mdi:file-check-outline"></iconify-icon></i>
        </div>
        <strong><?= esc((string) (int) ($stats['loa_letters'] ?? 0)) ?></strong>
        <small><iconify-icon icon="mdi:check-decagram-outline"></iconify-icon>Dokumen selesai diterbitkan</small>
    </article>

    <article class="summary-card tone-blue">
        <div class="summary-card-head">
            <span>Jurnal Aktif</span>
            <i><iconify-icon icon="mdi:book-open-page-variant-outline"></iconify-icon></i>
        </div>
        <strong><?= esc((string) (int) ($stats['journals'] ?? 0)) ?></strong>
        <small><iconify-icon icon="mdi:database-check-outline"></iconify-icon>Profil jurnal tersedia</small>
    </article>

    <article class="summary-card tone-amber">
        <div class="summary-card-head">
            <span>Artikel Edukatif</span>
            <i><iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon></i>
        </div>
        <strong><?= esc((string) (int) ($stats['educative_articles'] ?? 0)) ?></strong>
        <small><iconify-icon icon="mdi:lightbulb-on-outline"></iconify-icon>Konten literasi publikasi</small>
    </article>
</section>

<section class="admin-content-grid">
    <article class="admin-panel dashboard-list-panel wide">
        <div class="panel-heading">
            <div>
                <span class="panel-kicker">Aktivitas LoA</span>
                <h2>Permohonan LoA Terbaru</h2>
                <p>Menampilkan 15 data terbaru dari semua status permohonan.</p>
            </div>
            <div class="panel-actions">
                <a class="admin-btn primary" href="<?= site_url('dashboard/loa-requests') ?>"><iconify-icon icon="mdi:folder-edit-outline"></iconify-icon>Kelola LoA</a>
                <a class="admin-btn secondary" href="<?= site_url('dashboard/loa-letters') ?>"><iconify-icon icon="mdi:file-check-outline"></iconify-icon>Lihat LoA Terbit</a>
            </div>
        </div>

        <div class="admin-table">
            <div class="table-row table-head">
                <span>Kode</span>
                <span>Judul Artikel</span>
                <span>Status</span>
                <span>Tanggal</span>
            </div>
            <?php if ($loaRows): ?>
                <?php foreach ($loaRows as $row): ?>
                    <?php $meta = $statusMeta($row); ?>
                    <div class="table-row">
                        <span><a href="<?= site_url('dashboard/loa-requests/' . (int) $row['id']) ?>"><?= esc((string) ($row['request_code'] ?? '-')) ?></a></span>
                        <span><?= esc((string) ($row['title'] ?? '-')) ?></span>
                        <span><span class="status-pill <?= esc($meta['class'], 'attr') ?>"><?= esc($meta['label']) ?></span></span>
                        <span><?= esc(! empty($row['created_at']) ? date('d-m-Y', strtotime((string) $row['created_at'])) : '-') ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="table-row">
                    <span>-</span>
                    <span>Belum ada data permohonan LoA.</span>
                    <span><span class="status-pill muted">Kosong</span></span>
                    <span>-</span>
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
<?= $this->endSection() ?>
