@php
    $routeKehadiran = $routeKehadiran ?? request()->route()->getName();
    $routeKehadiran = \Illuminate\Support\Str::before($routeKehadiran, '.dashboard') . '.kehadiran';
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="ews-attendance-card">
        <p class="ews-card-title">Kehadiran Pagi</p>
        @if ($kehadiran['pagi'])
            <p class="ews-attendance-status">✓ Hadir</p>
            <p class="ews-attendance-time">{{ $kehadiran['pagi']->waktu_absen->format('H:i') }}</p>
        @else
            <p class="ews-pending">Belum mengisi kehadiran</p>
            <form action="{{ route($routeKehadiran) }}" method="POST">
                @csrf
                <input type="hidden" name="sesi" value="pagi">
                <button type="submit" class="ews-button ews-button-primary">
                    Hadir Pagi
                </button>
            </form>
        @endif
    </div>

    <div class="ews-attendance-card">
        <p class="ews-card-title">Kehadiran Sore</p>
        @if ($kehadiran['sore'])
            <p class="ews-attendance-status">✓ Hadir</p>
            <p class="ews-attendance-time">{{ $kehadiran['sore']->waktu_absen->format('H:i') }}</p>
        @else
            <p class="ews-pending">Belum mengisi kehadiran</p>
            <form action="{{ route($routeKehadiran) }}" method="POST">
                @csrf
                <input type="hidden" name="sesi" value="sore">
                <button type="submit" class="ews-button ews-button-primary">
                    Hadir Sore
                </button>
            </form>
        @endif
    </div>
</div>
