// app/Http/Controllers/Api/ZonaController.php
<?php

namespace App\Http\Controllers\Api;

use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZonaController extends Controller
{
    public function index()
    {
        $zona = Zona::orderBy('nama', 'asc')->get();
        return response()->json($zona);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:10|max:500',
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