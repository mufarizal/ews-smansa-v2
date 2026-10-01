@extends('layouts.app')

@section('sidebar-toggle')
    <button type="button" class="ews-button ews-button-secondary ews-menu-toggle" aria-label="Buka navigasi"
        aria-expanded="false" aria-controls="role-sidebar">☰</button>
@endsection

@section('content')
    <div class="ews-shell">
        <div class="ews-sidebar-backdrop" hidden></div>
        <aside class="ews-sidebar" id="role-sidebar" aria-label="Menu navigasi" tabindex="-1">
            <div class="ews-sidebar-header">
                <img src="{{ asset('img/logo-smansa.png') }}" alt="Logo sekolah" width="36" height="40">
                <span>SMAN 1 Cikarang Selatan</span>
                <button type="button" class="ews-button ews-button-secondary ews-sidebar-close"
                    aria-label="Tutup navigasi">×</button>
            </div>
            <p class="ews-sidebar-label">Guru BK</p>
            <nav class="ews-navigation" aria-label="Navigasi Guru BK">
                <a href="{{ route('guru_bk.dashboard') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_bk.dashboard') ? 'is-active' : '' }}"
                    @if (request()->routeIs('guru_bk.dashboard')) aria-current="page" @endif>Dashboard</a>
                <a href="{{ route('guru_bk.perilaku.index') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_bk.perilaku.*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('guru_bk.perilaku.*')) aria-current="page" @endif>Master Perilaku</a>
                <a href="{{ route('guru_bk.monitoring.index') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_bk.monitoring.*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('guru_bk.monitoring.*')) aria-current="page" @endif>
                    Monitoring Siswa
                </a>
            </nav>
        </aside>
        <div class="ews-content">
            @if (session('success'))
                <div role="status" class="ews-notice">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div role="alert" class="ews-notice ews-notice-error">{{ session('error') }}</div>
            @endif
            @yield('guru-bk-content')
        </div>
    </div>
@endsection
