<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$statusLabels = [
    'draft' => 'Draft',
    'published' => 'Terbit',
];
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $coverUrl = ! empty($row['cover_path'])
        ? site_url('dashboard/artikel-edukatif/' . (int) $row['id'] . '/cover?v=' . rawurlencode((string) ($row['updated_at'] ?? '')))
        : '';
    $statusKey = (string) ($row['status'] ?? 'draft');
    $publicUrl = $statusKey === 'published' ? site_url('artikel/' . (string) ($row['slug'] ?? '')) : '';
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no'       => esc((string) (($startNumber ?? 1) + $index)),
        'cover'    => $coverUrl !== '' ? '<img class="table-logo-preview" src="' . esc($coverUrl, 'attr') . '" alt="Cover artikel">' : '<span class="status-pill muted">Kosong</span>',
        'title'    => '<strong>' . esc((string) ($row['title'] ?? '-')) . '</strong><br><small>' . esc((string) ($row['slug'] ?? '-')) . '</small>',
        'category' => esc((string) ($row['category_name'] ?? 'Tanpa kategori')),
        'status'   => '<span class="status-pill ' . ($statusKey === 'published' ? 'done' : 'muted') . '">' . esc($statusLabels[$statusKey] ?? $statusKey) . '</span>',
        'date'     => ! empty($row['published_at']) ? date('d-m-Y H:i', strtotime((string) $row['published_at'])) : '-',
        'actions'  => '<div class="row-actions">'
            . ($publicUrl !== '' ? admin_action_link('view', $publicUrl, 'Lihat Publik', ['target' => '_blank', 'rel' => 'noopener']) : '')
            . admin_action_link('edit', site_url('dashboard/artikel-edukatif/' . (int) $row['id'] . '/edit'), 'Edit')
            . admin_action_form('delete', site_url('dashboard/artikel-edukatif/' . (int) $row['id'] . '/delete'), 'Hapus', 'Hapus artikel ini?')
            . '</div>',
    ];
}
?>
<section class="admin-panel admin-filter-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form article-filter-form" method="get" action="<?= site_url('dashboard/artikel-edukatif') ?>">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua</option>
                        <?php foreach (($statuses ?? []) as $item): ?>
                            <option value="<?= esc($item, 'attr') ?>" <?= (string) ($status ?? '') === $item ? 'selected' : '' ?>><?= esc($statusLabels[$item] ?? $item) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($search ?? ''), 'attr') ?>" placeholder="Judul / slug / kategori">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/artikel-edukatif') ?>">Reset</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="admin-panel mt-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <a href="<?= site_url('dashboard/artikel-edukatif/create') ?>"><iconify-icon icon="mdi:pencil-plus-outline"></iconify-icon>Tulis Artikel</a>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'articles-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/artikel-edukatif/bulk-delete'),
            'confirm' => 'Hapus artikel yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'cover', 'label' => 'Cover'],
            ['key' => 'title', 'label' => 'Judul Artikel', 'sortable' => true],
            ['key' => 'category', 'label' => 'Kategori', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'date', 'label' => 'Tanggal Terbit', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada artikel edukatif.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
