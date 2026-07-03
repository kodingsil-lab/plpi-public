<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$authors = [];
foreach (($row['authors_json'] ?? []) as $author) {
    $authors[] = is_array($author) ? (string) ($author['name'] ?? '') : (string) $author;
}
$affiliations = [];
foreach (($row['affiliations_json'] ?? []) as $affiliation) {
    $affiliations[] = is_array($affiliation) ? (string) ($affiliation['affiliation'] ?? '') : (string) $affiliation;
}
$statusLabels = [
    'pending' => 'Diproses',
    'revision' => 'Revisi',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
];
$statusClass = static fn(string $status): string => match ($status) {
    'approved' => 'info',
    'revision' => 'info',
    'rejected' => 'danger',
    default => 'warning',
};
$status = (string) ($row['status'] ?? 'pending');
$hasLetter = ! empty($row['has_published_letter']);
$statusLabel = $hasLetter ? 'LoA Terbit' : ($statusLabels[$status] ?? ucfirst($status));
$statusPillClass = $hasLetter ? 'done' : $statusClass($status);
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
$whatsappRaw = trim((string) ($row['whatsapp_number'] ?? ''));
$whatsappNumber = $normalizeWhatsappNumber($whatsappRaw);
?>
<section class="admin-panel user-form-panel">
    <div class="form-section">
        <h3><iconify-icon icon="mdi:file-document-outline"></iconify-icon>Data Permohonan</h3>
        <div class="admin-table request-detail-table">
            <div class="table-row"><span>Kode</span><span><?= esc((string) ($row['request_code'] ?? '-')) ?></span></div>
            <div class="table-row"><span>Jurnal</span><span><?= esc((string) ($row['journal_name'] ?? '-')) ?></span></div>
            <div class="table-row"><span>Judul</span><span><?= esc((string) ($row['title'] ?? '-')) ?></span></div>
            <div class="table-row"><span>Email</span><span><?= esc((string) ($row['corresponding_email'] ?? '-')) ?></span></div>
            <div class="table-row">
                <span>WhatsApp</span>
                <span>
                    <?php if ($whatsappRaw !== '' && $whatsappNumber !== ''): ?>
                        <a class="whatsapp-detail-link" href="https://wa.me/<?= esc($whatsappNumber, 'attr') ?>" target="_blank" rel="noopener noreferrer"><?= esc($whatsappRaw) ?></a>
                    <?php else: ?>
                        <?= esc($whatsappRaw !== '' ? $whatsappRaw : '-') ?>
                    <?php endif; ?>
                </span>
            </div>
            <div class="table-row"><span>Artikel URL</span><span><?= esc((string) ($row['article_url'] ?? '-')) ?></span></div>
            <div class="table-row"><span>Volume/Issue/Tahun</span><span><?= esc(trim((string) ($row['volume'] ?? '-') . ' / ' . (string) ($row['issue_number'] ?? '-') . ' / ' . (string) ($row['published_year'] ?? '-'))) ?></span></div>
            <div class="table-row"><span>Penulis</span><span><?= nl2br(esc(implode("\n", array_filter($authors)) ?: '-')) ?></span></div>
            <div class="table-row"><span>Afiliasi</span><span><?= nl2br(esc(implode("\n", array_filter($affiliations)) ?: '-')) ?></span></div>
            <div class="table-row"><span>Status</span><span><span class="status-pill <?= esc($statusPillClass, 'attr') ?>"><?= esc($statusLabel) ?></span></span></div>
        </div>
    </div>

    <div class="form-actions">
        <a class="admin-btn secondary" href="<?= site_url('dashboard/loa-requests') ?>">Kembali</a>
        <?php if (in_array((string) ($row['status'] ?? ''), ['pending', 'revision'], true) && empty($row['has_published_letter'])): ?>
            <form method="post" action="<?= site_url('dashboard/loa-requests/' . (int) $row['id'] . '/approve') ?>" onsubmit="return confirm('Setujui dan terbitkan LoA ini?')">
                <button class="admin-btn primary" type="submit">Setujui</button>
            </form>
            <form method="post" action="<?= site_url('dashboard/loa-requests/' . (int) $row['id'] . '/reject') ?>" onsubmit="return confirm('Tolak permohonan ini?')">
                <button class="admin-btn danger" type="submit">Tolak</button>
            </form>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
