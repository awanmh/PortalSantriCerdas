<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // reset cache (aman)
        Artisan::call('permission:cache-reset');

        // 1) Permissions (berikan deskripsi singkat di komentar)
        $perms = [
            // Admin (system/tim IT)
            'manage_users',         // create/update/delete user accounts
            'manage_classes',       // CRUD kelas
            'manage_zones',         // CRUD geofence/zone
            'manage_devices',       // register/remove fingerprint devices
            'view_audit_logs',      // lihat log audit

            // Guru pengajar
            'view_schedule',
            'view_class_students',
            'manage_attendance',    // cek / koreksi absensi kelas (terbatas)
            'input_grades',
            'view_reports',

            // Guru BK
            'view_bk_reports',
            'create_bk_report',
            'update_bk_report',
            'delete_bk_report',
            'view_student_progress',
            'counsel_student',
            'manage_behavior_notes',

            // Siswa
            'view_own_attendance',
            'absen_web',            // mengirim konfirmasi web (login & lokasi)
            'view_own_grades',
            'create_consultation_request',
        ];

        // Create permissions if not exist
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // 2) Roles
        $roles = [
            'admin',     // tim IT / Admin sistem
            'guru',      // guru pengajar
            'guru_bk',   // guru bimbingan konseling
            'siswa',     // siswa
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // 3) Assign permissions to roles
        // Admin gets everything
        $admin = Role::where('name', 'admin')->first();
        $admin->syncPermissions(Permission::all());

        // Guru (pengajar)
        $guru = Role::where('name', 'guru')->first();
        $guru->syncPermissions([
            'view_schedule',
            'view_class_students',
            'manage_attendance',
            'input_grades',
            'view_reports',
        ]);

        // Guru BK
        $bk = Role::where('name', 'guru_bk')->first();
        $bk->syncPermissions([
            'view_bk_reports',
            'create_bk_report',
            'update_bk_report',
            'delete_bk_report',
            'view_student_progress',
            'counsel_student',
            'manage_behavior_notes',
        ]);

        // Siswa
        $siswaRole = Role::where('name', 'siswa')->first();
        $siswaRole->syncPermissions([
            'view_own_attendance',
            'absen_web',
            'view_own_grades',
            'create_consultation_request',
        ]);

        // 4) Create sample users (only if not exist) and assign roles
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@smk.local'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('Admin@12345'),
            ]
        );
        $adminUser->assignRole('admin');

        $guruUser = User::firstOrCreate(
            ['email' => 'guru@smk.local'],
            [
                'name' => 'Pak Budi (Guru)',
                'password' => Hash::make('Guru@12345'),
            ]
        );
        $guruUser->assignRole('guru');

        $bkUser = User::firstOrCreate(
            ['email' => 'guru_bk@smk.local'],
            [
                'name' => 'Bu Sari (Guru BK)',
                'password' => Hash::make('Bk@12345'),
            ]
        );
        $bkUser->assignRole('guru_bk');

        $siswaUser = User::firstOrCreate(
            ['email' => 'siswa@smk.local'],
            [
                'name' => 'Andi (Siswa)',
                'password' => Hash::make('Siswa@12345'),
            ]
        );
        $siswaUser->assignRole('siswa');

        // reset permission cache to apply assignments
        Artisan::call('permission:cache-reset');
    }
}