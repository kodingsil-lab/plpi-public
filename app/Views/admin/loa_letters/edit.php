<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" action="<?= site_url('dashboard/loa-letters/' . (int) $row['id'] . '/update') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:file-check-outline"></iconify-icon>Data LoA</h3>
            <div class="form-grid">
                <label>
                    <span>Nomor LoA</span>
                    <input value="<?= esc((string) ($row['loa_number'] ?? ''), 'attr') ?>" readonly>
                </label>
                <label>
                    <span>Status</span>
                    <select name="status" required>
                        <option value="published" <?= (string) old('status', $row['status'] ?? '') === 'published' ? 'selected' : '' ?>>LoA Terbit</option>
                        <option value="revoked" <?= (string) old('status', $row['status'] ?? '') === 'revoked' ? 'selected' : '' ?>>Dicabut</option>
                    </select>
                </label>
                <label>
                    <span>Jurnal</span>
                    <select name="journal_id" required>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <option value="<?= (int) $journal['id'] ?>" <?= (int) old('journal_id', $row['journal_id'] ?? 0) === (int) $journal['id'] ? 'selected' : '' ?>><?= esc((string) $journal['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Email Korespondensi</span>
                    <input type="email" name="corresponding_email" value="<?= esc((string) old('corresponding_email', $row['corresponding_email'] ?? ''), 'attr') ?>" required>
                </label>
                <label class="span-2">
                    <span>Judul Artikel</span>
                    <textarea name="title" rows="3" data-editor="plain" required><?= esc((string) old('title', $row['title'] ?? '')) ?></textarea>
                </label>
                <label class="span-2">
                    <span>URL Artikel</span>
                    <input name="article_url" value="<?= esc((string) old('article_url', $row['article_url'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Volume</span>
                    <input name="volume" value="<?= esc((string) old('volume', $row['volume'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Issue</span>
                    <input name="issue_number" value="<?= esc((string) old('issue_number', $row['issue_number'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Tahun Terbit</span>
                    <input name="published_year" value="<?= esc((string) old('published_year', $row['published_year'] ?? ''), 'attr') ?>">
                </label>
                <label>
                    <span>Alasan Dicabut</span>
                    <input name="revoked_reason" value="<?= esc((string) old('revoked_reason', $row['revoked_reason'] ?? ''), 'attr') ?>">
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:account-multiple-outline"></iconify-icon>Penulis</h3>
            <div class="form-grid">
                <label>
                    <span>Nama Penulis</span>
                    <textarea name="authors_text" rows="6" data-editor="plain" required><?= esc((string) old('authors_text', $row['authors_text'] ?? '')) ?></textarea>
                </label>
                <label>
                    <span>Afiliasi</span>
                    <textarea name="affiliations_text" rows="6" data-editor="plain"><?= esc((string) old('affiliations_text', $row['affiliations_text'] ?? '')) ?></textarea>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/loa-letters') ?>">Kembali</a>
            <?php if (! empty($row['public_token'])): ?>
                <a class="admin-btn secondary" href="<?= site_url('loa/v/' . (string) $row['public_token'] . '/preview') ?>" target="_blank" rel="noopener">Preview PDF</a>
                <a class="admin-btn secondary" href="<?= site_url('loa/v/' . (string) $row['public_token'] . '/download') ?>">Unduh PDF</a>
            <?php endif; ?>
            <button class="admin-btn secondary" type="submit" formaction="<?= site_url('dashboard/loa-letters/' . (int) $row['id'] . '/regenerate') ?>">Regenerate PDF</button>
            <button class="admin-btn primary" type="submit">Simpan</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
