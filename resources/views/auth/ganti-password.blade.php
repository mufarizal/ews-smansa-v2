@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <div class="ews-password-page">
        <header class="ews-page-heading">
            <p class="ews-eyebrow">Pengaturan akun</p>
            <h1>Ganti Password</h1>
            <p>Perbarui password untuk menjaga keamanan akun Anda.</p>
        </header>
        <div class="ews-form-panel">
            <h2 class="text-base font-semibold mb-6">Password akun</h2>

            @if (session('success'))
                <div role="status" class="ews-notice">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="password_lama" class="block text-sm mb-1">Password Lama</label>
                    <input id="password_lama" required autocomplete="current-password" type="password" name="password_lama" aria-describedby="password_lama-hint" class="w-full border rounded px-3 py-2 text-sm">
                    <p id="password_lama-hint" class="ews-field-hint">Masukkan password yang saat ini digunakan.</p>
                    @error('password_lama')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_baru" class="block text-sm mb-1">Password Baru</label>
                    <input id="password_baru" required autocomplete="new-password" type="password" name="password_baru" aria-describedby="password_baru-hint" class="w-full border rounded px-3 py-2 text-sm">
                    <p id="password_baru-hint" class="ews-field-hint">Minimal 8 karakter.</p>
                    @error('password_baru')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_baru_confirmation" class="block text-sm mb-1">Konfirmasi Password Baru</label>
                    <input id="password_baru_confirmation" required autocomplete="new-password" type="password" name="password_baru_confirmation" aria-describedby="password_baru_confirmation-hint"
                        class="w-full border rounded px-3 py-2 text-sm">
                    <p id="password_baru_confirmation-hint" class="ews-field-hint">Ketik ulang password baru Anda.</p>
                </div>

                <button type="submit" class="ews-button ews-button-primary">
                    Simpan password
                </button>
                <label class="ews-show-password">
                    <input type="checkbox" data-show-password> Tampilkan password
                </label>
            </form>
        </div>
    </div>
@endsection
