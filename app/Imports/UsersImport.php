<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Ekstrak role dari email
        $email = strtolower($row['email']);
        $role = 'siswa'; // default role

        if (preg_match('/\.(guru|pengajar|teacher)@/', $email)) {
            $role = 'guru';
        } else if (preg_match('/\.(it|teknisi|troubleshoot)@/', $email)) {
            $role = 'it';
        } else if (preg_match('/\.(bk|konseling|psikolog)@/', $email)) {
            $role = 'bk';
        }

        $user = new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'),
        ]);

        // Assign role setelah user dibuat
        $user->save();
        $user->assignRole($role);

        // Jika role siswa, assign ke kelas
        if ($role === 'siswa' && isset($row['kelas_id'])) {
            $user->kelas()->attach($row['kelas_id']);
        }

        return $user;
    }
}
