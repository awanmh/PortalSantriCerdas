<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan data dasar sudah ada
        if (!Kelas::exists() || !Jurusan::exists()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Kelas atau Jurusan belum ada.');
            $this->command->info('💡 Silakan jalankan seeder Kelas dan Jurusan terlebih dahulu.');
            return;
        }

        // 2. Ambil guru dengan role yang benar
        $gurus = User::role('guru')->get();
        if ($gurus->isEmpty()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Tidak ada user dengan role guru.');
            $this->command->info('💡 Silakan buat user dengan role guru terlebih dahulu.');
            return;
        }

        // 3. Buat jadwal pelajaran untuk semua kelas
        $this->buatJadwalPelajaran($gurus);

        // 4. Buat event untuk kalender sekolah
        $this->buatEventSekolah($gurus);

        $this->command->info('✅ Seeder Jadwal berhasil dijalankan!');
    }

    private function buatJadwalPelajaran($gurus)
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $mataPelajaran = [
            'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS',
            'Seni Budaya', 'PJOK', 'PPKn', 'TIK', 'Agama'
        ];
        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $today = Carbon::today();
        $totalPelajaran = 0;

        foreach ($kelas as $k) {
            foreach ($hari as $day) {
                $dayOfWeek = array_search($day, ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
                $date = $today->copy()->next($dayOfWeek + 1);

                for ($i = 1; $i <= 4; $i++) {
                    $jamMulai = sprintf('%02d:00:00', 7 + ($i - 1) * 2);
                    $jamSelesai = sprintf('%02d:30:00', 8 + ($i - 1) * 2);

                    Jadwal::create([
                        'mata_pelajaran' => $mataPelajaran[array_rand($mataPelajaran)],
                        'deskripsi' => 'Pembelajaran ' . $mataPelajaran[array_rand($mataPelajaran)] . ' untuk kelas ' . $k->nama_kelas,
                        'tanggal' => $date->format('Y-m-d'),
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'guru_id' => $gurus->random()->id,
                        'kelas_id' => $k->id,
                        'jurusan_id' => $jurusan->random()->id,
                        'jenjang' => $k->tingkat,
                        'tipe' => 'pelajaran',
                    ]);
                    $totalPelajaran++;
                }
            }
        }
        $this->command->info(" • {$totalPelajaran} jadwal pelajaran berhasil dibuat");
    }

    private function buatEventSekolah($gurus)
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $eventTypes = [
            'Pelatihan' => 'Pelatihan penggunaan laboratorium komputer',
            'Seminar' => 'Seminar karir dengan alumni',
            'Lomba' => 'Lomba cerdas cermat antar kelas',
            'Ekstrakurikuler' => 'Ekstrakurikuler pramuka',
            'Kegiatan' => 'Kegiatan bakti sosial',
            'Ujian' => 'Ujian tengah semester',
            'Pertemuan' => 'Pertemuan orang tua murid'
        ];
        $totalEvent = 0;
        $today = Carbon::today();

        for ($i = 0; $i < 15; $i++) {
            $randomDate = $today->copy()->addDays(rand(1, 30));
            $eventType = array_rand($eventTypes);

            Jadwal::create([
                'mata_pelajaran' => $eventType, // Menyimpan nama event di kolom mata_pelajaran
                'deskripsi' => $eventTypes[$eventType],
                'tanggal' => $randomDate->format('Y-m-d'),
                'jam_mulai' => sprintf('%02d:00:00', rand(8, 15)),
                'jam_selesai' => sprintf('%02d:00:00', rand(16, 18)),
                'guru_id' => $gurus->random()->id,
                'kelas_id' => $kelas->random()->id,
                'jurusan_id' => $jurusan->random()->id,
                'jenjang' => strval(rand(10, 12)),
                'tipe' => 'event',
            ]);
            $totalEvent++;
        }
        $this->command->info(" • {$totalEvent} event sekolah berhasil dibuat");
    }
}
