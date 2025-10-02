# 🚀 Panduan Menjalankan Aplikasi Laravel

## 📋 Persyaratan Sistem

Pastikan sistem Anda memiliki:
- **PHP 8.1+** 
- **Composer** (Dependency Manager untuk PHP)
- **Node.js & NPM** (untuk asset compilation)
- **Database** (MySQL/PostgreSQL/SQLite)

## 🛠️ Langkah-langkah Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/anndaanhr/prak-pwl-2317051082.git
cd prak-web-lanjut-2317051082
```

### 2. Install Dependencies PHP
```bash
composer install
```

### 3. Install Dependencies Node.js
```bash
npm install
```

### 4. Setup Environment
```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

**Untuk SQLite (Recommended untuk development):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**Untuk MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_user_app
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database SQLite (jika menggunakan SQLite)
```bash
touch database/database.sqlite
```

### 7. Jalankan Migrasi dan Seeder
```bash
# Jalankan migrasi
php artisan migrate

# Jalankan seeder untuk data kelas
php artisan db:seed --class=KelasSeeder
```

### 8. Setup Storage Link
```bash
php artisan storage:link
```

### 9. Compile Assets (Opsional)
```bash
npm run dev
# atau untuk production
npm run build
```

## 🎯 Menjalankan Aplikasi

### Metode 1: Menggunakan Artisan Serve
```bash
php artisan serve
```
Aplikasi akan berjalan di: `http://localhost:8000`

### Metode 2: Menggunakan XAMPP/WAMP
1. Copy folder project ke `htdocs` (XAMPP) atau `www` (WAMP)
2. Akses melalui browser: `http://localhost/prak-web-lanjut-2317051082/public`

## 📱 Fitur Aplikasi

### 🏠 Halaman Utama
- **URL:** `/`
- **Deskripsi:** Landing page dengan navigasi ke fitur utama
- **Fitur:** Profile card dengan data mahasiswa

### 👥 Manajemen User
- **Card View:** `/user` - Tampilan card modern dengan search & filter
- **Table View:** `/user/table` - Tampilan tabel dinamis dengan sorting
- **Tambah User:** `/user/create` - Form untuk menambah user baru

### 👤 Profile
- **URL:** `/profile` - Redirect ke profile dengan data mahasiswa
- **URL Direct:** `/profile/Ananda Anhar Subing/2317051082/A`
- **Fitur:** Upload foto profile

## 🎨 Komponen yang Dibuat

### 1. Navbar Component
- **File:** `resources/views/layouts/navbar.blade.php`
- **Fitur:** Responsive navigation dengan dropdown menu

### 2. Footer Component  
- **File:** `resources/views/components/footer.blade.php`
- **Fitur:** Footer informatif dengan data developer

### 3. Dynamic Table Component
- **File:** `resources/views/components/dynamic-table.blade.php`
- **Fitur:** 
  - Sortable columns
  - Search functionality
  - Filter options
  - Action buttons
  - Responsive design

### 4. User Table Component
- **File:** `resources/views/components/user_table.blade.php`
- **Fitur:**
  - Card-based layout
  - Statistics cards
  - Real-time search
  - Class filtering
  - Hover effects

## 🗂️ Struktur Database

### Tabel Kelas
```sql
- id (Primary Key)
- nama_kelas (A, B, C, D)
- timestamps
```

### Tabel User
```sql
- id (Primary Key)  
- nama (String, 150 chars)
- npm (String, 20 chars, Unique)
- kelas_id (Foreign Key ke tabel kelas)
- timestamps
```

## 🔧 Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "Key not found"
```bash
php artisan key:generate
```

### Error: Database connection
1. Pastikan database service berjalan
2. Cek konfigurasi di file `.env`
3. Untuk SQLite, pastikan file database ada

### Error: Storage link
```bash
php artisan storage:link
```

### Error: Permission denied
```bash
chmod -R 755 storage bootstrap/cache
```

## 📸 Screenshot untuk Dokumentasi

Ambil screenshot dari:
1. **Halaman Welcome** - `http://localhost:8000/`
2. **List User (Card View)** - `http://localhost:8000/user`
3. **List User (Table View)** - `http://localhost:8000/user/table`
4. **Form Create User** - `http://localhost:8000/user/create`
5. **Profile Page** - `http://localhost:8000/profile`

## 👨‍💻 Informasi Developer

- **Nama:** Ananda Anhar Subing
- **NPM:** 2317051082
- **Kelas:** A
- **Mata Kuliah:** Praktikum Web Lanjut
- **Modul:** 4 - Controller & View

## 📝 Catatan Penting

1. Pastikan semua dependencies terinstall dengan benar
2. File `.env` harus dikonfigurasi sesuai environment
3. Jalankan migrasi sebelum menggunakan aplikasi
4. Untuk production, gunakan `npm run build` dan set `APP_ENV=production`

## 🆘 Bantuan

Jika mengalami masalah, cek:
1. Log Laravel di `storage/logs/laravel.log`
2. Error browser di Developer Tools (F12)
3. Pastikan semua service (database, web server) berjalan

---
**Happy Coding! 🎉**
