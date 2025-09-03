<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AbsensiSiswaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Izinkan request jika user yang login adalah siswa
        return Auth::check() && $this->user()->hasRole('siswa');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'lat'  => ['required', 'numeric', 'between:-90,90'],
            'lng'  => ['required', 'numeric', 'between:-180,180'],
        ];
    }
}