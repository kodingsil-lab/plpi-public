<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$statusLabels = [
    'pending' => 'Diproses',
    'revision' => 'Revisi',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
];
$statusClass = static fn(string $status): string => match ($status) {
    'approved' => 'info',
    'revision' => 'info',
    'rejected' => 'danger',
    default => 'warning',
};
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $status = (string) ($row['status'] ?? 'pending');
    $hasLetter = (bool) ($row['has_published_letter'] ?? false);
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no' => esc((string) (($startNumber ?? 1) + $index)),
        'code' => '<strong>' . esc((string) ($row['request_code'] ?? '-')) . '</strong><br><small>' . esc(date('d M Y', strtotime((string) ($row['created_at'] ?? 'now')))) . '</small>',
        'article' => esc((string) ($row['title'] ?? '-')) . '<br><small>' . esc((string) ($row['corresponding_email'] ?? '-')) . '</small>',
        'journal' => esc((string) ($row['journal_name'] ?? '-')),
        'status' => '<span class="status-pill ' . esc($hasLetter ? 'done' : $statusClass($status), 'attr') . '">' . esc($hasLetter ? 'LoA Terbit' : ($statusLabels[$status] ?? ucfirst($status))) . '</span>',
        'actions' => '<div class="row-actions">'
            . '<a class="icon-btn view" href="' . site_url('dashboard/loa-requests/' . (int) $row['id']) . '" title="Detail" aria-label="Detail"><iconify-icon icon="mdi:eye-outline"></iconify-icon></a>'
            . (((! $hasLetter) && in_array($status, ['pending', 'revision'], true)) ? '<form method="post" action="' . site_url('dashboard/loa-requests/' . (int) $row['id'] . '/approve') . '" onsubmit="return confirm(\'Setujui dan terbitkan LoA ini?\')"><button class="icon-btn success" type="submit" title="Setujui" aria-label="Setujui"><iconify-icon icon="mdi:check-circle-outline"></iconify-icon></button></form>' : '')
            . '<form method="post" action="' . site_url('dashboard/loa-requests/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus permohonan ini?\')"><button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button></form>'
            . '</div>',
    ];
}
?>
<section class="admin-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form loa-request-filter-form" method="get" action="<?= site_url('dashboard/loa-requests') ?>">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua</option>
                        <?php foreach (['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'terbit' => 'LoA Terbit', 'ditolak' => 'Ditolak'] as $value => $label): ?>
                            <option value="<?= esc($value, 'attr') ?>" <?= (string) ($filters['status'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Jurnal</span>
                    <select name="journal_id">
                        <option value="0">Semua Jurnal</option>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <option value="<?= (int) $journal['id'] ?>" <?= (int) ($filters['journal_id'] ?? 0) === (int) $journal['id'] ? 'selected' : '' ?>><?= esc((string) $journal['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($filters['q'] ?? ''), 'attr') ?>" placeholder="Nomor LoA / judul">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/loa-requests') ?>">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'loa-requests-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/loa-requests/bulk-delete'),
            'confirm' => 'Hapus permohonan yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'article', 'label' => 'Artikel / Email', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada permohonan LoA.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
