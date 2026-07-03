<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $isActive = (int) ($row['is_active'] ?? 0) === 1;
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no'       => esc((string) (($startNumber ?? 1) + $index)),
        'name'     => '<strong>' . esc((string) ($row['name'] ?? '-')) . '</strong>',
        'code'     => esc((string) ($row['code'] ?? '-')),
        'type'     => '<span class="status-pill ' . (($row['type'] ?? 'whatsapp') === 'email' ? 'info' : 'done') . '">' . esc(((string) ($row['type'] ?? 'whatsapp')) === 'email' ? 'Email' : 'WhatsApp') . '</span>',
        'status'   => '<span class="status-pill ' . ($isActive ? 'done' : 'muted') . '">' . ($isActive ? 'Aktif' : 'Nonaktif') . '</span>',
        'actions'  => '<div class="row-actions">'
            . '<a class="icon-btn edit" href="' . site_url('dashboard/messages/templates/' . (int) $row['id'] . '/edit') . '" title="Edit" aria-label="Edit"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></a>'
            . '<form method="post" action="' . site_url('dashboard/messages/templates/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus template ini?\')">'
            . '<button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button>'
            . '</form></div>',
    ];
}
?>
<section class="admin-panel admin-filter-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form whatsapp-template-filter-form" method="get" action="<?= site_url('dashboard/messages/templates') ?>">
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($search ?? ''), 'attr') ?>" placeholder="Nama / kode template">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/messages/templates') ?>">Reset</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="admin-panel mt-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <a href="<?= site_url('dashboard/messages/templates/create') ?>"><iconify-icon icon="mdi:message-plus-outline"></iconify-icon>Tambah</a>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'whatsapp-templates-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/messages/templates/bulk-delete'),
            'confirm' => 'Hapus template yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Template', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'type', 'label' => 'Tipe', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada template pesan.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
