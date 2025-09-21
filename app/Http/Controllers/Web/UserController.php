<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

/**
 * Mengelola semua operasi CRUD untuk pengguna.
 */
class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar semua pengguna dengan paginasi.
     */
    public function index(): Response
    {
        $users = User::query()
            ->with('roles:id,name') // Eager load hanya kolom id dan name dari relasi roles
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Mengirim array roles untuk fleksibilitas di frontend
                'roles' => $user->roles->map(fn($role) => ['id' => $role->id, 'name' => $role->name]),
                'created_at' => $user->created_at,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Menampilkan halaman form untuk membuat pengguna baru.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all(['id', 'name']),
            'kelas' => Kelas::all(['id', 'nama_kelas']),
        ]);
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'kelas_id' => ['nullable', 'integer', Rule::exists('kelas', 'id')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Jika rolenya siswa dan kelas_id diberikan, masukkan ke kelas
        if ($validated['role'] === 'siswa' && !empty($validated['kelas_id'])) {
            $user->kelas()->attach($validated['kelas_id']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form untuk mengedit pengguna.
     */
    public function edit(User $user): Response
    {
        // Muat relasi yang dibutuhkan oleh frontend (Edit.vue)
        $user->load(['roles:id,name', 'kelas:id,nama_kelas']);

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user, // Kirim seluruh objek user dengan relasi
            'roles' => Role::all(['id', 'name']),
            'kelas' => Kelas::all(['id', 'nama_kelas']),
        ]);
    }

    /**
     * Memperbarui data pengguna di dalam database.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'kelas_id' => ['nullable', 'integer', Rule::exists('kelas', 'id')],
        ]);

        // Siapkan data untuk diupdate
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        $user->update($updateData);

        // Update role pengguna
        $user->syncRoles($validated['role']);

        // Update kelas pengguna jika rolenya siswa
        if ($validated['role'] === 'siswa') {
            // sync() akan menangani penambahan, pembaruan, atau penghapusan dari kelas
            $user->kelas()->sync($validated['kelas_id'] ?? []);
        } else {
            // Jika role bukan lagi siswa, hapus dari semua kelas
            $user->kelas()->detach();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari database.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
