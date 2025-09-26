<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar pengguna, dengan opsi filter berdasarkan peran.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // ambil lebih banyak field biar frontend bisa render
        $users = $query->with('roles:name')
            ->orderBy('name', 'asc')
            ->get(['id','name','email','role_keyword','foto']);

        return response()->json($users);
    }

    /**
     * Menampilkan detail user.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::with('roles:name','kelas')->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update data user.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role_keyword' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'user' => $user
        ]);
    }

    /**
     * Hapus user.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // hapus foto profil kalau ada
        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
