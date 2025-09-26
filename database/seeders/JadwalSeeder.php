<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Pastikan data prasyarat tersedia
            if (
                !Kelas::exists() ||
                !User::role('guru')->whereNotNull('subject_taught')->exists() ||
                !MataPelajaran::exists()
            ) {
                $this->command->warn('⚠️ Seeder Jadwal dilewati: Data Kelas, Guru, atau Mata Pelajaran belum tersedia.');
                return;
            }

            // Reset data lama
            Jadwal::truncate();

            $this->buatJadwalPelajaran();
            $this->buatJadwalAcara();
        });

        $this->command->info('✅ Seeder Jadwal berhasil dijalankan!');
    }

    /**
     * Membuat jadwal pelajaran rutin Senin–Jumat.
     */
    private function buatJadwalPelajaran(): void
    {
        $kelasCollection = Kelas::all();
        $gurus = User::role('guru')->whereNotNull('subject_taught')->get()->shuffle();
        $hariKerja = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamSesi = ['07:00:00', '08:30:00', '10:30:00', '12:30:00'];
        $totalPelajaran = 0;
        $guruIndex = 0;

        foreach ($hariKerja as $hari) {
            foreach ($kelasCollection as $kelas) {
                foreach ($jamSesi as $jam) {
                    $assignedGuru = $gurus[$guruIndex];

                    // Cari ID mata pelajaran berdasarkan subject_taught guru
                    $mapel = MataPelajaran::where('nama', $assignedGuru->subject_taught)->first();

                    if (!$mapel) {
                        $this->command->warn("   > Mata pelajaran '{$assignedGuru->subject_taught}' tidak ditemukan. Lewati.");
                        continue;
                    }

                    Jadwal::create([
                        'mata_pelajaran_id' => $mapel->id,
                        'deskripsi'         => 'Pelajaran ' . $mapel->nama,
                        'hari'              => $hari,
                        'jam_mulai'         => $jam,
                        'jam_selesai'       => date('H:i:s', strtotime($jam . ' +90 minutes')),
                        'guru_id'           => $assignedGuru->id,
                        'kelas_id'          => $kelas->id,
                        'tipe'              => 'pelajaran',
                    ]);

                    $totalPelajaran++;
                    $guruIndex = ($guruIndex + 1) % $gurus->count();
                }
            }
        }

        $this->command->info("   > {$totalPelajaran} jadwal pelajaran berhasil dibuat.");
    }

    /**
     * Membuat jadwal acara sekolah.
     */
    private function buatJadwalAcara(): void
    {
        $eventTypes = [
            'Rapat Wali Murid' => 'Pembahasan perkembangan akademik.',
            'Seminar Karir'    => 'Seminar motivasi bersama praktisi industri.',
            'Class Meeting'    => 'Lomba persahabatan antar kelas.',
        ];

        $totalEvent = 0;

        foreach ($eventTypes as $namaAcara => $deskripsi) {
            Jadwal::create([
                'mata_pelajaran_id' => null, // acara tidak terkait mapel
                'deskripsi'         => $deskripsi,
                'tanggal'           => Carbon::today()->addDays(rand(5, 20))->toDateString(),
                'hari'              => null,
                'jam_mulai'         => '08:00:00',
                'jam_selesai'       => '11:00:00',
                'guru_id'           => null,
                'kelas_id'          => null,
                'tipe'              => 'acara',
            ]);
            $totalEvent++;
        }

        $this->command->info("   > {$totalEvent} jadwal acara sekolah berhasil dibuat.");
    }
}
