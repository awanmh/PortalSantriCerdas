<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Buat permissions (hanya jika belum ada)
        $permissions = [
            // Absensi
            'absensi-siswa-create',
            'absensi-siswa-view',
            'absensi-guru-create',
            'absensi-guru-view',
            'absensi-bk-view',

            // Zona
            'zona-manage',
            'zona-view',

            // Pelanggaran
            'pelanggaran-create',
            'pelanggaran-view',
            'pelanggaran-manage',

            // Kelas
            'kelas-view',
            'kelas-manage',

            // Jadwal
            'jadwal-view',
            'jadwal-manage',

            // User
            'user-view',
            'user-manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat role dan assign permissions (hanya jika belum ada)
        $siswa = Role::firstOrCreate(['name' => 'siswa']);
        $siswa->syncPermissions([
            'absensi-siswa-create',
            'absensi-siswa-view',
            'jadwal-view',
            'kelas-view'
        ]);

        $guru = Role::firstOrCreate(['name' => 'guru']);
        $guru->syncPermissions([
            'absensi-guru-create',
            'absensi-guru-view',
            'absensi-siswa-view',
            'pelanggaran-create',
            'jadwal-view',
            'kelas-view'
        ]);

        $it = Role::firstOrCreate(['name' => 'it']);
        $it->syncPermissions([
            'zona-manage',
            'absensi-guru-view',
            'absensi-siswa-view',
            'pelanggaran-view',
            'kelas-manage',
            'user-manage'
        ]);

        $bk = Role::firstOrCreate(['name' => 'bk']);
        $bk->syncPermissions([
            'pelanggaran-view',
            'pelanggaran-manage',
            'absensi-siswa-view',
            'absensi-guru-view',
            'jadwal-view',
            'kelas-view'
        ]);

        // Assign role default ke user admin (hanya jika belum memiliki role)
        $user = \App\Models\User::first();
        if ($user && !$user->hasRole(['siswa', 'guru', 'it', 'bk'])) {
            $user->assignRole('it');
        }
    }
}