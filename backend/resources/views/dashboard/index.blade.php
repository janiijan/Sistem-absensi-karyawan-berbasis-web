@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
  if (! $absensiHariIni?->jam_masuk) {
    $statusText = 'Belum absen hari ini';
  } elseif (! $absensiHariIni->jam_pulang) {
    $statusText = 'Masuk '.substr($absensiHariIni->jam_masuk, 0, 5).' · Belum pulang';
  } else {
    $statusText = 'Selesai · '.substr($absensiHariIni->jam_masuk, 0, 5).' – '.substr($absensiHariIni->jam_pulang, 0, 5);
  }
@endphp

<div class="topbar">
  <div>
    <button class="icon-btn mobile-toggle" data-toggle-sidebar aria-label="Menu" type="button">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="date-line">{{ $now->translatedFormat('l, d F Y') }}</p>
    <h1>Beranda</h1>
  </div>
  <div class="topbar-actions">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-outline" style="height:42px;padding:0 14px;" type="submit">Logout</button>
    </form>
  </div>
</div>

<section class="hero-card">
  <div>
    <h2>Halo, {{ $karyawan->nama }}</h2>
    <p>{{ $statusText }}</p>
    <div style="margin-top:18px;">
      <a href="{{ route('absensi.index') }}" class="btn btn-lg" style="background:#fff;color:var(--brand);">Ke Halaman Absen</a>
    </div>
  </div>
  <div class="clock-block">
    <div class="time" id="live-clock" data-server-time="{{ $now->timestamp }}">{{ $now->format('H:i:s') }}</div>
    <div class="tz">WIB · UTC+7</div>
  </div>
</section>

<section class="grid-3">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    </div>
    <div>
      <div class="label">Hadir bulan ini</div>
      <div class="value">{{ $hadirBulanIni }} hari</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
    </div>
    <div>
      <div class="label">Terlambat bulan ini</div>
      <div class="value">{{ $terlambatBulanIni }} kali</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 7v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>
    </div>
    <div>
      <div class="label">Jam kerja</div>
      <div class="value" style="font-size:1.05rem;">08:00 – 17:00</div>
    </div>
  </div>
</section>

<section class="grid-2">
  <article class="card">
    <h3>Aksi cepat</h3>
    <p class="hint">Langsung menuju fitur utama sistem.</p>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a href="{{ route('absensi.index') }}" class="btn btn-primary btn-lg btn-block">Absen Masuk / Pulang</a>
      <a href="{{ route('riwayat.index') }}" class="btn btn-outline btn-lg btn-block">Lihat Riwayat Absensi</a>
    </div>
  </article>
  <article class="card">
    <h3>Informasi lokasi</h3>
    <p class="hint">Referensi lokasi kantor (tampilan UI).</p>
    <div class="location-box" style="margin-bottom:0;">
      <div class="pin">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
      </div>
      <div>
        <strong>Kantor Pusat — Jakarta Selatan</strong>
        <span>Jl. Jenderal Sudirman No. 10</span>
      </div>
    </div>
  </article>
</section>
@endsection
