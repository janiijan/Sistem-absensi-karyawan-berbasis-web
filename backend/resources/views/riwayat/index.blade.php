@extends('layouts.app')

@section('title', 'Riwayat Absen')

@section('content')
@php
  $bulanNama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
  ];
@endphp

<div class="topbar">
  <div>
    <button class="icon-btn mobile-toggle" data-toggle-sidebar aria-label="Menu" type="button">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <p class="date-line">{{ $now->translatedFormat('l, d F Y') }}</p>
    <h1>Riwayat Absen</h1>
    <p class="date-line">Lihat dan pantau catatan kehadiran Anda.</p>
  </div>
  <div class="topbar-actions">
    <a href="{{ route('absensi.index') }}" class="btn btn-outline" style="height:42px;padding:0 14px;">Kembali ke Absen</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn btn-outline" style="height:42px;padding:0 14px;" type="submit">Logout</button>
    </form>
  </div>
</div>

<section class="grid-3">
  <div class="stat-card">
    <div class="stat-icon blue">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
    </div>
    <div>
      <div class="label">Total kehadiran</div>
      <div class="value">{{ $totalHadir }} hari</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <div>
      <div class="label">Tepat waktu</div>
      <div class="value">{{ $totalTepat }} kali</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
    </div>
    <div>
      <div class="label">Terlambat</div>
      <div class="value">{{ $totalTerlambat }} kali</div>
    </div>
  </div>
</section>

<section class="card">
  <h3>Daftar kehadiran</h3>
  <p class="hint">Rekap absensi berdasarkan periode yang dipilih.</p>

  <form method="GET" action="{{ route('riwayat.index') }}" class="filter-bar">
    <div class="field">
      <label for="filter-bulan">Bulan</label>
      <select id="filter-bulan" name="bulan">
        @for ($i = 1; $i <= 12; $i++)
          <option value="{{ $i }}" @selected($bulan === $i)>{{ $bulanNama[$i] }}</option>
        @endfor
      </select>
    </div>
    <div class="field">
      <label for="filter-tahun">Tahun</label>
      <select id="filter-tahun" name="tahun">
        @for ($y = $now->year; $y >= $now->year - 2; $y--)
          <option value="{{ $y }}" @selected($tahun === $y)>{{ $y }}</option>
        @endfor
      </select>
    </div>
    <button class="btn btn-primary" style="height:44px;padding:0 18px;" type="submit">Tampilkan</button>
  </form>

  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Jam Masuk</th>
          <th>Jam Pulang</th>
          <th>Durasi Kerja</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($riwayat as $index => $row)
          <tr>
            <td>{{ $riwayat->firstItem() + $index }}</td>
            <td>{{ $row->tanggal->translatedFormat('l, d M Y') }}</td>
            <td>{{ $row->jam_masuk ? substr($row->jam_masuk, 0, 5) : '-' }}</td>
            <td>{{ $row->jam_pulang ? substr($row->jam_pulang, 0, 5) : '-' }}</td>
            <td>{{ $row->durasi_label }}</td>
            <td>
              @if ($row->status === 'Terlambat')
                <span class="status-pill warn">Terlambat</span>
              @else
                <span class="status-pill ok">Tepat Waktu</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" style="text-align:center;color:var(--ink-soft);padding:32px 12px;">
              Belum ada data absensi pada periode ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($riwayat->hasPages())
    <div class="pagination">
      @if ($riwayat->onFirstPage())
        <button type="button" disabled>«</button>
      @else
        <a class="btn btn-outline" style="min-width:38px;height:38px;" href="{{ $riwayat->previousPageUrl() }}">«</a>
      @endif

      @foreach ($riwayat->getUrlRange(1, $riwayat->lastPage()) as $page => $url)
        @if ($page == $riwayat->currentPage())
          <button type="button" class="active">{{ $page }}</button>
        @else
          <a class="btn btn-outline" style="min-width:38px;height:38px;" href="{{ $url }}">{{ $page }}</a>
        @endif
      @endforeach

      @if ($riwayat->hasMorePages())
        <a class="btn btn-outline" style="min-width:38px;height:38px;" href="{{ $riwayat->nextPageUrl() }}">»</a>
      @else
        <button type="button" disabled>»</button>
      @endif
    </div>
  @endif
</section>
@endsection
