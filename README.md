# 📚 SMK Monitoring App (Backend + Frontend Options)

Aplikasi monitoring kegiatan di SMK, mencakup:
- Absensi siswa & guru (GPS + foto)
- Jadwal pelajaran
- Catatan pelanggaran siswa
- Manajemen user (Admin, Guru, Guru BK, Siswa)

Backend dibangun dengan **Laravel 11 + PostgreSQL (PostGIS)**, siap integrasi dengan GIS & IoT.

---

## 🚀 Fitur Utama
- ✅ Login dengan role (Admin, Guru, BK, Siswa)
- ✅ CRUD Jadwal Pelajaran
- ✅ Catatan Pelanggaran
- ✅ Absensi Guru & Siswa (API support foto + GPS)
- 🔜 Validasi GPS (radius sekolah)
- 🔜 Integrasi IoT (Face ID, WiFi Sekolah)

---

## 📦 Teknologi
- Laravel 11
- PostgreSQL + PostGIS
- Laravel Permission
- Vue 3 (Breeze) / Template Admin / SPA (opsional)
- TailwindCSS

---

## 🛠 Instalasi Cepat (Backend)
```bash
git clone -b backend-docs https://github.com/username/smk_monitoring_app.git
cd smk_monitoring_app

composer install
npm install
cp .env.example .env  # edit DB sesuai setting

php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
npm run dev

👤 Login default:

Role	Email	Password
Admin	admin@example.com
	password
Guru	guru@example.com
	password
GuruBK	gurubk@example.com
	password
Siswa	siswa@example.com
	password

  📡 API Contoh

GET /api/ping → tes koneksi

POST /api/absensi-guru

{ "guru_id": 2, "tanggal": "2025-08-22", "status": "hadir" }


POST /api/absensi-siswa

{ "siswa_id": 4, "jadwal_id": 1, "keterangan": "Sakit" }

🎨 Frontend Options

Frontend hanya menampilkan data dari API. Tim frontend bisa pilih opsi sesuai kemampuan:

🅰️ Laravel Breeze (Vue/React)

Paling mudah (sudah ada login/register).

Jalankan:

composer require laravel/breeze --dev
php artisan breeze:install vue   # atau react
npm install && npm run dev

🅱️ Template Admin (SB Admin 2 / CoreUI)

Tampilan langsung profesional.

Download template → copy ke public/ → panggil API dengan axios.

🅾️ Full SPA (Vue/React/Angular terpisah)

Untuk tim lebih mahir.

Jalankan:

npm create vue@latest smk-monitoring-frontend
cd smk-monitoring-frontend
npm install axios vue-router
npm run dev


🚦 Rekomendasi:

MVP Awal → Breeze (Vue)

Upgrade UI cepat → Template SB Admin

Jangka panjang → SPA terpisah

🗺 Roadmap

🔲 Validasi GPS (radius sekolah)

🔲 Peta GIS titik absensi

🔲 Integrasi WiFi sekolah

🔲 IoT Face Recognition

🔲 Export laporan (Excel/PDF)

📌 Progress Saat Ini

✅ Setup Laravel 11 + PostgreSQL
✅ Setup Role & Permission
✅ CRUD Jadwal
✅ CRUD Catatan Pelanggaran
✅ CRUD Absensi Guru & Siswa (API)
🔲 Integrasi GIS + IoT
🔲 Finalisasi Frontend