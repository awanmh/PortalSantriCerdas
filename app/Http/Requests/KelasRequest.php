<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Izin sudah di-handle oleh middleware di rute
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $kelasId = $this->route('kela')?->id; // Mengambil ID kelas dari rute saat update

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                // Nama kelas harus unik, misal "X RPL 1" tidak boleh ada dua
                Rule::unique('kelas', 'nama')->ignore($kelasId),
            ],
            'jurusan_id' => [
                'required',
                'integer',
                // Pastikan ID jurusan yang dikirim ada di tabel 'jurusan'
                Rule::exists('jurusan', 'id'),
            ],
            'wali_kelas_id' => [
                'required',
                'integer',
                // Pastikan ID user yang dipilih ada di tabel 'users'
                Rule::exists('users', 'id'),
                // Pastikan seorang guru tidak menjadi wali kelas di lebih dari satu kelas
                Rule::unique('kelas', 'wali_kelas_id')->ignore($kelasId),
            ],
        ];
    }
}
