<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan data Kelas & Guru tersedia
        if (!Kelas::exists()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Data Kelas belum ada.');
            return;
        }

        $gurus = User::role('guru')->get();
        if ($gurus->isEmpty()) {
            $this->command->warn('⚠️ Seeder Jadwal gagal: Tidak ada user dengan role guru.');
            return;
        }

        // Hapus jadwal lama
        Jadwal::truncate();

        // Buat jadwal tetap per hari
        $this->buatJadwalTetap($gurus);

        // Buat event sekolah khusus
        $this->buatEventSekolah($gurus);

        $this->command->info('✅ Seeder Jadwal berhasil dijalankan!');
    }

    /**
     * Membuat jadwal pelajaran tetap per hari (Senin–Jumat)
     */
    private function buatJadwalTetap($gurus)
    {
        $kelasCollection = Kelas::all();
        $mataPelajaran = [
            'Matematika Wajib', 'Bahasa Indonesia', 'Bahasa Inggris', 
            'Fisika', 'Kimia', 'Sejarah Indonesia', 'PJOK', 
            'PPKn', 'Dasar Desain Grafis', 'Pendidikan Agama'
        ];
        $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $totalPelajaran = 0;

        foreach ($hariKerja as $hari) {
            foreach ($kelasCollection as $kelas) {
                // 4 sesi per hari
                for ($sesi = 0; $sesi < 4; $sesi++) {
                    $jamMulai = date('H:i:s', strtotime("07:00 +".($sesi*90)." minutes"));
                    $jamSelesai = date('H:i:s', strtotime($jamMulai." +90 minutes"));

                    Jadwal::create([
                        'mata_pelajaran' => $mataPelajaran[array_rand($mataPelajaran)],
                        'deskripsi'      => 'Pembelajaran reguler di kelas.',
                        'hari'           => $hari, // <-- disesuaikan
                        'jam_mulai'      => $jamMulai,
                        'jam_selesai'    => $jamSelesai,
                        'guru_id'        => $gurus->random()->id,
                        'kelas_id'       => $kelas->id,
                        'tipe'           => 'pelajaran',
                    ]);
                    $totalPelajaran++;
                }
            }
        }

        $this->command->info("   > {$totalPelajaran} jadwal pelajaran tetap berhasil dibuat.");
    }

    /**
     * Buat event sekolah dengan tanggal khusus
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

        for ($i = 0; $i < 5; $i++) {
            $randomDate = now()->addDays(rand(1, 30));
            $eventType = array_rand($eventTypes);

           Jadwal::create([
    'mata_pelajaran' => $eventType,
    'deskripsi'      => $eventTypes[$eventType],
    'tanggal'        => $randomDate->toDateString(),
    'hari'           => null,
    'jam_mulai'      => '08:00:00',
    'jam_selesai'    => '11:00:00',
    'guru_id'        => rand(0,1) ? $gurus->random()->id : null,
    'kelas_id'       => null,
    'tipe'           => 'acara',
]);


            $totalEvent++;
        }

        $this->command->info("   > {$totalEvent} event sekolah berhasil dibuat.");
    }
}
