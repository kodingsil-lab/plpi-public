<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$rows = is_array($rows ?? null) ? $rows : [];
?>

<section class="template-public-hero">
    <div class="container template-public-wrap">
        <nav class="breadcrumb">
            <a href="<?= site_url('/') ?>">Beranda</a>
            <span>/</span>
            <strong>Template Artikel</strong>
        </nav>

        <span class="eyebrow">Template Artikel Jurnal</span>
        <h1>Template Artikel</h1>
        <p>Pilih template artikel sesuai jurnal tujuan. Link pada tombol download akan langsung mengunduh file template yang tersedia.</p>
    </div>
</section>

<section class="section template-public-section">
    <div class="container">
        <div class="template-table-card">
            <table class="template-public-table">
                <colgroup>
                    <col class="template-col-no">
                    <col class="template-col-name">
                    <col class="template-col-desc">
                    <col class="template-col-action">
                </colgroup>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jurnal</th>
                        <th>Website Jurnal</th>
                        <th>Template Artikel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $index => $journal): ?>
                        <?php
                            $slug = (string) ($journal['article_template_slug'] ?? '');
                            $hasTemplate = ! empty($journal['template_id']);
                            $downloadUrl = $hasTemplate && $slug !== '' ? site_url('TemplateArtikel-' . $slug) : '';
                            $websiteUrl = trim((string) ($journal['website_url'] ?? ''));
                        ?>
                        <tr>
                            <td data-label="No"><?= esc((string) ($index + 1)) ?></td>
                            <td data-label="Nama Jurnal">
                                <strong><?= esc((string) ($journal['name'] ?? 'Jurnal')) ?></strong>
                                <span><?= esc((string) ($journal['code'] ?? 'PLPI')) ?></span>
                            </td>
                            <td data-label="Website Jurnal">
                                <?php if ($websiteUrl !== ''): ?>
                                    <a class="template-website-link" href="<?= esc($websiteUrl, 'attr') ?>" target="_blank" rel="noopener noreferrer">
                                        <iconify-icon icon="mdi:open-in-new"></iconify-icon>
                                        Kunjungi Website
                                    </a>
                                <?php else: ?>
                                    <span class="status-badge process">Belum tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Template Artikel">
                                <?php if ($hasTemplate): ?>
                                    <div class="template-table-actions">
                                        <a class="template-action download" href="<?= esc($downloadUrl, 'attr') ?>">
                                            <iconify-icon icon="mdi:download-outline"></iconify-icon>
                                            Unduh
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="status-badge process">Belum tersedia</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if ($rows === []): ?>
                        <tr>
                            <td colspan="4">Data jurnal belum tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
