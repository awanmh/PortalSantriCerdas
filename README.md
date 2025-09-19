# Dokumentasi Bug: Unggah Foto Profil Gagal Secara Diam-diam

**Tanggal Laporan:** 19 September 2025  
**Status:** Belum Terpecahkan (Memerlukan Investigasi Lebih Lanjut)  
**Prioritas:** Kritis (Menghalangi alur wajib untuk pengguna siswa)  

---

## 1. Ringkasan Masalah
Fitur unggah foto profil di halaman `/profile` gagal menyimpan file foto ke server. Saat pengguna (terutama siswa) memilih foto baru dan menekan tombol "Simpan", halaman hanya me-reload kembali ke halaman profil tanpa menyimpan perubahan dan tanpa menampilkan pesan error yang jelas.

---

## 2. Gejala yang Teramati

**Perilaku Pengguna:**
- Pengguna memilih file, menekan "Simpan", halaman me-reload kembali ke `/profile`.

**Hasil:**
- Foto tidak tersimpan di folder `storage/app/public/profile-photos/`.
- Data lain (seperti nama atau email) juga tidak diperbarui.
- Tidak ada notifikasi "Profil berhasil diperbarui" yang muncul.

**Log Frontend (Console Browser):**
- Semua `console.log` di dalam `UpdateProfileInformationForm.vue` menunjukkan bahwa fungsi submit berjalan dengan benar.
- Permintaan Inertia.js dimulai (`🚀 [INERTIA START]`).
- Permintaan langsung menerima respons sukses (`✅ [INERTIA SUCCESS]`).
- Permintaan selesai (`🏁 [INERTIA FINISH]`).

**Log Backend (`laravel.log` & `php artisan serve`):**
- Tidak ada log sama sekali yang muncul dari dalam `ProfileController@update`.
- Ini menunjukkan eksekusi tidak pernah mencapai logika di dalam controller.

---

## 3. Investigasi & Langkah yang Telah Dicoba
Berikut daftar hal yang telah diperiksa dan dapat dikesampingkan sebagai penyebab utama:

1. **Konflik Metode POST vs PATCH**
   - **Status:** Sudah diperbaiki.
   - Rute `profile.update` di `routes/web.php` diubah menjadi `Route::post(...)`.
   - Komponen `UpdateProfileInformationForm.vue` sudah diperbarui untuk mengirim permintaan POST murni tanpa method spoofing (`_method: 'patch'`).

2. **Validasi Backend**
   - **Status:** Sudah benar.
   - `ProfileUpdateRequest.php` diperbarui dengan aturan validasi yang benar untuk `photo`, termasuk `requiredIf` untuk siswa.

3. **Logika Controller**
   - **Status:** Sudah benar.
   - `ProfileController.php` berisi logika yang memeriksa `$request->hasFile('photo')`, menyimpan file, memperbarui model, dan mengarahkan kembali.

4. **Struktur Model**
   - **Status:** Sudah benar.
   - `User.php` memiliki `profile_photo_path` di dalam `$fillable`.

5. **Masalah Cache**
   - **Status:** Telah dicoba dibersihkan berkali-kali:
     - `php artisan optimize:clear`
     - `php artisan config:clear`
     - `php artisan route:cache`
     - `php artisan permission:cache-reset`
     - Penghapusan manual folder `bootstrap/cache`
   - Tidak menyelesaikan masalah inti.

6. **Masalah Komponen Frontend**
   - **Status:** Sudah diperbaiki.
   - `PrimaryButton.vue` sudah memiliki `type="submit"` secara default.

---

## 4. Hipotesis Utama (Sumber Masalah yang Paling Mungkin)
Berdasarkan fakta:
- Frontend menerima respons "sukses", tetapi backend tidak pernah mencatat log di controller.

**Hipotesis:**
- Middleware atau konfigurasi di level server/framework mencegat permintaan `multipart/form-data` sebelum mencapai `ProfileController`.
- Respons tetap dikembalikan sebagai HTTP "sukses" (kemungkinan 302 Found atau 200 OK) oleh Inertia.js.
- Ini menjelaskan:
  - Tombol berfungsi
  - Inertia menganggapnya sukses
  - Halaman me-reload (sesuai respons redirect)
  - Foto tidak tersimpan karena logika controller tidak pernah dijalankan

---

## 5. Langkah Selanjutnya yang Disarankan
Developer berikutnya harus fokus pada "jembatan" antara router dan controller:

1. **Periksa Middleware Global & Grup**
   - Tinjau semua middleware yang terdaftar di `bootstrap/app.php` di grup `web`.
   - Periksa middleware pihak ketiga atau kustom yang mungkin mengganggu permintaan POST berisi file.

2. **Debugging di Level Rute**
   - Tempatkan `dd($request->all());` di `routes/web.php` untuk memastikan permintaan sampai di level routing.
   ```php
   // di routes/web.php
   Route::post('/', function (Request $request) {
       dd($request->all(), $request->hasFile('photo')); // Lakukan tes di sini
       // ... panggil controller
   })->name('update');
3. **Periksa Konfigurasi PHP & Server**
    - Periksa php.ini, khususnya post_max_size dan upload_max_filesize.
    - Jika ukuran file foto melebihi batas ini, permintaan bisa gagal diam-diam.

4. **Periksa Konfigurasi Sanctum**
    - Pastikan tidak ada konflik dengan otentikasi berbasis sesi web (EnsureFrontendRequestsAreStateful).