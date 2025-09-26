<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelasRequest;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for managing Kelas (Classes) data.
 */
class KelasController extends Controller
{
    /**
     * Display a listing of the classes.
     * Includes data for adding/editing classes.
     *
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Kelas/Index', [
            // Retrieve all classes with their related jurusan and waliKelas, ordered by class name.
            'kelas' => Kelas::with(['jurusan', 'waliKelas'])->orderBy('nama_kelas')->get(),

            // Retrieve all jurusans, ordered by name. Menghapus 'nama_singkat'
            'jurusan' => Jurusan::orderBy('nama')->get(['id', 'nama']), 
            
            // Retrieve all users with 'guru' role, ordered by name, for wali kelas selection.
            'guru' => User::role('guru')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created class in storage.
     *
     * @param  KelasRequest  $request
     * @return RedirectResponse
     */
    public function store(KelasRequest $request): RedirectResponse
    {
        // Validation is handled automatically by KelasRequest.
        Kelas::create($request->validated());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas baru berhasil ditambahkan.');
    }

    /**
     * Update the specified class in storage.
     *
     * @param  KelasRequest  $request
     * @param  Kelas  $kelas  The class instance to update via Route Model Binding.
     * @return RedirectResponse
     */
    public function update(KelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified class from storage.
     *
     * @param  Kelas  $kelas  The class instance to delete via Route Model Binding.
     * @return RedirectResponse
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        // Pengecekan integritas data: jangan hapus kelas jika masih ada siswa di dalamnya.
        // Sekarang menggunakan relasi `users()` (siswa) yang merupakan BelongsToMany.
        if ($kelas->users()->exists()) { // Menggunakan relasi `users` yang baru
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}