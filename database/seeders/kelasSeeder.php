<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $jurusans = Jurusan::all();
            $gurus = User::role('guru')->get();

            if ($jurusans->isEmpty() || $gurus->isEmpty()) {
                $this->command->warn('⚠️ Data Jurusan/Guru belum ada.');
                return;
            }

            Kelas::truncate();

            $jenjangs = ['X', 'XI', 'XII'];

            foreach ($jenjangs as $jenjang) {
                foreach ($jurusans as $jurusan) {
                    Kelas::create([
                        'nama_kelas'   => "{$jenjang} {$jurusan->nama_singkat}",
                        'jenjang'      => $jenjang,
                        'jurusan_id'   => $jurusan->id,
                        'wali_kelas_id'=> $gurus->random()->id,
                    ]);
                }
            }
        });

        $this->command->info('✅ Kelas Seeder berhasil dijalankan!');
    }
}
