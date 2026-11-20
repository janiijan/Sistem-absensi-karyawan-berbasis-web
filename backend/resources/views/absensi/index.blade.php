@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
@php
  $sudahMasuk = filled(optional($absensi)->jam_masuk);
  $sudahPulang = filled(optional($absensi)->jam_pulang);
@endphp

<div class="topbar">
  <div>
    <button class="icon-btn mobile-toggle" data-toggle-sidebar aria-label="Menu" type="button">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="date-line">{{ $now->translatedFormat('l, d F Y') }}</p>
    <h1>Absensi</h1>
  </div>
  <div class="topbar-actions">
    <a href="{{ route('riwayat.index') }}" class="btn btn-outline" style="height:42px;padding:0 14px;">Riwayat</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-outline" style="height:42px;padding:0 14px;" type="submit">Logout</button>
    </form>
  </div>
</div>

<section class="hero-card">
  <div>
    <h2>Halo, {{ $karyawan->nama }}</h2>
    <p>Catat kehadiran Anda hari ini. Waktu yang digunakan adalah waktu server (WIB).</p>
    <div class="meta-row" style="margin-top:16px;">
      @if (! $sudahMasuk)
        <span class="status-pill muted">Belum Absen</span>
      @elseif (! $sudahPulang)
        <span class="status-pill neutral">Sudah Absen Masuk</span>
        @if ($absensi->status === 'Terlambat')
          <span class="status-pill warn">Terlambat</span>
        @else
          <span class="status-pill ok">Tepat Waktu</span>
        @endif
      @else
        <span class="status-pill ok">Sudah Absen Pulang</span>
      @endif
    </div>
  </div>
  <div class="clock-block">
    <div class="time" id="live-clock" data-server-time="{{ $now->timestamp }}">{{ $now->format('H:i:s') }}</div>
    <div class="tz">WIB · UTC+7</div>
  </div>
</section>

<section class="grid-2">
  <article class="card">
    <h3>Absen Masuk</h3>
    <p class="hint">Aktif jika Anda belum absen masuk hari ini. Batas tepat waktu: 08:00 WIB.</p>
    <div class="location-box">
      <div class="pin">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
      </div>
      <div>
        <strong>Kantor Pusat — Jakarta Selatan</strong>
        <span>Jl. Jenderal Sudirman No. 10</span>
      </div>
    </div>
    <p style="margin-bottom:16px;color:var(--ink-soft);font-size:0.9rem;">
      Status:
      <strong>
        @if ($sudahMasuk)
          {{ \Illuminate\Support\Str::of($absensi->jam_masuk)->substr(0, 5) }} WIB
        @else
          Belum tercatat
        @endif
      </strong>
    </p>
    <form method="POST" action="{{ route('absensi.masuk') }}">
      @csrf
      <button class="btn btn-primary btn-lg btn-block" type="submit" @disabled($sudahMasuk)>Absen Masuk</button>
    </form>
    <p class="tips">Tips: Pastikan Anda siap bekerja sebelum menekan tombol absen masuk.</p>
  </article>

  <article class="card">
    <h3>Absen Pulang</h3>
    <p class="hint">Aktif setelah absen masuk berhasil. Durasi kerja dihitung otomatis.</p>
    <div class="location-box">
      <div class="pin">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
      </div>
      <div>
        <strong>Lokasi kantor</strong>
        <span>Area kantor · absensi berbasis web</span>
      </div>
    </div>
    <p style="margin-bottom:16px;color:var(--ink-soft);font-size:0.9rem;">
      Status:
      <strong>
        @if ($sudahPulang)
          {{ \Illuminate\Support\Str::of($absensi->jam_pulang)->substr(0, 5) }} WIB · {{ $absensi->durasi_label }}
        @elseif ($sudahMasuk)
          Siap absen pulang
        @else
          Belum tersedia
        @endif
      </strong>
    </p>
    <form method="POST" action="{{ route('absensi.pulang') }}">
      @csrf
      <button class="btn btn-success btn-lg btn-block" type="submit" @disabled(! $sudahMasuk || $sudahPulang)>Absen Pulang</button>
    </form>
    <p class="tips">Absen pulang hanya dapat dilakukan sekali per hari.</p>
  </article>
</section>

<section class="card">
  <h3>Jadwal Kerja</h3>
  <p class="hint">Referensi jam kerja standar perusahaan.</p>
  <div class="grid-2" style="margin:0;">
    <div class="location-box" style="margin:0;">
      <div class="pin">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 7v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>
      </div>
      <div>
        <strong>Jam Masuk</strong>
        <span>08:00 WIB</span>
      </div>
    </div>
    <div class="location-box" style="margin:0;">
      <div class="pin">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 7v5l3 2"/><circle cx="12" cy="12" r="9"/></svg>
      </div>
      <div>
        <strong>Jam Pulang</strong>
        <span>17:00 WIB</span>
      </div>
    </div>
  </div>
</section>
@endsection
