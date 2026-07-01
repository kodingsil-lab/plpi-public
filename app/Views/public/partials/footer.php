<?php
helper('app_settings');
$appSettings = plpi_app_settings();
$assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
$publicLogoPath = (string) ($appSettings['public_logo_path'] ?? $appSettings['header_logo_path'] ?? '');
$publicLogoUrl = $publicLogoPath !== '' ? plpi_asset_url($publicLogoPath) . '?v=' . $assetVersion : '';
?>
<footer class="site-footer">
    <div class="container footer-main">
        <div class="footer-about">
            <a href="<?= site_url('/') ?>" class="footer-brand">
                <span class="brand-mark">
                    <?php if ($publicLogoUrl !== ''): ?>
                        <img src="<?= esc($publicLogoUrl, 'attr') ?>" alt="Logo PLPI">
                    <?php else: ?>
                        <span>PL</span>
                    <?php endif; ?>
                </span>

                <span class="footer-brand-text">
                    <strong>PLPI</strong>
                    <small>Pusat Layanan Publikasi Ilmiah</small>
                </span>
            </a>

            <p>
                Sistem layanan public untuk pengajuan, pemantauan, dan verifikasi Letter of Acceptance
                serta penyediaan informasi jurnal dan literasi publikasi ilmiah.
            </p>

            <div class="footer-badges">
                <span>LoA</span>
                <span>Jurnal</span>
                <span>Artikel Edukatif</span>
            </div>
        </div>

        <div class="footer-column">
            <h4>Layanan</h4>
            <a href="<?= site_url('ajukan-loa') ?>">Pengajuan LoA</a>
            <a href="<?= site_url('verifikasi-loa') ?>">Verifikasi LoA</a>
            <a href="<?= site_url('/#permohonan-terbaru') ?>">Permohonan Terbaru</a>
            <a href="<?= site_url('/#profil-jurnal') ?>">Profil Jurnal</a>
        </div>

        <div class="footer-column">
            <h4>Artikel Edukatif</h4>
            <a href="<?= site_url('artikel/cara-menulis-artikel-ilmiah-yang-baik') ?>">Cara Menulis Artikel Ilmiah</a>
            <a href="<?= site_url('artikel/memahami-struktur-imrad-dalam-artikel-ilmiah') ?>">Struktur IMRAD</a>
            <a href="<?= site_url('artikel/tips-memilih-jurnal-yang-tepat') ?>">Tips Memilih Jurnal</a>
            <a href="<?= site_url('artikel/etika-publikasi-ilmiah-yang-perlu-dipahami-penulis') ?>">Etika Publikasi</a>
        </div>

        <div class="footer-column footer-contact">
            <h4>Kontak Layanan</h4>

            <div class="contact-item">
                <span>📍</span>
                <p>Unit layanan publikasi ilmiah dan pengelolaan jurnal.</p>
            </div>

            <div class="contact-item">
                <span>✉️</span>
                <p>admin@plpi.test</p>
            </div>

            <div class="contact-item">
                <span>🕘</span>
                <p>Senin–Jumat, 08.00–16.00 WITA</p>
            </div>
        </div>
    </div>

    <div class="container footer-strip">
        <p>
            &copy; <?= date('Y') ?> PLPI. Seluruh hak cipta dilindungi.
        </p>

        <div>
            <a href="#">Kebijakan Layanan</a>
            <a href="#">Bantuan</a>
        </div>
    </div>
</footer>
