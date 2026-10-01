@extends('layouts.kurikulum')

@section('title', 'Dashboard Kurikulum')

@section('kurikulum-content')
    <h1 class="text-xl font-semibold">Dashboard Kurikulum</h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}.</p>
@endsection
