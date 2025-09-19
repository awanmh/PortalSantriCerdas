<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ====== Definisi permissions ======
        $permissions = [
            // Absensi
            'create absensi siswa',
            'view absensi siswa',
            'create absensi guru',
            'view absensi guru',
            'view absensi bk',

            // Zona
            'manage zona',
            'view zona',

            // Pelanggaran (dibuat lebih spesifik)
            'create catatan pelanggaran',
            'view catatan pelanggaran',
            'update catatan pelanggaran',
            'delete catatan pelanggaran',

            // Kelas & Jurusan
            'view kelas',
            'manage kelas',
            'manage jurusan',

            // Jadwal
            'view jadwal',
            'manage jadwal',

            // User
            'view users',
            'manage users',
            
            // Laporan
            'view laporan absensi',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ====== Definisi roles ======
        $siswa = Role::firstOrCreate(['name' => 'siswa']);
        $guru  = Role::firstOrCreate(['name' => 'guru']);
        $bk    = Role::firstOrCreate(['name' => 'bk']);
        $it    = Role::firstOrCreate(['name' => 'it']);

        // ====== Mapping role -> permission ======
        $siswa->syncPermissions([
            'create absensi siswa',
            'view absensi siswa',
            'view jadwal',
            'view kelas',
        ]);

        $guru->syncPermissions([
            'create absensi guru',
            'view absensi guru',
            'view absensi siswa',
            'create catatan pelanggaran', // Guru hanya bisa membuat
            'view catatan pelanggaran',
            'view jadwal',
            'view kelas',
            'view laporan absensi',
        ]);

        $bk->syncPermissions([
            'create catatan pelanggaran', // BK bisa melakukan semuanya
            'view catatan pelanggaran',
            'update catatan pelanggaran',
            'delete catatan pelanggaran',
            'view absensi siswa',
            'view absensi guru',
            'view jadwal',
            'view kelas',
            'view laporan absensi',
        ]);

        // IT sebagai super admin → semua permission
        $it->syncPermissions(Permission::all());

        // ====== User default ======
        $admin = User::firstOrCreate(
            ['email' => 'admin@smkalikhlash.sch.id'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password'),
            ]
        );

        if (!$admin->hasRole('it')) {
            $admin->assignRole('it');
        }
    }
}

