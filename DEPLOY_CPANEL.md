# Deploy PLPI di cPanel

Dokumen ini untuk deploy harian ke hosting `plpi.unisap.ac.id`.

## Deploy Harian

Jalankan dari Terminal cPanel:

```bash
cd ~/plpi-deploy-src
git pull origin main
WEB_DIR="$HOME/plpi.unisap.ac.id" REPO_URL="git@github.com:kodingsil-lab/plpi-public.git" bash ~/plpi-deploy-src/deploy-cpanel.sh update
```

Mode `update` hanya mengambil kode terbaru, install dependency jika perlu, menyalin folder `public`, dan clear cache. Password database tidak diminta karena dibaca dari `/home/loaunisa/plpi-app/.env`.

## Deploy Pertama

Pakai hanya saat setup awal server baru:

```bash
cd ~
git clone git@github.com:kodingsil-lab/plpi-public.git plpi-deploy-src
WEB_DIR="$HOME/plpi.unisap.ac.id" REPO_URL="git@github.com:kodingsil-lab/plpi-public.git" bash ~/plpi-deploy-src/deploy-cpanel.sh install
```

Mode `install` akan import database hanya kalau dump SQL sudah tersedia di folder app server. Jangan dipakai untuk deploy harian.

Jika server baru belum punya file `.env`, jalankan `install` sekali dengan `PLPI_DB_PASS` sementara:

```bash
PLPI_DB_PASS="isi_password_database" WEB_DIR="$HOME/plpi.unisap.ac.id" REPO_URL="git@github.com:kodingsil-lab/plpi-public.git" bash ~/plpi-deploy-src/deploy-cpanel.sh install
```

Jangan simpan password ini di file Git. Setelah `.env` terbentuk di hosting, deploy harian tidak perlu memasukkan password lagi.

## Import Database Manual

Pakai hanya kalau memang ingin import ulang database:

```bash
WEB_DIR="$HOME/plpi.unisap.ac.id" REPO_URL="git@github.com:kodingsil-lab/plpi-public.git" bash ~/plpi-deploy-src/deploy-cpanel.sh import-db
```

Karena dump SQL tidak ikut Git, upload dump ke `/home/loaunisa/plpi-app/database/plpi_public_full.sql` dulu sebelum menjalankan `import-db`.

## Catatan Penting

- Secret database dan SMTP disimpan di `/home/loaunisa/plpi-app/.env`, bukan di dashboard dan bukan di Git.
- File dump database lokal seperti `database/*.sql` dan `database/*.sql.gz` diabaikan oleh Git.
- Setelah edit lokal, push dilakukan manual lewat GitHub Desktop.
