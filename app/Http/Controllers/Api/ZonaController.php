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
        // Menggunakan kolom 'nama_zona', 'lat', 'lng' yang sesuai migrasi
        $validator = Validator::make($request->all(), [
            'nama_zona' => 'required|string|max:255',
            'lat'       => 'required|numeric',
            'lng'       => 'required|numeric',
            'radius'    => 'required|numeric|min:10|max:500',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->is_active) {
            Zona::where('is_active', true)->update(['is_active' => false]);
        }

        $zona = Zona::create($request->all());

        return response()->json([
            'message' => 'Zona berhasil ditambahkan',
            'data' => $zona
        ], 201);
    }

    // ... method update dan destroy ...
}
