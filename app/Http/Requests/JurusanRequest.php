<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JurusanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Izinkan semua pengguna yang sudah terotentikasi dan memiliki izin yang tepat
        // (Izin sudah di-handle di level rute, jadi di sini kita set true)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Dapatkan ID jurusan dari rute, jika ada (untuk operasi update)
        $jurusanId = $this->route('jurusan')?->id;

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                // Pastikan nama jurusan unik.
                // Saat update, abaikan data jurusan yang sedang diedit.
                Rule::unique('jurusan', 'nama')->ignore($jurusanId),
            ],
        ];
    }
}
