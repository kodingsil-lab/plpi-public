<?= $this->extend('public/layouts/main') ?>

<?= $this->section('content') ?>

<?php
$shareUrl = current_url();
$shareTitle = $article['title'];
$emailSubject = rawurlencode($shareTitle);
$emailBody = rawurlencode($shareTitle . "\n\n" . $shareUrl);
$whatsappText = rawurlencode($shareTitle . "\n" . $shareUrl);
?>

<section class="article-hero-new">
    <div class="container narrow">
        <nav class="breadcrumb">
            <a href="<?= site_url('/') ?>">Beranda</a>
            <span>/</span>
            <a href="<?= site_url('artikel') ?>">Artikel Edukatif</a>
            <span>/</span>
            <strong><?= esc($article['category']) ?></strong>
        </nav>

        <span class="eyebrow"><?= esc($article['category']) ?></span>

        <h1><?= esc($article['title']) ?></h1>

        <p class="article-lead">
            <?= esc($article['summary']) ?>
        </p>

        <div class="article-author-row">
            <div class="author-avatar">PL</div>
            <div>
                <strong>Tim PLPI</strong>
                <span><?= esc($article['date']) ?> • <?= esc($article['read_time']) ?></span>
            </div>
        </div>

        <figure class="article-hero-image">
            <img
                src="<?= esc($article['image']) ?>"
                alt="<?= esc($article['image_alt']) ?>"
                loading="eager"
            >
        </figure>
    </div>
</section>

<section class="article-reading-section">
    <div class="container article-reading-layout">

        <aside class="article-sidebar">
            <div class="reading-card sticky-card">
                <span class="reading-card-label">Navigasi Artikel</span>
                <a href="#ringkasan">Ringkasan</a>
                <a href="#isi-artikel">Isi Artikel</a>
                <a href="#penutup">Penutup</a>
                <a href="#artikel-terkait">Artikel Terkait</a>
            </div>

            <div class="reading-card">
                <span class="reading-card-label">Info Bacaan</span>

                <p>Kategori</p>
                <strong><?= esc($article['category']) ?></strong>

                <p>Estimasi</p>
                <strong><?= esc($article['read_time']) ?></strong>

                <p>Tanggal</p>
                <strong><?= esc($article['date']) ?></strong>
            </div>
        </aside>

        <article class="article-content enhanced" id="isi-artikel">
            <div class="article-summary-box" id="ringkasan">
                <span>Ringkasan Artikel</span>
                <p><?= esc($article['summary']) ?></p>
            </div>

            <div class="article-prose">
                <?php if (! empty($article['content_html'])): ?>
                    <?= $article['content_html'] ?>
                <?php else: ?>
                    <?php foreach ($article['content'] as $index => $paragraph): ?>
                        <?php if ($index === 0): ?>
                            <p class="dropcap"><?= esc($paragraph) ?></p>
                        <?php else: ?>
                            <p><?= esc($paragraph) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="article-share">
                <div>
                    <span>Bagikan artikel ini</span>
                    <p>Bagikan artikel melalui email atau WhatsApp kepada penulis, editor, atau pengelola jurnal.</p>
                </div>

                <div class="share-actions" aria-label="Bagikan artikel">
                    <a
                        href="mailto:?subject=<?= $emailSubject ?>&body=<?= $emailBody ?>"
                        class="share-btn share-email"
                        aria-label="Bagikan lewat email"
                    >
                        <span class="share-icon" aria-hidden="true">
                            <iconify-icon icon="mdi:email-outline"></iconify-icon>
                        </span>
                        <span>Email</span>
                    </a>

                    <a
                        href="https://wa.me/?text=<?= $whatsappText ?>"
                        class="share-btn share-whatsapp"
                        target="_blank"
                        rel="noopener"
                        aria-label="Bagikan lewat WhatsApp"
                    >
                        <span class="share-icon" aria-hidden="true">
                            <iconify-icon icon="mdi:whatsapp"></iconify-icon>
                        </span>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </article>
    </div>
</section>

<section class="section soft-bg" id="artikel-terkait">
    <div class="container">
        <div class="section-between">
            <div class="section-heading">
                <span>Rekomendasi Bacaan</span>
                <h2>Artikel Terkait</h2>
                <p>Bacaan lain yang dapat membantu penulis memahami publikasi ilmiah secara lebih praktis.</p>
            </div>

            <a href="<?= site_url('artikel') ?>" class="btn-secondary">Lihat Semua</a>
        </div>

        <div class="article-grid">
            <?php foreach ($relatedArticles as $item): ?>
                <article class="article-card">
                    <a href="<?= site_url('artikel/' . $item['slug']) ?>" class="article-card-image">
                        <img
                            src="<?= esc($item['image']) ?>"
                            alt="<?= esc($item['image_alt']) ?>"
                            loading="lazy"
                        >
                    </a>

                    <div class="article-meta">
                        <span><?= esc($item['category']) ?></span>
                        <small><?= esc($item['read_time']) ?></small>
                    </div>

                    <h3><?= esc($item['title']) ?></h3>
                    <p><?= esc($item['summary']) ?></p>

                    <a href="<?= site_url('artikel/' . $item['slug']) ?>">Baca Artikel</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
