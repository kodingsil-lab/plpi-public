<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$formatSize = static function ($bytes): string {
    $bytes = (int) $bytes;
    if ($bytes <= 0) {
        return '-';
    }

    return $bytes >= 1048576
        ? number_format($bytes / 1048576, 1, ',', '.') . ' MB'
        : number_format($bytes / 1024, 0, ',', '.') . ' KB';
};

$publicListUrl = site_url('TemplateArtikel');
$csrfInput = csrf_field();
$tableRows = [];
foreach (($rows ?? []) as $index => $row) {
    $journalId = (int) ($row['id'] ?? 0);
    $slug = trim((string) ($row['article_template_slug'] ?? ''));
    $publicUrl = $slug !== '' ? site_url('TemplateArtikel-' . $slug) : '';
    $hasTemplate = ! empty($row['template_id']);
    $templateId = (int) ($row['template_id'] ?? 0);

    $templateInfo = $hasTemplate
        ? '<div class="template-file-cell"><strong>' . esc((string) ($row['original_name'] ?? '-')) . '</strong><small>.' . esc((string) ($row['file_ext'] ?? 'docx')) . ' &bull; ' . esc($formatSize($row['file_size'] ?? 0)) . '</small></div>'
        : '<span class="status-pill muted">Belum tersedia</span>';

    $linkBox = $publicUrl !== ''
        ? '<div class="template-link-box"><span title="' . esc($publicUrl, 'attr') . '">' . esc($publicUrl) . '</span><button class="icon-btn neutral js-copy-template-link" type="button" data-link="' . esc($publicUrl, 'attr') . '" title="Salin link" aria-label="Salin link"><iconify-icon icon="mdi:content-copy"></iconify-icon></button></div>'
        : '<span class="status-pill muted">Link belum tersedia</span>';

    $downloadAction = $hasTemplate
        ? '<a class="icon-btn download" href="' . site_url('dashboard/journal-templates/' . $templateId . '/download') . '" title="Download template" aria-label="Download template"><iconify-icon icon="mdi:download-outline"></iconify-icon></a>'
        : '<button class="icon-btn neutral" type="button" disabled title="Belum tersedia" aria-label="Belum tersedia"><iconify-icon icon="mdi:download-off-outline"></iconify-icon></button>';

    $tableRows[] = [
        'no'       => esc((string) ($index + 1)),
        'journal'  => '<div class="template-journal-cell"><strong>' . esc((string) ($row['name'] ?? '-')) . '</strong><small>' . esc((string) ($row['code'] ?? '-')) . '</small></div>',
        'template' => $templateInfo,
        'link'     => $linkBox,
        'upload'   => '<form class="template-upload-form" method="post" enctype="multipart/form-data" action="' . site_url('dashboard/journal-templates/' . $journalId . '/upload') . '">' . $csrfInput . '<label class="template-file-picker"><input type="file" name="template_file" accept=".doc,.docx" required><span>Pilih File</span><small data-file-name></small></label><button class="admin-btn primary" type="submit">Upload</button></form>',
        'actions'  => '<div class="row-actions">' . $downloadAction
            . '<form method="post" action="' . site_url('dashboard/journal-templates/' . $journalId . '/regenerate-link') . '" onsubmit="return confirm(\'Generate ulang link template jurnal ini?\')">'
            . $csrfInput
            . '<button class="icon-btn edit" type="submit" title="Generate ulang link" aria-label="Generate ulang link"><iconify-icon icon="mdi:link-variant-plus"></iconify-icon></button>'
            . '</form></div>',
    ];
}
?>

<section class="admin-panel">
    <div class="panel-heading">
        <div>
            <span>Link Publik Per Jurnal</span>
            <h2>Template Artikel</h2>
            <p>Upload satu file template Word terbaru untuk tiap jurnal. Link public tidak tampil di halaman utama dan bisa dibagikan langsung ke penulis.</p>
        </div>
    </div>

    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <div class="template-public-page-box">
        <div>
            <span>Halaman Public</span>
            <strong>Daftar Template Artikel</strong>
            <p>Bagikan link ini untuk membuka halaman public yang berisi semua template artikel jurnal.</p>
        </div>
        <div class="template-public-page-link">
            <span title="<?= esc($publicListUrl, 'attr') ?>"><?= esc($publicListUrl) ?></span>
            <button class="admin-btn secondary js-copy-template-link" type="button" data-link="<?= esc($publicListUrl, 'attr') ?>">Salin Link</button>
        </div>
    </div>

    <?= admin_table([
        'id' => 'journal-templates-table',
        'search' => true,
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal', 'sortable' => true],
            ['key' => 'template', 'label' => 'File Template', 'sortable' => true],
            ['key' => 'link', 'label' => 'Link Share'],
            ['key' => 'upload', 'label' => 'Upload Template'],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada data jurnal.',
    ]) ?>
</section>

<script>
document.addEventListener('click', function (event) {
    const button = event.target.closest('.js-copy-template-link');
    if (!button) return;

    const link = button.dataset.link || '';
    if (!link) return;

    navigator.clipboard.writeText(link).then(function () {
        button.classList.add('success');
        window.setTimeout(function () {
            button.classList.remove('success');
        }, 900);
    });
});

document.addEventListener('change', function (event) {
    const input = event.target.closest('.template-file-picker input[type="file"]');
    if (!input) return;

    const picker = input.closest('.template-file-picker');
    const label = picker ? picker.querySelector('[data-file-name]') : null;
    if (!label) return;

    label.textContent = input.files && input.files.length ? input.files[0].name : '';
});
</script>
<?= $this->endSection() ?>
