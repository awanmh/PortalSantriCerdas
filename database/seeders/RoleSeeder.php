<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Hapus semua roles dan permissions jika ingin fresh start
            // Role::query()->delete();
            // Permission::query()->delete();

            // Buat roles
            $roleIt = Role::firstOrCreate(['name' => 'it', 'guard_name' => 'web']);
            $roleBk = Role::firstOrCreate(['name' => 'bk', 'guard_name' => 'web']);
            $roleGuru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
            $roleSiswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

            // Contoh permissions (sesuaikan dengan kebutuhan Anda)
            $permissions = [
                'view dashboard',
                'manage users',
                'manage roles',
                'manage permissions',
                'manage jurusan',
                'manage kelas',
                'manage jadwal',
                'manage siswa',
                'manage catatan pelanggaran',
                'view laporan absensi',
                'manage zona',
                'access live map',
                'absen mandiri', // Untuk siswa
            ];

            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }

            // Dapatkan semua permissions yang baru dibuat
            $allPermissions = Permission::all();

            // Assign permissions ke roles
            $roleIt->syncPermissions($allPermissions); // IT punya semua hak akses

            $roleGuru->syncPermissions([
                'view dashboard',
                'view laporan absensi',
                'manage catatan pelanggaran', // Guru bisa input catatan
                'manage jadwal', // Guru bisa melihat/mengelola jadwal yang diampunya
                'access live map', // Jika guru perlu melihat lokasi siswa
            ]);

            $roleBk->syncPermissions([
                'view dashboard',
                'view laporan absensi',
                'manage catatan pelanggaran', // BK mengelola catatan
            ]);

            $roleSiswa->syncPermissions([
                'view dashboard',
                'absen mandiri',
                // Siswa tidak punya hak manage, hanya lihat jadwal dan absen
            ]);
        });
        $this->command->info('✅ Role Seeder berhasil dijalankan!');
    }
}