<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Reset cached roles and permissions
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            // ====== Definisi Permissions ======
            $permissions = [
                'view dashboard', 'manage users', 'manage jurusan',
                'manage kelas', 'manage jadwal', 'manage zona',
                'view laporan absensi', 'manage catatan pelanggaran',
                'access live map', 'absen mandiri',
            ];

            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }

            // ====== Definisi Roles ======
            $roleIt = Role::firstOrCreate(['name' => 'it', 'guard_name' => 'web']);
            $roleBk = Role::firstOrCreate(['name' => 'bk', 'guard_name' => 'web']);
            $roleGuru = Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
            $roleSiswa = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

            // ====== Mapping Role -> Permission ======
            
            // IT sebagai super admin → semua permission
            $roleIt->syncPermissions(Permission::all());

            // Guru
            $roleGuru->syncPermissions([
                'view dashboard',
                'access live map',
                'manage catatan pelanggaran', // Guru bisa membuat, melihat, mengedit, & menghapus catatannya sendiri
            ]);

            // BK
            $roleBk->syncPermissions([
                'view dashboard',
                'access live map',
                'manage catatan pelanggaran', // BK bisa mengelola semua catatan
                'view laporan absensi',
            ]);

            // Siswa
            $roleSiswa->syncPermissions([
                'view dashboard',
                'absen mandiri',
            ]);
        });

        $this->command->info('✅ Role & Permission Seeder berhasil dijalankan!');
    }
}