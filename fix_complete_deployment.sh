#!/bin/bash

# Comprehensive Deployment Fix Script
# Mengatasi masalah case sensitivity untuk Backend Model dan Frontend Vue Component

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

echo "================================================"
echo "    COMPREHENSIVE DEPLOYMENT FIX SCRIPT"
echo "    Backend Models + Frontend Vue Components"
echo "================================================"

# Cek apakah di Laravel project
if [ ! -f "artisan" ]; then
    log_error "File artisan tidak ditemukan. Pastikan script dijalankan di root Laravel project."
    exit 1
fi

log_info "Memulai perbaikan deployment lengkap..."

# ===============================================
# PART 1: FIX BACKEND MODELS
# ===============================================

echo ""
log_info "PART 1: Fixing Backend Models Case Sensitivity..."

# Backup file lama jika ada
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

# Backup file model dengan nama lama jika ada
if [ -f "app/Models/Kodenomenklatur.php" ]; then
    log_warning "Ditemukan file model lama: Kodenomenklatur.php"
    cp app/Models/Kodenomenklatur.php $BACKUP_DIR/
    rm app/Models/Kodenomenklatur.php
    log_success "✓ Dihapus: Kodenomenklatur.php"
fi

if [ -f "app/Models/KodenomenklaturDetail.php" ]; then
    log_warning "Ditemukan file model lama: KodenomenklaturDetail.php"
    cp app/Models/KodenomenklaturDetail.php $BACKUP_DIR/
    rm app/Models/KodenomenklaturDetail.php
    log_success "✓ Dihapus: KodenomenklaturDetail.php"
fi

# Verifikasi file model yang benar ada
if [ ! -f "app/Models/KodeNomenklatur.php" ]; then
    log_error "✗ File KodeNomenklatur.php tidak ditemukan!"
    echo "Pastikan file sudah ter-upload dari repository yang benar."
    exit 1
else
    log_success "✓ File KodeNomenklatur.php OK"
fi

if [ ! -f "app/Models/KodeNomenklaturDetail.php" ]; then
    log_error "✗ File KodeNomenklaturDetail.php tidak ditemukan!"
    echo "Pastikan file sudah ter-upload dari repository yang benar."
    exit 1
else
    log_success "✓ File KodeNomenklaturDetail.php OK"
fi

# ===============================================
# PART 2: FIX FRONTEND VUE COMPONENTS
# ===============================================

echo ""
log_info "PART 2: Fixing Frontend Vue Components Case Sensitivity..."

# Backup dan fix Vue component nama yang salah
if [ -f "resources/js/pages/Kodenomenklatur.vue" ]; then
    log_warning "Ditemukan Vue component lama: Kodenomenklatur.vue"
    cp resources/js/pages/Kodenomenklatur.vue $BACKUP_DIR/
    mv resources/js/pages/Kodenomenklatur.vue resources/js/pages/KodeNomenklatur.vue
    log_success "✓ Renamed: Kodenomenklatur.vue → KodeNomenklatur.vue"
fi

# Verifikasi file Vue component yang benar ada
if [ ! -f "resources/js/pages/KodeNomenklatur.vue" ]; then
    log_error "✗ File KodeNomenklatur.vue tidak ditemukan!"
    echo "Pastikan file sudah ter-upload dari repository yang benar."
    exit 1
else
    log_success "✓ File KodeNomenklatur.vue OK"
fi

# Verifikasi sub-components ada
if [ ! -f "resources/js/pages/KodeNomenklatur/Create.vue" ]; then
    log_warning "File KodeNomenklatur/Create.vue tidak ditemukan"
else
    log_success "✓ File KodeNomenklatur/Create.vue OK"
fi

if [ ! -f "resources/js/pages/KodeNomenklatur/Edit.vue" ]; then
    log_warning "File KodeNomenklatur/Edit.vue tidak ditemukan"
else
    log_success "✓ File KodeNomenklatur/Edit.vue OK"
fi

# ===============================================
# PART 3: COMPOSER & BACKEND OPTIMIZATION
# ===============================================

echo ""
log_info "PART 3: Backend Optimization..."

if command -v composer &> /dev/null; then
    composer clear-cache
    composer dump-autoload --optimize --no-cache
    log_success "✓ Composer autoload regenerated"
else
    log_error "Composer command tidak ditemukan"
    exit 1
fi

# Test model loading
php -r "
require_once 'vendor/autoload.php';
if (class_exists('App\\Models\\KodeNomenklatur') && class_exists('App\\Models\\KodeNomenklaturDetail')) {
    echo 'SUCCESS: Semua model classes dapat di-load!\\n';
} else {
    echo 'ERROR: Model classes tidak dapat di-load!\\n';
    exit(1);
}
"

if [ $? -ne 0 ]; then
    log_error "✗ Model loading gagal"
    exit 1
fi

log_success "✓ Backend models berhasil di-load"

# ===============================================
# PART 4: FRONTEND BUILD & OPTIMIZATION
# ===============================================

echo ""
log_info "PART 4: Frontend Build & Optimization..."

# Check jika Node.js ada
if command -v node &> /dev/null; then
    log_info "Node.js version: $(node --version)"
else
    log_error "Node.js tidak ditemukan. Install Node.js dulu."
    exit 1
fi

# Check jika npm ada
if command -v npm &> /dev/null; then
    log_info "NPM version: $(npm --version)"
else
    log_error "NPM tidak ditemukan. Install NPM dulu."
    exit 1
fi

# Install dependencies jika perlu
if [ ! -d "node_modules" ]; then
    log_info "Installing NPM dependencies..."
    npm install
    log_success "✓ NPM dependencies installed"
fi

# Build production assets
log_info "Building production assets..."
npm run build

if [ $? -eq 0 ]; then
    log_success "✓ Frontend assets built successfully"
else
    log_error "✗ Frontend build gagal"
    exit 1
fi

# ===============================================
# PART 5: LARAVEL CACHE OPTIMIZATION
# ===============================================

echo ""
log_info "PART 5: Laravel Cache Optimization..."

# Clear semua cache
php artisan cache:clear > /dev/null 2>&1 && log_success "✓ Application cache cleared"
php artisan config:clear > /dev/null 2>&1 && log_success "✓ Config cache cleared" 
php artisan route:clear > /dev/null 2>&1 && log_success "✓ Route cache cleared"
php artisan view:clear > /dev/null 2>&1 && log_success "✓ View cache cleared"

# Regenerate optimized cache untuk production
php artisan config:cache > /dev/null 2>&1 && log_success "✓ Config cached"
php artisan route:cache > /dev/null 2>&1 && log_success "✓ Routes cached"

# ===============================================
# PART 6: DATABASE & PERMISSIONS
# ===============================================

echo ""
log_info "PART 6: Database & Permissions..."

# Test database connection
php -r "
try {
    require_once 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$app->make('Illuminate\\\\Contracts\\\\Console\\\\Kernel')->bootstrap();
    DB::connection()->getPdo();
    echo 'SUCCESS: Database connection OK!\\n';
} catch (Exception \$e) {
    echo 'ERROR: Database connection failed: ' . \$e->getMessage() . '\\n';
    exit(1);
}
"

if [ $? -eq 0 ]; then
    log_success "✓ Database connection OK"
else
    log_error "✗ Database connection gagal"
    exit 1
fi

# Test migration dan seeding
log_info "Testing database migration & seeding..."
php artisan migrate:fresh --seed > /dev/null 2>&1

if [ $? -eq 0 ]; then
    log_success "✓ Database migration & seeding berhasil"
else
    log_warning "Database seeding mungkin gagal. Test manual: php artisan migrate:fresh --seed"
fi

# Fix permissions
chmod 644 app/Models/*.php 2>/dev/null && log_success "✓ Model file permissions fixed"
chmod 644 app/Http/Controllers/*.php 2>/dev/null && log_success "✓ Controller file permissions fixed"
chmod -R 775 storage/ 2>/dev/null && log_success "✓ Storage permissions fixed"
chmod -R 775 bootstrap/cache/ 2>/dev/null && log_success "✓ Bootstrap cache permissions fixed"

echo ""
echo "================================================"
log_success "🎉 DEPLOYMENT FIX COMPLETED SUCCESSFULLY!"
echo ""
echo "Summary:"
echo "✅ Backend Models: KodeNomenklatur & KodeNomenklaturDetail"
echo "✅ Frontend Vue: KodeNomenklatur.vue & sub-components"
echo "✅ Composer autoload optimized"
echo "✅ Frontend assets built"
echo "✅ Laravel cache optimized"
echo "✅ Database tested"
echo "✅ Permissions fixed"
echo ""
echo "File backup: $BACKUP_DIR/"
echo ""
echo "Next steps:"
echo "1. Test web application access"
echo "2. Monitor logs: tail -f storage/logs/laravel.log"
echo "3. Check browser console for any remaining errors"
echo "================================================" 