<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="logo">SI</div>
    <div>
      <strong>Sepa Indonesia</strong>
      <small>Attendance</small>
    </div>
  </div>

  <div class="nav-label">Menu Utama</div>
  <ul class="nav-list">
    <li>
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
        Dashboard
      </a>
    </li>
    <li>
      <a href="{{ route('absensi.index') }}" class="{{ request()->routeIs('absensi.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
        Absen
      </a>
    </li>
    <li>
      <a href="{{ route('riwayat.index') }}" class="{{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
        Riwayat Absen
      </a>
    </li>
  </ul>

  <div class="sidebar-user">
    <div class="avatar">{{ auth()->user()->initials }}</div>
    <div>
      <strong>{{ auth()->user()->nama }}</strong>
      <span>{{ auth()->user()->id_karyawan }}</span>
    </div>
  </div>
</aside>
