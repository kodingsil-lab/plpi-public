<!doctype html>
<html lang="id">
<head>
    <?php
        helper('app_settings');
        $appSettings = plpi_app_settings();
        $assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
        $loginLogoPath = (string) ($appSettings['login_logo_path'] ?? $appSettings['public_logo_path'] ?? '');
        $loginLogoUrl = $loginLogoPath !== '' ? plpi_asset_url($loginLogoPath) . '?v=' . $assetVersion : '';
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Masuk Dashboard') ?> | PLPI</title>
    <?= plpi_favicon_tags($appSettings) ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('plpi/css/public.css?v=2026070103') ?>">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-brand-card" aria-label="PLPI">
            <a href="<?= site_url('/') ?>" class="auth-brand">
                <span class="brand-mark">
                    <?php if ($loginLogoUrl !== ''): ?>
                        <img src="<?= esc($loginLogoUrl, 'attr') ?>" alt="Logo PLPI">
                    <?php else: ?>
                        <span>PL</span>
                    <?php endif; ?>
                </span>

                <span>
                    <strong>PLPI</strong>
                    <small>Pusat Layanan Publikasi Ilmiah</small>
                </span>
            </a>
        </section>

        <section class="auth-card">
            <div class="auth-intro">
                <h1>Masuk ke akun Anda</h1>
                <p>Silakan masukkan detail akun untuk melanjutkan ke dashboard PLPI.</p>
            </div>

            <?php if (session()->getFlashdata('login_error')): ?>
                <div class="auth-alert">
                    <?= esc(session()->getFlashdata('login_error')) ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" action="<?= site_url('login') ?>" method="post">
                <?= csrf_field() ?>
                <div class="auth-field">
                    <label for="identity">Email atau username <span>*</span></label>
                    <div class="auth-input-wrap icon-user">
                        <input
                            type="text"
                            id="identity"
                            name="identity"
                            placeholder="Masukkan email atau username"
                            value="<?= old('identity') ?>"
                            autocomplete="username"
                            required
                        >
                    </div>
                </div>

                <div class="auth-field">
                    <label for="password">Kata sandi <span>*</span></label>
                    <div class="auth-input-wrap icon-lock">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="password-toggle" aria-label="Tampilkan kata sandi" data-password-toggle>
                            <span></span>
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat saya</span>
                    </label>

                    <a href="#">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="auth-submit">Masuk</button>
            </form>
        </section>
    </main>

    <script src="<?= base_url('plpi/js/public.js') ?>"></script>
    <?= plpi_statcounter_code($appSettings) ?>
</body>
</html>
