<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php
$tableRows = [];
$recruitmentLinks = [];
foreach (($journals ?? []) as $index => $journal) {
    $slug = trim((string) ($journal['slug'] ?? ''));
    if ($slug === '') {
        continue;
    }

    $url = site_url('rekrutmen-editor-reviewer/jurnal/' . $slug);
    $recruitmentLinks[] = [
        'no' => $index + 1,
        'name' => (string) ($journal['name'] ?? '-'),
        'code' => (string) ($journal['code'] ?? '-'),
        'url' => $url,
    ];
}

foreach (($rows ?? []) as $index => $row) {
    $profileLinks = [];
    foreach ([
        'Scholar' => $row['google_scholar_id'] ?? '',
        'SINTA' => $row['sinta_id'] ?? '',
        'Scopus' => $row['scopus_id'] ?? '',
        'ORCID' => $row['orcid_id'] ?? '',
    ] as $label => $value) {
        $value = trim((string) $value);
        if ($value !== '') {
            $profileLinks[] = '<span class="status-pill muted">' . esc($label) . ': ' . esc($value) . '</span>';
        }
    }
    $email = trim((string) ($row['email'] ?? ''));
    $phone = trim((string) ($row['phone'] ?? ''));
    $status = trim((string) ($row['status'] ?? 'baru'));
    $statusLabels = [
        'baru' => 'Baru',
        'diproses' => 'Diproses',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $tableRows[] = [
        '_bulk_id'  => (string) ($row['id'] ?? ''),
        'no'        => esc((string) (($startNumber ?? 1) + $index)),
        'code'      => '<strong>' . esc((string) ($row['application_code'] ?? '-')) . '</strong>',
        'applicant' => '<strong>' . esc((string) ($row['full_name'] ?? '-')) . '</strong><br><small>' . esc((string) ($row['institution'] ?? '-')) . '</small>',
        'journal'   => esc((string) ($row['journal_name'] ?? '-')) . '<br><span class="status-pill info">' . esc((string) ($row['role_requested'] ?? '-')) . '</span>',
        'contact'   => ($email !== '' ? '<a href="mailto:' . esc($email, 'attr') . '">' . esc($email) . '</a>' : '-')
            . ($phone !== '' ? '<br><small><a href="https://wa.me/' . esc(preg_replace('/\D+/', '', $phone) ?: $phone, 'attr') . '" target="_blank" rel="noopener">' . esc($phone) . '</a></small>' : ''),
        'profiles'  => $profileLinks !== [] ? '<div class="profile-pill-stack">' . implode('', $profileLinks) . '</div>' : '<span class="status-pill muted">Kosong</span>',
        'expertise' => esc((string) ($row['expertise'] ?? '-')),
        'status'    => '<span class="status-pill ' . ($status === 'baru' ? 'info' : 'done') . '">' . esc($statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status))) . '</span>',
        'date'      => ! empty($row['created_at']) ? date('d-m-Y H:i', strtotime((string) $row['created_at'])) : '-',
        'actions'   => '<div class="row-actions">'
            . admin_action_form('delete', site_url('dashboard/rekrutmen-editor-reviewer/' . (int) $row['id'] . '/delete'), 'Hapus', 'Hapus data pendaftaran ini?')
            . '</div>',
    ];
}
?>
<section class="admin-panel recruitment-links-panel">
    <div class="table-section-header">
        <h2><iconify-icon icon="mdi:link-variant"></iconify-icon>Link Rekrutmen per Jurnal</h2>
    </div>
    <div class="admin-data-table-wrap">
        <table class="admin-data-table recruitment-link-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jurnal</th>
                    <th>Kode</th>
                    <th>Link Rekrutmen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($recruitmentLinks): ?>
                <?php foreach ($recruitmentLinks as $link): ?>
                    <tr>
                        <td><?= esc((string) $link['no']) ?></td>
                        <td><strong><?= esc($link['name']) ?></strong></td>
                        <td><?= esc($link['code']) ?></td>
                        <td>
                            <a class="recruitment-share-link" href="<?= esc($link['url'], 'attr') ?>" target="_blank" rel="noopener">
                                <?= esc($link['url']) ?>
                            </a>
                        </td>
                        <td>
                            <a class="admin-btn secondary recruitment-open-btn" href="<?= esc($link['url'], 'attr') ?>" target="_blank" rel="noopener">
                                Buka Form
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr data-empty-row><td colspan="5">Belum ada jurnal dengan slug yang dapat dibagikan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-panel admin-filter-panel">
    <?php if (! empty($databaseError)): ?>
        <div class="admin-alert error"><?= esc((string) $databaseError) ?></div>
    <?php endif; ?>

    <div class="panel-toolbar">
        <div class="panel-actions">
            <form class="admin-filter-form recruitment-filter-form" method="get" action="<?= site_url('dashboard/rekrutmen-editor-reviewer') ?>">
                <label>
                    <span>Jurnal</span>
                    <select name="journal_id">
                        <option value="">Semua Jurnal</option>
                        <?php foreach (($journals ?? []) as $journal): ?>
                            <option value="<?= (int) $journal['id'] ?>" <?= (int) ($filters['journal_id'] ?? 0) === (int) $journal['id'] ? 'selected' : '' ?>>
                                <?= esc((string) $journal['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Peran</span>
                    <select name="role">
                        <option value="">Semua</option>
                        <option value="Reviewer" <?= (($filters['role'] ?? '') === 'Reviewer') ? 'selected' : '' ?>>Reviewer</option>
                        <option value="Editor" <?= (($filters['role'] ?? '') === 'Editor') ? 'selected' : '' ?>>Editor</option>
                    </select>
                </label>
                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="">Semua</option>
                        <?php foreach (($statuses ?? []) as $item): ?>
                            <option value="<?= esc($item, 'attr') ?>" <?= (($filters['status'] ?? '') === $item) ? 'selected' : '' ?>>
                                <?= esc(ucfirst($item)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <span>Pencarian</span>
                    <input type="search" name="q" value="<?= esc((string) ($filters['q'] ?? ''), 'attr') ?>" placeholder="Kode / nama / email / institusi / bidang">
                </label>
                <div class="filter-actions">
                    <button class="admin-btn primary" type="submit">Terapkan</button>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/rekrutmen-editor-reviewer') ?>">Reset</a>
                    <a class="admin-btn secondary" href="<?= site_url('dashboard/rekrutmen-editor-reviewer/export/excel') ?>">Export Excel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="admin-panel mt-panel">
    <?= admin_table([
        'id' => 'editor-reviewer-table',
        'search' => false,
        'bulk' => [
            'action' => site_url('dashboard/rekrutmen-editor-reviewer/bulk-delete'),
            'confirm' => 'Hapus data pendaftaran yang dipilih?',
        ],
        'columns' => [
            ['key' => 'no', 'label' => 'No', 'sortable' => true],
            ['key' => 'code', 'label' => 'Kode', 'sortable' => true],
            ['key' => 'applicant', 'label' => 'Pendaftar', 'sortable' => true],
            ['key' => 'journal', 'label' => 'Jurnal & Peran', 'sortable' => true],
            ['key' => 'contact', 'label' => 'Kontak', 'sortable' => true],
            ['key' => 'profiles', 'label' => 'Indeksasi'],
            ['key' => 'expertise', 'label' => 'Bidang', 'sortable' => true],
            ['key' => 'status', 'label' => 'Status', 'sortable' => true],
            ['key' => 'date', 'label' => 'Tanggal', 'sortable' => true],
            ['key' => 'actions', 'label' => 'Aksi'],
        ],
        'rows' => $tableRows,
        'empty' => 'Belum ada data pendaftaran editor atau reviewer.',
    ]) ?>

    <?php if (isset($pager)): ?>
        <div class="table-pagination-footer">
            <div class="table-pagination-info">Menampilkan <?= count($rows ?? []) ?> data</div>
            <?= $pager->links('default', 'admin_table') ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
