@extends('Layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-4 rounded-lg shadow">Absensi Siswa</div>
    <div class="bg-white p-4 rounded-lg shadow">Absensi Guru</div>
    <div class="bg-white p-4 rounded-lg shadow">Catatan Pelanggaran</div>
</div>
@endsection
