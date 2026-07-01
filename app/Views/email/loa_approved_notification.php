<?php
/**
 * @var array $letter
 * @var string $authors
 * @var string $journalName
 * @var string $editorName
 * @var string $journalUrl
 */
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f4fbf8;
            color: #17324d;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }
        .email-wrap {
            max-width: 640px;
            margin: 0 auto;
            padding: 28px 16px;
        }
        .email-card {
            background: #ffffff;
            border: 1px solid #cde8e3;
            border-radius: 14px;
            overflow: hidden;
        }
        .email-header {
            background: #0f8a7a;
            color: #ffffff;
            padding: 24px 28px;
        }
        .email-header h1 {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.25;
        }
        .email-header p {
            margin: 0;
            color: #dff7f2;
            font-size: 14px;
        }
        .email-body {
            padding: 26px 28px;
        }
        .email-body p {
            margin: 0 0 14px;
        }
        .loa-details {
            margin: 22px 0;
            padding: 18px;
            background: #f7fcfb;
            border: 1px solid #cde8e3;
            border-left: 5px solid #0f8a7a;
            border-radius: 10px;
        }
        .detail-row {
            margin: 0 0 10px;
        }
        .detail-row:last-child {
            margin-bottom: 0;
        }
        .label {
            display: block;
            color: #0f766e;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
        }
        .value {
            display: block;
            color: #17324d;
            font-size: 15px;
            font-weight: 600;
            word-break: break-word;
        }
        .signature {
            margin-top: 22px;
        }
        .journal-link {
            color: #0f8a7a;
            word-break: break-all;
        }
        .email-footer {
            padding: 18px 28px;
            background: #f7fcfb;
            border-top: 1px solid #cde8e3;
            color: #60708a;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-wrap">
        <div class="email-card">
            <div class="email-header">
                <h1>Letter of Acceptance Terbit</h1>
                <p><?= esc($journalName ?? 'Jurnal') ?></p>
            </div>
            <div class="email-body">
                <p>Dengan hormat,</p>
                <p>Kami informasikan bahwa naskah Anda telah diterima untuk dipublikasikan di <?= esc($journalName ?? 'jurnal kami') ?>.</p>

                <div class="loa-details">
                    <div class="detail-row">
                        <span class="label">Nomor LoA</span>
                        <span class="value"><?= esc((string) ($letter['loa_number'] ?? '-')) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Judul Artikel</span>
                        <span class="value"><?= esc((string) ($letter['title'] ?? '-')) ?></span>
                    </div>
                    <?php if (! empty($authors)): ?>
                        <div class="detail-row">
                            <span class="label">Penulis</span>
                            <span class="value"><?= esc($authors) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (! empty($letter['published_at'])): ?>
                        <div class="detail-row">
                            <span class="label">Tanggal Terbit</span>
                            <span class="value"><?= esc(date('d F Y', strtotime((string) $letter['published_at']))) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <p>Dokumen LoA dalam format PDF kami lampirkan pada email ini. Keaslian dokumen dapat diverifikasi melalui QR Code yang terdapat pada PDF atau melalui halaman verifikasi PLPI.</p>

                <div class="signature">
                    <p>Salam hormat,</p>
                    <p><strong><?= esc($editorName ?? 'Pimpinan Redaksi') ?></strong><br><?= esc($journalName ?? 'Jurnal') ?></p>
                    <?php if (! empty($journalUrl ?? '')): ?>
                        <p><a class="journal-link" href="<?= esc($journalUrl, 'attr') ?>"><?= esc($journalUrl) ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="email-footer">
                Email ini dikirim otomatis oleh sistem PLPI. Jika membutuhkan bantuan, silakan hubungi kontak redaksi jurnal terkait.
            </div>
        </div>
    </div>
</body>
</html>
