<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignSiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Hanya admin/IT yang dapat mengelola siswa di kelas
        return $this->user()->hasRole('it'); // Sesuaikan dengan role yang berhak
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'siswa_id' => [
                'required', 
                'integer', 
                'exists:users,id',
                // Pastikan user ini memiliki role 'siswa'
                Rule::exists('model_has_roles', 'model_id')->where(function ($query) {
                    $query->where('role_id', \Spatie\Permission\Models\Role::where('name', 'siswa')->first()->id ?? null);
                }),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'siswa_id.required' => 'ID siswa wajib diisi.',
            'siswa_id.integer' => 'ID siswa harus berupa angka.',
            'siswa_id.exists' => 'Siswa dengan ID tersebut tidak ditemukan.',
            'siswa_id.exists' => 'Siswa dengan ID tersebut tidak ditemukan atau bukan berstatus siswa.', // Custom message for rule
        ];
    }
}