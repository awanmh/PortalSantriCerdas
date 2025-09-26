<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CatatanPelanggaranRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan untuk membuat permintaan ini.
     * Hanya pengguna dengan peran 'guru' atau 'bk' yang diizinkan.
     */
    public function authorize(): bool
    {
        // Mengambil pengguna yang sedang terotentikasi
        $user = $this->user();

        // Mengembalikan true jika pengguna memiliki salah satu dari peran yang diizinkan
        return $user && $user->hasAnyRole(['guru', 'bk', 'it']);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
            'jenis' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'poin' => ['required', 'integer', 'min:1', 'max:100'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ];
    }

    /**
     * Dapatkan pesan error kustom untuk aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'Siswa wajib dipilih.',
            'user_id.exists' => 'Siswa yang dipilih tidak valid.',
            'jenis.required' => 'Jenis pelanggaran wajib diisi.',
            'deskripsi.required' => 'Deskripsi tidak boleh kosong.',
            'poin.required' => 'Poin pelanggaran wajib diisi.',
            'poin.integer' => 'Poin harus berupa angka.',
            'tanggal.required' => 'Tanggal kejadian wajib diisi.',
        ];
    }
}

