<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
$createRow = $createRow ?? [];
foreach (($rows ?? []) as $index => $row) {
    $status = (string) ($row['status_pembayaran'] ?? 'Belum Dibayar');
    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no'       => esc((string) (($startNumber ?? 1) + $index)),
        'invoice'  => '<strong>' . esc((string) ($row['nomor_invoice'] ?? '-')) . '</strong><br><small>' . esc(invoice_date($row['tanggal_invoice'] ?? null)) . '</small>',
        'article'  => esc((string) ($row['judul_artikel'] ?? '-')) . '<br><small>' . esc((string) ($row['nama_penulis'] ?? '-')) . '</small>',
        'journal'  => esc((string) ($row['nama_jurnal'] ?? '-')),
        'amount'   => '<strong>' . esc(invoice_currency($row['jumlah_tagihan'] ?? 0)) . '</strong>',
        'due'      => esc(invoice_date($row['jatuh_tempo'] ?? null)),
        'status'   => '<span class="status-pill ' . esc(invoice_status_class($status), 'attr') . '">' . esc($status) . '</span>',
        'actions'  => '<div class="row-actions">'
            . '<a class="icon-btn view" href="' . site_url('dashboard/invoice-jurnal/' . (int) $row['id']) . '" title="Detail" aria-label="Detail"><iconify-icon icon="mdi:eye-outline"></iconify-icon></a>'
            . '<a class="icon-btn edit" href="' . site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/edit') . '" title="Edit" aria-label="Edit"><iconify-icon icon="mdi:pencil-outline"></iconify-icon></a>'
            . '<a class="icon-btn print" href="' . site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/print') . '" target="_blank" rel="noopener noreferrer" title="Cetak" aria-label="Cetak"><iconify-icon icon="mdi:printer-outline"></iconify-icon></a>'
            . '<form method="post" action="' . site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus invoice ini?\')">'
            . '<button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button>'
            . '</form></div>',
    ];
}
?>
<section class="admin-panel admin-filter-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form invoice-filter-form" method="get" action="<?= site_url('dashboard/invoice-jurnal') ?>">
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua</option>
                        <?php foreach (($statuses ?? []) as $option): ?>
                            <option value="<?= esc((string) $option, 'attr') ?>" <?= (string) ($status ?? '') === (string) $option ? 'selected' : '' ?>><?= esc((string) $option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($search ?? ''), 'attr') ?>" placeholder="Invoice / artikel / penulis / jurnal">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/invoice-jurnal') ?>">Reset</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="admin-panel mt-panel">
    <div class="panel-toolbar">
        <div class="panel-actions">
            <button class="admin-btn primary" type="button" data-open-modal="invoiceCreateModal">Tambah</button>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'invoice-jurnal-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/invoice-jurnal/bulk-delete'),
            'confirm' => 'Hapus invoice yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'invoice', 'label' => 'Invoice', 'sortable' => true],
            ['key' => 'article', 'label' => 'Artikel / Penulis', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal', 'sortable' => true],
            ['key' => 'amount', 'label' => 'Tagihan', 'sortable' => true],
            ['key' => 'due', 'label' => 'Jatuh Tempo', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada data invoice jurnal.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>

<div class="admin-modal invoice-modal <?= ! empty($showCreateModal) ? 'is-open' : '' ?>" id="invoiceCreateModal" aria-hidden="<?= ! empty($showCreateModal) ? 'false' : 'true' ?>">
    <div class="admin-modal-backdrop" data-close-modal></div>
    <section class="admin-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="invoiceCreateTitle">
        <header class="admin-modal-header">
            <h2 id="invoiceCreateTitle">Buat Invoice Jurnal</h2>
            <button class="modal-close-btn" type="button" data-close-modal aria-label="Tutup"><iconify-icon icon="mdi:close"></iconify-icon></button>
        </header>

        <form class="admin-form admin-modal-form" method="post" action="<?= site_url('dashboard/invoice-jurnal') ?>">
            <input type="hidden" name="nomor_invoice" value="<?= esc((string) old('nomor_invoice', $createRow['nomor_invoice'] ?? ''), 'attr') ?>">
            <div class="invoice-modal-grid">
                <label>
                    <span>Tanggal Invoice</span>
                    <input type="date" name="tanggal_invoice" value="<?= esc((string) old('tanggal_invoice', $createRow['tanggal_invoice'] ?? date('Y-m-d')), 'attr') ?>" required>
                </label>
                <label>
                    <span>Jatuh Tempo</span>
                    <input type="date" name="jatuh_tempo" value="<?= esc((string) old('jatuh_tempo', $createRow['jatuh_tempo'] ?? ''), 'attr') ?>">
                </label>
                <label class="span-2">
                    <span>Judul Artikel</span>
                    <textarea name="judul_artikel" rows="4" data-editor="plain" required><?= esc((string) old('judul_artikel', $createRow['judul_artikel'] ?? '')) ?></textarea>
                </label>
                <label>
                    <span>Nama Penulis</span>
                    <input name="nama_penulis" value="<?= esc((string) old('nama_penulis', $createRow['nama_penulis'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Institusi Penulis</span>
                    <input name="institusi_penulis" value="<?= esc((string) old('institusi_penulis', $createRow['institusi_penulis'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Nama Jurnal</span>
                    <?php $selectedJournal = (string) old('nama_jurnal', $createRow['nama_jurnal'] ?? ''); ?>
                    <select name="nama_jurnal" required>
                        <option value="">Pilih jurnal</option>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <?php $journalName = (string) ($journal['name'] ?? ''); ?>
                            <option value="<?= esc($journalName, 'attr') ?>" <?= $selectedJournal === $journalName ? 'selected' : '' ?>><?= esc($journalName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Jumlah Tagihan</span>
                    <input type="number" min="0" step="1000" name="jumlah_tagihan" value="<?= esc((string) old('jumlah_tagihan', $createRow['jumlah_tagihan'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Status Pembayaran</span>
                    <select name="status_pembayaran" required>
                        <?php foreach (($statuses ?? []) as $option): ?>
                            <option value="<?= esc((string) $option, 'attr') ?>" <?= (string) old('status_pembayaran', $createRow['status_pembayaran'] ?? 'Belum Dibayar') === (string) $option ? 'selected' : '' ?>>
                                <?= esc((string) $option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="span-2">
                    <span>Keterangan</span>
                    <textarea name="keterangan" rows="4" data-editor="plain"><?= esc((string) old('keterangan', $createRow['keterangan'] ?? '')) ?></textarea>
                </label>
            </div>

            <div class="form-actions">
                <button class="admin-btn secondary" type="button" data-close-modal>Batal</button>
                <button class="admin-btn primary" type="submit">Simpan</button>
            </div>
        </form>
    </section>
</div>
<?= $this->endSection() ?>
