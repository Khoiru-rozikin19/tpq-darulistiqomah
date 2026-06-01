#!/usr/bin/env bash
#
# ============================================================================
#  SETUP SCRIPT - Sistem Keuangan Madrasah Darul Istiqomah
#  Target: Ubuntu 24.04 LTS (DigitalOcean VPS)
#  Stack : Nginx + PHP 8.4 + SQLite + Node.js 22 + Let's Encrypt SSL
# ============================================================================
#
#  PETUNJUK PENGGUNAAN:
#  1. Upload project ke VPS (misal via git clone atau scp)
#  2. Jalankan: chmod +x setup.sh && sudo bash setup.sh
#  3. Script akan meminta input nama domain
#  4. Tunggu sampai selesai, website siap diakses!
#
#  AKUN DEFAULT SETELAH DEPLOY:
#  - Admin TU    : admin@darulistiqomah.com  / password
#  - Kepala      : kepala@darulistiqomah.com / password
#
# ============================================================================

set -euo pipefail

# ─────────────────────────────────────────────
#  WARNA OUTPUT
# ─────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# ─────────────────────────────────────────────
#  FUNGSI UTILITAS
# ─────────────────────────────────────────────
info()    { echo -e "${BLUE}[INFO]${NC} $1"; }
success() { echo -e "${GREEN}[  OK]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[FAIL]${NC} $1"; exit 1; }
step()    { echo -e "\n${CYAN}${BOLD}━━━ $1 ━━━${NC}\n"; }

# ─────────────────────────────────────────────
#  CEK ROOT
# ─────────────────────────────────────────────
if [[ $EUID -ne 0 ]]; then
    error "Script ini harus dijalankan sebagai root. Gunakan: sudo bash setup.sh"
fi

# ─────────────────────────────────────────────
#  INPUT DOMAIN
# ─────────────────────────────────────────────
echo ""
echo -e "${BOLD}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${BOLD}║   🕌  Installer Sistem Keuangan Darul Istiqomah  🕌     ║${NC}"
echo -e "${BOLD}║   Ubuntu 24.04 + Nginx + PHP 8.4 + SQLite + SSL         ║${NC}"
echo -e "${BOLD}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""

read -rp "$(echo -e "${YELLOW}Masukkan nama domain (contoh: keuangan.darulistiqomah.com): ${NC}")" DOMAIN

if [[ -z "$DOMAIN" ]]; then
    error "Domain tidak boleh kosong!"
fi

# Validasi format domain sederhana
if [[ ! "$DOMAIN" =~ ^[a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]*[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$ ]]; then
    error "Format domain tidak valid: $DOMAIN"
fi

read -rp "$(echo -e "${YELLOW}Apakah ingin mengaktifkan SSL/HTTPS via Let's Encrypt? (y/n) [y]: ${NC}")" ENABLE_SSL
ENABLE_SSL=${ENABLE_SSL:-y}

if [[ "$ENABLE_SSL" == "y" || "$ENABLE_SSL" == "Y" ]]; then
    read -rp "$(echo -e "${YELLOW}Masukkan email untuk sertifikat SSL (contoh: admin@email.com): ${NC}")" SSL_EMAIL
    if [[ -z "$SSL_EMAIL" ]]; then
        error "Email SSL tidak boleh kosong jika SSL diaktifkan!"
    fi
fi

echo ""
info "Domain        : ${BOLD}$DOMAIN${NC}"
info "SSL/HTTPS     : ${BOLD}$([ "$ENABLE_SSL" == "y" ] && echo "Ya" || echo "Tidak")${NC}"
info "Direktori     : ${BOLD}/var/www/tpq${NC}"
echo ""
read -rp "$(echo -e "${YELLOW}Lanjutkan instalasi? (y/n) [y]: ${NC}")" CONFIRM
CONFIRM=${CONFIRM:-y}

if [[ "$CONFIRM" != "y" && "$CONFIRM" != "Y" ]]; then
    info "Instalasi dibatalkan."
    exit 0
fi

# ─────────────────────────────────────────────
#  VARIABEL KONFIGURASI
# ─────────────────────────────────────────────
APP_DIR="/var/www/tpq"
APP_USER="www-data"
PHP_VERSION="8.4"

# ─────────────────────────────────────────────
#  STEP 1: UPDATE SISTEM & INSTALL DEPENDENSI
# ─────────────────────────────────────────────
step "1/9 - Memperbarui Sistem & Menginstal Dependensi Dasar"

export DEBIAN_FRONTEND=noninteractive

apt-get update -y
apt-get upgrade -y
apt-get install -y \
    software-properties-common \
    curl \
    wget \
    unzip \
    git \
    rsync \
    acl \
    ufw \
    sqlite3 \
    supervisor

success "Dependensi dasar terinstal."

# ─────────────────────────────────────────────
#  STEP 2: INSTALL PHP 8.4
# ─────────────────────────────────────────────
step "2/9 - Menginstal PHP ${PHP_VERSION} & Ekstensi yang Dibutuhkan"

# Tambah PPA Ondrej jika PHP 8.4 belum tersedia
if ! command -v php${PHP_VERSION} &> /dev/null; then
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y
fi

apt-get install -y \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-common \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-readline \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-fileinfo

# Tuning php.ini untuk production
PHP_INI="/etc/php/${PHP_VERSION}/fpm/php.ini"
sed -i "s/^upload_max_filesize.*/upload_max_filesize = 10M/" "$PHP_INI"
sed -i "s/^post_max_size.*/post_max_size = 12M/" "$PHP_INI"
sed -i "s/^memory_limit.*/memory_limit = 256M/" "$PHP_INI"
sed -i "s/^max_execution_time.*/max_execution_time = 60/" "$PHP_INI"
sed -i "s/^;cgi.fix_pathinfo=.*/cgi.fix_pathinfo=0/" "$PHP_INI"

systemctl restart php${PHP_VERSION}-fpm
systemctl enable php${PHP_VERSION}-fpm

# Set default CLI php version
update-alternatives --set php /usr/bin/php${PHP_VERSION} || true

# Stop dan disable versi php-fpm lama agar hemat RAM pada VPS 1GB
for old_ver in "8.1" "8.2" "8.3"; do
    if [[ "$old_ver" != "$PHP_VERSION" ]]; then
        if systemctl is-active --quiet php${old_ver}-fpm; then
            info "Menghentikan layanan php${old_ver}-fpm untuk menghemat RAM..."
            systemctl stop php${old_ver}-fpm || true
            systemctl disable php${old_ver}-fpm || true
        fi
    fi
done

success "PHP ${PHP_VERSION}-FPM terinstal dan dikonfigurasi."

# ─────────────────────────────────────────────
#  STEP 3: INSTALL NGINX
# ─────────────────────────────────────────────
step "3/9 - Menginstal & Mengkonfigurasi Nginx"

apt-get install -y nginx

# Hapus default site
rm -f /etc/nginx/sites-enabled/default

# Buat konfigurasi Nginx untuk Laravel
cat > /etc/nginx/sites-available/tpq <<NGINX_CONF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};

    root ${APP_DIR}/public;
    index index.php index.html;

    charset utf-8;
    client_max_body_size 12M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript image/svg+xml;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # Cache static assets
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    # Deny dotfiles
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
}
NGINX_CONF

ln -sf /etc/nginx/sites-available/tpq /etc/nginx/sites-enabled/tpq

# Test konfigurasi Nginx
nginx -t || error "Konfigurasi Nginx tidak valid!"

systemctl restart nginx
systemctl enable nginx

success "Nginx terinstal dan dikonfigurasi untuk ${DOMAIN}."

# ─────────────────────────────────────────────
#  STEP 4: INSTALL NODE.JS 22 LTS
# ─────────────────────────────────────────────
step "4/9 - Menginstal Node.js 22 LTS & NPM"

if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs
fi

success "Node.js $(node -v) dan NPM $(npm -v) terinstal."

# ─────────────────────────────────────────────
#  STEP 5: INSTALL COMPOSER
# ─────────────────────────────────────────────
step "5/9 - Menginstal Composer"

if ! command -v composer &> /dev/null; then
    cd /tmp
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [[ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]]; then
        rm composer-setup.php
        error "Checksum Composer installer tidak cocok! Instalasi dibatalkan."
    fi

    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi

success "Composer $(composer --version --no-ansi 2>/dev/null | head -1) terinstal."

# ─────────────────────────────────────────────
#  STEP 6: SETUP APLIKASI LARAVEL
# ─────────────────────────────────────────────
step "6/9 - Menyiapkan Aplikasi Laravel"

# Pastikan direktori app sudah ada (diasumsikan sudah di-clone/upload)
if [[ ! -f "${APP_DIR}/artisan" ]]; then
    # Jika script dijalankan dari dalam project directory
    SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
    if [[ -f "${SCRIPT_DIR}/artisan" ]]; then
        info "Mendeteksi project di ${SCRIPT_DIR}, menyalin ke ${APP_DIR}..."
        mkdir -p "${APP_DIR}"
        rsync -a --exclude='node_modules' --exclude='vendor' --exclude='.env' "${SCRIPT_DIR}/" "${APP_DIR}/"
    else
        error "File artisan tidak ditemukan di ${APP_DIR}. Pastikan project sudah di-upload/clone ke ${APP_DIR} terlebih dahulu."
    fi
fi

cd "${APP_DIR}"

# 6a. Install dependensi PHP (production)
info "Menginstal dependensi Composer (production)..."
composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
success "Dependensi Composer terinstal."

# 6b. Install dependensi Node & Build Vite assets
info "Menginstal dependensi NPM dan melakukan build Vite..."
npm ci --ignore-scripts 2>&1 | tail -3
npm run build 2>&1 | tail -5
success "Asset Vite berhasil di-build."

# 6c. Setup file .env
if [[ ! -f "${APP_DIR}/.env" ]]; then
    cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
    info "File .env dibuat dari .env.example"
fi

# 6d. Konfigurasi .env untuk production
info "Mengkonfigurasi .env untuk production..."

# Tentukan APP_URL berdasarkan SSL
if [[ "$ENABLE_SSL" == "y" || "$ENABLE_SSL" == "Y" ]]; then
    APP_URL_VALUE="https://${DOMAIN}"
else
    APP_URL_VALUE="http://${DOMAIN}"
fi

sed -i "s|^APP_NAME=.*|APP_NAME=\"Sistem Keuangan Darul Istiqomah\"|" .env
sed -i "s|^APP_ENV=.*|APP_ENV=production|" .env
sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" .env
sed -i "s|^APP_URL=.*|APP_URL=${APP_URL_VALUE}|" .env
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|" .env
sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=database|" .env
sed -i "s|^CACHE_STORE=.*|CACHE_STORE=database|" .env
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=database|" .env

success ".env dikonfigurasi untuk production."

# 6e. Generate App Key
php artisan key:generate --force --no-interaction
success "APP_KEY telah di-generate."

# 6f. Buat file database SQLite
info "Menyiapkan database SQLite..."
touch "${APP_DIR}/database/database.sqlite"
success "File database SQLite dibuat."

# 6g. Jalankan migrasi dan seeder
info "Menjalankan migrasi database..."
php artisan migrate --force --no-interaction
success "Migrasi database berhasil."

info "Menjalankan seeder (data awal)..."
php artisan db:seed --force --no-interaction
success "Data awal berhasil di-seed."

# 6h. Buat storage link
php artisan storage:link --force --no-interaction 2>/dev/null || true
success "Storage link dibuat."

# 6i. Cache konfigurasi untuk production
info "Melakukan optimasi cache Laravel..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
php artisan event:cache --no-interaction 2>/dev/null || true
success "Cache Laravel dioptimalkan."

# ─────────────────────────────────────────────
#  STEP 7: ATUR PERMISSION FILE
# ─────────────────────────────────────────────
step "7/9 - Mengatur Permission & Kepemilikan File"

# Set ownership
chown -R ${APP_USER}:${APP_USER} "${APP_DIR}"

# Set permission untuk direktori dan file
find "${APP_DIR}" -type f -exec chmod 644 {} \;
find "${APP_DIR}" -type d -exec chmod 755 {} \;

# Storage dan cache harus writable
chmod -R 775 "${APP_DIR}/storage"
chmod -R 775 "${APP_DIR}/bootstrap/cache"
chmod 664 "${APP_DIR}/database/database.sqlite"

# Pastikan artisan executable
chmod 755 "${APP_DIR}/artisan"

success "Permission dan kepemilikan file telah diatur."

# ─────────────────────────────────────────────
#  STEP 8: KONFIGURASI FIREWALL
# ─────────────────────────────────────────────
step "8/9 - Mengkonfigurasi Firewall (UFW)"

ufw --force reset > /dev/null 2>&1
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

success "Firewall dikonfigurasi (SSH + Nginx diizinkan)."

# ─────────────────────────────────────────────
#  STEP 9: SSL VIA LET'S ENCRYPT (OPSIONAL)
# ─────────────────────────────────────────────
if [[ "$ENABLE_SSL" == "y" || "$ENABLE_SSL" == "Y" ]]; then
    step "9/9 - Menginstal Sertifikat SSL (Let's Encrypt)"

    apt-get install -y certbot python3-certbot-nginx

    info "Meminta sertifikat SSL untuk ${DOMAIN}..."
    info "Pastikan DNS domain ${DOMAIN} sudah mengarah ke IP VPS ini!"
    echo ""

    certbot --nginx \
        -d "${DOMAIN}" \
        --non-interactive \
        --agree-tos \
        --email "${SSL_EMAIL}" \
        --redirect \
        || {
            warn "SSL gagal diinstal. Kemungkinan DNS belum mengarah ke server ini."
            warn "Anda bisa menjalankan ulang setelah DNS terkonfigurasi:"
            warn "  sudo certbot --nginx -d ${DOMAIN} --email ${SSL_EMAIL}"
        }

    # Auto-renewal cron
    systemctl enable certbot.timer 2>/dev/null || true

    success "SSL Let's Encrypt berhasil dikonfigurasi."
else
    step "9/9 - SSL Dilewati (HTTP Only)"
    info "SSL tidak diaktifkan. Website akan diakses via HTTP."
    info "Untuk mengaktifkan nanti, jalankan:"
    info "  sudo apt install certbot python3-certbot-nginx"
    info "  sudo certbot --nginx -d ${DOMAIN}"
fi

# ─────────────────────────────────────────────
#  SETUP SUPERVISOR (untuk queue worker)
# ─────────────────────────────────────────────
info "Mengkonfigurasi Supervisor untuk Queue Worker..."

cat > /etc/supervisor/conf.d/tpq-worker.conf <<SUPERVISOR_CONF
[program:tpq-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${APP_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${APP_USER}
numprocs=1
redirect_stderr=true
stdout_logfile=${APP_DIR}/storage/logs/worker.log
stopwaitsecs=3600
SUPERVISOR_CONF

supervisorctl reread
supervisorctl update
supervisorctl start tpq-worker:* 2>/dev/null || true

success "Supervisor queue worker dikonfigurasi."

# ─────────────────────────────────────────────
#  SETUP CRON (scheduler)
# ─────────────────────────────────────────────
info "Mengkonfigurasi Laravel Scheduler Cron..."

CRON_JOB="* * * * * cd ${APP_DIR} && php artisan schedule:run >> /dev/null 2>&1"
(crontab -u ${APP_USER} -l 2>/dev/null | grep -v "artisan schedule:run"; echo "${CRON_JOB}") | crontab -u ${APP_USER} -

success "Cron scheduler dikonfigurasi."

# ─────────────────────────────────────────────
#  INSTALL TPQCTL (Control Panel)
# ─────────────────────────────────────────────
info "Menginstal TPQ Control Panel (tpqctl)..."

if [[ -f "${APP_DIR}/tpqctl.sh" ]]; then
    cp "${APP_DIR}/tpqctl.sh" /usr/local/bin/tpqctl
    chmod +x /usr/local/bin/tpqctl
    success "Perintah 'tpqctl' tersedia secara global."
fi

# Simpan konfigurasi untuk tpqctl
cat > "${APP_DIR}/.tpqctl.conf" <<TPQCONF
# TPQ Control Panel Config — auto-generated by setup.sh
DOMAIN="${DOMAIN}"
GIT_REMOTE=""
TPQCONF
chown ${APP_USER}:${APP_USER} "${APP_DIR}/.tpqctl.conf" 2>/dev/null || true

# ─────────────────────────────────────────────
#  SELESAI!
# ─────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}╔═══════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}${BOLD}║                                                           ║${NC}"
echo -e "${GREEN}${BOLD}║   ✅  INSTALASI SELESAI DENGAN SUKSES!                    ║${NC}"
echo -e "${GREEN}${BOLD}║                                                           ║${NC}"
echo -e "${GREEN}${BOLD}╚═══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${CYAN}${BOLD}Informasi Akses:${NC}"
echo -e "  🌐 URL Website     : ${BOLD}${APP_URL_VALUE}${NC}"
echo -e "  📂 Direktori App   : ${BOLD}${APP_DIR}${NC}"
echo -e "  🗄️  Database        : ${BOLD}${APP_DIR}/database/database.sqlite${NC}"
echo ""
echo -e "${CYAN}${BOLD}Akun Login Default:${NC}"
echo -e "  👤 Admin TU        : ${BOLD}admin@darulistiqomah.com${NC}  /  ${BOLD}password${NC}"
echo -e "  👤 Kepala Madrasah : ${BOLD}kepala@darulistiqomah.com${NC} /  ${BOLD}password${NC}"
echo ""
echo -e "${YELLOW}${BOLD}⚠️  PENTING: Segera ganti password default setelah login!${NC}"
echo ""
echo -e "${CYAN}${BOLD}Panel Kontrol (tpqctl):${NC}"
echo -e "  📊 Buka panel      : ${BOLD}sudo tpqctl${NC}"
echo -e "  📊 Cek status      : ${BOLD}sudo tpqctl status${NC}"
echo -e "  🔄 Update GitHub   : ${BOLD}sudo tpqctl update${NC}"
echo -e "  🔁 Reinstall       : ${BOLD}sudo tpqctl reinstall${NC}"
echo -e "  🗑️  Uninstall       : ${BOLD}sudo tpqctl uninstall${NC}"
echo ""

