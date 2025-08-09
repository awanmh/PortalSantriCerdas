# PortalSantriCerdas
Dashboard digital untuk santri modern.
# 🚀 Sistem Absensi & Pelacakan Siswa Cerdas

![Banner Proyek](https://placehold.co/800x300/3498db/ffffff?text=Sistem+Absensi+SMK+Pondok)

Selamat datang di dokumentasi resmi Sistem Absensi & Pelacakan Siswa. Dokumen ini adalah panduan lengkap untuk memahami, menginstal, dan menjalankan proyek ini dari awal hingga akhir.

---

## 🎯 1. Tujuan & Deskripsi Proyek

Proyek ini bertujuan untuk menciptakan sebuah sistem absensi modern yang **akurat, aman, dan anti-titip absen**. Sistem ini dirancang untuk mengatasi tantangan absensi manual dengan mengimplementasikan mekanisme **verifikasi ganda (dua faktor)**:

1.  **👆 Verifikasi Fisik (Bukti Kehadiran):** Siswa menempelkan sidik jari pada alat pemindai di dekat pintu kelas. Ini adalah "tiket masuk" pertama yang membuktikan kehadiran fisik siswa di lokasi.
2.  **📱 Konfirmasi Digital (Aktivasi Sesi):** Setelah scan, siswa wajib login ke **Portal Web Sekolah** menggunakan perangkatnya (HP/Tablet). Login ini mengonfirmasi identitas siswa dan secara otomatis mengaktifkan pelacakan GPS selama jam sekolah.

> **Aturan Emas:** Kehadiran siswa dianggap **SAH** hanya jika **kedua langkah** di atas berhasil dilakukan. Jika tidak, status kehadiran akan ditandai tidak lengkap.

---

## ✨ 2. Arsitektur & Alur Kerja Sistem

Untuk memahami cara kerja sistem, bayangkan ada tiga pemain utama yang saling berkomunikasi:

![Diagram Alur Kerja](https://placehold.co/800x250/f1f1f1/333333?text=Alat+Fingerprint+->+Backend+(Laravel)+<-+Frontend+(React))

1.  **Alat Fingerprint (Perangkat Keras):**
    * **Tugas:** Membaca sidik jari dan mengirimkan ID uniknya ke Backend.
    * **Lokasi:** Terpasang di dinding.
    * **Aksi:** `HTTP POST` ke API Laravel.

2.  **Backend (Laravel):**
    * **Tugas:** Otak dari sistem. Menerima data, memvalidasi, menyimpan ke database, dan mengelola semua logika bisnis.
    * **Lokasi:** Berjalan di server.
    * **Aksi:** Menyediakan API, mengelola database, mengecek geofencing, mengirim notifikasi.

3.  **Frontend (React + Tailwind CSS):**
    * **Tugas:** Antarmuka yang dilihat dan digunakan oleh siswa dan guru.
    * **Lokasi:** Diakses melalui browser di HP/komputer.
    * **Aksi:** Menampilkan form login, dashboard, status absensi, dan berkomunikasi dengan Backend untuk mengambil/mengirim data.

---

## 🛠️ 3. Panduan Instalasi Lengkap

Ikuti panduan ini langkah demi langkah untuk menjalankan proyek di komputer lokal Anda.

### A. Prasyarat (Hal yang Wajib Ada)

Pastikan perangkat Anda sudah terinstal software berikut sebelum melanjutkan:

* **PHP:** Versi `^8.1` atau lebih tinggi
* **Composer:** Manajer paket untuk PHP
* **Node.js:** Versi `^18.0` atau lebih tinggi
* **NPM:** Manajer paket untuk Node.js
* **Database:** MySQL atau MariaDB
* **Git:** Untuk mengunduh kode proyek

### B. Langkah 1: Persiapan Awal

1.  **Clone Repositori Proyek**
    Buka terminal, masuk ke direktori kerja Anda, dan jalankan:
    ```bash
    git clone [URL_REPOSITORI_ANDA]
    cd [NAMA_FOLDER_PROYEK]
    ```

2.  **Buat Database Kosong**
    Buka aplikasi database Anda (misal: phpMyAdmin, HeidiSQL) dan buat sebuah database baru (contoh: `absensi_smk`).

### C. Langkah 2: Setup Backend (Laravel)

Semua perintah ini dijalankan dari direktori utama proyek.

1.  **Install Dependensi PHP**
    ```bash
    composer install
    ```

2.  **Konfigurasi Environment**
    Salin file konfigurasi contoh dan sesuaikan dengan lingkungan Anda.
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan **wajib edit** bagian database:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=absensi_smk  # <-- Ganti dengan nama database Anda
    DB_USERNAME=root         # <-- Ganti dengan username database Anda
    DB_PASSWORD=             # <-- Ganti dengan password database Anda
    ```

3.  **Generate Kunci Aplikasi**
    Perintah ini penting untuk keamanan aplikasi.
    ```bash
    php artisan key:generate
    ```

4.  **Buat Struktur Tabel Database**
    Perintah ini akan membaca file migrasi dan membuat semua tabel yang dibutuhkan.
    ```bash
    php artisan migrate
    ```

### D. Langkah 3: Setup Frontend (React)

Semua perintah ini juga dijalankan dari direktori utama proyek.

1.  **Install Dependensi JavaScript**
    ```bash
    npm install
    ```

2.  **Konfigurasi Environment Frontend**
    Frontend perlu tahu alamat API Backend. Buka file `.env` di root proyek (file yang sama dengan konfigurasi database) dan tambahkan baris ini:
    ```env
    # Tambahkan di bagian bawah file .env
    VITE_API_BASE_URL=[http://127.0.0.1:8000/api](http://127.0.0.1:8000/api)
    ```

### E. Langkah 4: Menjalankan Aplikasi

Sekarang, saatnya menyalakan sistem! Anda butuh **dua terminal** yang berjalan bersamaan.

* **Terminal 1: Jalankan Server Backend**
    ```bash
    php artisan serve
    ```
    > 🟢 **Berhasil jika muncul:** `Server running on [http://127.0.0.1:8000]`

* **Terminal 2: Jalankan Server Frontend**
    ```bash
    npm run dev
    ```
    > 🔵 **Berhasil jika muncul:** `Local: http://localhost:5173/`

**Selesai!** Buka `http://localhost:5173` di browser Anda untuk melihat portal web siswa.

### F. Langkah 5: Konfigurasi Alat Fingerprint

Terakhir, konfigurasikan perangkat keras Anda:

1.  **Endpoint API:** Arahkan perangkat untuk mengirim `HTTP POST` ke:
    `http://[IP_KOMPUTER_ANDA]:8000/api/absensi/fingerprint`
    *(Ganti `[IP_KOMPUTER_ANDA]` dengan alamat IP lokal komputer Anda, bukan `127.0.0.1`)*

2.  **Body Request:** Pastikan format data yang dikirim adalah JSON seperti ini:
    ```json
    {
      "fingerprint_id": "ID_UNIK_DARI_HASIL_SCAN"
    }
    ```
