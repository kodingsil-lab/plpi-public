<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$normalizeWhatsappNumber = static function (?string $raw): string {
    $digits = preg_replace('/\D+/', '', (string) $raw) ?? '';
    if ($digits === '') {
        return '';
    }
    if (str_starts_with($digits, '00')) {
        $digits = substr($digits, 2);
    }
    if (str_starts_with($digits, '0')) {
        $digits = '62' . substr($digits, 1);
    } elseif (str_starts_with($digits, '8')) {
        $digits = '62' . $digits;
    }

    return strlen($digits) >= 8 ? $digits : '';
};

$formatAuthors = static function ($raw): string {
    $authors = is_array($raw) ? $raw : json_decode((string) $raw, true);
    if (! is_array($authors) || $authors === []) {
        return '-';
    }

    $names = [];
    foreach ($authors as $author) {
        $name = is_array($author) ? trim((string) ($author['name'] ?? '')) : trim((string) $author);
        $name = trim(preg_replace('/^(Ketua|Anggota(?:\s*\d*)?)\s*[:\-]\s*/iu', '', $name) ?? $name);
        if ($name !== '') {
            $names[] = $name;
        }
    }

    return $names !== [] ? implode(', ', $names) : '-';
};

$statusMeta = static function (string $status): array {
    $status = strtolower(trim($status));
    if ($status === 'notifikasi terkirim') {
        return ['label' => 'Terkirim', 'class' => 'done'];
    }

    return ['label' => 'Menunggu', 'class' => 'info'];
};

$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $status = $statusMeta((string) ($row['status'] ?? 'menunggu'));
    $token = (string) ($row['public_token'] ?? '');
    $downloadUrl = $token !== '' ? site_url('loa/v/' . $token . '/download') : site_url('/');
    $verifyUrl = site_url('verifikasi-loa');
    $journalUrl = trim((string) ($row['journal_url'] ?? '')) ?: site_url('/');
    $authorsText = $formatAuthors($row['authors_json'] ?? null);
    $whatsappNumber = $normalizeWhatsappNumber($row['whatsapp_number'] ?? null);
    $whatsappMessage = implode("\n", [
        'Yth. Bapak/Ibu Penulis,',
        '*' . $authorsText . '*',
        '',
        'Letter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:',
        '',
        'Judul:',
        '*' . (string) ($row['title'] ?? '-') . '*',
        '',
        'Nomor LoA:',
        '*' . (string) ($row['loa_number'] ?? '-') . '*',
        '',
        'Silakan mengunduh dokumen LoA melalui tautan berikut:',
        $downloadUrl,
        '',
        'Verifikasi LoA dapat dilakukan melalui tautan berikut:',
        $verifyUrl,
        '',
        'Hormat kami,',
        '*Tim Editor*',
        '*' . (string) ($row['journal_name'] ?? '-') . '*',
        $journalUrl,
    ]);
    $whatsappAction = $whatsappNumber !== ''
        ? '<a class="icon-btn whatsapp" href="https://wa.me/' . esc($whatsappNumber, 'attr') . '?text=' . rawurlencode($whatsappMessage) . '" target="_blank" rel="noopener" title="Kirim WhatsApp" aria-label="Kirim WhatsApp"><iconify-icon icon="ic:baseline-whatsapp"></iconify-icon></a>'
        : '<button class="icon-btn whatsapp" type="button" disabled title="Nomor WA belum tersedia" aria-label="Nomor WA belum tersedia"><iconify-icon icon="ic:baseline-whatsapp"></iconify-icon></button>';

    $tableRows[] = [
        '_bulk_id' => (string) ($row['id'] ?? ''),
        'no' => esc((string) (($startNumber ?? 1) + $index)),
        'number' => '<strong>' . esc((string) ($row['loa_number'] ?? '-')) . '</strong><br><small>' . esc((string) ($row['corresponding_email'] ?? '-')) . '</small>',
        'journal' => esc((string) ($row['journal_name'] ?? '-')),
        'article' => esc((string) ($row['title'] ?? '-')),
        'status' => '<span class="status-pill ' . esc($status['class'], 'attr') . '">' . esc($status['label']) . '</span>',
        'date' => esc(! empty($row['sent_at']) ? date('d M Y H:i', strtotime((string) $row['sent_at'])) : (! empty($row['published_at']) ? date('d M Y', strtotime((string) $row['published_at'])) : '-')),
        'actions' => '<div class="row-actions">'
            . $whatsappAction
            . '<form method="post" action="' . site_url('dashboard/notifikasi/' . (int) $row['id'] . '/kirim-email') . '" onsubmit="return confirm(\'Kirim email notifikasi LoA ini?\')"><button class="icon-btn email" type="submit" title="Kirim Email" aria-label="Kirim Email"><iconify-icon icon="mdi:email-send"></iconify-icon></button></form>'
            . '<form method="post" action="' . site_url('dashboard/notifikasi/' . (int) $row['id'] . '/delete') . '" onsubmit="return confirm(\'Hapus item notifikasi ini?\')"><button class="icon-btn delete" type="submit" title="Hapus" aria-label="Hapus"><iconify-icon icon="mdi:trash-can-outline"></iconify-icon></button></form>'
            . '</div>',
    ];
}
?>
<section class="admin-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <?= admin_table([
        'id' => 'loa-notifications-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/notifikasi/bulk-delete'),
            'confirm' => 'Hapus notifikasi yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'number', 'label' => 'Nomor LoA / Email', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal', 'sortable' => true],
            ['key' => 'article', 'label' => 'Judul', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'date', 'label' => 'Tanggal', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada item notifikasi. Item akan muncul otomatis dari LoA yang sudah terbit.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
