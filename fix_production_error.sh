#!/bin/bash

# Production Fix Script untuk KodeNomenklatur Error
# Jalankan di server production Laravel

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

echo "============================================"
echo "  Laravel Production Error Fix Script"
echo "  Target: KodeNomenklatur Class Not Found"
echo "============================================"

# Cek apakah di Laravel project
if [ ! -f "artisan" ]; then
    log_error "File artisan tidak ditemukan. Pastikan script dijalankan di root Laravel project."
    exit 1
fi

log_info "Memulai perbaikan production error..."

# 1. Verifikasi file model
echo ""
log_info "1. Memverifikasi file model..."

if [ -f "app/Models/KodeNomenklatur.php" ]; then
    log_success "✓ File app/Models/KodeNomenklatur.php ditemukan"
    
    # Cek permission file
    if [ -r "app/Models/KodeNomenklatur.php" ]; then
        log_success "✓ File readable"
    else
        log_warning "File permission mungkin bermasalah, memperbaiki..."
        chmod 644 app/Models/KodeNomenklatur.php
    fi
    
    # Cek syntax PHP
    if php -l app/Models/KodeNomenklatur.php > /dev/null 2>&1; then
        log_success "✓ Syntax PHP valid"
    else
        log_error "✗ Syntax PHP error di file model"
        php -l app/Models/KodeNomenklatur.php
    fi
else
    log_error "✗ File app/Models/KodeNomenklatur.php TIDAK DITEMUKAN!"
    echo "Kemungkinan masalah deployment. Pastikan file ter-upload ke server."
    exit 1
fi

# 2. Cek file controller
echo ""
log_info "2. Memverifikasi controller..."

if [ -f "app/Http/Controllers/KodeNomenklaturController.php" ]; then
    log_success "✓ Controller file ditemukan"
    
    # Cek use statement
    if grep -q "use App\\Models\\KodeNomenklatur;" app/Http/Controllers/KodeNomenklaturController.php; then
        log_success "✓ Use statement benar"
    else
        log_error "✗ Use statement salah atau tidak ditemukan"
    fi
else
    log_error "✗ Controller file tidak ditemukan"
    exit 1
fi

# 3. Regenerate autoload
echo ""
log_info "3. Regenerating composer autoload..."

if [ -f "composer.json" ]; then
    # Clear composer cache dulu
    if command -v composer &> /dev/null; then
        composer clear-cache
        log_info "Composer cache cleared"
        
        # Regenerate autoload
        composer dump-autoload --optimize --no-cache
        log_success "✓ Composer autoload regenerated"
    else
        log_error "Composer command tidak ditemukan"
        exit 1
    fi
else
    log_error "composer.json tidak ditemukan"
    exit 1
fi

# 4. Clear Laravel cache
echo ""
log_info "4. Clearing Laravel caches..."

php artisan cache:clear > /dev/null 2>&1 && log_success "✓ Application cache cleared"
php artisan config:clear > /dev/null 2>&1 && log_success "✓ Config cache cleared"
php artisan route:clear > /dev/null 2>&1 && log_success "✓ Route cache cleared"
php artisan view:clear > /dev/null 2>&1 && log_success "✓ View cache cleared"

# 5. Regenerate optimized cache
echo ""
log_info "5. Regenerating optimized caches..."

php artisan config:cache > /dev/null 2>&1 && log_success "✓ Config cached"
php artisan route:cache > /dev/null 2>&1 && log_success "✓ Routes cached"

# 6. Test class loading
echo ""
log_info "6. Testing model class loading..."

php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
    
    if (class_exists('App\\\\Models\\\\KodeNomenklatur')) {
        echo 'SUCCESS: KodeNomenklatur class dapat di-load!\\n';
        \$count = App\\\\Models\\\\KodeNomenklatur::count();
        echo 'Total records: ' . \$count . '\\n';
    } else {
        echo 'ERROR: KodeNomenklatur class tidak dapat di-load!\\n';
        exit(1);
    }
} catch (Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . '\\n';
    exit(1);
}
"

if [ $? -eq 0 ]; then
    log_success "✓ Model instantiation berhasil!"
else
    log_error "✗ Model instantiation gagal!"
    exit 1
fi

# 7. Fix permission jika perlu
echo ""
log_info "7. Checking dan fixing permissions..."

# Fix permission untuk file-file penting
chmod 644 app/Models/*.php 2>/dev/null && log_success "✓ Model file permissions fixed"
chmod 644 app/Http/Controllers/*.php 2>/dev/null && log_success "✓ Controller file permissions fixed"
chmod -R 775 storage/ 2>/dev/null && log_success "✓ Storage permissions fixed"
chmod -R 775 bootstrap/cache/ 2>/dev/null && log_success "✓ Bootstrap cache permissions fixed"

echo ""
echo "============================================"
log_success "🎉 PERBAIKAN SELESAI!"
echo ""
echo "Langkah selanjutnya:"
echo "1. Test akses web application"
echo "2. Monitor error logs: tail -f storage/logs/laravel.log"
echo "3. Jika masih error, cek web server error log"
echo "============================================" 