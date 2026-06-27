<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Pusat Layanan Publikasi Ilmiah') ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Public CSS -->
    <link rel="stylesheet" href="<?= base_url('plpi/css/public.css') ?>">
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
</body>
</html>
