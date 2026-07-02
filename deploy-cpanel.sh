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
COMPOSER_BIN="${COMPOSER_BIN:-}"
SMTP_HOST="${PLPI_SMTP_HOST:-plpi.unisap.ac.id}"
SMTP_PORT="${PLPI_SMTP_PORT:-465}"
SMTP_USER="${PLPI_SMTP_USER:-info@plpi.unisap.ac.id}"
SMTP_PASS="${PLPI_SMTP_PASS:-}"
SMTP_CRYPTO="${PLPI_SMTP_CRYPTO:-ssl}"
MAIL_FROM_EMAIL="${PLPI_MAIL_FROM_EMAIL:-info@plpi.unisap.ac.id}"
MAIL_FROM_NAME="${PLPI_MAIL_FROM_NAME:-Pusat Layanan Publikasi Ilmiah}"

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

require_db_password() {
    if [ -z "$DB_PASS" ]; then
        fail "Password database belum tersedia. Isi di $APP_DIR/.env atau jalankan sekali dengan PLPI_DB_PASS=\"password\"."
    fi
}

env_value() {
    local key="$1"
    local file="$APP_DIR/.env"

    [ -f "$file" ] || return 0

    awk -F '=' -v key="$key" '
        $1 ~ "^[[:space:]]*" key "[[:space:]]*$" {
            value = $2
            for (i = 3; i <= NF; i++) {
                value = value "=" $i
            }
            sub(/^[[:space:]]*/, "", value)
            sub(/[[:space:]]*$/, "", value)
            sub(/^'\''/, "", value)
            sub(/'\''$/, "", value)
            print value
            exit
        }
    ' "$file"
}

preserve_existing_secrets() {
    if [ -z "$DB_PASS" ]; then
        DB_PASS="$(env_value "database.default.password")"
    fi

    if [ -z "$SMTP_PASS" ]; then
        SMTP_PASS="$(env_value "plpi.smtp.password")"
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
        git -C "$APP_DIR" remote set-url origin "$REPO_URL"
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
    local composer_cmd=""

    if [ -n "$COMPOSER_BIN" ] && [ -x "$COMPOSER_BIN" ]; then
        composer_cmd="$COMPOSER_BIN"
    elif command -v composer >/dev/null 2>&1; then
        composer_cmd="$(command -v composer)"
    else
        for candidate in \
            "$HOME/bin/composer" \
            "$HOME/.local/bin/composer" \
            "/opt/cpanel/composer/bin/composer" \
            "/usr/local/bin/composer" \
            "/usr/bin/composer"
        do
            if [ -x "$candidate" ]; then
                composer_cmd="$candidate"
                break
            fi
        done
    fi

    if [ -n "$composer_cmd" ]; then
        "$composer_cmd" install --working-dir="$APP_DIR" --no-dev --optimize-autoloader
        return
    fi

    if [ -f "$APP_DIR/composer.phar" ]; then
        php "$APP_DIR/composer.phar" install --working-dir="$APP_DIR" --no-dev --optimize-autoloader
        return
    fi

    log "Composer tidak ditemukan, download composer.phar lokal"
    if command -v curl >/dev/null 2>&1; then
        curl -fsSL https://getcomposer.org/composer-stable.phar -o "$APP_DIR/composer.phar"
    elif command -v wget >/dev/null 2>&1; then
        wget -q https://getcomposer.org/composer-stable.phar -O "$APP_DIR/composer.phar"
    else
        php -r "copy('https://getcomposer.org/composer-stable.phar', '$APP_DIR/composer.phar');"
    fi

    [ -f "$APP_DIR/composer.phar" ] || fail "Gagal download composer.phar. Upload composer.phar ke $APP_DIR atau set COMPOSER_BIN=/path/to/composer."
    php "$APP_DIR/composer.phar" install --working-dir="$APP_DIR" --no-dev --optimize-autoloader
}

write_env() {
    log "Tulis .env production"
    preserve_existing_secrets
    require_db_password

    cat > "$APP_DIR/.env" <<EOF
CI_ENVIRONMENT = production

app.baseURL = '$APP_URL'
app.indexPage = ''

database.default.hostname = $DB_HOST
database.default.database = $DB_NAME
database.default.username = $DB_USER
database.default.password = $DB_PASS
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = $DB_PORT

encryption.key = $ENCRYPTION_KEY

plpi.smtp.host = $SMTP_HOST
plpi.smtp.port = $SMTP_PORT
plpi.smtp.user = $SMTP_USER
plpi.smtp.password = '$SMTP_PASS'
plpi.smtp.crypto = $SMTP_CRYPTO
plpi.mail.fromEmail = $MAIL_FROM_EMAIL
plpi.mail.fromName = '$MAIL_FROM_NAME'
EOF
    chmod 600 "$APP_DIR/.env" || true
}

import_database() {
    require_db_password

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
        write_env
        if [ -f "$APP_DIR/database/plpi_public_full.sql" ]; then
            import_database
        else
            log "Lewati import database karena dump SQL tidak ada di repo/app"
        fi
        publish_public_folder
        set_permissions
        clear_cache
        show_summary
        ;;
    update)
        checkout_code
        install_dependencies
        write_env
        publish_public_folder
        set_permissions
        clear_cache
        show_summary
        ;;
    import-db)
        checkout_code
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
