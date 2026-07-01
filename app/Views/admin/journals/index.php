<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $logoUrl = ! empty($row['logo_path'])
        ? site_url('dashboard/journals/' . (int) $row['id'] . '/logo?v=' . rawurlencode((string) ($row['updated_at'] ?? '')))
        : '';
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no'       => esc((string) (($startNumber ?? 1) + $index)),
        'logo'     => $logoUrl !== '' ? '<img class="table-logo-preview" src="' . esc($logoUrl, 'attr') . '" alt="Logo jurnal">' : '<span class="status-pill muted">Kosong</span>',
        'code'     => '<strong>' . esc((string) ($row['code'] ?? '-')) . '</strong>',
        'name'     => esc((string) ($row['name'] ?? '-')),
        'publisher'=> esc((string) ($row['publisher_name'] ?? '-')),
        'e_issn'   => esc((string) ($row['e_issn'] ?? '-')),
        'p_issn'   => esc((string) ($row['p_issn'] ?? '-')),
        'actions'  => '<div class="row-actions">'
            . '<a class="icon-btn edit" href="' . site_url('dashboard/journals/' . (int) $row['id'] . '/edit') . '" title="Edit" aria-label="Edit"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></a>'
            . '<form method="post" action="' . site_url('dashboard/journals/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus jurnal ini?\')">'
            . '<button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button>'
            . '</form></div>',
    ];
}
?>
<section class="admin-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <a href="<?= site_url('dashboard/journals/create') ?>"><iconify-icon icon="mdi:book-plus-outline"></iconify-icon>Tambah</a>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'journals-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/journals/bulk-delete'),
            'confirm' => 'Hapus jurnal yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'logo', 'label' => 'Logo'],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Jurnal', 'sortable' => true],
            ['key' => 'publisher', 'label' => 'Publisher', 'sortable' => true],
            ['key' => 'e_issn', 'label' => 'E-ISSN', 'sortable' => true],
            ['key' => 'p_issn', 'label' => 'P-ISSN', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada data jurnal.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
