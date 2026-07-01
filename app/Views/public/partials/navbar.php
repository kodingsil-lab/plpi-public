<?php
helper('app_settings');
$uri = service('uri');
$segment1 = $uri->getSegment(1);
$isHome = empty($segment1);
$appSettings = plpi_app_settings();
$assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
$publicLogoPath = (string) ($appSettings['public_logo_path'] ?? $appSettings['header_logo_path'] ?? '');
$publicLogoUrl = $publicLogoPath !== '' ? plpi_asset_url($publicLogoPath) . '?v=' . $assetVersion : '';
?>

<header class="site-header">
    <div class="container nav-wrap">
        <a href="<?= site_url('/') ?>" class="brand">
            <span class="brand-mark">
                <?php if ($publicLogoUrl !== ''): ?>
                    <img src="<?= esc($publicLogoUrl, 'attr') ?>" alt="Logo PLPI">
                <?php else: ?>
                    <span>PL</span>
                <?php endif; ?>
            </span>

            <span class="brand-text">
                <strong>PLPI</strong>
                <small>Pusat Layanan Publikasi Ilmiah</small>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-label="Buka menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="nav-menu">
            <a href="<?= site_url('/') ?>" class="<?= $isHome ? 'active' : '' ?>">
                Beranda
            </a>

            <a href="<?= site_url('ajukan-loa') ?>" class="<?= $segment1 === 'ajukan-loa' ? 'active' : '' ?>">
                Ajukan LoA
            </a>

            <a href="<?= site_url('verifikasi-loa') ?>" class="<?= $segment1 === 'verifikasi-loa' ? 'active' : '' ?>">
                Verifikasi LoA
            </a>

            <a href="<?= site_url('artikel') ?>" class="<?= $segment1 === 'artikel' ? 'active' : '' ?>">
                Artikel Edukatif
            </a>

            <a href="<?= site_url('/#profil-jurnal') ?>">
                Profil Jurnal
            </a>

            <a href="<?= site_url('login') ?>" class="btn-nav">
                Masuk Dashboard
            </a>
        </nav>
    </div>

    <div class="nav-backdrop"></div>
</header>
