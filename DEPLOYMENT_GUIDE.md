# 🚀 **DEPLOYMENT GUIDE: Fix KodeNomenklatur Case Sensitivity**

## 📋 **Problem Summary**
- **Error**: `Class "App\Models\KodeNomenklatur" not found` di production server
- **Cause**: File model di server menggunakan nama `Kodenomenklatur.php` (huruf n kecil) 
- **Solution**: Pastikan semua model menggunakan PascalCase yang benar: `KodeNomenklatur`

---

## 🛠️ **DEPLOYMENT STEPS**

### **Option A: Automated Fix (Recommended)**

1. **Upload files ke server:**
   ```bash
   # Upload script dan model files yang benar
   scp fix_model_case_sensitivity.sh user@your-server:/path/to/laravel/
   scp -r app/Models/ user@your-server:/path/to/laravel/app/
   ```

2. **SSH ke server dan jalankan script:**
   ```bash
   ssh user@your-server
   cd /path/to/laravel/project
   chmod +x fix_model_case_sensitivity.sh
   ./fix_model_case_sensitivity.sh
   ```

### **Option B: Manual Fix**

1. **SSH ke server:**
   ```bash
   ssh user@your-server
   cd /path/to/laravel/project
   ```

2. **Backup dan hapus file lama:**
   ```bash
   # Backup file lama jika ada
   mkdir -p backup_models_$(date +%Y%m%d)
   [ -f "app/Models/Kodenomenklatur.php" ] && cp app/Models/Kodenomenklatur.php backup_models_$(date +%Y%m%d)/
   [ -f "app/Models/KodenomenklaturDetail.php" ] && cp app/Models/KodenomenklaturDetail.php backup_models_$(date +%Y%m%d)/
   
   # Hapus file lama
   rm -f app/Models/Kodenomenklatur.php
   rm -f app/Models/KodenomenklaturDetail.php
   ```

3. **Upload file model yang benar:**
   ```bash
   # Pastikan file dengan nama benar ada:
   ls -la app/Models/KodeNomenklatur.php
   ls -la app/Models/KodeNomenklaturDetail.php
   ```

4. **Regenerate autoload:**
   ```bash
   composer clear-cache
   composer dump-autoload --optimize --no-cache
   ```

5. **Clear Laravel cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan config:cache
   php artisan route:cache
   ```

6. **Test model loading:**
   ```bash
   php -r "require 'vendor/autoload.php'; echo (class_exists('App\\Models\\KodeNomenklatur') ? 'KodeNomenklatur: OK' : 'KodeNomenklatur: FAIL') . PHP_EOL;"
   php -r "require 'vendor/autoload.php'; echo (class_exists('App\\Models\\KodeNomenklaturDetail') ? 'KodeNomenklaturDetail: OK' : 'KodeNomenklaturDetail: FAIL') . PHP_EOL;"
   ```

7. **Test database migration & seeding:**
   ```bash
   # Reset database dan seed
   php artisan migrate:fresh --seed
   ```

---

## ✅ **VERIFICATION CHECKLIST**

Setelah deployment, pastikan:

- [ ] ✅ File `app/Models/KodeNomenklatur.php` ada di server
- [ ] ✅ File `app/Models/KodeNomenklaturDetail.php` ada di server  
- [ ] ❌ File `app/Models/Kodenomenklatur.php` TIDAK ada (file lama)
- [ ] ❌ File `app/Models/KodenomenklaturDetail.php` TIDAK ada (file lama)
- [ ] ✅ `composer dump-autoload` berhasil
- [ ] ✅ Model classes dapat di-load
- [ ] ✅ Database seeder berjalan tanpa error
- [ ] ✅ Web application dapat diakses

---

## 🔍 **TROUBLESHOOTING**

### **Jika error masih muncul:**

1. **Cek permission file:**
   ```bash
   chmod 644 app/Models/KodeNomenklatur.php
   chmod 644 app/Models/KodeNomenklaturDetail.php
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

2. **Cek syntax PHP:**
   ```bash
   php -l app/Models/KodeNomenklatur.php
   php -l app/Models/KodeNomenklaturDetail.php
   ```

3. **Cek autoload files:**
   ```bash
   grep -r "KodeNomenklatur" vendor/composer/autoload_*.php
   ```

4. **Monitor logs:**
   ```bash
   tail -f storage/logs/laravel.log
   tail -f /var/log/apache2/error.log  # atau nginx error log
   ```

### **Error umum dan solusi:**

| Error | Cause | Solution |
|-------|-------|----------|
| `Class not found` | Autoload tidak terupdate | `composer dump-autoload` |
| `Permission denied` | File permission salah | `chmod 644 app/Models/*.php` |
| `Syntax error` | File corrupt | Re-upload file dari repository |
| `Duplicate entry` | Data sudah ada | `php artisan migrate:fresh --seed` |

---

## 📝 **BEST PRACTICES**

### **Untuk deployment selanjutnya:**

1. **Gunakan deployment script:**
   ```bash
   #!/bin/bash
   # deploy.sh
   
   set -e
   
   # Pull latest code
   git pull origin main
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   
   # Clear dan regenerate cache
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   
   # Run migrations
   php artisan migrate --force
   
   # Fix permissions
   chmod -R 775 storage bootstrap/cache
   
   echo "Deployment completed!"
   ```

2. **Environment check:**
   ```bash
   # Pastikan environment production
   php artisan env
   
   # Cek PHP version
   php -v
   
   # Test database connection
   php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"
   ```

3. **Pre-deployment testing:**
   ```bash
   # Test di staging environment dulu
   composer install
   php artisan test
   php artisan migrate:fresh --seed --env=staging
   ```

---

## 🎯 **QUICK COMMANDS REFERENCE**

```bash
# Fix case sensitivity (one-liner)
rm -f app/Models/Kodenomenklatur.php app/Models/KodenomenklaturDetail.php && composer dump-autoload --optimize && php artisan cache:clear && php artisan config:cache

# Test models
php -r "require 'vendor/autoload.php'; echo 'Models: ' . (class_exists('App\\Models\\KodeNomenklatur') && class_exists('App\\Models\\KodeNomenklaturDetail') ? 'OK' : 'FAIL') . PHP_EOL;"

# Full reset & seed
php artisan migrate:fresh --seed

# Monitor logs
tail -f storage/logs/laravel.log
```

---

## 📞 **SUPPORT**

Jika masih ada masalah setelah mengikuti guide ini:

1. Capture error message lengkap
2. Cek Laravel log: `storage/logs/laravel.log`
3. Cek web server error log
4. Pastikan semua file ter-upload dengan benar dari repository

**Good luck with your deployment! 🚀** 