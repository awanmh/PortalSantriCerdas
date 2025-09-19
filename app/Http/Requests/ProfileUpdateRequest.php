<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var \App\Models\User $user */
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            // Foto sekarang 'required' (wajib) HANYA JIKA:
            // 1. Pengguna memiliki peran 'siswa'.
            // 2. Kolom 'profile_photo_path' di database masih kosong (null).
            'photo' => [
                Rule::requiredIf(fn () => $user->hasRole('siswa') && is_null($user->profile_photo_path)),
                'nullable', // Tetap nullable agar tidak error jika tidak ada file yang dikirim
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ];
    }

    /**
     * Pesan error kustom untuk aturan validasi foto.
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Anda harus mengunggah foto profil untuk melanjutkan.',
        ];
    }
}

