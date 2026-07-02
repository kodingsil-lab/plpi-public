<?php
helper('app_settings');
$appSettings = plpi_app_settings();
$assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
$publicLogoPath = (string) ($appSettings['public_logo_path'] ?? $appSettings['header_logo_path'] ?? '');
$publicLogoUrl = $publicLogoPath !== '' ? plpi_asset_url($publicLogoPath) . '?v=' . $assetVersion : '';
$footerArticles = [
    ['title' => 'Cara Menulis Artikel Ilmiah', 'slug' => 'cara-menulis-artikel-ilmiah-yang-baik'],
    ['title' => 'Struktur IMRAD', 'slug' => 'memahami-struktur-imrad-dalam-artikel-ilmiah'],
    ['title' => 'Tips Memilih Jurnal', 'slug' => 'tips-memilih-jurnal-yang-tepat'],
    ['title' => 'Etika Publikasi', 'slug' => 'etika-publikasi-ilmiah-yang-perlu-dipahami-penulis'],
];

try {
    $db = \Config\Database::connect();
    if ($db->tableExists('educational_articles')) {
        $rows = $db->table('educational_articles')
            ->select('title, slug')
            ->where('status', 'published')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('published_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        if ($rows !== []) {
            $footerArticles = array_map(static fn(array $row): array => [
                'title' => (string) ($row['title'] ?? ''),
                'slug' => (string) ($row['slug'] ?? ''),
            ], $rows);
        }
    }
} catch (\Throwable $e) {
    // Keep static fallback links.
}
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
                PLPI dikelola oleh UPT Publikasi dan Penerbitan Universitas San Pedro sebagai layanan pengajuan LoA,
                informasi jurnal, pendampingan publikasi, dan edukasi literasi ilmiah yang terintegrasi.
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
            <?php foreach (array_slice($footerArticles, 0, 5) as $article): ?>
                <?php if (($article['slug'] ?? '') !== '' && ($article['title'] ?? '') !== ''): ?>
                    <a href="<?= site_url('artikel/' . (string) $article['slug']) ?>"><?= esc((string) $article['title']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="footer-column footer-contact">
            <h4>Kontak Layanan</h4>

            <div class="contact-item">
                <span>📍</span>
                <p>UPT Publikasi dan Penerbitan Universitas San Pedro</p>
            </div>

            <div class="contact-item">
                <span>✉️</span>
                <p><a href="mailto:info@plpi.unisap.ac.id">info@plpi.unisap.ac.id</a></p>
            </div>

            <div class="contact-item">
                <span>WA</span>
                <p><a href="https://wa.me/6282213331314" target="_blank" rel="noopener noreferrer">0822-1333-1314</a></p>
            </div>

            <div class="contact-item">
                <span>🕘</span>
                <p>Senin-Jumat, 08.00-17.00 WITA</p>
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
