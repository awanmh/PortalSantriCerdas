# 📚 SMK Monitoring App (Backend)

Aplikasi ini adalah **backend** untuk sistem monitoring kegiatan di SMK, termasuk:
- Jadwal pelajaran, event sekolah
- Pencatatan pelanggaran siswa
- Manajemen data guru, kelas, dan jurusan
- Role & permission berbasis **Laravel Permission**
- Siap terhubung dengan IoT (misalnya alat PinjerScreen) untuk monitoring otomatis

Backend ini dibuat menggunakan **Laravel 11** dengan database **PostgreSQL + PostGIS** untuk mendukung data spasial (lokasi).

---

## 🚀 Fitur Utama

- **Autentikasi & Role Management**:
  - Admin
  - Guru
  - Guru BK
  - Siswa
- **Manajemen Jadwal**:
  - Jadwal pelajaran per kelas, jurusan, dan jenjang (X, XI, XII)
  - Jadwal event sekolah
- **Catatan Pelanggaran**:
  - Input pelanggaran oleh guru
  - Rekap pelanggaran oleh guru BK
- **Integrasi IoT (Coming Soon)**:
  - Komunikasi antara backend dan perangkat IoT untuk presensi/monitoring

---

## 📦 Teknologi yang Digunakan

- **Laravel 11**
- **PostgreSQL** (Database utama)
- **PostGIS** (Geospatial Extension untuk lokasi)
- **Laravel Permission** (Manajemen Role)
- **Inertia.js + Vue 3** (Default frontend Laravel Breeze)
- **Tailwind CSS** (Styling bawaan Breeze)

---

## 🛠 Persiapan Sebelum Install

1. **Install Git**
   - [Download Git](https://git-scm.com/downloads)
   - Cek instalasi:
     ```bash
     git --version
     ```

2. **Install PHP (versi ≥ 8.2)**
   - Bisa pakai [XAMPP](https://www.apachefriends.org/) atau Laragon.

3. **Install Composer**
   - [Download Composer](https://getcomposer.org/)

4. **Install Node.js (≥ 18)**
   - [Download Node.js](https://nodejs.org/)

5. **Siapkan PostgreSQL + PostGIS**
   - Install PostgreSQL
   - Tambahkan ekstensi PostGIS di database:
     ```sql
     CREATE EXTENSION postgis;
     ```

---

## 📥 Instalasi (Backend)

```bash
# 1. Clone repository (branch backend)
git clone -b backend https://github.com/awanmh/PortalSantriCerdas.git
cd PortalSantriCerdas

# 2. Install dependencies PHP
composer install

# 3. Install dependencies JS
npm install

# 4. Duplikasi file .env
cp .env.example .env

# 5. Edit konfigurasi database di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smk_monitoring
DB_USERNAME=postgres
DB_PASSWORD=your_password

# 6. Generate key Laravel
php artisan key:generate

# 7. Jalankan migrasi + seeder
php artisan migrate:fresh --seed

# 8. Jalankan server Laravel
php artisan serve

# 9. Jalankan Vite (frontend default)
npm run dev
🌐 Akses Aplikasi
URL: http://127.0.0.1:8000

Login Default (dari seeder RolePermissionSeeder):

Admin
Email: admin@example.com
Password: password

Guru
Email: guru@example.com
Password: password

Guru BK
Email: gurubk@example.com
Password: password

Siswa
Email: siswa@example.com
Password: password

app/
 ├── Http/
 │   ├── Controllers/    # Semua controller
 │   ├── Middleware/     # Middleware aplikasi
 │
 ├── Models/             # Model Eloquent

database/
 ├── migrations/         # Struktur tabel
 ├── seeders/            # Data awal

routes/
 ├── web.php              # Routing web
 ├── auth.php             # Routing auth

resources/
 ├── js/                  # Vue.js components
 ├── views/               # Blade templates

public/
 ├── assets/              # File publik


# Menjalankan migrasi + seeder
php artisan migrate:fresh --seed

# Membuat controller
php artisan make:controller NamaController

# Membuat model + migration
php artisan make:model NamaModel -m

# Membuat seeder
php artisan make:seeder NamaSeeder

🗂 Branch Repository
main → Branch gabungan backend + frontend (final release)

backend → Branch khusus API & backend

🤝 Kontribusi
Fork repo

Buat branch baru (feature/nama-fitur)

Commit perubahan

Push ke branch baru

Buat Pull Request

📜 Lisensi
Proyek ini bersifat open-source dan menggunakan lisensi MIT.

📌 Progress Saat Ini
 Setup Laravel 11 + PostgreSQL

 Setup Role & Permission

 CRUD Jadwal

 CRUD Catatan Pelanggaran

 Integrasi IoT

 Finalisasi frontend dari tim lain