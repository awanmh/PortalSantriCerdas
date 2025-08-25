//resources/views/absensi/siswa.blade.php
@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Absensi Siswa</h1>
    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Nama Siswa</th>
                <th class="p-2">Tanggal</th>
                <th class="p-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensi as $item)
                <tr>
                    <td class="p-2">{{ $item->siswa->name }}</td>
                    <td class="p-2">{{ $item->tanggal }}</td>
                    <td class="p-2">{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
