<!doctype html>
<html lang="id">
<head>
    <?php
        helper('app_settings');
        $appSettings = plpi_app_settings();
        $assetVersion = rawurlencode((string) ($appSettings['updated_at'] ?? time()));
        $adminLogoPath = (string) ($appSettings['header_logo_path'] ?? '');
        $adminLogoUrl = $adminLogoPath !== '' ? plpi_asset_url($adminLogoPath) . '?v=' . $assetVersion : '';
        $faviconPath = (string) ($appSettings['favicon_path'] ?? '');
        $faviconUrl = $faviconPath !== '' ? plpi_asset_url($faviconPath) . '?v=' . $assetVersion : '';
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dashboard Admin') ?> | PLPI</title>
    <?php if ($faviconUrl !== ''): ?>
        <link rel="icon" href="<?= esc($faviconUrl, 'attr') ?>">
        <link rel="shortcut icon" href="<?= esc($faviconUrl, 'attr') ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('plpi/css/admin.css?v=2026070301') ?>">
    <script defer src="https://code.iconify.design/iconify-icon/3.0.0/iconify-icon.min.js"></script>
</head>
<body>
    <?php
        $activeMenu = $activeMenu ?? 'dashboard';
        $initials = strtoupper(substr((string) ($adminName ?? 'SP'), 0, 1) . substr((string) ($adminRole ?? 'P'), 0, 1));
        $titleIcons = [
            'dashboard' => ['icon' => 'mdi:view-dashboard-outline', 'tone' => 'teal'],
            'loa_requests' => ['icon' => 'mdi:send-outline', 'tone' => 'blue'],
            'loa_letters' => ['icon' => 'mdi:file-check-outline', 'tone' => 'green'],
            'loa_notifications' => ['icon' => 'mdi:bell-outline', 'tone' => 'amber'],
            'journals' => ['icon' => 'mdi:book-open-page-variant-outline', 'tone' => 'blue'],
            'publishers' => ['icon' => 'mdi:office-building-outline', 'tone' => 'teal'],
            'articles' => ['icon' => 'mdi:newspaper-variant-outline', 'tone' => 'amber'],
            'article_categories' => ['icon' => 'mdi:shape-outline', 'tone' => 'purple'],
            'article_create' => ['icon' => 'mdi:pencil-plus-outline', 'tone' => 'green'],
            'invoice_jurnal' => ['icon' => 'mdi:receipt-text-outline', 'tone' => 'purple'],
            'editor_reviewer' => ['icon' => 'mdi:account-group-outline', 'tone' => 'green'],
            'message_whatsapp' => ['icon' => 'mdi:whatsapp', 'tone' => 'green'],
            'message_email' => ['icon' => 'mdi:email-send-outline', 'tone' => 'blue'],
            'message_templates' => ['icon' => 'mdi:message-text-outline', 'tone' => 'blue'],
            'settings_application' => ['icon' => 'mdi:cog-outline', 'tone' => 'slate'],
            'users' => ['icon' => 'mdi:account-cog-outline', 'tone' => 'purple'],
        ];
        $titleIcon = $titleIcons[$activeMenu] ?? ['icon' => 'mdi:file-outline', 'tone' => 'teal'];
    ?>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="<?= site_url('/') ?>" class="admin-brand">
                <span>
                    <?php if ($adminLogoUrl !== ''): ?>
                        <img src="<?= esc($adminLogoUrl, 'attr') ?>" alt="Logo PLPI">
                    <?php else: ?>
                        PL
                    <?php endif; ?>
                </span>
                <div>
                    <strong>PLPI</strong>
                    <small>Pusat Layanan Publikasi Ilmiah</small>
                </div>
            </a>

            <nav class="admin-nav" aria-label="Navigasi admin">
                <a href="<?= site_url('dashboard') ?>" class="nav-item <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon tone-teal"><iconify-icon icon="mdi:view-dashboard-outline"></iconify-icon></span>
                    Dashboard
                </a>

                <p>Manajemen Layanan</p>

                <div class="nav-group <?= in_array($activeMenu, ['loa_requests', 'loa_letters', 'loa_notifications'], true) ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-blue"><iconify-icon icon="mdi:file-document-edit-outline"></iconify-icon></span>
                        <span>Manajemen LoA</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/loa-requests') ?>" class="<?= $activeMenu === 'loa_requests' ? 'active' : '' ?>"><iconify-icon icon="mdi:send-outline"></iconify-icon>Permohonan LoA</a>
                        <a href="<?= site_url('dashboard/loa-letters') ?>" class="<?= $activeMenu === 'loa_letters' ? 'active' : '' ?>"><iconify-icon icon="mdi:file-check-outline"></iconify-icon>LoA Terbit</a>
                        <a href="<?= site_url('dashboard/notifikasi') ?>" class="<?= $activeMenu === 'loa_notifications' ? 'active' : '' ?>"><iconify-icon icon="mdi:bell-outline"></iconify-icon>Notifikasi</a>
                    </div>
                </div>

                <div class="nav-group <?= in_array($activeMenu, ['journals', 'publishers'], true) ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-cyan"><iconify-icon icon="mdi:book-open-page-variant-outline"></iconify-icon></span>
                        <span>Manajemen Jurnal</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/journals') ?>" class="<?= $activeMenu === 'journals' ? 'active' : '' ?>"><iconify-icon icon="mdi:database-outline"></iconify-icon>Data Jurnal</a>
                        <a href="<?= site_url('dashboard/publishers') ?>" class="<?= $activeMenu === 'publishers' ? 'active' : '' ?>"><iconify-icon icon="mdi:office-building-outline"></iconify-icon>Publisher</a>
                    </div>
                </div>

                <div class="nav-group <?= in_array($activeMenu, ['articles', 'article_categories', 'article_create'], true) ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-amber"><iconify-icon icon="mdi:newspaper-variant-outline"></iconify-icon></span>
                        <span>Artikel Edukatif</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/artikel-edukatif') ?>" class="<?= $activeMenu === 'articles' ? 'active' : '' ?>"><iconify-icon icon="mdi:format-list-bulleted-square"></iconify-icon>Semua Artikel</a>
                        <a href="<?= site_url('dashboard/artikel-edukatif/kategori') ?>" class="<?= $activeMenu === 'article_categories' ? 'active' : '' ?>"><iconify-icon icon="mdi:shape-outline"></iconify-icon>Kategori Artikel</a>
                        <a href="<?= site_url('dashboard/artikel-edukatif/create') ?>" class="<?= $activeMenu === 'article_create' ? 'active' : '' ?>"><iconify-icon icon="mdi:pencil-plus-outline"></iconify-icon>Tulis Artikel</a>
                    </div>
                </div>

                <div class="nav-group">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-purple"><iconify-icon icon="mdi:receipt-text-outline"></iconify-icon></span>
                        <span>Manajemen Invoice</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/invoice-jurnal') ?>" class="<?= $activeMenu === 'invoice_jurnal' ? 'active' : '' ?>"><iconify-icon icon="mdi:file-table-outline"></iconify-icon>Invoice Jurnal</a>
                    </div>
                </div>

                <div class="nav-group <?= $activeMenu === 'editor_reviewer' ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-green"><iconify-icon icon="mdi:account-group-outline"></iconify-icon></span>
                        <span>Manajemen Rekrutmen</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/rekrutmen-editor-reviewer') ?>" class="<?= $activeMenu === 'editor_reviewer' ? 'active' : '' ?>"><iconify-icon icon="mdi:account-edit-outline"></iconify-icon>Editor & Reviewer</a>
                    </div>
                </div>

                <div class="nav-group <?= in_array($activeMenu, ['message_whatsapp', 'message_email', 'message_templates'], true) ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-whatsapp"><iconify-icon icon="mdi:message-processing-outline"></iconify-icon></span>
                        <span>Manajemen Pesan</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/messages/whatsapp/send') ?>" class="<?= $activeMenu === 'message_whatsapp' ? 'active' : '' ?>"><iconify-icon icon="mdi:whatsapp"></iconify-icon>Kirim Pesan WhatsApp</a>
                        <a href="<?= site_url('dashboard/messages/email/send') ?>" class="<?= $activeMenu === 'message_email' ? 'active' : '' ?>"><iconify-icon icon="mdi:email-send-outline"></iconify-icon>Kirim Pesan Email</a>
                        <a href="<?= site_url('dashboard/messages/templates') ?>" class="<?= $activeMenu === 'message_templates' ? 'active' : '' ?>"><iconify-icon icon="mdi:message-text-outline"></iconify-icon>Template Pesan</a>
                    </div>
                </div>

                <p>Pengaturan</p>

                <div class="nav-group <?= in_array($activeMenu, ['settings_application', 'users'], true) ? 'is-open' : '' ?>">
                    <button type="button" class="nav-parent">
                        <span class="nav-icon tone-slate"><iconify-icon icon="mdi:cog-outline"></iconify-icon></span>
                        <span>Pengaturan</span>
                    </button>
                    <div class="nav-submenu">
                        <a href="<?= site_url('dashboard/settings/application') ?>" class="<?= $activeMenu === 'settings_application' ? 'active' : '' ?>"><iconify-icon icon="mdi:tune-variant"></iconify-icon>Aplikasi</a>
                        <a href="<?= site_url('dashboard/users') ?>" class="<?= $activeMenu === 'users' ? 'active' : '' ?>"><iconify-icon icon="mdi:account-cog-outline"></iconify-icon>Pengguna</a>
                    </div>
                </div>

                <a href="<?= site_url('logout') ?>" class="nav-item logout">
                    <span class="nav-icon tone-red"><iconify-icon icon="mdi:logout-variant"></iconify-icon></span>
                    Logout
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <div class="page-title-wrap">
                    <i class="page-title-icon tone-<?= esc($titleIcon['tone'], 'attr') ?>">
                        <iconify-icon icon="<?= esc($titleIcon['icon'], 'attr') ?>"></iconify-icon>
                    </i>
                    <div>
                        <span><?= esc($eyebrow ?? 'Dashboard') ?></span>
                        <h1><?= esc($pageTitle ?? 'Panel Admin PLPI') ?></h1>
                    </div>
                </div>

                <div class="admin-user">
                    <div>
                        <strong><?= esc($adminName ?? 'Superadmin PLPI') ?></strong>
                        <small><?= esc(ucfirst((string) ($adminRole ?? 'superadmin'))) ?></small>
                    </div>
                    <span><?= esc($initials) ?></span>
                </div>
            </header>

            <?php if (session('success')): ?>
                <div class="admin-alert success"><?= esc((string) session('success')) ?></div>
            <?php endif; ?>
            <?php if (session('error')): ?>
                <div class="admin-alert error"><?= esc((string) session('error')) ?></div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <script defer src="<?= base_url('plpi/js/admin-editor.js?v=2026070202') ?>"></script>
    <script defer src="<?= base_url('plpi/js/admin-table.js?v=2026070201') ?>"></script>
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

        document.querySelectorAll('.nav-parent').forEach(function (button) {
            button.addEventListener('click', function () {
                const group = button.closest('.nav-group');
                if (!group) return;
                document.querySelectorAll('.nav-group.is-open').forEach(function (openGroup) {
                    if (openGroup !== group) openGroup.classList.remove('is-open');
                });
                group.classList.toggle('is-open');
            });
        });
    </script>
</body>
</html>
