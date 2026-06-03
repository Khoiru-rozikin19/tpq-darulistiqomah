#!/usr/bin/env bash
#
# ============================================================================
#  TPQCTL — Panel Kontrol Sistem Keuangan Darul Istiqomah
#  Manajemen website Laravel di VPS Ubuntu 24.04
# ============================================================================
#
#  PENGGUNAAN:
#    sudo bash tpqctl.sh            → Buka panel interaktif
#    sudo bash tpqctl.sh status     → Langsung cek status
#    sudo bash tpqctl.sh update     → Langsung update dari GitHub
#    sudo bash tpqctl.sh reinstall  → Langsung reinstall
#    sudo bash tpqctl.sh uninstall  → Langsung uninstall
#
# ============================================================================

set -uo pipefail

# ─────────────────────────────────────────────
#  KONSTANTA
# ─────────────────────────────────────────────
APP_DIR="/var/www/tpq"
APP_USER="www-data"
PHP_VERSION="8.4"
CONFIG_FILE="${APP_DIR}/.tpqctl.conf"
VERSION="1.0.0"

# ─────────────────────────────────────────────
#  WARNA
# ─────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
BOLD='\033[1m'
DIM='\033[2m'
NC='\033[0m'
BG_GREEN='\033[42m'
BG_RED='\033[41m'
BG_YELLOW='\033[43m'
BG_BLUE='\033[44m'

# ─────────────────────────────────────────────
#  FUNGSI UTILITAS
# ─────────────────────────────────────────────
info()    { echo -e "  ${BLUE}ℹ${NC}  $1"; }
success() { echo -e "  ${GREEN}✔${NC}  $1"; }
warn()    { echo -e "  ${YELLOW}⚠${NC}  $1"; }
fail()    { echo -e "  ${RED}✘${NC}  $1"; }
divider() { echo -e "  ${DIM}────────────────────────────────────────────────────${NC}"; }

check_root() {
    if [[ $EUID -ne 0 ]]; then
        echo -e "${RED}Script ini harus dijalankan sebagai root.${NC}"
        echo -e "Gunakan: ${BOLD}sudo bash tpqctl.sh${NC}"
        exit 1
    fi
    # Tambahkan direktori aplikasi ke safe.directory Git untuk user root
    if command -v git &>/dev/null; then
        git config --global --add safe.directory "$APP_DIR" 2>/dev/null || true
    fi
}

check_installed() {
    if [[ ! -f "${APP_DIR}/artisan" ]]; then
        echo -e "${RED}Website belum terinstal di ${APP_DIR}.${NC}"
        echo -e "Jalankan ${BOLD}sudo bash setup.sh${NC} terlebih dahulu."
        exit 1
    fi
}

# Baca konfigurasi tersimpan (domain, git remote)
load_config() {
    DOMAIN=""
    GIT_REMOTE=""
    if [[ -f "$CONFIG_FILE" ]]; then
        source "$CONFIG_FILE"
    fi
    # Fallback: coba baca domain dari Nginx config
    if [[ -z "$DOMAIN" && -f /etc/nginx/sites-available/tpq ]]; then
        DOMAIN=$(grep -oP 'server_name\s+\K[^;]+' /etc/nginx/sites-available/tpq 2>/dev/null | head -1 | xargs)
    fi
    # Fallback: coba baca dari .env
    if [[ -z "$DOMAIN" && -f "${APP_DIR}/.env" ]]; then
        DOMAIN=$(grep -oP '^APP_URL=https?://\K.*' "${APP_DIR}/.env" 2>/dev/null | head -1)
    fi
    # Fallback: coba baca git remote dari repo
    if [[ -z "$GIT_REMOTE" && -d "${APP_DIR}/.git" ]]; then
        local r_name
        r_name=$(cd "$APP_DIR" && git remote 2>/dev/null | head -1)
        r_name=${r_name:-origin}
        GIT_REMOTE=$(cd "$APP_DIR" && git remote get-url "$r_name" 2>/dev/null || echo "")
    fi
}

save_config() {
    cat > "$CONFIG_FILE" <<EOF
# TPQ Control Panel Config — auto-generated
DOMAIN="${DOMAIN}"
GIT_REMOTE="${GIT_REMOTE}"
EOF
    chown ${APP_USER}:${APP_USER} "$CONFIG_FILE" 2>/dev/null || true
}

# Mendapatkan status service → "active" atau "inactive"
svc_status() {
    systemctl is-active "$1" 2>/dev/null || echo "not-found"
}

# Format status dengan warna
svc_badge() {
    local status
    status=$(svc_status "$1")
    case "$status" in
        active)   echo -e "${BG_GREEN}${BOLD} AKTIF ${NC}" ;;
        inactive) echo -e "${BG_RED}${BOLD} MATI  ${NC}" ;;
        *)        echo -e "${BG_YELLOW}${BOLD} N/A   ${NC}" ;;
    esac
}

# ─────────────────────────────────────────────
#  HEADER
# ─────────────────────────────────────────────
show_header() {
    clear
    echo ""
    echo -e "  ${CYAN}${BOLD}╔═══════════════════════════════════════════════════════╗${NC}"
    echo -e "  ${CYAN}${BOLD}║${NC}   🕌  ${BOLD}TPQ Control Panel${NC}  ${DIM}v${VERSION}${NC}                        ${CYAN}${BOLD}║${NC}"
    echo -e "  ${CYAN}${BOLD}║${NC}   ${DIM}Sistem Keuangan Madrasah Darul Istiqomah${NC}            ${CYAN}${BOLD}║${NC}"
    echo -e "  ${CYAN}${BOLD}╚═══════════════════════════════════════════════════════╝${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  1. STATUS
# ═════════════════════════════════════════════
do_status() {
    show_header
    load_config

    echo -e "  ${BOLD}📊 STATUS WEBSITE${NC}"
    divider

    # Domain & URL
    local app_url
    app_url=$(grep -oP '^APP_URL=\K.*' "${APP_DIR}/.env" 2>/dev/null || echo "N/A")
    echo -e "  🌐 Domain          : ${BOLD}${DOMAIN:-N/A}${NC}"
    echo -e "  🔗 URL             : ${BOLD}${app_url}${NC}"
    echo -e "  📂 Direktori       : ${BOLD}${APP_DIR}${NC}"
    echo ""

    # Services
    echo -e "  ${BOLD}🔧 STATUS LAYANAN${NC}"
    divider
    printf "  %-22s %s\n" "Nginx"              "$(svc_badge nginx)"
    printf "  %-22s %s\n" "PHP ${PHP_VERSION}-FPM" "$(svc_badge php${PHP_VERSION}-fpm)"
    printf "  %-22s %s\n" "Supervisor"         "$(svc_badge supervisor)"
    printf "  %-22s %s\n" "UFW Firewall"       "$(svc_badge ufw)"
    echo ""

    # SSL
    echo -e "  ${BOLD}🔒 SSL / HTTPS${NC}"
    divider
    if [[ -n "$DOMAIN" ]] && certbot certificates -d "$DOMAIN" 2>/dev/null | grep -q "Certificate Name"; then
        local expiry
        expiry=$(certbot certificates -d "$DOMAIN" 2>/dev/null | grep -oP 'Expiry Date: \K[^ ]+' | head -1)
        echo -e "  Sertifikat         : ${GREEN}${BOLD}AKTIF${NC}"
        echo -e "  Kadaluarsa         : ${BOLD}${expiry:-N/A}${NC}"
    else
        echo -e "  Sertifikat         : ${YELLOW}${BOLD}TIDAK ADA / HTTP${NC}"
    fi
    echo ""

    # Laravel
    echo -e "  ${BOLD}⚙️  APLIKASI LARAVEL${NC}"
    divider
    local app_env app_debug
    app_env=$(grep -oP '^APP_ENV=\K.*' "${APP_DIR}/.env" 2>/dev/null || echo "N/A")
    app_debug=$(grep -oP '^APP_DEBUG=\K.*' "${APP_DIR}/.env" 2>/dev/null || echo "N/A")
    echo -e "  Environment        : ${BOLD}${app_env}${NC}"
    if [[ "$app_debug" == "true" ]]; then
        echo -e "  Debug Mode         : ${RED}${BOLD}AKTIF (tidak aman!)${NC}"
    else
        echo -e "  Debug Mode         : ${GREEN}${BOLD}NONAKTIF${NC}"
    fi

    local php_ver node_ver composer_ver
    php_ver=$(php -v 2>/dev/null | head -1 | awk '{print $2}' || echo "N/A")
    node_ver=$(node -v 2>/dev/null || echo "N/A")
    composer_ver=$(composer --version --no-ansi 2>/dev/null | grep -oP 'Composer version \K[^ ]+' || echo "N/A")
    echo -e "  PHP                : ${BOLD}${php_ver}${NC}"
    echo -e "  Node.js            : ${BOLD}${node_ver}${NC}"
    echo -e "  Composer           : ${BOLD}${composer_ver}${NC}"
    echo ""

    # Database
    echo -e "  ${BOLD}🗄️  DATABASE${NC}"
    divider
    local db_file="${APP_DIR}/database/database.sqlite"
    if [[ -f "$db_file" ]]; then
        local db_size
        db_size=$(du -h "$db_file" 2>/dev/null | awk '{print $1}')
        echo -e "  SQLite             : ${GREEN}${BOLD}OK${NC}  (${db_size})"
    else
        echo -e "  SQLite             : ${RED}${BOLD}TIDAK DITEMUKAN${NC}"
    fi

    # Disk
    local disk_usage
    disk_usage=$(df -h "$APP_DIR" 2>/dev/null | tail -1 | awk '{printf "%s / %s (%s terpakai)", $3, $2, $5}')
    echo -e "  Disk               : ${BOLD}${disk_usage}${NC}"
    echo ""

    # Git
    echo -e "  ${BOLD}📦 GIT REPOSITORY${NC}"
    divider
    if [[ -d "${APP_DIR}/.git" ]]; then
        local branch commit remote_url
        branch=$(cd "$APP_DIR" && git branch --show-current 2>/dev/null || echo "N/A")
        commit=$(cd "$APP_DIR" && git log -1 --format="%h — %s" 2>/dev/null || echo "N/A")
        remote_url=$(cd "$APP_DIR" && git remote get-url origin 2>/dev/null || echo "N/A")
        echo -e "  Remote             : ${BOLD}${remote_url}${NC}"
        echo -e "  Branch             : ${BOLD}${branch}${NC}"
        echo -e "  Commit terakhir    : ${DIM}${commit}${NC}"
    else
        echo -e "  Git                : ${YELLOW}${BOLD}BUKAN REPO GIT${NC}"
        echo -e "  ${DIM}(Untuk menggunakan fitur Update, init repo git terlebih dahulu)${NC}"
    fi
    echo ""

    # Uptime
    local uptime_str
    uptime_str=$(uptime -p 2>/dev/null || uptime)
    echo -e "  ${BOLD}🖥️  SERVER${NC}"
    divider
    echo -e "  Uptime             : ${BOLD}${uptime_str}${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  2. CEK UPDATE & APPLY DARI GITHUB
# ═════════════════════════════════════════════
do_update() {
    show_header
    load_config

    echo -e "  ${BOLD}🔄 CEK UPDATE DARI GITHUB${NC}"
    divider
    echo ""

    cd "$APP_DIR"

    # Cek apakah git repo
    if [[ ! -d "${APP_DIR}/.git" ]]; then
        warn "Direktori ${APP_DIR} bukan repository Git."
        echo ""
        read -rp "$(echo -e "  ${YELLOW}Masukkan URL repository GitHub: ${NC}")" input_remote

        if [[ -z "$input_remote" ]]; then
            fail "URL remote tidak boleh kosong."
            return 1
        fi

        info "Menginisialisasi git repository..."
        git init
        git remote add origin "$input_remote"
        GIT_REMOTE="$input_remote"
        save_config

        info "Melakukan fetch dari remote..."
        git fetch origin

        local default_branch
        default_branch=$(git remote show origin 2>/dev/null | grep 'HEAD branch' | awk '{print $NF}')
        default_branch=${default_branch:-main}

        info "Mereset ke branch ${default_branch}..."
        git checkout -f "$default_branch" 2>/dev/null || git checkout -f -b "$default_branch" "origin/$default_branch"

        success "Repository berhasil diinisialisasi."
        echo ""
    fi

    local branch
    branch=$(git branch --show-current 2>/dev/null || echo "main")

    local remote_name
    remote_name=$(git config --get branch."$branch".remote 2>/dev/null || echo "origin")

    local remote_url
    remote_url=$(git remote get-url "$remote_name" 2>/dev/null || echo "")

    if [[ -z "$remote_url" ]]; then
        # Coba ambil remote pertama yang ada
        remote_name=$(git remote 2>/dev/null | head -1)
        remote_name=${remote_name:-origin}
        remote_url=$(git remote get-url "$remote_name" 2>/dev/null || echo "")
    fi

    if [[ -z "$remote_url" ]]; then
        fail "Remote Git tidak ditemukan."
        read -rp "$(echo -e "  ${YELLOW}Masukkan URL repository GitHub: ${NC}")" input_remote
        if [[ -n "$input_remote" ]]; then
            git remote add origin "$input_remote" 2>/dev/null || git remote set-url origin "$input_remote"
            remote_name="origin"
            remote_url="$input_remote"
            GIT_REMOTE="$input_remote"
            save_config
        else
            fail "Dibatalkan."
            return 1
        fi
    fi

    echo -e "  Remote   : ${BOLD}${remote_url}${NC} (via ${remote_name})"
    echo -e "  Branch   : ${BOLD}${branch}${NC}"
    echo ""

    # Fetch
    info "Mengambil perubahan terbaru dari remote..."
    git fetch "$remote_name" "$branch" 2>&1 | while read -r line; do echo -e "  ${DIM}${line}${NC}"; done

    # Cek apakah ada perubahan
    local local_hash remote_hash
    local_hash=$(git rev-parse HEAD 2>/dev/null)
    remote_hash=$(git rev-parse FETCH_HEAD 2>/dev/null)

    if [[ "$local_hash" == "$remote_hash" ]]; then
        echo ""
        success "Website sudah menggunakan versi terbaru! Tidak ada pembaruan."
        echo ""
        echo -e "  Commit saat ini: ${DIM}$(git log -1 --format='%h — %s (%cr)' 2>/dev/null)${NC}"
        echo ""
        return 0
    fi

    # Ada perubahan
    echo ""
    echo -e "  ${YELLOW}${BOLD}Pembaruan tersedia!${NC}"
    echo ""
    echo -e "  ${BOLD}Perubahan yang akan diterapkan:${NC}"
    divider
    git log --oneline "${local_hash}..${remote_hash}" 2>/dev/null | head -20 | while read -r line; do
        echo -e "  ${GREEN}+${NC} ${line}"
    done
    echo ""

    # File yang berubah
    local changed_files
    changed_files=$(git diff --stat "${local_hash}..${remote_hash}" 2>/dev/null | tail -1)
    echo -e "  ${DIM}${changed_files}${NC}"
    echo ""

    read -rp "$(echo -e "  ${YELLOW}Terapkan pembaruan ini? (y/n) [y]: ${NC}")" confirm_update
    confirm_update=${confirm_update:-y}

    if [[ "$confirm_update" != "y" && "$confirm_update" != "Y" ]]; then
        info "Pembaruan dibatalkan."
        return 0
    fi

    echo ""
    info "Mengaktifkan mode maintenance..."
    php artisan down --retry=60 2>/dev/null || true

    # Backup database
    local backup_file="${APP_DIR}/database/database_backup_$(date +%Y%m%d_%H%M%S).sqlite"
    if [[ -f "${APP_DIR}/database/database.sqlite" ]]; then
        cp "${APP_DIR}/database/database.sqlite" "$backup_file"
        success "Backup database: ${DIM}${backup_file}${NC}"
    fi

    # Pull perubahan
    info "Menarik perubahan dari remote..."
    git reset --hard FETCH_HEAD 2>&1 | while read -r line; do echo -e "  ${DIM}${line}${NC}"; done

    # Rebuild dependencies
    info "Menginstal dependensi Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3

    info "Menginstal dependensi NPM & rebuild Vite..."
    npm ci --ignore-scripts 2>&1 | tail -3
    npm run build 2>&1 | tail -3

    # Migrasi database
    info "Menjalankan migrasi database..."
    php artisan migrate --force --no-interaction 2>&1 | tail -5

    # Rebuild cache
    info "Membersihkan & membangun ulang cache..."
    php artisan optimize:clear --no-interaction 2>/dev/null
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
    php artisan event:cache --no-interaction 2>/dev/null || true

    # Fix permissions
    chown -R ${APP_USER}:${APP_USER} "$APP_DIR"
    chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod 664 "${APP_DIR}/database/database.sqlite" 2>/dev/null || true

    # Restart services
    info "Merestart layanan..."
    systemctl restart php${PHP_VERSION}-fpm
    systemctl restart nginx
    supervisorctl restart tpq-worker:* 2>/dev/null || true

    # Disable maintenance
    php artisan up 2>/dev/null || true

    echo ""
    success "Pembaruan berhasil diterapkan!"
    echo ""
    echo -e "  Commit sekarang: ${DIM}$(git log -1 --format='%h — %s (%cr)' 2>/dev/null)${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  3. REINSTALL
# ═════════════════════════════════════════════
do_reinstall() {
    show_header

    echo -e "  ${BOLD}🔁 REINSTALL WEBSITE${NC}"
    divider
    echo ""
    warn "Operasi ini akan:"
    echo -e "    ${RED}•${NC} Menghapus database dan semua data yang ada"
    echo -e "    ${RED}•${NC} Menjalankan ulang migrasi dari awal"
    echo -e "    ${RED}•${NC} Mengisi data default dari seeder"
    echo -e "    ${RED}•${NC} Rebuild semua asset dan cache"
    echo ""

    read -rp "$(echo -e "  ${RED}${BOLD}Apakah Anda yakin? Ketik 'REINSTALL' untuk konfirmasi: ${NC}")" confirm
    if [[ "$confirm" != "REINSTALL" ]]; then
        info "Reinstall dibatalkan."
        return 0
    fi

    echo ""

    cd "$APP_DIR"

    info "Mengaktifkan mode maintenance..."
    php artisan down --retry=60 2>/dev/null || true

    # Backup database lama (safety net)
    if [[ -f "${APP_DIR}/database/database.sqlite" ]]; then
        local backup_file="${APP_DIR}/database/database_pre_reinstall_$(date +%Y%m%d_%H%M%S).sqlite"
        cp "${APP_DIR}/database/database.sqlite" "$backup_file"
        success "Backup database lama: ${DIM}${backup_file}${NC}"
    fi

    # Reinstall composer
    info "Menginstal ulang dependensi Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -3

    # Reinstall npm & build
    info "Menginstal ulang dependensi NPM & rebuild Vite..."
    rm -rf node_modules
    npm ci --ignore-scripts 2>&1 | tail -3
    npm run build 2>&1 | tail -3

    # Fresh migrate + seed
    info "Menjalankan migrate:fresh --seed..."
    php artisan migrate:fresh --seed --force --no-interaction 2>&1 | tail -10

    # Storage link
    php artisan storage:link --force --no-interaction 2>/dev/null || true

    # Rebuild cache
    info "Membangun ulang cache Laravel..."
    php artisan optimize:clear --no-interaction 2>/dev/null
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
    php artisan event:cache --no-interaction 2>/dev/null || true

    # Fix permissions
    chown -R ${APP_USER}:${APP_USER} "$APP_DIR"
    chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod 664 "${APP_DIR}/database/database.sqlite"

    # Restart services
    info "Merestart layanan..."
    systemctl restart php${PHP_VERSION}-fpm
    systemctl restart nginx
    supervisorctl restart tpq-worker:* 2>/dev/null || true

    # Up
    php artisan up 2>/dev/null || true

    echo ""
    success "Reinstall selesai! Website kembali ke kondisi awal."
    echo ""
    echo -e "  ${CYAN}Akun login default:${NC}"
    echo -e "    Admin TU        : ${BOLD}admin@darulistiqomah.com${NC}  /  ${BOLD}password${NC}"
    echo -e "    Kepala Madrasah : ${BOLD}kepala@darulistiqomah.com${NC} /  ${BOLD}password${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  4. UNINSTALL
# ═════════════════════════════════════════════
do_uninstall() {
    show_header

    echo -e "  ${BOLD}🗑️  UNINSTALL WEBSITE${NC}"
    divider
    echo ""
    echo -e "  ${RED}${BOLD}PERINGATAN: Operasi ini akan menghapus SEMUA data!${NC}"
    echo ""
    echo -e "  Yang akan dihapus:"
    echo -e "    ${RED}•${NC} Seluruh file aplikasi di ${APP_DIR}"
    echo -e "    ${RED}•${NC} Konfigurasi Nginx untuk website ini"
    echo -e "    ${RED}•${NC} Konfigurasi Supervisor queue worker"
    echo -e "    ${RED}•${NC} Cron job Laravel scheduler"
    echo -e "    ${RED}•${NC} Sertifikat SSL (jika ada)"
    echo ""
    echo -e "  Yang ${GREEN}TIDAK${NC} akan dihapus:"
    echo -e "    ${GREEN}•${NC} PHP, Nginx, Node.js, Composer (tetap terinstal)"
    echo -e "    ${GREEN}•${NC} Konfigurasi firewall UFW"
    echo ""

    read -rp "$(echo -e "  ${RED}${BOLD}Ketik nama domain website untuk konfirmasi: ${NC}")" confirm_domain

    load_config

    if [[ "$confirm_domain" != "$DOMAIN" ]]; then
        fail "Domain tidak cocok. Uninstall dibatalkan."
        echo -e "  ${DIM}(Diharapkan: ${DOMAIN})${NC}"
        return 1
    fi

    read -rp "$(echo -e "  ${RED}${BOLD}TERAKHIR: Ketik 'HAPUS SEMUA' untuk benar-benar menghapus: ${NC}")" final_confirm
    if [[ "$final_confirm" != "HAPUS SEMUA" ]]; then
        info "Uninstall dibatalkan."
        return 0
    fi

    echo ""

    # Backup database sebelum hapus
    if [[ -f "${APP_DIR}/database/database.sqlite" ]]; then
        local backup_dir="/root/tpq_backup_$(date +%Y%m%d_%H%M%S)"
        mkdir -p "$backup_dir"
        cp "${APP_DIR}/database/database.sqlite" "$backup_dir/"
        cp "${APP_DIR}/.env" "$backup_dir/" 2>/dev/null || true
        success "Backup terakhir disimpan di: ${BOLD}${backup_dir}${NC}"
    fi

    # Stop & remove supervisor
    info "Menghentikan queue worker..."
    supervisorctl stop tpq-worker:* 2>/dev/null || true
    rm -f /etc/supervisor/conf.d/tpq-worker.conf
    supervisorctl reread 2>/dev/null || true
    supervisorctl update 2>/dev/null || true

    # Remove cron
    info "Menghapus cron scheduler..."
    crontab -u ${APP_USER} -l 2>/dev/null | grep -v "artisan schedule:run" | crontab -u ${APP_USER} - 2>/dev/null || true

    # Remove SSL
    if certbot certificates -d "$DOMAIN" 2>/dev/null | grep -q "Certificate Name"; then
        info "Menghapus sertifikat SSL..."
        certbot delete --cert-name "$DOMAIN" --non-interactive 2>/dev/null || true
    fi

    # Remove Nginx
    info "Menghapus konfigurasi Nginx..."
    rm -f /etc/nginx/sites-enabled/tpq
    rm -f /etc/nginx/sites-available/tpq
    systemctl restart nginx

    # Remove application files
    info "Menghapus file aplikasi..."
    rm -rf "$APP_DIR"

    echo ""
    success "Uninstall selesai. Website telah dihapus sepenuhnya."
    echo ""
    if [[ -n "${backup_dir:-}" ]]; then
        echo -e "  ${CYAN}Backup database & .env tersimpan di:${NC}"
        echo -e "  ${BOLD}${backup_dir}${NC}"
    fi
    echo ""
    echo -e "  ${DIM}Untuk menginstal ulang, upload project dan jalankan setup.sh${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  5. RESTART SERVICES
# ═════════════════════════════════════════════
do_restart() {
    show_header

    echo -e "  ${BOLD}🔄 RESTART LAYANAN${NC}"
    divider
    echo ""

    info "Merestart PHP ${PHP_VERSION}-FPM..."
    systemctl restart php${PHP_VERSION}-fpm && success "PHP-FPM direstart." || fail "Gagal restart PHP-FPM."

    info "Merestart Nginx..."
    systemctl restart nginx && success "Nginx direstart." || fail "Gagal restart Nginx."

    info "Merestart Queue Worker..."
    supervisorctl restart tpq-worker:* 2>/dev/null && success "Queue worker direstart." || warn "Queue worker tidak ditemukan."

    info "Membersihkan cache Laravel..."
    cd "$APP_DIR"
    php artisan optimize:clear --no-interaction 2>/dev/null
    php artisan config:cache --no-interaction 2>/dev/null
    php artisan route:cache --no-interaction 2>/dev/null
    php artisan view:cache --no-interaction 2>/dev/null

    echo ""
    success "Semua layanan telah direstart."
    echo ""
}

# ═════════════════════════════════════════════
#  6. LIHAT LOG
# ═════════════════════════════════════════════
do_logs() {
    show_header

    echo -e "  ${BOLD}📋 LOG LARAVEL (25 baris terakhir)${NC}"
    divider
    echo ""

    local log_file="${APP_DIR}/storage/logs/laravel.log"
    if [[ -f "$log_file" ]]; then
        tail -25 "$log_file" | while read -r line; do
            echo -e "  ${DIM}${line}${NC}"
        done
    else
        warn "File log tidak ditemukan: ${log_file}"
    fi

    echo ""
    echo -e "  ${DIM}Untuk melihat log secara realtime:${NC}"
    echo -e "  ${BOLD}tail -f ${log_file}${NC}"
    echo ""
}

# ═════════════════════════════════════════════
#  7. BUAT USER BARU
# ═════════════════════════════════════════════
do_create_user() {
    show_header
    echo -e "  ${BOLD}👤 BUAT USER BARU${NC}"
    divider
    echo ""

    read -rp "$(echo -e "  Nama Lengkap     : ")" input_name
    if [[ -z "$input_name" ]]; then
        fail "Nama tidak boleh kosong."
        return 1
    fi

    read -rp "$(echo -e "  Email            : ")" input_email
    if [[ -z "$input_email" ]]; then
        fail "Email tidak boleh kosong."
        return 1
    fi

    # Validasi format email sederhana
    if [[ ! "$input_email" =~ ^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$ ]]; then
        fail "Format email tidak valid."
        return 1
    fi

    read -rsp "$(echo -e "  Password         : ")" input_password
    echo ""
    if [[ -z "$input_password" ]]; then
        fail "Password tidak boleh kosong."
        return 1
    fi

    if [[ ${#input_password} -lt 8 ]]; then
        warn "Password terlalu pendek (minimal 8 karakter)."
    fi

    echo ""
    read -rp "$(echo -e "  Tanggal Lahir (YYYY-MM-DD): ")" input_dob
    if [[ ! "$input_dob" =~ ^[0-9]{4}-[0-9]{2}-[0-9]{2}$ ]]; then
        fail "Format tanggal lahir tidak valid (harus YYYY-MM-DD)."
        return 1
    fi

    read -rp "$(echo -e "  Tahun Mulai Kerja (YYYY) [2026]: ")" input_year
    input_year=${input_year:-2026}
    if [[ ! "$input_year" =~ ^[0-9]{4}$ ]]; then
        fail "Format tahun tidak valid."
        return 1
    fi

    echo ""
    echo -e "  Jenis Kelamin (L/P):"
    echo -e "    ${CYAN}L${NC}) Laki-laki"
    echo -e "    ${CYAN}P${NC}) Perempuan"
    read -rp "$(echo -e "  Pilihan [L/P]: ")" gender_choice
    gender_choice=$(echo "$gender_choice" | tr '[:lower:]' '[:upper:]')
    if [[ "$gender_choice" != "L" && "$gender_choice" != "P" ]]; then
        fail "Pilihan jenis kelamin tidak valid (harus L atau P)."
        return 1
    fi

    echo ""
    echo -e "  Pilih Role/Jabatan:"
    echo -e "    ${CYAN}1${NC}) Admin TU"
    echo -e "    ${CYAN}2${NC}) Kepala Madrasah"
    echo -e "    ${CYAN}3${NC}) Guru"
    echo -e "    ${CYAN}4${NC}) Bendahara"
    read -rp "$(echo -e "  Pilihan [1-4]: ")" role_choice

    local input_role input_jabatan
    case "$role_choice" in
        1)
            input_role="admin_tu"
            input_jabatan="Admin TU"
            ;;
        2)
            input_role="kepala_madrasah"
            input_jabatan="Kepala Madrasah"
            ;;
        3)
            input_role="guru"
            input_jabatan="Guru"
            ;;
        4)
            input_role="bendahara"
            input_jabatan="Bendahara"
            ;;
        *)
            fail "Pilihan role tidak valid."
            return 1
            ;;
    esac

    echo ""
    info "Sedang membuat user & pegawai di database..."

    cd "$APP_DIR"

    # Escape karakter petik tunggal agar tidak merusak script PHP
    local escaped_name
    escaped_name=$(echo "$input_name" | sed "s/'/\\\\'/g")

    local php_code
    php_code=$(cat <<EOF
\$email = '$input_email';
\$user = App\Models\User::where('email', \$email)->first();
if (\$user) {
    echo "EXISTS";
    exit(1);
}

// Generate NIP
\$dobClean = str_replace('-', '', '$input_dob');
\$startWork = '$input_year' . '01';
\$genderDigit = ('$gender_choice' === 'L') ? '1' : '2';
\$nipPrefix = \$dobClean . \$startWork . \$genderDigit;
\$nip = '';
for (\$i = 1; \$i <= 999; \$i++) {
    \$tempNip = \$nipPrefix . sprintf('%03d', \$i);
    if (!App\Models\Pegawai::where('nip', \$tempNip)->exists()) {
        \$nip = \$tempNip;
        break;
    }
}
if (empty(\$nip)) {
    \$nip = \$nipPrefix . sprintf('%03d', rand(1, 999));
}

// Create User
App\Models\User::create([
    'name' => '$escaped_name',
    'email' => \$email,
    'password' => Illuminate\Support\Facades\Hash::make('$input_password'),
    'role' => '$input_role',
]);

// Create Pegawai
App\Models\Pegawai::create([
    'nip' => \$nip,
    'nama' => '$escaped_name',
    'jenis_kelamin' => '$gender_choice',
    'jabatan' => '$input_jabatan',
    'status' => 'aktif',
]);
echo "SUCCESS_NIP:" . \$nip;
EOF
)

    local result
    result=$(php artisan tinker --execute="$php_code" 2>&1)

    if [[ "$result" == *"EXISTS"* ]]; then
        fail "Email '${input_email}' sudah terdaftar!"
    elif [[ "$result" == *"SUCCESS_NIP:"* ]]; then
        local generated_nip
        generated_nip=$(echo "$result" | grep -oP "SUCCESS_NIP:\K[0-9]+")
        success "User & Pegawai '${input_name}' (${input_jabatan}) berhasil dibuat!"
        info "NIP yang dihasilkan: ${BOLD}${generated_nip}${NC}"
    else
        fail "Gagal membuat user."
        echo -e "  Detail error: ${DIM}${result}${NC}"
    fi
    echo ""
}

# ═════════════════════════════════════════════
#  8. HAPUS USER
# ═════════════════════════════════════════════
do_delete_user() {
    show_header
    echo -e "  ${BOLD}❌ HAPUS USER & PEGAWAI${NC}"
    divider
    echo ""

    read -rp "$(echo -e "  Masukkan Email User yang akan dihapus: ")" input_email
    if [[ -z "$input_email" ]]; then
        fail "Email tidak boleh kosong."
        return 1
    fi

    cd "$APP_DIR"

    # Periksa apakah user ada
    local check_code
    check_code=$(cat <<EOF
\$user = App\Models\User::where('email', '$input_email')->first();
if (\$user) {
    echo "FOUND:" . \$user->name . " (Role: " . \$user->role . ")";
} else {
    echo "NOT_FOUND";
}
EOF
)
    local check_result
    check_result=$(php artisan tinker --execute="$check_code" 2>&1)

    if [[ "$check_result" == *"NOT_FOUND"* ]]; then
        fail "User dengan email '${input_email}' tidak ditemukan."
        return 1
    fi

    # Parsing nama dan role
    local user_info
    user_info=$(echo "$check_result" | grep "FOUND:")
    echo -e "  ${YELLOW}User ditemukan:${NC} ${user_info#FOUND:}"
    echo ""
    warn "Perhatian: Menghapus user ini juga akan menghapus data pegawai yang sesuai."
    read -rp "$(echo -e "  Apakah Anda yakin ingin menghapus? (y/n): ")" confirm_delete

    if [[ "$confirm_delete" != "y" && "$confirm_delete" != "Y" ]]; then
        info "Penghapusan dibatalkan."
        return 0
    fi

    echo ""
    info "Sedang menghapus user dan data pegawai..."

    local delete_code
    delete_code=$(cat <<EOF
\$user = App\Models\User::where('email', '$input_email')->first();
if (\$user) {
    \$name = \$user->name;
    \$user->delete();
    App\Models\Pegawai::where('nama', \$name)->delete();
    echo "SUCCESS";
} else {
    echo "NOT_FOUND";
}
EOF
)

    local result
    result=$(php artisan tinker --execute="$delete_code" 2>&1)

    if [[ "$result" == *"SUCCESS"* ]]; then
        success "User dengan email '${input_email}' beserta data pegawainya berhasil dihapus!"
    else
        fail "Gagal menghapus user."
        echo -e "  Detail error: ${DIM}${result}${NC}"
    fi
    echo ""
}

# ═════════════════════════════════════════════
#  9. BACKUP WEBSITE LOKAL
# ═════════════════════════════════════════════
do_backup() {
    show_header
    echo -e "  ${BOLD}📦 BACKUP WEBSITE LOKAL${NC}"
    divider
    echo ""

    local backup_dir="/var/www/tpq_backups"
    mkdir -p "$backup_dir"
    chown -R ${APP_USER}:${APP_USER} "$backup_dir" 2>/dev/null || true

    local backup_filename="tpq_backup_$(date +%Y%m%d_%H%M%S).tar.gz"
    local backup_path="${backup_dir}/${backup_filename}"

    info "Mengaktifkan mode maintenance..."
    cd "$APP_DIR"
    php artisan down --retry=60 2>/dev/null || true

    info "Membuat arsip backup..."
    # Kita backup: .env, database/database.sqlite, dan folder storage/app
    if [[ ! -f "database/database.sqlite" ]]; then
        fail "Database database.sqlite tidak ditemukan. Backup gagal."
        php artisan up 2>/dev/null || true
        return 1
    fi

    # Buat arsip
    tar -czf "$backup_path" -C "$APP_DIR" .env database/database.sqlite storage/app 2>&1 | while read -r line; do echo -e "  ${DIM}${line}${NC}"; done

    # Cek apakah tar berhasil
    if [[ $? -eq 0 && -f "$backup_path" ]]; then
        local file_size
        file_size=$(du -h "$backup_path" 2>/dev/null | awk '{print $1}')
        success "Backup berhasil dibuat secara lokal!"
        echo -e "  Nama File: ${BOLD}${backup_filename}${NC}"
        echo -e "  Ukuran   : ${BOLD}${file_size:-N/A}${NC}"
        echo -e "  Lokasi   : ${DIM}${backup_path}${NC}"
    else
        fail "Gagal membuat arsip backup lokal."
        php artisan up 2>/dev/null || true
        return 1
    fi

    info "Menonaktifkan mode maintenance..."
    php artisan up 2>/dev/null || true
    echo ""
}

# ═════════════════════════════════════════════
#  10. RESTORE WEBSITE LOKAL
# ═════════════════════════════════════════════
do_restore() {
    show_header
    echo -e "  ${BOLD}⏪ RESTORE WEBSITE LOKAL${NC}"
    divider
    echo ""

    local backup_dir="/var/www/tpq_backups"
    mkdir -p "$backup_dir"

    local selected_backup=""

    # List files in backup_dir
    local files=()
    while IFS= read -r line; do
        files+=("$line")
    done < <(find "$backup_dir" -maxdepth 1 -name "tpq_backup_*.tar.gz" -printf "%f\n" 2>/dev/null | sort -r)

    if [[ ${#files[@]} -eq 0 ]]; then
        warn "Tidak ditemukan file backup (*.tar.gz) di ${backup_dir}."
        read -rp "$(echo -e "  Masukkan path absolut file backup manual: ")" manual_path
        if [[ -f "$manual_path" ]]; then
            selected_backup="$manual_path"
        else
            fail "File tidak ditemukan di ${manual_path}."
            return 1
        fi
    else
        echo -e "  Daftar backup lokal yang tersedia:"
        local idx=1
        for file in "${files[@]}"; do
            echo -e "    ${CYAN}${idx}${NC}) ${file}"
            idx=$((idx + 1))
        done
        read -rp "$(echo -e "  Pilih nomor backup [1-$((idx-1))]: ")" file_choice
        if [[ "$file_choice" -ge 1 && "$file_choice" -lt "$idx" ]]; then
            selected_backup="${backup_dir}/${files[$((file_choice-1))]}"
        else
            fail "Pilihan tidak valid."
            return 1
        fi
    fi

    # Mulai proses restore
    echo ""
    echo -e "  ${RED}${BOLD}PERINGATAN: Operasi ini akan menimpa database, file .env, dan asset storage Anda saat ini!${NC}"
    read -rp "$(echo -e "  ${RED}${BOLD}Apakah Anda yakin ingin melanjutkan? Ketik 'RESTORE' untuk konfirmasi: ${NC}")" confirm_restore
    if [[ "$confirm_restore" != "RESTORE" ]]; then
        info "Restore dibatalkan."
        return 0
    fi

    echo ""
    info "Memulai pemulihan dari ${selected_backup}..."

    # Buat backup penyelamat .env & database saat ini (safety backup)
    if [[ -f "${APP_DIR}/database/database.sqlite" ]]; then
        cp "${APP_DIR}/database/database.sqlite" "${APP_DIR}/database/database.sqlite.bak" 2>/dev/null || true
    fi
    if [[ -f "${APP_DIR}/.env" ]]; then
        cp "${APP_DIR}/.env" "${APP_DIR}/.env.bak" 2>/dev/null || true
    fi

    info "Mengaktifkan mode maintenance..."
    cd "$APP_DIR"
    php artisan down --retry=60 2>/dev/null || true

    info "Mengekstrak file backup..."
    tar -xzf "$selected_backup" -C "$APP_DIR"
    if [[ $? -ne 0 ]]; then
        fail "Gagal mengekstrak backup! Mengembalikan cadangan sementara..."
        if [[ -f "${APP_DIR}/database/database.sqlite.bak" ]]; then
            mv "${APP_DIR}/database/database.sqlite.bak" "${APP_DIR}/database/database.sqlite"
        fi
        if [[ -f "${APP_DIR}/.env.bak" ]]; then
            mv "${APP_DIR}/.env.bak" "${APP_DIR}/.env"
        fi
        php artisan up 2>/dev/null || true
        return 1
    fi

    # Hapus file safety backup jika sukses
    rm -f "${APP_DIR}/database/database.sqlite.bak"
    rm -f "${APP_DIR}/.env.bak"

    info "Mengatur perizinan file..."
    chown -R ${APP_USER}:${APP_USER} "$APP_DIR"
    chmod -R 775 "${APP_DIR}/storage" "${APP_DIR}/bootstrap/cache"
    chmod 664 "${APP_DIR}/database/database.sqlite" 2>/dev/null || true

    # Jalankan migrasi opsional & rebuild cache
    info "Menjalankan migrasi database..."
    php artisan migrate --force --no-interaction 2>&1 | tail -5

    info "Membangun ulang cache Laravel..."
    php artisan optimize:clear --no-interaction 2>/dev/null
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
    php artisan event:cache --no-interaction 2>/dev/null || true

    # Restart layanan
    info "Merestart PHP-FPM & Nginx..."
    systemctl restart php${PHP_VERSION}-fpm 2>/dev/null || true
    systemctl restart nginx 2>/dev/null || true
    supervisorctl restart tpq-worker:* 2>/dev/null || true

    # Nonaktifkan maintenance mode
    php artisan up 2>/dev/null || true

    success "Website berhasil direstore sepenuhnya!"
    echo ""
}

# ═════════════════════════════════════════════
#  MENU UTAMA
# ═════════════════════════════════════════════
show_menu() {
    show_header
    load_config

    echo -e "  ${BOLD}Domain${NC}: ${CYAN}${DOMAIN:-Belum dikonfigurasi}${NC}"
    echo ""
    divider
    echo ""
    echo -e "  ${BOLD}Pilih menu:${NC}"
    echo ""
    echo -e "    ${CYAN}${BOLD}1${NC})  📊  Status Website"
    echo -e "    ${CYAN}${BOLD}2${NC})  🔄  Cek Update & Terapkan dari GitHub"
    echo -e "    ${CYAN}${BOLD}3${NC})  🔁  Reinstall Website (reset ke awal)"
    echo -e "    ${CYAN}${BOLD}4${NC})  🗑️   Uninstall Website"
    echo -e "    ${CYAN}${BOLD}5${NC})  ♻️   Restart Semua Layanan"
    echo -e "    ${CYAN}${BOLD}6${NC})  📋  Lihat Log Laravel"
    echo -e "    ${CYAN}${BOLD}7${NC})  👤  Buat User & Pegawai Baru"
    echo -e "    ${CYAN}${BOLD}8${NC})  ❌  Hapus User & Pegawai"
    echo -e "    ${CYAN}${BOLD}9${NC})  📦  Backup Website (Lokal)"
    echo -e "    ${CYAN}${BOLD}10${NC}) ⏪  Restore Website (Lokal)"
    echo -e "    ${CYAN}${BOLD}0${NC})  🚪  Keluar"
    echo ""
    divider
    echo ""
}

interactive_menu() {
    while true; do
        show_menu

        read -rp "$(echo -e "  ${YELLOW}Masukkan pilihan [0-10]: ${NC}")" choice
        echo ""

        case "$choice" in
            1) do_status;    read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            2) check_installed; do_update;    read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            3) check_installed; do_reinstall; read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            4) check_installed; do_uninstall; read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            5) check_installed; do_restart;   read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            6) check_installed; do_logs;      read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            7) check_installed; do_create_user; read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            8) check_installed; do_delete_user; read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            9) check_installed; do_backup;      read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            10) check_installed; do_restore;    read -rp "$(echo -e "  ${DIM}Tekan Enter untuk kembali...${NC}")" ;;
            0|q|Q|exit)
                echo -e "  ${GREEN}Sampai jumpa! 👋${NC}"
                echo ""
                exit 0
                ;;
            *)
                warn "Pilihan tidak valid. Silakan pilih 0-10."
                sleep 1
                ;;
        esac
    done
}

# ═════════════════════════════════════════════
#  ENTRYPOINT
# ═════════════════════════════════════════════
check_root

# Mendukung argument langsung: tpqctl.sh status|update|reinstall|uninstall
case "${1:-}" in
    status)
        check_installed
        do_status
        ;;
    update)
        check_installed
        do_update
        ;;
    reinstall)
        check_installed
        do_reinstall
        ;;
    uninstall)
        check_installed
        do_uninstall
        ;;
    restart)
        check_installed
        do_restart
        ;;
    logs)
        check_installed
        do_logs
        ;;
    create-user)
        check_installed
        do_create_user
        ;;
    delete-user)
        check_installed
        do_delete_user
        ;;
    backup)
        check_installed
        do_backup
        ;;
    restore)
        check_installed
        do_restore
        ;;
    "")
        interactive_menu
        ;;
    *)
        echo -e "${RED}Perintah tidak dikenal: ${1}${NC}"
        echo ""
        echo "Penggunaan:"
        echo "  sudo bash tpqctl.sh              # Menu interaktif"
        echo "  sudo bash tpqctl.sh status        # Cek status"
        echo "  sudo bash tpqctl.sh update        # Update dari GitHub"
        echo "  sudo bash tpqctl.sh reinstall     # Reinstall"
        echo "  sudo bash tpqctl.sh uninstall     # Uninstall"
        echo "  sudo bash tpqctl.sh restart       # Restart layanan"
        echo "  sudo bash tpqctl.sh logs          # Lihat log"
        echo "  sudo bash tpqctl.sh create-user   # Buat user baru"
        echo "  sudo bash tpqctl.sh delete-user   # Hapus user"
        echo "  sudo bash tpqctl.sh backup        # Backup website"
        echo "  sudo bash tpqctl.sh restore       # Restore website"
        exit 1
        ;;
esac
