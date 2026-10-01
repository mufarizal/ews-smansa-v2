@extends('layouts.app')

@section('sidebar-toggle')
    <button type="button" class="ews-button ews-button-secondary ews-menu-toggle" aria-label="Buka navigasi" aria-expanded="false" aria-controls="role-sidebar">☰</button>
@endsection

@section('content')
    <div class="ews-shell">
        <div class="ews-sidebar-backdrop" hidden></div>
        <aside class="ews-sidebar" id="role-sidebar" aria-label="Menu navigasi" tabindex="-1">
            <div class="ews-sidebar-header">
                <img src="{{ asset('img/logo-smansa.png') }}" alt="Logo sekolah" width="36" height="40">
                <span>SMAN 1 Cikarang Selatan</span>
                <button type="button" class="ews-button ews-button-secondary ews-sidebar-close" aria-label="Tutup navigasi">×</button>
            </div>
            <p class="ews-sidebar-label">Guru Mapel</p>
            <nav class="ews-navigation" aria-label="Navigasi Guru Mapel">
                <a href="{{ route('guru_mapel.dashboard') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_mapel.dashboard') ? 'is-active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('guru_mapel.absensi.index') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_mapel.absensi.*') ? 'is-active' : '' }}">
                    Absensi Mapel
                </a>
                <a href="{{ route('guru_mapel.tugas.index') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_mapel.tugas.*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('guru_mapel.tugas.*')) aria-current="page" @endif>
                    Tugas
                </a>
                <a href="{{ route('guru_mapel.ujian.index') }}"
                    class="ews-nav-link {{ request()->routeIs('guru_mapel.ujian.*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('guru_mapel.ujian.*')) aria-current="page" @endif>
                    Ujian Harian
                </a>
                <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-nav-link {{ request()->routeIs('guru_mapel.perilaku.*') ? 'is-active' : '' }}" @if (request()->routeIs('guru_mapel.perilaku.*')) aria-current="page" @endif>Perilaku Siswa</a>
            </nav>
        </aside>

        <div class="ews-content">
            @if (session('success'))
                <div role="status" class="ews-notice">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div role="alert" class="ews-notice ews-notice-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('guru-mapel-content')
        </div>
    </div>
@endsection
