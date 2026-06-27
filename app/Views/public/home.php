<?= $this->extend('public/layouts/main') ?>

<?= $this->section('content') ?>

<section class="home-hero">
    <div class="container home-hero-grid">
        <div class="home-hero-content">
            <span class="eyebrow">Pusat Layanan Publikasi Ilmiah</span>

            <h1>Ajukan, pantau, dan verifikasi LoA publikasi secara lebih mudah.</h1>

            <p>
                PLPI membantu penulis dan pengelola jurnal dalam proses pengajuan Letter of Acceptance,
                verifikasi dokumen, serta akses informasi jurnal dan literasi publikasi ilmiah.
            </p>

            <div class="home-hero-actions">
                <a href="<?= site_url('ajukan-loa') ?>" class="btn-primary">Ajukan LoA</a>
                <a href="<?= site_url('verifikasi-loa') ?>" class="btn-secondary">Verifikasi LoA</a>
                <a href="<?= site_url('artikel') ?>" class="btn-link">Baca Artikel Ilmiah</a>
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
            <div class="visual-card visual-card-main">
                <div class="visual-card-header">
                    <span>Dokumen LoA</span>
                    <strong>Disetujui</strong>
                </div>

                <div class="visual-doc">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span class="short"></span>
                </div>

                <div class="visual-signature">
                    <div></div>
                    <span>PLPI-00064</span>
                </div>
            </div>

            <div class="visual-floating-card card-top">
                <span>📄</span>
                <div>
                    <strong>52</strong>
                    <small>Permohonan</small>
                </div>
            </div>

            <div class="visual-floating-card card-bottom">
                <span>✅</span>
                <div>
                    <strong>48</strong>
                    <small>LoA Terbit</small>
                </div>
            </div>
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
            <span>Layanan Utama</span>
            <h2>Satu pintu untuk kebutuhan publikasi ilmiah</h2>
            <p>
                Layanan dirancang agar proses administrasi publikasi menjadi lebih ringkas,
                terdokumentasi, dan mudah diverifikasi.
            </p>
        </div>

        <div class="home-service-grid">
            <div class="home-service-card">
                <div class="home-service-icon">📄</div>
                <span>01</span>
                <h3>Pengajuan LoA</h3>
                <p>Penulis dapat mengajukan permohonan LoA dengan data naskah, jurnal, dan identitas penulis secara terstruktur.</p>
                <a href="<?= site_url('ajukan-loa') ?>">Mulai Pengajuan</a>
            </div>

            <div class="home-service-card featured">
                <div class="home-service-icon">🔍</div>
                <span>02</span>
                <h3>Verifikasi LoA</h3>
                <p>Pengguna dapat mengecek keaslian dan status LoA berdasarkan kode unik yang diterbitkan oleh sistem.</p>
                <a href="<?= site_url('verifikasi-loa') ?>">Verifikasi Sekarang</a>
            </div>

            <div class="home-service-card">
                <div class="home-service-icon">📚</div>
                <span>03</span>
                <h3>Informasi Jurnal</h3>
                <p>Profil jurnal ditampilkan agar penulis dapat melihat identitas, kategori, dan tautan jurnal yang tersedia.</p>
                <a href="#profil-jurnal">Lihat Jurnal</a>
            </div>
        </div>
    </div>
</section>

<section class="section home-process-section">
    <div class="container">
        <div class="section-heading center">
            <span>Alur Layanan</span>
            <h2>Proses pengajuan dibuat sederhana</h2>
            <p>Pengguna mengikuti empat tahapan utama sampai LoA dapat diterbitkan dan diverifikasi.</p>
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
                <h3>Verifikasi Publik</h3>
                <p>LoA dapat diverifikasi melalui halaman public.</p>
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

            <a href="<?= site_url('verifikasi-loa') ?>" class="btn-secondary">Cek LoA</a>
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
                <h2>Artikel Ilmiah</h2>
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
                        <span>Jurnal</span>
                        <strong><?= strtoupper(substr($journal['name'], 0, 1)) ?></strong>
                    </div>

                    <div class="journal-info-new">
                        <small><?= esc($journal['category']) ?></small>
                        <h3><?= esc($journal['name']) ?></h3>
                        <p><?= esc($journal['issn']) ?></p>
                        <a href="<?= esc($journal['url']) ?>">Lihat Jurnal</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
