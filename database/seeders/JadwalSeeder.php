<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Memastikan data prasyarat sudah ada
            if (!Kelas::exists() || !User::role('guru')->whereNotNull('subject_taught')->exists()) {
                $this->command->warn('⚠️ Seeder Jadwal gagal: Data Kelas atau Guru dengan `subject_taught` belum ada. Jalankan seeder yang relevan terlebih dahulu.');
                return;
            }

            // Menghapus jadwal lama untuk memulai dari awal
            Jadwal::truncate();

            $this->buatJadwalPelajaran();
            $this->buatJadwalAcara();
        });

        $this->command->info('✅ Seeder Jadwal berhasil dijalankan!');
    }

    /**
     * Membuat jadwal pelajaran rutin yang logis dari Senin-Jumat.
     * Setiap kelas akan memiliki jadwal penuh dan setiap guru akan mengajar mata pelajarannya.
     */
    private function buatJadwalPelajaran(): void
    {
        $kelasCollection = Kelas::all();
        $gurus = User::role('guru')->whereNotNull('subject_taught')->get()->shuffle(); // Ambil dan acak urutan guru
        $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamSesi = ['07:00:00', '08:30:00', '10:30:00', '12:30:00'];
        $totalPelajaran = 0;
        $guruIndex = 0;

        foreach ($hariKerja as $hari) {
            foreach ($kelasCollection as $kelas) {
                foreach ($jamSesi as $jam) {
                    // Ambil guru berikutnya dalam urutan, kembali ke awal jika sudah habis (round-robin)
                    $assignedGuru = $gurus[$guruIndex];

                    Jadwal::create([
                        'mata_pelajaran' => $assignedGuru->subject_taught, // Mata pelajaran sesuai spesialisasi guru
                        'deskripsi'      => 'Pelajaran ' . $assignedGuru->subject_taught,
                        'hari'           => $hari,
                        'jam_mulai'      => $jam,
                        'jam_selesai'    => date('H:i:s', strtotime($jam . ' +90 minutes')),
                        'guru_id'        => $assignedGuru->id,
                        'kelas_id'       => $kelas->id,
                        'tipe'           => 'pelajaran',
                    ]);

                    $totalPelajaran++;

                    // Pindah ke guru selanjutnya
                    $guruIndex = ($guruIndex + 1) % $gurus->count();
                }
            }
        }
        $this->command->info("   > {$totalPelajaran} jadwal pelajaran berhasil dibuat.");
    }

    /**
     * Membuat jadwal acara sekolah dengan tanggal spesifik.
     */
    private function buatJadwalAcara(): void
    {
        $eventTypes = [
            'Rapat Wali Murid' => 'Pembahasan perkembangan akademik.',
            'Seminar Karir' => 'Seminar motivasi bersama praktisi industri.',
            'Class Meeting' => 'Lomba persahabatan antar kelas.',
        ];

        $totalEvent = 0;
        foreach ($eventTypes as $namaAcara => $deskripsi) {
            Jadwal::create([
                'mata_pelajaran' => $namaAcara,
                'deskripsi'      => $deskripsi,
                'tanggal'        => Carbon::today()->addDays(rand(5, 20))->toDateString(),
                'hari'           => null,
                'jam_mulai'      => '08:00:00',
                'jam_selesai'    => '11:00:00',
                'guru_id'        => null,
                'kelas_id'       => null,
                'tipe'           => 'acara',
            ]);
            $totalEvent++;
        }
        $this->command->info("   > {$totalEvent} jadwal acara sekolah berhasil dibuat.");
    }
}

