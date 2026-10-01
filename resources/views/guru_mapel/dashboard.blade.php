@extends('layouts.guru_mapel')

@section('title', 'Dashboard Guru Mapel')

@section('guru-mapel-content')
    <h1 class="text-xl font-semibold mb-4">Dashboard Guru Mapel</h1>

    @include('partials.kehadiran-guru-banner')

    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}.</p>
@endsection
