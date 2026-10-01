@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="ews-login-layout">
    <section class="ews-login-intro" aria-labelledby="school-title">
        <img src="{{ asset('img/logo-smansa.png') }}" alt="Logo SMAN 1 Cikarang Selatan" width="160" height="160">
        <p class="ews-eyebrow">SISTEM EWS</p>
        <h2 id="school-title">SMAN 1<br>Cikarang Selatan</h2>
        <p>Sistem pemantauan sekolah untuk mendukung pendampingan siswa dan pengelolaan kegiatan akademik.</p>
    </section>
    <section class="ews-login-form" aria-labelledby="login-title">
    <p class="ews-eyebrow">Selamat datang</p>
    <h1 id="login-title">Masuk ke akun Anda</h1>
    <p class="ews-auth-description">Gunakan akun sekolah untuk mengakses Sistem EWS.</p>

    @if ($errors->any())
    <div role="alert" class="ews-notice ews-notice-error">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="block text-sm mb-1">Email / NIP / NIS</label>
            <input id="email" autocomplete="username" type="text" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="block text-sm mb-1">Password</label>
            <input id="password" autocomplete="current-password" type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4 flex items-center">
            <input type="checkbox" name="remember" id="remember" class="mr-2">
            <label for="remember" class="text-sm">Ingat saya</label>
        </div>

        <button type="submit" class="ews-button ews-button-primary w-full">
            Masuk
        </button>
    </form>
    </section>
</div>
@endsection
