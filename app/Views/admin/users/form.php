<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<?php $isEdit = ! empty($row); ?>
<section class="admin-panel user-form-panel">
    <form class="admin-form" method="post" action="<?= $isEdit ? site_url('dashboard/users/' . (int) $row['id'] . '/update') : site_url('dashboard/users') ?>">
        <div class="form-section">
            <h3><iconify-icon icon="mdi:account-card-outline"></iconify-icon>Identitas Akun</h3>
            <div class="form-grid">
                <label>
                    <span>Username</span>
                    <input name="username" value="<?= esc((string) old('username', $row['username'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Nama</span>
                    <input name="name" value="<?= esc((string) old('name', $row['name'] ?? ''), 'attr') ?>" required>
                </label>
                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="<?= esc((string) old('email', $row['email'] ?? ''), 'attr') ?>" required>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:shield-account-outline"></iconify-icon>Hak Akses</h3>
            <div class="form-grid">
                <label>
                    <span>Role</span>
                    <select name="role">
                        <?php $roleValue = (string) old('role', $row['role'] ?? 'admin'); ?>
                        <option value="superadmin" <?= $roleValue === 'superadmin' ? 'selected' : '' ?>>Super Admin</option>
                        <option value="admin" <?= $roleValue === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </label>
                <label>
                    <span>Status</span>
                    <?php $activeValue = (string) old('is_active', (string) ($row['is_active'] ?? '1')); ?>
                    <select name="is_active">
                        <option value="1" <?= $activeValue === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= $activeValue === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="form-section">
            <h3><iconify-icon icon="mdi:key-outline"></iconify-icon>Keamanan</h3>
            <div class="form-grid">
                <label>
                    <span><?= $isEdit ? 'Password Baru (opsional)' : 'Password' ?></span>
                    <input type="password" name="password" <?= $isEdit ? '' : 'required' ?>>
                    <?php if ($isEdit): ?><small>Kosongkan jika tidak ingin mengganti password.</small><?php endif; ?>
                </label>
            </div>
        </div>

        <div class="form-actions">
            <a class="admin-btn secondary" href="<?= site_url('dashboard/users') ?>"><iconify-icon icon="mdi:arrow-left"></iconify-icon>Kembali</a>
            <button class="admin-btn primary" type="submit"><iconify-icon icon="mdi:content-save-outline"></iconify-icon>Simpan</button>
        </div>
    </form>
</section>
<?= $this->endSection() ?>
