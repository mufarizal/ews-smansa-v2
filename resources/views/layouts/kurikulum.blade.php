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
            <p class="ews-sidebar-label">Kurikulum</p>
            <nav class="ews-navigation" aria-label="Navigasi Kurikulum">
                <a href="{{ route('kurikulum.dashboard') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.dashboard') ? 'is-active' : '' }}">Dashboard</a>
                <a href="{{ route('kurikulum.semesters.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.semesters.*') ? 'is-active' : '' }}">Semester</a>
                <a href="{{ route('kurikulum.mapels.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.mapels.*') ? 'is-active' : '' }}">Mata
                    Pelajaran</a>
                <a href="{{ route('kurikulum.kelas.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.kelas.*') ? 'is-active' : '' }}">Kelas</a>
                <a href="{{ route('kurikulum.gurus.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.gurus.*') ? 'is-active' : '' }}">Data
                    Guru</a>
                <a href="{{ route('kurikulum.siswas.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.siswas.*') ? 'is-active' : '' }}">Data
                    Siswa</a>
                <a href="{{ route('kurikulum.guru-mapel-kelas.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.guru-mapel-kelas.*') ? 'is-active' : '' }}">
                    Penugasan Mengajar
                </a>
                <a href="{{ route('kurikulum.guru-bk-kelas.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.guru-bk-kelas.*') ? 'is-active' : '' }}">
                    Penugasan Guru BK
                </a>
                <a href="{{ route('kurikulum.jadwals.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.jadwals.*') ? 'is-active' : '' }}">
                    Jadwal
                </a>
                <a href="{{ route('kurikulum.kehadiran-guru.index') }}"
                    class="ews-nav-link {{ request()->routeIs('kurikulum.kehadiran-guru.*') ? 'is-active' : '' }}">
                    Kehadiran Guru
                </a>
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

            @yield('kurikulum-content')
        </div>
    </div>
@endsection
