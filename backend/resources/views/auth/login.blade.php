@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="login-page">
  <aside class="login-hero">
    <div class="brand-mark">
      <div class="logo">SI</div>
      <div>
        <strong>Sepa Indonesia</strong>
        <span>Attendance</span>
      </div>
    </div>

    <div class="login-hero-copy">
      <h1>Catat kehadiran lebih cepat, akurat, dan transparan.</h1>
      <p>Sistem Absensi Karyawan Berbasis Web untuk absen masuk, absen pulang, dan pantau riwayat kehadiran Anda.</p>
    </div>

    <div class="login-hero-meta">Universitas Indraprasta PGRI · Rekayasa Perangkat Lunak</div>
  </aside>

  <main class="login-panel">
    <div class="login-card">
      <div class="eyebrow">Selamat datang</div>
      <h2>Masuk ke akun Anda</h2>
      <p class="subtitle">Gunakan ID karyawan dan password yang telah disediakan.</p>

      @if ($errors->any())
        <div class="alert alert-error show" role="alert">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('login.attempt') }}" novalidate>
        @csrf
        <div class="form-group">
          <label for="id_karyawan">ID Karyawan</label>
          <div class="input-wrap">
            <input id="id_karyawan" name="id_karyawan" type="text" value="{{ old('id_karyawan') }}" placeholder="Contoh: KRY001" autocomplete="username" required />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="current-password" required />
            <button type="button" class="toggle-pass" data-toggle-password aria-label="Tampilkan password">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="form-row">
          <span style="color:var(--muted)">Demo: KRY001 / password123</span>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
      </form>

      <p class="login-foot">Dengan masuk, Anda menyetujui kebijakan penggunaan sistem absensi perusahaan.</p>
    </div>
  </main>
</div>
@endsection
