<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSiswaRequest;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role; // Tambahkan ini

/**
 * Controller for managing students assigned to a specific class via a pivot table.
 */
class KelasSiswaController extends Controller
{
    /**
     * Display the form to manage students for a specific class.
     *
     * @param  Kelas  $kelas The class instance via Route Model Binding.
     * @return Response
     */
    public function index(Kelas $kelas): Response
    {
        // Get the ID of the 'siswa' role once to avoid repeated queries
        $siswaRoleId = Role::where('name', 'siswa')->first()->id ?? null;

        // Load assigned students for this class with the 'siswa' role
        $assignedStudents = $kelas->users()
                                    ->whereHas('roles', function ($query) use ($siswaRoleId) {
                                        $query->where('role_id', $siswaRoleId);
                                    })
                                    ->orderBy('name')
                                    ->get(['users.id', 'name']);

        // Load students who have the 'siswa' role
        // and are not assigned to this specific class
        $unassignedStudents = User::whereHas('roles', function ($query) use ($siswaRoleId) {
                                    $query->where('role_id', $siswaRoleId);
                                })
                                ->whereDoesntHave('kelas', function ($query) use ($kelas) {
                                    $query->where('kelas.id', $kelas->id);
                                })
                                ->orderBy('name')
                                ->get(['id', 'name']);

        return Inertia::render('Admin/Kelas/ManageSiswa', [
            'kelas' => $kelas->load('jurusan'), // Load jurusan for display
            'assignedStudents' => $assignedStudents,
            'unassignedStudents' => $unassignedStudents,
        ]);
    }

    /**
     * Assign a student to the specified class.
     *
     * @param  AssignSiswaRequest  $request
     * @param  Kelas  $kelas
     * @return RedirectResponse
     */
    public function assign(AssignSiswaRequest $request, Kelas $kelas): RedirectResponse
    {
        $siswaId = $request->validated('siswa_id');

        // Attach the student to the class using the pivot table.
        // `attach` akan mencegah duplikasi jika relasi sudah ada.
        $kelas->users()->attach($siswaId);

        return redirect()->route('admin.kelas.manage-siswa', $kelas->id)
            ->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    /**
     * Detach a student from the specified class.
     *
     * @param  Kelas  $kelas
     * @param  User  $siswa  The student (User) instance to detach.
     * @return RedirectResponse
     */
    public function detach(Kelas $kelas, User $siswa): RedirectResponse
    {
        // Detach the student from the class using the pivot table.
        $kelas->users()->detach($siswa->id);

        return redirect()->route('admin.kelas.manage-siswa', $kelas->id)
            ->with('success', 'Siswa berhasil dihapus dari kelas.');
    }
}