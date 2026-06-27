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
            <a href="<?= site_url('artikel') ?>">Artikel Ilmiah</a>
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
                <?php foreach ($article['content'] as $index => $paragraph): ?>
                    <?php if ($index === 0): ?>
                        <p class="dropcap"><?= esc($paragraph) ?></p>
                    <?php else: ?>
                        <p><?= esc($paragraph) ?></p>
                    <?php endif; ?>
                <?php endforeach; ?>

                <blockquote id="penutup">
                    Artikel ilmiah yang baik tidak hanya benar secara struktur, tetapi juga jelas, etis, mudah dipahami, dan relevan dengan pembaca sasaran.
                </blockquote>
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
                            <svg viewBox="0 0 24 24" focusable="false">
                                <path d="M4 6h16v12H4z"></path>
                                <path d="m4 7 8 6 8-6"></path>
                            </svg>
                        </span>
                        Email
                    </a>

                    <a
                        href="https://wa.me/?text=<?= $whatsappText ?>"
                        class="share-btn share-whatsapp"
                        target="_blank"
                        rel="noopener"
                        aria-label="Bagikan lewat WhatsApp"
                    >
                        <span class="share-icon" aria-hidden="true">
                            <svg viewBox="0 0 32 32" focusable="false" class="whatsapp-icon">
                                <path d="M16.01 4.01a11.9 11.9 0 0 0-10.2 18.04L4 28l6.12-1.74A11.94 11.94 0 1 0 16.01 4.01Zm0 21.82c-1.92 0-3.72-.55-5.24-1.5l-.38-.23-3.63 1.03 1.05-3.52-.25-.4a9.87 9.87 0 1 1 8.45 4.62Zm5.42-7.4c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.89 1.22 3.09c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.08 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.08-.13-.27-.2-.57-.35Z"></path>
                            </svg>
                        </span>
                        WhatsApp
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
