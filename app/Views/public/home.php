<?= $this->extend('public/layouts/main') ?>

<?= $this->section('content') ?>

<section class="home-hero">
    <div class="container home-hero-grid">
        <div class="home-hero-content">
            <span class="eyebrow">Pusat Layanan Publikasi Ilmiah</span>

            <h1>Pusat Layanan Pendukung Publikasi Ilmiah</h1>

            <p>
                PLPI merupakan portal informasi terintegrasi untuk membantu para penulis dalam proses 
                pengajuan Letter of Acceptance jurnal, pemantauan dokumen, serta akses informasi jurnal dan literasi publikasi ilmiah.
            </p>

            <div class="home-hero-actions">
                <a href="<?= site_url('ajukan-loa') ?>" class="btn-primary">Ajukan LoA</a>
                <a href="<?= site_url('verifikasi-loa') ?>" class="btn-secondary">Verifikasi LoA</a>
                <a href="<?= site_url('artikel') ?>" class="btn-link">Baca Artikel Edukatif</a>
            </div>

            <div class="home-trust-row">
                <div>
                    <strong>Transparan</strong>
                    <span>Status pengajuan dapat dipantau.</span>
                </div>
                <div>
                    <strong>Terintegrasi</strong>
                    <span>Data LoA, jurnal, dan artikel dalam satu halaman.</span>
                </div>
            </div>
        </div>

        <div class="home-hero-visual">
            <img
                src="<?= base_url('plpi/images/hero1.png') ?>"
                alt="Ilustrasi layanan publikasi ilmiah PLPI"
                class="home-hero-image"
            >

        </div>
    </div>
</section>

<section class="home-stats-section">
    <div class="container">
        <div class="home-stats-grid">
            <?php foreach ($stats as $index => $stat): ?>
                <div class="home-stat-card">
                    <div class="home-stat-icon">
                        <?php if ($index === 0): ?>
                            📥
                        <?php elseif ($index === 1): ?>
                            ✅
                        <?php elseif ($index === 2): ?>
                            ⏳
                        <?php else: ?>
                            🔄
                        <?php endif; ?>
                    </div>

                    <div>
                        <small><?= esc($stat['label']) ?></small>
                        <strong class="count-up" data-count="<?= esc($stat['value']) ?>">
                            <?= esc($stat['value']) ?>
                        </strong>
                        <span><?= esc($stat['note']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading center">
            <span>PORTAL LAYANAN PUBLIKASI</span>
            <h2>Ajukan, Verifikasi, dan Telusuri Informasi Jurnal dalam Satu Sistem</h2>
            <p>
                Portal ini dirancang untuk membantu penulis mengurus permohonan LoA, memverifikasi keaslian dokumen LoA,
                memperoleh informasi jurnal,serta mengakses artikel-artikel edukatif seputar publikasi ilmiah.
            </p>
        </div>

        <div class="home-service-grid">
            <div class="home-service-card">
                <div class="home-service-icon">📄</div>
                <span>01</span>
                <h3>Pengajuan LoA</h3>
                <p>Ajukan permohonan Letter of Acceptance berdasarkan data artikel, identitas penulis, dan jurnal tujuan secara terstruktur.</p>
                <a href="<?= site_url('ajukan-loa') ?>">Ajukan LoA</a>
            </div>

            <div class="home-service-card featured">
                <div class="home-service-icon">🔍</div>
                <span>02</span>
                <h3>Verifikasi LoA</h3>
                <p>Periksa keaslian dan status LoA menggunakan nomor dokumen yang diterbitkan oleh sistem.</p>
                <a href="<?= site_url('verifikasi-loa') ?>">Verifikasi LoA</a>
            </div>

            <div class="home-service-card">
                <div class="home-service-icon">📚</div>
                <span>03</span>
                <h3>Informasi Jurnal</h3>
                <p>Temukan profil jurnal, fokus dan ruang lingkup, jadwal terbit, serta tautan OJS resmi yang tersedia.</p>
                <a href="#profil-jurnal">Lihat Informasi Jurnal</a>
            </div>

            <div class="home-service-card">
                <div class="home-service-icon">&#128240;</div>
                <span>04</span>
                <h3>Artikel Edukatif</h3>
                <p>Akses artikel edukatif seputar publikasi ilmiah, pengelolaan naskah, jurnal, dan literasi akademik.</p>
                <a href="<?= site_url('artikel') ?>">Baca Artikel Edukatif</a>
            </div>
        </div>
    </div>
</section>

<section class="section home-process-section">
    <div class="container">
        <div class="section-heading center">
            <span>Alur Layanan</span>
            <h2>Proses pengajuan dibuat sederhana</h2>
            <p>Pengguna mengikuti empat tahapan utama sampai LoA dapat diterbitkan oleh admin.</p>
        </div>

        <div class="process-grid">
            <div class="process-item">
                <div>1</div>
                <h3>Isi Formulir</h3>
                <p>Penulis mengisi data naskah dan identitas pengajuan.</p>
            </div>

            <div class="process-item">
                <div>2</div>
                <h3>Validasi Admin</h3>
                <p>Admin memeriksa kelengkapan dan kesesuaian data.</p>
            </div>

            <div class="process-item">
                <div>3</div>
                <h3>LoA Diterbitkan</h3>
                <p>Sistem menerbitkan LoA dengan kode unik.</p>
            </div>

            <div class="process-item">
                <div>4</div>
                <h3>Arsip Data</h3>
                <p>Data permohonan dan LoA terbit tersimpan di dashboard admin.</p>
            </div>
        </div>
    </div>
</section>

<section class="section soft-bg" id="permohonan-terbaru">
    <div class="container">
        <div class="section-between">
            <div class="section-heading">
                <span>Data Terbaru</span>
                <h2>Permohonan Terbaru</h2>
                <p>Daftar terbaru pengajuan LoA yang masuk ke sistem.</p>
            </div>

            <a href="<?= site_url('ajukan-loa') ?>" class="btn-secondary">Ajukan LoA</a>
        </div>

        <div class="request-list">
            <?php foreach ($latestRequests as $index => $request): ?>
                <div class="request-item">
                    <div class="request-number">
                        <?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>
                    </div>

                    <div class="request-content">
                        <strong><?= esc($request['code']) ?></strong>
                        <h3><?= esc($request['title']) ?></h3>
                        <small><?= esc($request['date']) ?></small>
                    </div>

                    <div class="request-status">
                        <span class="status-badge <?= strtolower($request['status']) === 'disetujui' ? 'success' : 'process' ?>">
                            <?= esc($request['status']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-between">
            <div class="section-heading">
                <span>Literasi Publikasi</span>
                <h2>Artikel Edukatif</h2>
                <p>
                    Artikel edukatif untuk membantu penulis memahami penulisan,
                    struktur naskah, etika publikasi, dan proses submit jurnal.
                </p>
            </div>

            <a href="<?= site_url('artikel') ?>" class="btn-secondary">Lihat Semua</a>
        </div>

        <div class="home-article-grid">
            <?php foreach ($articles as $article): ?>
                <article class="home-article-card">
                    <a href="<?= site_url('artikel/' . $article['slug']) ?>" class="home-article-image">
                        <img
                            src="<?= esc($article['image']) ?>"
                            alt="<?= esc($article['image_alt']) ?>"
                            loading="lazy"
                        >
                    </a>

                    <div class="home-article-top">
                        <div class="home-article-icon">
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

                        <span><?= esc($article['category']) ?></span>
                    </div>

                    <h3><?= esc($article['title']) ?></h3>
                    <p><?= esc($article['summary']) ?></p>

                    <div class="home-article-bottom">
                        <small><?= esc($article['read_time']) ?></small>
                        <a href="<?= site_url('artikel/' . $article['slug']) ?>">Baca Artikel</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section home-journal-section" id="profil-jurnal">
    <div class="container">
        <div class="section-heading center">
            <span>Profil Jurnal</span>
            <h2>Jurnal yang Tersedia</h2>
            <p>
                Informasi jurnal ditampilkan secara ringkas agar penulis dapat mengenali
                identitas dan ruang lingkup jurnal tujuan.
            </p>
        </div>

        <div class="home-journal-grid">
            <?php foreach ($journals as $index => $journal): ?>
                <div class="home-journal-card">
                    <div class="journal-cover-new">
                        <?php if (! empty($journal['logo_url'])): ?>
                            <img src="<?= esc((string) $journal['logo_url'], 'attr') ?>" alt="Logo <?= esc($journal['name']) ?>" loading="lazy">
                        <?php else: ?>
                            <span>Jurnal</span>
                            <strong><?= strtoupper(substr($journal['name'], 0, 1)) ?></strong>
                        <?php endif; ?>
                    </div>

                    <div class="journal-info-new">
                        <small><?= esc($journal['category']) ?></small>
                        <h3><?= esc($journal['name']) ?></h3>
                        <p><?= esc($journal['issn']) ?></p>
                        <div class="journal-action-row">
                            <a href="<?= esc($journal['url']) ?>" target="_blank" rel="noopener noreferrer">Lihat Jurnal</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
