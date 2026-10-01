@extends('layouts.guru_bk')

@section('title', 'Dashboard Guru BK')

@section('guru-bk-content')
    <div class="ews-dashboard">
        <header class="ews-page-heading">
            <p class="ews-eyebrow">Guru BK</p>
            <h1>Dashboard</h1>
            <p>Selamat datang, {{ auth()->user()->name }}.</p>
        </header>


        <section aria-labelledby="kehadiran-heading">
            <div class="ews-section-heading">
                <h2 id="kehadiran-heading">Kehadiran Anda</h2>
                <p>Catat kehadiran untuk sesi pagi dan sore.</p>
            </div>
            @include('partials.kehadiran-guru-banner')
        </section>
    </div>
@endsection
