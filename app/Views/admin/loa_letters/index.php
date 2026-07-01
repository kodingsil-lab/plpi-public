<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $status = (string) ($row['status'] ?? 'published');
    $token = (string) ($row['public_token'] ?? '');
    $pdfActions = $token !== ''
        ? '<a class="icon-btn view" href="' . site_url('loa/v/' . $token . '/preview') . '" target="_blank" rel="noopener" title="Preview PDF" aria-label="Preview PDF"><iconify-icon icon="mdi:file-eye-outline"></iconify-icon></a>'
            . '<a class="icon-btn download" href="' . site_url('loa/v/' . $token . '/download') . '" title="Unduh PDF" aria-label="Unduh PDF"><iconify-icon icon="mdi:download-outline"></iconify-icon></a>'
        : '';
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no' => esc((string) (($startNumber ?? 1) + $index)),
        'number' => '<strong>' . esc((string) ($row['loa_number'] ?? '-')) . '</strong><br><small>' . esc(date('d M Y', strtotime((string) ($row['published_at'] ?? 'now')))) . '</small>',
        'article' => esc((string) ($row['title'] ?? '-')) . '<br><small>' . esc((string) ($row['corresponding_email'] ?? '-')) . '</small>',
        'journal' => esc((string) ($row['journal_name'] ?? '-')),
        'status' => '<span class="status-pill ' . esc($status === 'published' ? 'done' : 'muted', 'attr') . '">' . esc($status === 'published' ? 'LoA Terbit' : 'Dicabut') . '</span>',
        'actions' => '<div class="row-actions">'
            . $pdfActions
            . '<a class="icon-btn edit" href="' . site_url('dashboard/loa-letters/' . (int) $row['id'] . '/edit') . '" title="Edit" aria-label="Edit"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></a>'
            . '<form method="post" action="' . site_url('dashboard/loa-letters/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus LoA ini?\')"><button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button></form>'
            . '</div>',
    ];
}
?>
<section class="admin-panel admin-filter-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form loa-letter-filter-form" method="get" action="<?= site_url('dashboard/loa-letters') ?>">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua</option>
                        <option value="published" <?= (string) ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>LoA Terbit</option>
                        <option value="revoked" <?= (string) ($filters['status'] ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut</option>
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
                    <button class="admin-btn primary" type="submit"><iconify-icon icon="mdi:filter"></iconify-icon>Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/loa-letters') ?>"><iconify-icon icon="mdi:refresh"></iconify-icon>Reset</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="admin-panel mt-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'loa-letters-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/loa-letters/bulk-delete'),
            'confirm' => 'Hapus LoA yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'number', 'label' => 'Nomor LoA', 'sortable' => true],
            ['key' => 'article', 'label' => 'Artikel / Email', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada LoA terbit.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
