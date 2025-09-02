<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;

class UserManagementController extends Controller
{
    /**
     * Import data user dari file Excel
     */
    public function importUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return response()->json([
                'message' => 'Data user berhasil diimport'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengimport data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload foto profil user
     */
    public function uploadProfilePicture(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Hapus foto lama jika ada
        if ($user->foto && file_exists(public_path('storage/' . $user->foto))) {
            unlink(public_path('storage/' . $user->foto));
        }

        // Simpan foto baru
        $fotoPath = $request->file('foto')->store('profile', 'public');
        $user->update(['foto' => $fotoPath]);

        return response()->json([
            'message' => 'Foto profil berhasil diupload',
            'foto_url' => asset('storage/' . $fotoPath)
        ]);
    }
}