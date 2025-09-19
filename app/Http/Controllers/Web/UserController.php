<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
                // Mengirim seluruh array roles untuk fleksibilitas di frontend
                'roles' => $user->roles->map(fn($role) => ['id' => $role->id, 'name' => $role->name]),
                'created_at' => $user->created_at, // Biarkan frontend yang memformat tanggal
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            // DIHAPUS: Baris 'flash' tidak diperlukan, Inertia menanganinya secara otomatis.
        ]);
    }

    /**
     * Menampilkan halaman form untuk membuat pengguna baru.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            // DIUBAH: Mengirim array objek (id & name) agar bisa di-loop di Vue
            'roles' => Role::all(['id', 'name']),
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
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Redirect dengan flash message 'success'
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form untuk mengedit pengguna.
     */
    public function edit(User $user): Response
    {
        $user->load('roles:id,name');

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? null,
            ],
            // DIUBAH: Menyesuaikan dengan method create()
            'roles' => Role::all(['id', 'name']),
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
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['role']);

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
