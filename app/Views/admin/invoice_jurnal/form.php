<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($row['id']); ?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" action="<?= $isEdit ? site_url('dashboard/invoice-jurnal/' . (int) $row['id'] . '/update') : site_url('dashboard/invoice-jurnal') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:receipt-text-outline"></iconify-icon>Identitas Invoice</h3>
            <div class="form-grid">
                <label>
                    <span>Nomor Invoice</span>
                    <input name="nomor_invoice" value="<?= esc((string) old('nomor_invoice', $row['nomor_invoice'] ?? ''), 'attr') ?>" placeholder="Otomatis jika dikosongkan">
                </label>
                <label>
                    <span>Status Pembayaran</span>
                    <select name="status_pembayaran" required>
                        <?php foreach (($statuses ?? []) as $option): ?>
                            <option value="<?= esc((string) $option, 'attr') ?>" <?= (string) old('status_pembayaran', $row['status_pembayaran'] ?? 'Belum Dibayar') === (string) $option ? 'selected' : '' ?>>
                                <?= esc((string) $option) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Tanggal Invoice</span>
                    <input type="date" name="tanggal_invoice" value="<?= esc((string) old('tanggal_invoice', $row['tanggal_invoice'] ?? date('Y-m-d')), 'attr') ?>" required>
                </label>
                <label>
                    <span>Jatuh Tempo</span>
                    <input type="date" name="jatuh_tempo" value="<?= esc((string) old('jatuh_tempo', $row['jatuh_tempo'] ?? ''), 'attr') ?>">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:file-document-edit-outline"></iconify-icon>Data Artikel</h3>
            <div class="form-grid">
                <label class="span-2">
                    <span>Judul Artikel</span>
                    <textarea name="judul_artikel" rows="3" data-editor="plain" required><?= esc((string) old('judul_artikel', $row['judul_artikel'] ?? '')) ?></textarea>
                </label>
                <label>
                    <span>Nama Penulis</span>
                    <input name="nama_penulis" value="<?= esc((string) old('nama_penulis', $row['nama_penulis'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Institusi Penulis</span>
                    <input name="institusi_penulis" value="<?= esc((string) old('institusi_penulis', $row['institusi_penulis'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Nama Jurnal</span>
                    <input name="nama_jurnal" list="journal-options" value="<?= esc((string) old('nama_jurnal', $row['nama_jurnal'] ?? ''), 'attr') ?>" required>
                    <datalist id="journal-options">
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <option value="<?= esc((string) ($journal['name'] ?? ''), 'attr') ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </label>
                <label>
                    <span>Jumlah Tagihan</span>
                    <input type="number" min="0" step="1000" name="jumlah_tagihan" value="<?= esc((string) old('jumlah_tagihan', $row['jumlah_tagihan'] ?? ''), 'attr') ?>" required>
                </label>
                <label class="span-2">
                    <span>Keterangan</span>
                    <textarea name="keterangan" rows="4" data-editor="plain"><?= esc((string) old('keterangan', $row['keterangan'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/invoice-jurnal') ?>">Kembali</a>
            <button class="admin-btn primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
