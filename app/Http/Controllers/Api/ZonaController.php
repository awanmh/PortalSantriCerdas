<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // <-- DITAMBAHKAN
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZonaController extends Controller
{
    public function index()
    {
        // Menggunakan kolom 'nama_zona' yang sesuai migrasi
        $zona = Zona::orderBy('nama_zona', 'asc')->get();
        return response()->json($zona);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_zona'  => 'required|string|max:255',
            'lat'        => 'required|numeric',
            'lng'        => 'required|numeric',
            'radius'     => 'required|numeric|min:10|max:500',
            'is_active'  => 'boolean',
            'name'       => 'nullable|string|max:255',
            'polygon'    => 'nullable|string',
            'description'=> 'nullable|string',
            'color'      => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // hanya boleh ada 1 zona aktif
        if ($request->boolean('is_active')) {
            Zona::where('is_active', true)->update(['is_active' => false]);
        }

        $zona = Zona::create($validator->validated());

        return response()->json([
            'message' => 'Zona berhasil ditambahkan',
            'data' => $zona
        ], 201);
    }
}