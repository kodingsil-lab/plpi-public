<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>
<?php
$journal = is_array($journal ?? null) ? $journal : [];
$intro = trim((string) ($journal['recruitment_intro'] ?? ''));
$journalLogoUrl = ! empty($journal['logo_path']) && ! empty($journal['id'])
    ? site_url('journal-logo/' . (int) $journal['id'] . '?v=' . rawurlencode((string) ($journal['updated_at'] ?? '')))
    : '';
$journalIssn = array_values(array_filter([
    trim((string) ($journal['e_issn'] ?? '')) !== '' ? 'E-ISSN: ' . trim((string) $journal['e_issn']) : '',
    trim((string) ($journal['p_issn'] ?? '')) !== '' ? 'P-ISSN: ' . trim((string) $journal['p_issn']) : '',
    trim((string) ($journal['issn'] ?? '')) !== '' ? 'ISSN: ' . trim((string) $journal['issn']) : '',
]));
?>

<section class="section loa-request-section">
    <div class="container">
        <div class="loa-page-title">
            <h1>Rekrutmen Editor & Reviewer</h1>
            <p><?= esc((string) ($journal['name'] ?? 'Jurnal')) ?></p>
        </div>

        <?php if (session('error')): ?>
            <div class="public-alert error"><?= esc((string) session('error')) ?></div>
        <?php endif; ?>
        <?php if (session('success')): ?>
            <div class="public-alert success"><?= esc((string) session('success')) ?></div>
        <?php endif; ?>

        <form class="loa-public-form" method="post" action="<?= site_url('rekrutmen-editor-reviewer/jurnal/' . (string) ($journal['slug'] ?? '')) ?>">
            <div class="loa-form-panel">
                <div class="loa-form-heading">
                    <span>01</span>
                    <div>
                        <h2>Pengantar</h2>
                        <p>Informasi jurnal dan rekrutmen editor atau reviewer.</p>
                    </div>
                </div>

                <div class="recruitment-journal-summary">
                    <div class="recruitment-journal-cover">
                        <?php if ($journalLogoUrl !== ''): ?>
                            <img src="<?= esc($journalLogoUrl, 'attr') ?>" alt="Cover <?= esc((string) ($journal['name'] ?? 'jurnal'), 'attr') ?>">
                        <?php else: ?>
                            <span>Jurnal</span>
                            <strong><?= esc(strtoupper(substr((string) ($journal['name'] ?? 'J'), 0, 1))) ?></strong>
                        <?php endif; ?>
                    </div>
                    <div class="recruitment-journal-meta">
                        <small>Jurnal tujuan</small>
                        <h2><?= esc((string) ($journal['name'] ?? 'Jurnal')) ?></h2>
                        <?php if ($journalIssn !== []): ?>
                            <div class="recruitment-journal-issn">
                                <?php foreach ($journalIssn as $issn): ?>
                                    <span><?= esc($issn) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>ISSN belum tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="recruitment-intro-box">
                    <?php $introHtml = trim((string) ($journal['recruitment_intro_html'] ?? $intro)); ?>
                    <?php if ($introHtml !== ''): ?>
                        <?= $introHtml ?>
                    <?php else: ?>
                        <p>Form ini ditujukan untuk dosen, peneliti, dan praktisi yang berminat bergabung sebagai Editor atau Reviewer. Data yang dikirim akan digunakan oleh pengelola jurnal untuk meninjau kesesuaian bidang keahlian dan rekam jejak publikasi.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="loa-form-panel">
                <div class="loa-form-heading">
                    <span>02</span>
                    <div>
                        <h2>Form Pendaftaran</h2>
                        <p>Lengkapi data diri untuk bergabung sebagai editor atau reviewer pada jurnal ini.</p>
                    </div>
                </div>

                <div class="loa-form-grid">
                    <label class="span-2">
                        <span>Jurnal</span>
                        <input value="<?= esc((string) ($journal['name'] ?? '-'), 'attr') ?>" readonly>
                    </label>

                    <label>
                        <span>Nama Lengkap dan Gelar</span>
                        <input name="full_name" value="<?= esc((string) old('full_name'), 'attr') ?>" required placeholder="Contoh: Dr. Nama Lengkap, M.Pd.">
                    </label>

                    <label>
                        <span>Asal Institusi</span>
                        <input name="institution" value="<?= esc((string) old('institution'), 'attr') ?>" required placeholder="Nama institusi/perguruan tinggi">
                    </label>

                    <label>
                        <span class="field-label">
                            Bergabung Sebagai
                            <span class="field-tooltip" tabindex="0" aria-label="Pilih Editor jika ingin membantu pengelolaan naskah, atau Reviewer jika ingin menelaah artikel sesuai bidang keahlian.">?</span>
                        </span>
                        <select name="role_requested" required>
                            <option value="">Pilih peran</option>
                            <option value="Reviewer" <?= old('role_requested') === 'Reviewer' ? 'selected' : '' ?>>Reviewer</option>
                            <option value="Editor" <?= old('role_requested') === 'Editor' ? 'selected' : '' ?>>Editor</option>
                        </select>
                    </label>

                    <label>
                        <span class="field-label">
                            Alamat Email
                            <span class="field-tooltip" tabindex="0" aria-label="Gunakan email aktif karena informasi seleksi, SK, dan sertifikat akan dikirim melalui email ini.">?</span>
                        </span>
                        <input type="email" name="email" value="<?= esc((string) old('email'), 'attr') ?>" required placeholder="contoh@email.com">
                        <small>Digunakan untuk pengiriman SK dan sertifikat.</small>
                    </label>

                    <label>
                        <span class="field-label">
                            Nomor Handphone/WA
                            <span class="field-tooltip" tabindex="0" aria-label="Masukkan nomor WhatsApp aktif, boleh diawali 08 atau kode negara +62.">?</span>
                        </span>
                        <input name="phone" value="<?= esc((string) old('phone'), 'attr') ?>" required placeholder="0812xxxxxx / +62812xxxxxx">
                    </label>

                    <label>
                        <span class="field-label">
                            Google Scholar ID
                            <span class="field-tooltip" tabindex="0" aria-label="Tempel tautan profil Google Scholar, biasanya berisi scholar.google.com/citations?user=kodeprofil.">?</span>
                        </span>
                        <input name="google_scholar_id" value="<?= esc((string) old('google_scholar_id'), 'attr') ?>" required placeholder="https://scholar.google.com/citations?user=...">
                    </label>

                    <label>
                        <span class="field-label">
                            SINTA ID
                            <span class="field-tooltip" tabindex="0" aria-label="Tempel tautan profil SINTA, contohnya sinta.kemdikbud.go.id/authors/profile/nomorid.">?</span>
                        </span>
                        <input name="sinta_id" value="<?= esc((string) old('sinta_id'), 'attr') ?>" required placeholder="https://sinta.kemdikbud.go.id/authors/profile/...">
                    </label>

                    <label>
                        <span class="field-label">
                            Scopus ID
                            <span class="field-tooltip" tabindex="0" aria-label="Opsional. Isi tautan profil Scopus atau Author ID jika tersedia.">?</span>
                        </span>
                        <input name="scopus_id" value="<?= esc((string) old('scopus_id'), 'attr') ?>" placeholder="https://www.scopus.com/authid/detail.uri?authorId=...">
                    </label>

                    <label>
                        <span class="field-label">
                            ORCID ID
                            <span class="field-tooltip" tabindex="0" aria-label="Opsional. Isi tautan ORCID dengan format orcid.org/0000-0000-0000-0000 jika sudah punya.">?</span>
                        </span>
                        <input name="orcid_id" value="<?= esc((string) old('orcid_id'), 'attr') ?>" placeholder="https://orcid.org/0000-0000-0000-0000">
                    </label>

                    <label class="span-2">
                        <span class="field-label">
                            Bidang Keahlian
                            <span class="field-tooltip" tabindex="0" aria-label="Tulis beberapa bidang yang paling sesuai dengan kompetensi atau publikasi Anda, pisahkan dengan koma.">?</span>
                        </span>
                        <textarea name="expertise" rows="5" required placeholder="Pendidikan Bahasa Indonesia, Pendidikan Matematika, Pendidikan IPA, dll."><?= esc((string) old('expertise')) ?></textarea>
                    </label>
                </div>
            </div>

            <div class="loa-form-actions">
                <a href="<?= site_url('/#profil-jurnal') ?>" class="btn-secondary">Kembali</a>
                <button type="submit" class="btn-primary">Kirim Pendaftaran</button>
            </div>
        </form>
    </div>
</section>

<?= $this->endSection() ?>
