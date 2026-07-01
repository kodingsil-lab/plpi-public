<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Cetak Invoice') ?></title>
    <style>
        body { color: #0b2545; font-family: Arial, sans-serif; margin: 0; background: #f8fafc; }
        .page { background: #fff; margin: 24px auto; max-width: 840px; min-height: 1120px; padding: 48px; box-shadow: 0 20px 60px rgba(15, 23, 42, .08); }
        .top { align-items: flex-start; border-bottom: 2px solid #0f8b7f; display: flex; justify-content: space-between; gap: 32px; padding-bottom: 24px; }
        .brand strong { display: block; font-size: 28px; letter-spacing: 0; }
        .brand span { color: #64748b; display: block; font-size: 13px; margin-top: 4px; text-transform: uppercase; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 32px; margin: 0; }
        .invoice-title span { color: #0f8b7f; font-weight: 700; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 32px; }
        .box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 18px; }
        .box h2 { font-size: 14px; margin: 0 0 14px; text-transform: uppercase; color: #0f8b7f; }
        .line { display: grid; grid-template-columns: 150px 1fr; gap: 16px; margin: 10px 0; }
        .line span { color: #64748b; }
        .article { margin-top: 28px; }
        .article h2 { font-size: 16px; margin-bottom: 8px; }
        .article p { line-height: 1.7; margin: 0; }
        .items { border-collapse: collapse; margin-top: 32px; width: 100%; }
        .items th { background: #0f8b7f; color: #fff; font-size: 12px; letter-spacing: .04em; padding: 12px 14px; text-align: left; text-transform: uppercase; }
        .items td { border-bottom: 1px solid #e5e7eb; color: #0b2545; padding: 14px; vertical-align: top; }
        .items tbody tr:last-child td { border-bottom: 2px solid #0f8b7f; }
        .items .center { text-align: center; }
        .items .right { text-align: right; white-space: nowrap; }
        .item-title { display: block; font-weight: 700; line-height: 1.45; }
        .item-meta { color: #64748b; display: block; font-size: 12px; line-height: 1.5; margin-top: 5px; }
        .total { align-items: center; border-radius: 12px; display: flex; justify-content: space-between; margin-top: 32px; padding: 22px 24px; background: #ecfdf5; border: 1px solid #bbf7d0; }
        .total span { color: #0f766e; font-weight: 700; text-transform: uppercase; }
        .total strong { font-size: 28px; }
        .note { color: #64748b; line-height: 1.7; margin-top: 28px; }
        .toolbar { margin: 18px auto 0; max-width: 840px; text-align: right; }
        .toolbar button { background: #0f8b7f; border: 0; border-radius: 10px; color: #fff; cursor: pointer; font-weight: 700; padding: 12px 18px; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none; }
            .page { box-shadow: none; margin: 0; max-width: none; min-height: auto; padding: 24px; }
        }
    </style>
</head>
<body>
    <?php $status = (string) ($row['status_pembayaran'] ?? 'Belum Dibayar'); ?>
    <div class="toolbar"><button type="button" onclick="window.print()">Cetak Invoice</button></div>
    <main class="page">
        <section class="top">
            <div class="brand">
                <strong>PLPI</strong>
                <span>Pusat Layanan Publikasi Ilmiah</span>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <span><?= esc((string) ($row['nomor_invoice'] ?? '-')) ?></span>
            </div>
        </section>

        <section class="grid">
            <div class="box">
                <h2>Ditagihkan Kepada</h2>
                <div class="line"><span>Nama</span><strong><?= esc((string) ($row['nama_penulis'] ?? '-')) ?></strong></div>
                <div class="line"><span>Institusi</span><strong><?= esc((string) ($row['institusi_penulis'] ?? '-')) ?></strong></div>
                <div class="line"><span>Jurnal</span><strong><?= esc((string) ($row['nama_jurnal'] ?? '-')) ?></strong></div>
            </div>
            <div class="box">
                <h2>Informasi Invoice</h2>
                <div class="line"><span>Tanggal</span><strong><?= esc(invoice_date($row['tanggal_invoice'] ?? null)) ?></strong></div>
                <div class="line"><span>Jatuh Tempo</span><strong><?= esc(invoice_date($row['jatuh_tempo'] ?? null)) ?></strong></div>
                <div class="line"><span>Status</span><strong><?= esc($status) ?></strong></div>
            </div>
        </section>

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 54px;">No</th>
                    <th>Item</th>
                    <th class="center" style="width: 72px;">Qty</th>
                    <th class="right" style="width: 150px;">Harga</th>
                    <th class="right" style="width: 150px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $amount = (float) ($row['jumlah_tagihan'] ?? 0); ?>
                <tr>
                    <td class="center">1</td>
                    <td>
                        <span class="item-title"><?= esc((string) ($row['judul_artikel'] ?? '-')) ?></span>
                        <span class="item-meta">Biaya layanan publikasi jurnal: <?= esc((string) ($row['nama_jurnal'] ?? '-')) ?></span>
                    </td>
                    <td class="center">1</td>
                    <td class="right"><?= esc(invoice_currency($amount)) ?></td>
                    <td class="right"><?= esc(invoice_currency($amount)) ?></td>
                </tr>
            </tbody>
        </table>

        <section class="total">
            <span>Total Tagihan</span>
            <strong><?= esc(invoice_currency($amount ?? 0)) ?></strong>
        </section>

        <?php if (! empty($row['keterangan'])): ?>
            <p class="note"><?= nl2br(esc((string) $row['keterangan'])) ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
