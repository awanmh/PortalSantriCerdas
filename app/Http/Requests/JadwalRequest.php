<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya pengguna dengan izin 'manage jadwal' yang diizinkan.
     */
    public function authorize(): bool
    {
        // Memastikan pengguna yang login memiliki izin yang diperlukan.
        return $this->user()->can('manage jadwal');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string'],
            'tanggal'        => ['required', 'date'],
            'jam_mulai'      => ['required', 'date_format:H:i'],
            'jam_selesai'    => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'tipe'           => ['required', 'string', 'max:50'],
            'kelas_id'       => ['required', 'integer', Rule::exists('kelas', 'id')],
            'guru_id'        => ['nullable', 'integer', Rule::exists('users', 'id')],
        ];
    }

    /**
     * Pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'kelas_id.required' => 'Kolom kelas wajib diisi.',
            'guru_id.exists'    => 'Guru yang dipilih tidak valid.',
        ];
    }
}