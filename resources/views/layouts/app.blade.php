<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('img/logo-smansa.png') }}">
    <title>@yield('title', 'Dashboard') - Sistem EWS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="ews-app">
    <nav class="ews-topbar">
        <div class="ews-brand">
            @yield('sidebar-toggle')
            <img src="{{ asset('img/logo-smansa.png') }}" alt="Logo SMAN 1 Cikarang Selatan" width="44"
                height="44">
            <div><span class="ews-brand-name">Sistem EWS</span><span class="ews-brand-school">SMAN 1 Cikarang
                    Selatan</span></div>
        </div>

        @php
            $userRoles = auth()->user()->roles;
            $activeRole = $userRoles->first(fn($role) => request()->routeIs("{$role->name}.*"));
        @endphp
        <details class="ews-profile">
            <summary class="ews-profile-trigger" aria-label="Menu akun {{ auth()->user()->name }}">
                <span class="ews-avatar"
                    aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="ews-profile-caption">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ $activeRole?->label ?? 'Pengaturan akun' }}</span>
                </span>
                <span aria-hidden="true">⌄</span>
            </summary>
            <div class="ews-profile-panel">
                <div class="ews-profile-heading">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Akun sekolah</span>
                </div>
                <div class="ews-profile-section">
                    <p>{{ $userRoles->count() > 1 ? 'Pindah dashboard' : 'Dashboard' }}</p>
                    @foreach ($userRoles as $role)
                        <a href="{{ route("{$role->name}.dashboard") }}"
                            class="ews-profile-item {{ request()->routeIs("{$role->name}.*") ? 'is-active' : '' }}"
                            @if (request()->routeIs("{$role->name}.*")) aria-current="page" @endif>
                            <span>{{ $role->label }}</span>
                            @if (request()->routeIs("{$role->name}.*"))
                                <small>Aktif</small>
                            @endif
                        </a>
                    @endforeach
                </div>
                <div class="ews-profile-section">
                    <a href="{{ route('password.edit') }}"
                        class="ews-profile-item {{ request()->routeIs('password.*') ? 'is-active' : '' }}"
                        @if (request()->routeIs('password.*')) aria-current="page" @endif>Ganti password</a>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ews-profile-section">
                    @csrf
                    <button type="submit" class="ews-profile-item ews-profile-logout">Keluar dari akun</button>
                </form>
            </div>
        </details>
    </nav>

    <main class="ews-main">
        @yield('content')
    </main>
</body>

</html>
