//resources/views/profil.blade.php
@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Profil</h1>
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label>Nama</label>
            <input type="text" name="name" value="{{ auth()->user()->name }}" class="border p-2 rounded w-full">
        </div>
        <div class="mt-4">
            <label>Email</label>
            <input type="email" name="email" value="{{ auth()->user()->email }}" class="border p-2 rounded w-full">
        </div>
        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">Update</button>
    </form>
@endsection
