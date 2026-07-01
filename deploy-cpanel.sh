#!/usr/bin/env bash
set -euo pipefail

APP_NAME="plpi"
REPO_URL="${REPO_URL:-https://github.com/kodingsil-lab/plpi-public.git}"
BRANCH="main"

APP_DIR="${APP_DIR:-$HOME/plpi-app}"
WEB_DIR="${WEB_DIR:-$HOME/plpi}"
BACKUP_DIR="${BACKUP_DIR:-$HOME/backups/plpi-deploy}"

APP_URL="${APP_URL:-https://plpi.unisap.ac.id/}"
DB_HOST="${DB_HOST:-localhost}"
DB_NAME="${DB_NAME:-loaunisa_plpi}"
DB_USER="${DB_USER:-loaunisa_plpiuser}"
DB_PASS="${PLPI_DB_PASS:-}"
DB_PORT="${DB_PORT:-3306}"
ENCRYPTION_KEY="${ENCRYPTION_KEY:-hex2bin:9b7d0c3f2a1e4d5c6b7f8091a2b3c4d59e8f7a6b5c4d3e2f1a0b9c8d7e6f5a4}"

ACTION="${1:-install}"

log() {
    printf '\n[%s] %s\n' "$APP_NAME" "$1"
}

fail() {
    printf '\n[%s] ERROR: %s\n' "$APP_NAME" "$1" >&2
    exit 1
}

need_command() {
    command -v "$1" >/dev/null 2>&1 || fail "Command '$1' tidak tersedia di hosting."
}

ask_db_password() {
    if [ -z "$DB_PASS" ]; then
        printf 'Password database %s: ' "$DB_USER"
        stty -echo
        read -r DB_PASS
        stty echo
        printf '\n'
    fi
}

backup_webroot_once() {
    mkdir -p "$BACKUP_DIR"
    mkdir -p "$WEB_DIR"

    if [ ! -f "$WEB_DIR/.plpi_deployed" ] && [ "$(find "$WEB_DIR" -mindepth 1 -maxdepth 1 2>/dev/null | wc -l)" -gt 0 ]; then
        local stamp
        stamp="$(date +%Y%m%d-%H%M%S)"
        log "Backup isi webroot lama ke $BACKUP_DIR/webroot-$stamp.tar.gz"
        tar -czf "$BACKUP_DIR/webroot-$stamp.tar.gz" -C "$WEB_DIR" .
    fi
}

checkout_code() {
    if [ -d "$APP_DIR/.git" ]; then
        log "Update repo di $APP_DIR"
        git -C "$APP_DIR" fetch origin "$BRANCH"
        git -C "$APP_DIR" reset --hard "origin/$BRANCH"
    else
        if [ -e "$APP_DIR" ] && [ "$(find "$APP_DIR" -mindepth 1 -maxdepth 1 2>/dev/null | wc -l)" -gt 0 ]; then
            fail "$APP_DIR sudah ada dan bukan repo Git. Pindahkan/hapus dulu folder itu."
        fi

        log "Clone repo ke $APP_DIR"
        git clone --branch "$BRANCH" "$REPO_URL" "$APP_DIR"
    fi
}

install_dependencies() {
    log "Install composer dependencies"
    if command -v composer >/dev/null 2>&1; then
        composer install --working-dir="$APP_DIR" --no-dev --optimize-autoloader
    elif [ -f "$APP_DIR/composer.phar" ]; then
        php "$APP_DIR/composer.phar" install --working-dir="$APP_DIR" --no-dev --optimize-autoloader
    else
        fail "Composer tidak ditemukan. Install Composer di cPanel atau upload composer.phar."
    fi
}

write_env() {
    log "Tulis .env production"
    cat > "$APP_DIR/.env" <<EOF
CI_ENVIRONMENT = production

app.baseURL = '$APP_URL'

database.default.hostname = $DB_HOST
database.default.database = $DB_NAME
database.default.username = $DB_USER
database.default.password = $DB_PASS
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = $DB_PORT

encryption.key = $ENCRYPTION_KEY
EOF
    chmod 600 "$APP_DIR/.env" || true
}

import_database() {
    ask_db_password

    if [ ! -f "$APP_DIR/database/plpi_public_full.sql" ]; then
        fail "Dump database tidak ditemukan: $APP_DIR/database/plpi_public_full.sql"
    fi

    log "Import database ke $DB_NAME"
    mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$APP_DIR/database/plpi_public_full.sql"
}

publish_public_folder() {
    backup_webroot_once

    log "Salin folder public ke webroot $WEB_DIR"
    if command -v rsync >/dev/null 2>&1; then
        rsync -a --delete --exclude='.well-known/' "$APP_DIR/public/" "$WEB_DIR/"
    else
        find "$WEB_DIR" -mindepth 1 -maxdepth 1 ! -name '.well-known' -exec rm -rf {} +
        cp -a "$APP_DIR/public/." "$WEB_DIR/"
    fi

    log "Patch index.php agar membaca app dari $APP_DIR"
    perl -0pi -e "s#require FCPATH \\. '../app/Config/Paths\\.php';#require '${APP_DIR}/app/Config/Paths.php';#" "$WEB_DIR/index.php"
    touch "$WEB_DIR/.plpi_deployed"
}

set_permissions() {
    log "Set permission writable dan web assets"
    chmod -R 775 "$APP_DIR/writable" || true
    chmod -R 755 "$WEB_DIR" || true
}

clear_cache() {
    log "Clear cache CodeIgniter"
    php "$APP_DIR/spark" cache:clear || true
}

show_summary() {
    cat <<EOF

Deploy selesai.

App dir : $APP_DIR
Webroot : $WEB_DIR
URL     : $APP_URL
DB      : $DB_NAME

Jika domain belum tampil, cek:
1. Document root domain plpi.unisap.ac.id mengarah ke $WEB_DIR
2. PHP version minimal 8.2
3. Database/user sudah dibuat dan user punya ALL PRIVILEGES
EOF
}

need_command git
need_command php
need_command mysql
need_command tar
need_command perl

case "$ACTION" in
    install)
        checkout_code
        install_dependencies
        ask_db_password
        write_env
        import_database
        publish_public_folder
        set_permissions
        clear_cache
        show_summary
        ;;
    update)
        checkout_code
        install_dependencies
        ask_db_password
        write_env
        publish_public_folder
        set_permissions
        clear_cache
        show_summary
        ;;
    import-db)
        checkout_code
        ask_db_password
        write_env
        import_database
        clear_cache
        show_summary
        ;;
    *)
        cat <<EOF
Usage:
  bash deploy-cpanel.sh install    # deploy pertama + import database
  bash deploy-cpanel.sh update     # update kode tanpa import database
  bash deploy-cpanel.sh import-db  # import ulang database dari dump

Opsional:
  APP_DIR="\$HOME/plpi-app" WEB_DIR="\$HOME/plpi" PLPI_DB_PASS="password" bash deploy-cpanel.sh install
EOF
        exit 1
        ;;
esac
