<?= $this->extend('public/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$categories = array_values(array_unique(array_column($articles, 'category')));
$featuredArticle = $articles[0] ?? null;
$articleItems = array_slice($articles, 1);
?>

<section class="article-list-hero">
    <div class="container article-list-hero-grid">
        <div>
            <nav class="breadcrumb">
                <a href="<?= site_url('/') ?>">Beranda</a>
                <span>/</span>
                <strong>Artikel Edukatif</strong>
            </nav>

            <span class="eyebrow">Literasi Publikasi Ilmiah</span>

            <h1>Artikel Edukatif</h1>

            <p>
                Kumpulan artikel edukatif tentang penulisan artikel ilmiah, struktur naskah,
                etika publikasi, strategi memilih jurnal, dan pengelolaan publikasi.
            </p>
        </div>

        <div class="article-hero-card">
            <span>Total Artikel</span>
            <strong><?= count($articles) ?></strong>
            <p>Artikel panduan tersedia untuk membantu penulis memahami proses publikasi ilmiah.</p>
        </div>
    </div>
</section>

<section class="article-filter-section">
    <div class="container">
        <div class="article-filter-card">
            <div class="article-search-box">
                <span>🔎</span>
                <input 
                    type="text" 
                    id="articleSearch" 
                    placeholder="Cari artikel berdasarkan judul, kategori, atau ringkasan..."
                    autocomplete="off"
                >
            </div>

            <div class="article-category-filter">
                <button type="button" class="category-filter active" data-category="all">
                    Semua
                </button>

                <?php foreach ($categories as $category): ?>
                    <button type="button" class="category-filter" data-category="<?= esc($category) ?>">
                        <?= esc($category) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php if ($featuredArticle): ?>
<section class="section article-featured-section">
    <div class="container">
        <div class="section-heading">
            <span>Artikel Pilihan</span>
            <h2>Bacaan Utama</h2>
            <p>Artikel yang direkomendasikan untuk dibaca terlebih dahulu oleh penulis.</p>
        </div>

        <article 
            class="featured-article-card js-article-card"
            data-title="<?= esc(strtolower($featuredArticle['title'])) ?>"
            data-category="<?= esc(strtolower($featuredArticle['category'])) ?>"
            data-summary="<?= esc(strtolower($featuredArticle['summary'])) ?>"
        >
            <div class="featured-article-visual">
                <img
                    src="<?= esc($featuredArticle['image']) ?>"
                    alt="<?= esc($featuredArticle['image_alt']) ?>"
                    loading="lazy"
                >
                <div class="featured-icon">✍️</div>
                <span><?= esc($featuredArticle['category']) ?></span>
            </div>

            <div class="featured-article-content">
                <div class="article-meta">
                    <span><?= esc($featuredArticle['category']) ?></span>
                    <small><?= esc($featuredArticle['date']) ?> • <?= esc($featuredArticle['read_time']) ?></small>
                </div>

                <h3><?= esc($featuredArticle['title']) ?></h3>

                <p><?= esc($featuredArticle['summary']) ?></p>

                <a href="<?= site_url('artikel/' . $featuredArticle['slug']) ?>" class="btn-primary">
                    Baca Artikel
                </a>
            </div>
        </article>
    </div>
</section>
<?php endif; ?>

<section class="section article-list-section">
    <div class="container">
        <div class="section-between">
            <div class="section-heading">
                <span>Semua Artikel</span>
                <h2>Daftar Artikel</h2>
                <p>Pilih artikel berdasarkan kebutuhan penulisan dan publikasi ilmiah.</p>
            </div>

            <div class="article-count-badge">
                <span id="articleCount"><?= count($articleItems) ?></span>
                <small>artikel ditemukan</small>
            </div>
        </div>

        <div class="article-grid article-grid-modern" id="articleGrid">
            <?php foreach ($articleItems as $article): ?>
                <article 
                    class="article-card article-card-modern js-article-card"
                    data-title="<?= esc(strtolower($article['title'])) ?>"
                    data-category="<?= esc(strtolower($article['category'])) ?>"
                    data-summary="<?= esc(strtolower($article['summary'])) ?>"
                >
                    <a href="<?= site_url('artikel/' . $article['slug']) ?>" class="article-card-image">
                        <img
                            src="<?= esc($article['image']) ?>"
                            alt="<?= esc($article['image_alt']) ?>"
                            loading="lazy"
                        >
                    </a>

                    <div class="article-card-top">
                        <div class="article-card-icon">
                            <?php if ($article['category'] === 'Penulisan Ilmiah'): ?>
                                ✍️
                            <?php elseif ($article['category'] === 'Struktur Artikel'): ?>
                                🧩
                            <?php elseif ($article['category'] === 'Publikasi'): ?>
                                🚀
                            <?php elseif ($article['category'] === 'Etika Publikasi'): ?>
                                ⚖️
                            <?php else: ?>
                                📘
                            <?php endif; ?>
                        </div>

                        <span class="article-category-pill">
                            <?= esc($article['category']) ?>
                        </span>
                    </div>

                    <h3><?= esc($article['title']) ?></h3>

                    <p><?= esc($article['summary']) ?></p>

                    <div class="article-card-bottom">
                        <small><?= esc($article['date']) ?> • <?= esc($article['read_time']) ?></small>
                        <a href="<?= site_url('artikel/' . $article['slug']) ?>">Baca</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="article-empty-state" id="articleEmpty" style="display: none;">
            <div>🔍</div>
            <h3>Artikel tidak ditemukan</h3>
            <p>Coba gunakan kata kunci lain atau pilih kategori berbeda.</p>
            <button type="button" id="resetArticleFilter">Tampilkan Semua Artikel</button>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
