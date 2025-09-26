<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage jadwal');
    }

    public function rules(): array
    {
        return [
            'mata_pelajaran_id' => [
                'required',
                'integer',
                Rule::exists('mata_pelajarans', 'id'),
            ],
            'deskripsi' => ['nullable', 'string'],
            'tipe' => [
                'required',
                Rule::in(['pelajaran', 'acara']),
            ],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kelas_id' => [
                Rule::requiredIf($this->tipe === 'pelajaran'),
                'nullable',
                'integer',
                Rule::exists('kelas', 'id'),
            ],
            'guru_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'hari' => [
                Rule::requiredIf($this->tipe === 'pelajaran'),
                'nullable',
                'string',
                Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']),
            ],
            'tanggal' => [
                Rule::requiredIf($this->tipe === 'acara'),
                'nullable',
                'date',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran yang dipilih tidak valid.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
            'kelas_id.required' => 'Kelas wajib diisi untuk jadwal pelajaran.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'guru_id.exists' => 'Guru yang dipilih tidak valid.',
            'hari.required' => 'Hari wajib diisi untuk jadwal pelajaran.',
            'hari.in' => 'Hari tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi untuk jadwal acara.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tipe.in' => 'Tipe jadwal tidak valid.',
        ];
    }
}
