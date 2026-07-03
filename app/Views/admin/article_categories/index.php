<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$isEdit = ! empty($editRow);
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $tableRows[] = [
        '_bulk_id'   => (string) ($row['id'] ?? ''),
        'no'         => esc((string) (($startNumber ?? 1) + $index)),
        'name'       => '<strong>' . esc((string) ($row['name'] ?? '-')) . '</strong>',
        'slug'       => esc((string) ($row['slug'] ?? '-')),
        'status'     => '<span class="status-pill ' . ((int) ($row['is_active'] ?? 1) === 1 ? 'done' : 'muted') . '">' . ((int) ($row['is_active'] ?? 1) === 1 ? 'Aktif' : 'Nonaktif') . '</span>',
        'sort_order' => esc((string) ($row['sort_order'] ?? 0)),
        'actions'    => '<div class="row-actions">'
            . admin_action_link('edit', site_url('dashboard/artikel-edukatif/kategori/' . (int) $row['id'] . '/edit'), 'Edit')
            . admin_action_form('delete', site_url('dashboard/artikel-edukatif/kategori/' . (int) $row['id'] . '/delete'), 'Hapus', 'Hapus kategori ini?')
            . '</div>',
    ];
}
?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" action="<?= $isEdit ? site_url('dashboard/artikel-edukatif/kategori/' . (int) $editRow['id'] . '/update') : site_url('dashboard/artikel-edukatif/kategori') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:shape-outline"></iconify-icon><?= $isEdit ? 'Edit Kategori' : 'Tambah Kategori' ?></h3>
            <div class="form-grid">
                <label>
                    <span>Nama Kategori</span>
                    <input name="name" value="<?= esc((string) old('name', $editRow['name'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Slug</span>
                    <input name="slug" value="<?= esc((string) old('slug', $editRow['slug'] ?? ''), 'attr') ?>" placeholder="Otomatis jika dikosongkan">
                </label>
                <label>
                    <span>Status</span>
                    <?php $activeValue = (string) old('is_active', $editRow['is_active'] ?? 1); ?>
                    <select name="is_active">
                        <option value="1" <?= $activeValue === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $activeValue === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </label>
                <label>
                    <span>Urutan</span>
                    <input type="number" name="sort_order" value="<?= esc((string) old('sort_order', $editRow['sort_order'] ?? 0), 'attr') ?>">
                </label>
                <label class="span-2">
                    <span>Deskripsi</span>
                    <textarea name="description" rows="3"><?= esc((string) old('description', $editRow['description'] ?? '')) ?></textarea>
                </label>
            </div>
            <div class="form-actions">
                <?php if ($isEdit): ?>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/artikel-edukatif/kategori') ?>">Batal Edit</a>
                <?php endif; ?>
                <button class="admin-btn primary" type="submit">Simpan</button>
            </div>
        </div>
    </form>
</section>

<section class="admin-panel admin-filter-panel mt-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form article-category-filter-form" method="get" action="<?= site_url('dashboard/artikel-edukatif/kategori') ?>">
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($search ?? ''), 'attr') ?>" placeholder="Nama / slug / deskripsi">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/artikel-edukatif/kategori') ?>">Reset</a>
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
        'id' => 'article-categories-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/artikel-edukatif/kategori/bulk-delete'),
            'confirm' => 'Hapus kategori yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'name', 'label' => 'Kategori', 'sortable' => true],
            ['key' => 'slug', 'label' => 'Slug', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'sort_order', 'label' => 'Urutan', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada kategori artikel.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
