<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi dengan data jurusan.
     */
    public function create(): Response
    {
        $jurusan = Jurusan::orderBy('nama')->get(['id', 'nama']);

        return Inertia::render('Auth/Register', [
            'jurusan' => $jurusan,
        ]);
    }

    /**
     * Menangani permintaan registrasi yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Ganti 'smkalikhlash.sch.id' dengan domain email sekolah Anda yang sebenarnya
        $schoolDomain = 'smkalikhlash.sch.id';

        $request->validate([
            'name' => 'required|string|max:255',
            // --- PERUBAHAN DI SINI ---
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                'ends_with:@'.$schoolDomain
            ],
            // --- AKHIR PERUBAHAN ---
            'jurusan_id' => ['required', 'integer', Rule::exists('jurusan', 'id')],
            'angkatan' => 'required|digits:4|integer|min:2015',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            // Menambahkan pesan error kustom agar lebih jelas
            'email.ends_with' => 'Pendaftaran harus menggunakan email resmi sekolah (@'.$schoolDomain.').'
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->siswa()->create([
                'nama' => $user->name,
                'jurusan_id' => $request->jurusan_id,
                'angkatan' => $request->angkatan,
            ]);
            
            $user->assignRole('siswa');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

