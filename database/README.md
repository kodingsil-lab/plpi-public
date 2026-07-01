# Backup Database PLPI

File `plpi_public_full.sql` berisi struktur dan data lengkap database lokal `plpi_public`.

## Restore Lokal

1. Buat database:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS plpi_public CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
```

2. Import dump:

```bash
mysql -u root plpi_public < database/plpi_public_full.sql
```

3. Salin/cek `.env` agar koneksi database mengarah ke `plpi_public`.

## Uploads

File upload penting ikut repository:

- `public/uploads/app-settings`
- `writable/uploads/journals`
- `writable/uploads/loa`
- `writable/uploads/publishers`

Folder runtime seperti cache, logs, session, dan debugbar tidak ikut Git.
