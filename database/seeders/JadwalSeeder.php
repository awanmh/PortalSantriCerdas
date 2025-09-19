<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Carbon;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan data dasar sudah ada
        if (!Kelas::exists()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Data Kelas belum ada.');
            return;
        }

        // 2. Ambil guru dengan role yang benar
        $gurus = User::role('guru')->get();
        if ($gurus->isEmpty()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Tidak ada user dengan role guru.');
            return;
        }

        // 3. Hapus jadwal lama untuk menghindari duplikasi
        Jadwal::truncate();

        // 4. Buat jadwal pelajaran untuk semua kelas
        $this->buatJadwalPelajaran($gurus);

        // 5. Buat event untuk kalender sekolah
        $this->buatEventSekolah($gurus);

        $this->command->info('✅ Seeder Jadwal berhasil dijalankan!');
    }

    /**
     * Membuat jadwal pelajaran dummy untuk 5 hari kerja ke depan.
     */
    private function buatJadwalPelajaran($gurus)
    {
        $kelasCollection = Kelas::all();
        $mataPelajaran = [
            'Matematika Wajib', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia',
            'Sejarah Indonesia', 'PJOK', 'PPKn', 'Dasar Desain Grafis', 'Pendidikan Agama'
        ];
        $totalPelajaran = 0;
        $tanggalSekarang = Carbon::now()->startOfWeek(); // Mulai dari hari Senin minggu ini

        // Loop untuk 5 hari kerja (Senin - Jumat)
        for ($hari = 0; $hari < 5; $hari++) {
            $tanggalJadwal = $tanggalSekarang->copy()->addDays($hari);

            foreach ($kelasCollection as $k) {
                // Buat 4 sesi pelajaran per hari untuk setiap kelas
                for ($sesi = 1; $sesi <= 4; $sesi++) {
                    $jamMulai = Carbon::createFromTime(7, 0, 0)->addHours($sesi - 1)->addMinutes(($sesi - 1) * 30);
                    $jamSelesai = $jamMulai->copy()->addMinutes(90); // Durasi 90 menit

                    Jadwal::create([
                        'mata_pelajaran' => $mataPelajaran[array_rand($mataPelajaran)],
                        'deskripsi'      => 'Pembelajaran reguler di kelas.',
                        'tanggal'        => $tanggalJadwal->toDateString(),
                        'jam_mulai'      => $jamMulai->toTimeString(),
                        'jam_selesai'    => $jamSelesai->toTimeString(),
                        'guru_id'        => $gurus->random()->id,
                        'kelas_id'       => $k->id,
                        // DIHAPUS: jurusan_id dan jenjang karena sudah ada di relasi Kelas
                        'tipe'           => 'pelajaran',
                    ]);
                    $totalPelajaran++;
                }
            }
        }
        $this->command->info("   > {$totalPelajaran} jadwal pelajaran berhasil dibuat.");
    }

    /**
     * Membuat event sekolah dummy dalam 30 hari ke depan.
     */
    private function buatEventSekolah($gurus)
    {
        $eventTypes = [
            'Rapat Wali Murid' => 'Pembahasan perkembangan akademik semester ganjil.',
            'Seminar Karir' => 'Seminar motivasi bersama praktisi industri.',
            'Class Meeting' => 'Lomba persahabatan antar kelas.',
            'Ujian Tengah Semester' => 'Pelaksanaan Ujian Tengah Semester untuk semua jenjang.',
            'Peringatan Hari Besar' => 'Upacara dan kegiatan dalam rangka memperingati hari besar nasional.'
        ];
        $totalEvent = 0;
        $today = Carbon::today();

        for ($i = 0; $i < 5; $i++) {
            $randomDate = $today->copy()->addDays(rand(1, 30));
            $eventType = array_rand($eventTypes);

            Jadwal::create([
                'mata_pelajaran' => $eventType, // Menyimpan nama event di kolom mata_pelajaran
                'deskripsi'      => $eventTypes[$eventType],
                'tanggal'        => $randomDate->toDateString(),
                'jam_mulai'      => '08:00:00',
                'jam_selesai'    => '11:00:00',
                // Membuat beberapa event tidak terikat guru/kelas spesifik (acara umum)
                'guru_id'        => rand(0, 1) ? $gurus->random()->id : null,
                'kelas_id'       => null, // Event umum tidak terikat pada satu kelas
                'tipe'           => 'acara',
            ]);
            $totalEvent++;
        }
        $this->command->info("   > {$totalEvent} event sekolah berhasil dibuat.");
    }
}
