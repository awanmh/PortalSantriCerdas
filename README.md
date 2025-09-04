#### 📡 **Progress Endpoint API Penting**

| Endpoint | Method | Deskripsi | Middleware | Status |
|----------|--------|-----------|------------|--------|
| `/api/login` | POST | Login user | - | 200 OK|
| `/api/absen/siswa` | POST | Absensi siswa | `auth:sanctum, role:siswa` | 200 OK|
| `/api/absen/guru/masuk` | POST | Absensi masuk guru | `auth:sanctum, role:guru` | 200 OK|
| `/api/absen/guru/pulang` | POST | Absensi pulang guru | `auth:sanctum, role:guru` | 200 OK|
| `/api/zona` | GET/POST/PUT/DELETE | Manajemen zona | `auth:sanctum, role:it` | 200 OK |
| `/api/catatan-pelanggaran` | GET/POST/PUT/DELETE | Catatan pelanggaran | `auth:sanctum, role:guru,bk` | 200 OK|
| `/api/export/absensi/siswa` | GET | Export absensi siswa | `auth:sanctum, role:it,guru,bk` | 200 OK |

### 1. Login
<img src="assets/LoginIT.png" width="300"/>
<img src="assets/LoginBK.png" width="300"/>
<img src="assets/LoginGuru.png" width="300"/>
<img src="assets/LoginSiswa.png" width="300"/>

### 2. Absen Siswa
<img src="assets/AbsenSiswa.png" width="300"/>

### 3. Guru Masuk
<img src="assets/GuruMasuk.png" width="300"/>

### 4. Guru Pulang
<img src="assets/GuruKeluar.png" width="300"/>

### 5. Zona
<img src="assets/DaftarZona.png" width="300"/>

Belum Dibenahi yang GET

### 6. Catatan Pelanggaran
<img src="assets/CatatanPelanggaran.png" width="300"/>
<img src="assets/CatatanPelanggaranGET.png" width="300"/>

### 7. Export Absensi Siswa
Belum Dibenahi