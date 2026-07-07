<?php

if (! function_exists('admin_table')) {
    /**
     * @param array{
     *   id:string,
     *   columns:array<int,array<string,mixed>>,
     *   rows:array<int,array<string,mixed>>,
     *   bulk?:array<string,mixed>,
     *   search?:bool,
     *   empty?:string
     * } $config
     */
    function admin_table(array $config): string
    {
        $id = (string) ($config['id'] ?? 'admin-table');
        $columns = $config['columns'] ?? [];
        $rows = $config['rows'] ?? [];
        $bulk = $config['bulk'] ?? null;
        $showSearch = (bool) ($config['search'] ?? true);
        $empty = (string) ($config['empty'] ?? 'Belum ada data.');
        $title = (string) ($config['title'] ?? admin_table_title($id));
        $icon = (string) ($config['icon'] ?? admin_table_icon($id));
        $columnCount = count($columns) + ($bulk ? 1 : 0);

        ob_start();
        ?>
        <div class="table-helper" data-table-helper>
            <?php if (! $showSearch): ?>
                <div class="table-section-header">
                    <h2><iconify-icon icon="<?= esc($icon, 'attr') ?>"></iconify-icon><?= esc($title) ?></h2>
                    <div class="table-helper-meta">
                        <span data-table-count><?= count($rows) ?> data</span>
                        <?php if ($bulk): ?>
                            <form class="bulk-delete-form" method="post" action="<?= esc((string) ($bulk['action'] ?? '#'), 'attr') ?>" data-bulk-form data-confirm="<?= esc((string) ($bulk['confirm'] ?? 'Lanjutkan aksi massal?'), 'attr') ?>">
                                <div data-bulk-inputs></div>
                                <button class="admin-btn danger bulk-action-btn" type="submit" data-bulk-submit disabled>
                                    <span data-bulk-label>Pilih data</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="table-helper-toolbar <?= ! $showSearch ? 'no-table-search' : '' ?>">
                <?php if ($showSearch): ?>
                    <label class="table-search">
                        <iconify-icon icon="mdi:magnify"></iconify-icon>
                        <input type="search" data-table-search placeholder="Cari data...">
                    </label>
                <?php endif; ?>
                <?php if ($showSearch): ?>
                    <div class="table-helper-meta">
                        <span data-table-count><?= count($rows) ?> data</span>
                        <?php if ($bulk): ?>
                            <form class="bulk-delete-form" method="post" action="<?= esc((string) ($bulk['action'] ?? '#'), 'attr') ?>" data-bulk-form data-confirm="<?= esc((string) ($bulk['confirm'] ?? 'Lanjutkan aksi massal?'), 'attr') ?>">
                                <div data-bulk-inputs></div>
                                <button class="admin-btn danger bulk-action-btn" type="submit" data-bulk-submit disabled>
                                    <span data-bulk-label>Pilih data</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="admin-data-table-wrap">
                <table id="<?= esc($id, 'attr') ?>" class="admin-data-table" data-interactive-table>
                    <thead>
                        <tr>
                            <?php if ($bulk): ?>
                                <th class="check-col"><input type="checkbox" data-bulk-all aria-label="Pilih semua"></th>
                            <?php endif; ?>
                            <?php foreach ($columns as $column): ?>
                                <th <?= ! empty($column['sortable']) ? 'data-sortable="1"' : '' ?>>
                                    <?= esc((string) ($column['label'] ?? '')) ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($rows): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php if ($bulk): ?>
                                    <td class="check-col">
                                        <input
                                            type="checkbox"
                                            data-bulk-row
                                            value="<?= esc((string) ($row['_bulk_id'] ?? ''), 'attr') ?>"
                                            aria-label="Pilih baris"
                                            <?= ! empty($row['_bulk_disabled']) ? 'disabled' : '' ?>
                                        >
                                    </td>
                                <?php endif; ?>
                                <?php foreach ($columns as $column): ?>
                                    <?php $key = (string) ($column['key'] ?? ''); ?>
                                    <td data-label="<?= esc((string) ($column['label'] ?? ''), 'attr') ?>">
                                        <?= $row[$key] ?? '' ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr data-empty-row><td colspan="<?= $columnCount ?>"><?= esc($empty) ?></td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}

if (! function_exists('admin_table_title')) {
    function admin_table_title(string $id): string
    {
        return match ($id) {
            'loa-requests-table' => 'Daftar Permohonan LoA',
            'loa-letters-table' => 'Daftar LoA Terbit',
            'loa-notifications-table' => 'Daftar Notifikasi',
            'journals-table' => 'Daftar Jurnal',
            'journal-templates-table' => 'Daftar Template Artikel',
            'publishers-table' => 'Daftar Publisher',
            'articles-table' => 'Daftar Artikel',
            'article-categories-table' => 'Daftar Kategori Artikel',
            'invoice-jurnal-table' => 'Daftar Invoice Jurnal',
            'editor-reviewer-table' => 'Daftar Pendaftar Editor & Reviewer',
            'whatsapp-templates-table' => 'Daftar Template WhatsApp',
            'users-table' => 'Daftar Pengguna',
            default => 'Daftar Data',
        };
    }
}

if (! function_exists('admin_table_icon')) {
    function admin_table_icon(string $id): string
    {
        return match ($id) {
            'loa-requests-table' => 'mdi:send-outline',
            'loa-letters-table' => 'mdi:file-check-outline',
            'loa-notifications-table' => 'mdi:bell-outline',
            'journals-table' => 'mdi:book-open-page-variant-outline',
            'journal-templates-table' => 'mdi:file-word-outline',
            'publishers-table' => 'mdi:office-building-outline',
            'articles-table' => 'mdi:newspaper-variant-outline',
            'article-categories-table' => 'mdi:shape-outline',
            'invoice-jurnal-table' => 'mdi:receipt-text-outline',
            'editor-reviewer-table' => 'mdi:account-group-outline',
            'whatsapp-templates-table' => 'mdi:message-text-outline',
            'users-table' => 'mdi:account-cog-outline',
            default => 'mdi:table',
        };
    }
}

if (! function_exists('admin_action_link')) {
    function admin_action_link(string $type, string $url, string $label = '', array $attributes = []): string
    {
        $meta = admin_action_meta($type);
        $label = $label !== '' ? $label : $meta['label'];
        $attrs = admin_action_attrs($attributes + [
            'href' => $url,
            'class' => 'icon-btn ' . $meta['class'],
            'title' => $label,
            'aria-label' => $label,
        ]);

        return '<a ' . $attrs . '><iconify-icon icon="' . esc($meta['icon'], 'attr') . '"></iconify-icon></a>';
    }
}

if (! function_exists('admin_action_button')) {
    function admin_action_button(string $type, string $label = '', array $attributes = []): string
    {
        $meta = admin_action_meta($type);
        $label = $label !== '' ? $label : $meta['label'];
        $attrs = admin_action_attrs($attributes + [
            'type' => 'button',
            'class' => 'icon-btn ' . $meta['class'],
            'title' => $label,
            'aria-label' => $label,
        ]);

        return '<button ' . $attrs . '><iconify-icon icon="' . esc($meta['icon'], 'attr') . '"></iconify-icon></button>';
    }
}

if (! function_exists('admin_action_form')) {
    function admin_action_form(string $type, string $url, string $label = '', string $confirm = '', array $buttonAttributes = []): string
    {
        $buttonAttributes['type'] = $buttonAttributes['type'] ?? 'submit';
        $formAttrs = [
            'method' => 'post',
            'action' => $url,
        ];
        if ($confirm !== '') {
            $formAttrs['onsubmit'] = "return confirm('" . addslashes($confirm) . "')";
        }

        return '<form ' . admin_action_attrs($formAttrs) . '>' . admin_action_button($type, $label, $buttonAttributes) . '</form>';
    }
}

if (! function_exists('admin_action_meta')) {
    function admin_action_meta(string $type): array
    {
        return match ($type) {
            'view', 'detail', 'preview' => ['class' => 'view', 'icon' => 'mdi:eye-outline', 'label' => 'Lihat'],
            'edit' => ['class' => 'edit', 'icon' => 'mdi:pencil-outline', 'label' => 'Edit'],
            'approve' => ['class' => 'success', 'icon' => 'mdi:check-circle-outline', 'label' => 'Setujui'],
            'download' => ['class' => 'download', 'icon' => 'mdi:download-outline', 'label' => 'Unduh'],
            'print' => ['class' => 'print', 'icon' => 'mdi:printer-outline', 'label' => 'Cetak'],
            'email' => ['class' => 'email', 'icon' => 'mdi:email-send', 'label' => 'Kirim Email'],
            'whatsapp' => ['class' => 'whatsapp', 'icon' => 'ic:baseline-whatsapp', 'label' => 'Kirim WhatsApp'],
            'delete', 'trash' => ['class' => 'delete', 'icon' => 'mdi:trash-can-outline', 'label' => 'Hapus'],
            default => ['class' => 'neutral', 'icon' => 'mdi:dots-horizontal', 'label' => 'Aksi'],
        };
    }
}

if (! function_exists('admin_action_attrs')) {
    function admin_action_attrs(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false) {
                continue;
            }
            if ($value === true) {
                $html[] = esc((string) $name, 'attr');
                continue;
            }
            $html[] = esc((string) $name, 'attr') . '="' . esc((string) $value, 'attr') . '"';
        }

        return implode(' ', $html);
    }
}
