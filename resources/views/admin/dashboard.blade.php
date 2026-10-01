@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('admin-content')
    <h1 class="text-xl font-semibold">Dashboard Admin</h1>
    <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}.</p>
@endsection
