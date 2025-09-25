<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('manage jadwal');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string'],
            'tipe'           => ['required', 'string', Rule::in(['pelajaran', 'acara'])], // Pastikan tipe hanya 'pelajaran' atau 'acara'
            'jam_mulai'      => ['required', 'date_format:H:i'],
            'jam_selesai'    => ['required', 'date_format:H:i', 'after:jam_mulai'],

            // Validasi kondisional berdasarkan 'tipe'
            'kelas_id'       => [
                Rule::requiredIf($this->tipe === 'pelajaran'), // Wajib jika tipe pelajaran
                'nullable', // Bisa null jika tipe acara
                'integer',
                Rule::exists('kelas', 'id')
            ],
            'guru_id'        => [
                // Guru bisa nullable untuk acara, atau jika pelajaran tapi belum ditentukan
                'nullable',
                'integer',
                Rule::exists('users', 'id')
            ],
            'hari'           => [
                Rule::requiredIf($this->tipe === 'pelajaran'), // Wajib jika tipe pelajaran
                'nullable', // Bisa null jika tipe acara
                'string',
                Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']), // Opsional: batasi nilai hari
            ],
            'tanggal'        => [
                Rule::requiredIf($this->tipe === 'acara'), // Wajib jika tipe acara
                'nullable', // Bisa null jika tipe pelajaran
                'date',
            ],
        ];
    }

    /**
     * Pesan error kustom untuk validasi.
     */
    public function messages(): array
    {
        return [
            'jam_selesai.after'    => 'Jam selesai harus setelah jam mulai.',
            'kelas_id.required'    => 'Kolom kelas wajib diisi untuk jadwal pelajaran.',
            'guru_id.exists'       => 'Guru yang dipilih tidak valid.',
            'hari.required'        => 'Kolom hari wajib diisi untuk jadwal pelajaran.',
            'tanggal.required'     => 'Kolom tanggal wajib diisi untuk jadwal acara.',
            'tipe.in'              => 'Tipe jadwal tidak valid.',
        ];
    }
}