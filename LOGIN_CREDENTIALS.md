# Login Credentials - User Management System

## Default User Accounts

Setelah menjalankan seeder dengan perintah `php artisan db:seed`, gunakan credentials berikut untuk login:

### 🔑 Super Administrator
**Email:** `admin@trackingapp.com`  
**Password:** `Admin123!`  
**Role:** Super Admin  
**Division:** IT Department  
**Akses:** Full system access - semua fitur dan permissions

### 👤 Administrator
**Email:** `admin@example.com`  
**Password:** `Admin123!`  
**Role:** Admin  
**Division:** HR Department  
**Akses:** Administrative access dengan permissions terbatas

### 👥 Regular User
**Email:** `user@example.com`  
**Password:** `User123!`  
**Role:** User  
**Division:** Finance Department  
**Akses:** Basic user dengan permissions terbatas (view only)

---

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure Database
Edit file `.env` dan isi konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tracking_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Create Database
```sql
CREATE DATABASE tracking_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Database (Create Default Users)
```bash
php artisan db:seed
```

### 7. Start Server
```bash
php artisan serve
```

### 8. Access Application
Buka browser dan kunjungi: `http://localhost:8000`

---

## ⚠️ IMPORTANT SECURITY NOTES

1. **Change Default Passwords Immediately**  
   Setelah pertama kali login, segera ubah password default untuk semua user.

2. **Production Environment**  
   - Set `APP_ENV=production` di file `.env`
   - Set `APP_DEBUG=false`
   - Gunakan password yang kuat
   - Enable HTTPS

3. **Database Security**  
   - Gunakan strong database password
   - Jangan expose database credentials
   - Regular backup database

4. **Session Security**  
   - Configure session timeout sesuai kebutuhan
   - Monitor active sessions melalui Session Monitoring page
   - Force logout suspicious sessions

---

## System Features

Semua user dapat mengakses:
- ✅ Dashboard dengan statistics
- ✅ Profile management
- ✅ View own sessions

Super Admin dapat mengakses semua:
- ✅ User Management
- ✅ Roles Management
- ✅ Permissions Management
- ✅ Divisions Management
- ✅ Delegated Admin Management
- ✅ Audit Logs
- ✅ Session Monitoring
- ✅ System Settings

---

## Support

Jika mengalami masalah login:
1. Pastikan database sudah di-migrate dan di-seed
2. Pastikan user status adalah 'active'
3. Check logs di `storage/logs/laravel.log`
4. Verify database connection di `.env`

---

**Last Updated:** 2025-11-02  
**System Version:** 1.0.0


