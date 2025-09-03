<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Ekstrak role dari email
        $email = strtolower($request->email);
        $role = 'siswa'; // default role
        $roleKeyword = '';

        // Cek untuk role guru
        if (preg_match('/\.(guru|pengajar|teacher)@/', $email, $matches)) {
            $role = 'guru';
            $roleKeyword = $matches[1];
        }
        // Cek untuk role IT
        else if (preg_match('/\.(it|teknisi|troubleshoot)@/', $email, $matches)) {
            $role = 'it';
            $roleKeyword = $matches[1];
        }
        // Cek untuk role BK
        else if (preg_match('/\.(bk|konseling|psikolog)@/', $email, $matches)) {
            $role = 'bk';
            $roleKeyword = $matches[1];
        }
        // Role siswa sudah sebagai default

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role ke user
        $user->assignRole($role);

        // Jika role siswa, assign ke kelas default
        if ($role === 'siswa') {
            $kelas = Kelas::first();
            if ($kelas) {
                $user->kelas()->attach($kelas->id);
            }
        }

        // Simpan informasi role keyword untuk referensi
        $user->update([
            'role_keyword' => $roleKeyword
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard'));
    }
}