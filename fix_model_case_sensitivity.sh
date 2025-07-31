#!/bin/bash

# Script untuk memperbaiki masalah case sensitivity model di production
# Mengatasi konflik Kodenomenklatur vs KodeNomenklatur

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

echo "============================================="
echo "    Model Case Sensitivity Fix Script"
echo "    Kodenomenklatur → KodeNomenklatur"
echo "============================================="

# Cek apakah di Laravel project
if [ ! -f "artisan" ]; then
    log_error "File artisan tidak ditemukan. Pastikan script dijalankan di root Laravel project."
    exit 1
fi

log_info "Memulai perbaikan case sensitivity untuk model..."

# 1. Backup file lama jika ada
echo ""
log_info "1. Backup file model lama (jika ada)..."

BACKUP_DIR="backup_models_$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

# Backup file dengan nama lama jika ada
if [ -f "app/Models/Kodenomenklatur.php" ]; then
    log_warning "Ditemukan file model lama: Kodenomenklatur.php"
    cp app/Models/Kodenomenklatur.php $BACKUP_DIR/
    log_success "✓ Backup Kodenomenklatur.php"
fi

if [ -f "app/Models/KodenomenklaturDetail.php" ]; then
    log_warning "Ditemukan file model lama: KodenomenklaturDetail.php"
    cp app/Models/KodenomenklaturDetail.php $BACKUP_DIR/
    log_success "✓ Backup KodenomenklaturDetail.php"
fi

# 2. Hapus file dengan nama salah
echo ""
log_info "2. Menghapus file model dengan case sensitivity salah..."

if [ -f "app/Models/Kodenomenklatur.php" ]; then
    rm app/Models/Kodenomenklatur.php
    log_success "✓ Dihapus: Kodenomenklatur.php"
fi

if [ -f "app/Models/KodenomenklaturDetail.php" ]; then
    rm app/Models/KodenomenklaturDetail.php
    log_success "✓ Dihapus: KodenomenklaturDetail.php"
fi

# 3. Verifikasi file yang benar ada
echo ""
log_info "3. Memverifikasi file model yang benar..."

if [ ! -f "app/Models/KodeNomenklatur.php" ]; then
    log_error "✗ File KodeNomenklatur.php tidak ditemukan!"
    echo "Pastikan file sudah ter-upload dari repository yang benar."
    exit 1
else
    log_success "✓ File KodeNomenklatur.php ditemukan"
fi

if [ ! -f "app/Models/KodeNomenklaturDetail.php" ]; then
    log_error "✗ File KodeNomenklaturDetail.php tidak ditemukan!"
    echo "Pastikan file sudah ter-upload dari repository yang benar."
    exit 1
else
    log_success "✓ File KodeNomenklaturDetail.php ditemukan"
fi

# 4. Cek class name di dalam file
echo ""
log_info "4. Memverifikasi class names dalam file..."

# Cek KodeNomenklatur
KODE_CLASS=$(grep "^class " app/Models/KodeNomenklatur.php | head -1)
if echo "$KODE_CLASS" | grep -q "class KodeNomenklatur"; then
    log_success "✓ Class name KodeNomenklatur benar"
else
    log_error "✗ Class name dalam KodeNomenklatur.php salah: $KODE_CLASS"
    exit 1
fi

# Cek KodeNomenklaturDetail  
DETAIL_CLASS=$(grep "^class " app/Models/KodeNomenklaturDetail.php | head -1)
if echo "$DETAIL_CLASS" | grep -q "class KodeNomenklaturDetail"; then
    log_success "✓ Class name KodeNomenklaturDetail benar"
else
    log_error "✗ Class name dalam KodeNomenklaturDetail.php salah: $DETAIL_CLASS"
    exit 1
fi

# 5. Clear composer cache dan regenerate autoload
echo ""
log_info "5. Regenerating composer autoload..."

if command -v composer &> /dev/null; then
    composer clear-cache
    composer dump-autoload --optimize --no-cache
    log_success "✓ Composer autoload regenerated"
else
    log_error "Composer command tidak ditemukan"
    exit 1
fi

# 6. Clear Laravel cache
echo ""
log_info "6. Clearing Laravel caches..."

php artisan cache:clear > /dev/null 2>&1 && log_success "✓ Application cache cleared"
php artisan config:clear > /dev/null 2>&1 && log_success "✓ Config cache cleared" 
php artisan route:clear > /dev/null 2>&1 && log_success "✓ Route cache cleared"
php artisan view:clear > /dev/null 2>&1 && log_success "✓ View cache cleared"

# 7. Test model loading
echo ""
log_info "7. Testing model class loading..."

# Test KodeNomenklatur
php -r "
require_once 'vendor/autoload.php';
if (class_exists('App\\Models\\KodeNomenklatur')) {
    echo 'SUCCESS: KodeNomenklatur class loaded!\\n';
} else {
    echo 'ERROR: KodeNomenklatur class not found!\\n';
    exit(1);
}
" 

if [ $? -ne 0 ]; then
    log_error "✗ KodeNomenklatur gagal di-load"
    exit 1
fi

# Test KodeNomenklaturDetail
php -r "
require_once 'vendor/autoload.php';
if (class_exists('App\\Models\\KodeNomenklaturDetail')) {
    echo 'SUCCESS: KodeNomenklaturDetail class loaded!\\n';
} else {
    echo 'ERROR: KodeNomenklaturDetail class not found!\\n';
    exit(1);
}
"

if [ $? -ne 0 ]; then
    log_error "✗ KodeNomenklaturDetail gagal di-load"
    exit 1
fi

log_success "✓ Semua model berhasil di-load!"

# 8. Test database seeder
echo ""
log_info "8. Testing UserSeeder..."

php artisan db:seed --class=UserSeeder 2>/dev/null

if [ $? -eq 0 ]; then
    log_success "✓ UserSeeder berhasil dijalankan!"
else
    log_warning "UserSeeder mungkin gagal karena duplicate data. Test dengan migrate:fresh --seed"
fi

echo ""
echo "============================================="
log_success "🎉 PERBAIKAN SELESAI!"
echo ""
echo "File backup tersimpan di: $BACKUP_DIR/"
echo ""
echo "Langkah selanjutnya:"
echo "1. Test akses web application"
echo "2. Jika perlu reset database: php artisan migrate:fresh --seed"
echo "3. Monitor logs: tail -f storage/logs/laravel.log"
echo "=============================================" 