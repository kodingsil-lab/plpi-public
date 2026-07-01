<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $role = (string) ($row['role'] ?? '-');
    $isActive = (int) ($row['is_active'] ?? 0) === 1;
    $tableRows[] = [
        '_bulk_id'       => (string) ($row['id'] ?? ''),
        '_bulk_disabled' => (int) ($row['id'] ?? 0) === (int) session('admin_user_id'),
        'no'             => esc((string) (($startNumber ?? 1) + $index)),
        'username'       => '<strong>' . esc((string) ($row['username'] ?? '-')) . '</strong>',
        'name'           => esc((string) ($row['name'] ?? '-')),
        'email'          => esc((string) ($row['email'] ?? '-')),
        'role'           => '<span class="status-pill info">' . esc($role === 'superadmin' ? 'Super Admin' : 'Admin') . '</span>',
        'status'         => '<span class="status-pill ' . ($isActive ? 'done' : 'muted') . '">' . ($isActive ? 'Aktif' : 'Nonaktif') . '</span>',
        'actions'        => '<div class="row-actions">'
            . '<a class="icon-btn edit" href="' . site_url('dashboard/users/' . (int) $row['id'] . '/edit') . '" title="Edit" aria-label="Edit"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></a>'
            . '<form method="post" action="' . site_url('dashboard/users/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus pengguna ini?\')">'
            . '<button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button>'
            . '</form></div>',
    ];
}
?>
<section class="admin-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <a href="<?= site_url('dashboard/users/create') ?>"><iconify-icon icon="mdi:account-plus-outline"></iconify-icon>Tambah</a>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'users-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/users/bulk-delete'),
            'confirm' => 'Hapus pengguna yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'username', 'label' => 'Username', 'sortable' => true],
            ['key' => 'name', 'label' => 'Nama Admin', 'sortable' => true],
            ['key' => 'email', 'label' => 'Email', 'sortable' => true],
            ['key' => 'role', 'label' => 'Role', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada data pengguna.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
