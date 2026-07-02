<!doctype html>
<html lang="id">
<head>
    <?php
        helper('app_settings');
        $appSettings = plpi_app_settings();
        $assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
        $faviconPath = (string) ($appSettings['favicon_path'] ?? '');
        $faviconUrl = $faviconPath !== '' ? plpi_asset_url($faviconPath) . '?v=' . $assetVersion : '';
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Pusat Layanan Publikasi Ilmiah') ?></title>
    <?php if ($faviconUrl !== ''): ?>
        <link rel="icon" href="<?= esc($faviconUrl, 'attr') ?>">
        <link rel="shortcut icon" href="<?= esc($faviconUrl, 'attr') ?>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Public CSS -->
    <link rel="stylesheet" href="<?= base_url('plpi/css/public.css?v=2026070205') ?>">
</head>
<body>

<?= $this->include('public/partials/navbar') ?>

<div class="reading-progress">
    <span id="readingProgressBar"></span>
</div>

<main>
    <?= $this->renderSection('content') ?>
</main>

<?= $this->include('public/partials/footer') ?>

    <script src="<?= base_url('plpi/js/public.js') ?>"></script>
    <script>
        (function () {
            const tokenName = <?= json_encode(csrf_token()) ?>;
            const tokenHash = <?= json_encode(csrf_hash()) ?>;

            function ensureToken(form) {
                if (!form || String(form.method || '').toLowerCase() !== 'post') return;
                if (form.querySelector('input[name="' + tokenName + '"]')) return;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = tokenName;
                input.value = tokenHash;
                form.prepend(input);
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form').forEach(ensureToken);
            });

            document.addEventListener('submit', function (event) {
                ensureToken(event.target);
            }, true);
        })();
    </script>
</body>
</html>
