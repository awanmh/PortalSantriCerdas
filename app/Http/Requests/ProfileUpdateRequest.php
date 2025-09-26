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

        // --- PERBAIKAN DI SINI ---
        // Logika validasi sekarang dibagi menjadi dua skenario:
        // 1. Jika permintaan berisi file 'photo', kita hanya validasi foto.
        // 2. Jika tidak, kita validasi data teks (nama dan email).

        // Skenario 1: Permintaan ini adalah untuk mengunggah foto.
        if ($this->hasFile('photo')) {
            return [
                'photo' => [
                    'required', // Foto wajib ada jika ini adalah permintaan unggah foto
                    'image',
                    'mimes:jpg,jpeg,png',
                    'max:2048', // Maksimal 2MB
                ],
            ];
        }

        // Skenario 2: Permintaan ini adalah untuk memperbarui nama dan email.
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
        ];
    }

    /**
     * Pesan error kustom untuk aturan validasi foto.
     */
    public function messages(): array
    {
        return [
            'photo.required' => 'Anda harus memilih file gambar untuk diunggah.',
            'photo.image' => 'File yang dipilih harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ];
    }
}
