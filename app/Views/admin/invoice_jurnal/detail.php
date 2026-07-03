<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $status = (string) ($row['status_pembayaran'] ?? 'Belum Dibayar'); ?>
<section class="admin-panel user-form-panel">
    <div class="detail-header">
        <div>
            <strong><?= esc((string) ($row['nomor_invoice'] ?? '-')) ?></strong>
            <span class="status-pill <?= esc(invoice_status_class($status), 'attr') ?>"><?= esc($status) ?></span>
        </div>
        <div class="row-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/invoice-jurnal') ?>">Kembali</a>
            <a class="admin-btn secondary" href="<?= site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/print') ?>" target="_blank" rel="noopener noreferrer">Cetak</a>
            <a class="admin-btn primary" href="<?= site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/edit') ?>">Edit</a>
        </div>
    </div>

    <div class="form-section">
        <h3><iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>Ringkasan Invoice</h3>
        <div class="detail-grid">
            <span>Nomor Invoice</span><strong><?= esc((string) ($row['nomor_invoice'] ?? '-')) ?></strong>
            <span>Tanggal Invoice</span><strong><?= esc(invoice_date($row['tanggal_invoice'] ?? null)) ?></strong>
            <span>Jatuh Tempo</span><strong><?= esc(invoice_date($row['jatuh_tempo'] ?? null)) ?></strong>
            <span>Jumlah Tagihan</span><strong><?= esc(invoice_currency($row['jumlah_tagihan'] ?? 0)) ?></strong>
            <span>Nama Penulis</span><strong><?= esc((string) ($row['nama_penulis'] ?? '-')) ?></strong>
            <span>Institusi Penulis</span><strong><?= esc((string) ($row['institusi_penulis'] ?? '-')) ?></strong>
            <span>Nama Jurnal</span><strong><?= esc((string) ($row['nama_jurnal'] ?? '-')) ?></strong>
            <span>Judul Artikel</span><strong><?= esc((string) ($row['judul_artikel'] ?? '-')) ?></strong>
            <span>Keterangan</span><strong><?= nl2br(esc((string) ($row['keterangan'] ?? '-'))) ?></strong>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
