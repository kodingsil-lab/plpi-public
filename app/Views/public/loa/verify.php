<?= $this->extend('public/layouts/main') ?>
<?= $this->section('content') ?>

<section class="section loa-verify-section">
    <div class="container">
        <div class="loa-page-title">
            <h1>Verifikasi Letter of Acceptance Anda</h1>
        </div>

        <?php if (session('error')): ?>
            <div class="public-alert error"><?= esc((string) session('error')) ?></div>
        <?php endif; ?>

        <form class="loa-verify-card" method="post" action="<?= site_url('verifikasi-loa') ?>">
            <div class="loa-form-heading">
                <span>✓</span>
                <div>
                    <h2>Verifikasi LoA</h2>
                    <p>Gunakan nomor lengkap, misalnya <strong>001/LOA/ABDIUNISAP/UPT-UNISAP/V/2026</strong>.</p>
                </div>
            </div>

            <div class="loa-verify-body">
                <label>
                    <span>Nomor LoA</span>
                    <input type="text" name="number" value="<?= esc((string) old('number'), 'attr') ?>" required placeholder="Masukkan nomor LoA">
                </label>

                <div class="loa-form-actions">
                    <a href="<?= site_url('/') ?>" class="btn-secondary">Kembali</a>
                    <button type="submit" class="btn-primary">Verifikasi</button>
                </div>
            </div>
        </form>
    </div>
</section>

<?= $this->endSection() ?>
