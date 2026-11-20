/* Sepa Indonesia — shared UI logic (demo / frontend only) */
(function () {
  const STORAGE_KEY = 'sepa_session';
  const ATTENDANCE_KEY = 'sepa_attendance';
  const WORK_START = '08:00';

  const demoUser = {
    id_karyawan: 'KRY001',
    password: 'password123',
    nama: 'Andi Pratama',
    role: 'Karyawan',
    initials: 'AP',
  };

  function todayKey() {
    const d = new Date();
    return d.toISOString().slice(0, 10);
  }

  function formatDateLong(date = new Date()) {
    return date.toLocaleDateString('id-ID', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  function formatTime(date = new Date()) {
    return date.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: false,
    });
  }

  function formatHM(date = new Date()) {
    return date.toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    });
  }

  function getSession() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
    } catch {
      return null;
    }
  }

  function setSession(user) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(user));
  }

  function clearSession() {
    localStorage.removeItem(STORAGE_KEY);
  }

  function requireAuth() {
    const session = getSession();
    if (!session) {
      window.location.href = 'login.html';
      return null;
    }
    return session;
  }

  function getAttendanceStore() {
    try {
      return JSON.parse(localStorage.getItem(ATTENDANCE_KEY) || '{}');
    } catch {
      return {};
    }
  }

  function saveAttendanceStore(store) {
    localStorage.setItem(ATTENDANCE_KEY, JSON.stringify(store));
  }

  function seedDemoHistory() {
    const store = getAttendanceStore();
    if (store._seeded) return;
    const samples = [];
    const now = new Date();
    for (let i = 1; i <= 12; i++) {
      const d = new Date(now);
      d.setDate(now.getDate() - i);
      if (d.getDay() === 0 || d.getDay() === 6) continue;
      const late = i % 5 === 0;
      const masuk = late ? '08:24' : '07:52';
      samples.push({
        tanggal: d.toISOString().slice(0, 10),
        jam_masuk: masuk,
        jam_pulang: '17:05',
        status: late ? 'Terlambat' : 'Tepat Waktu',
        durasi: late ? '8 jam 41 menit' : '9 jam 13 menit',
      });
    }
    store.history = samples;
    store._seeded = true;
    saveAttendanceStore(store);
  }

  function getTodayRecord() {
    const store = getAttendanceStore();
    return (store.days && store.days[todayKey()]) || null;
  }

  function upsertTodayRecord(patch) {
    const store = getAttendanceStore();
    store.days = store.days || {};
    store.days[todayKey()] = {
      ...(store.days[todayKey()] || { tanggal: todayKey() }),
      ...patch,
    };
    saveAttendanceStore(store);
    return store.days[todayKey()];
  }

  function isLate(timeStr) {
    const [h, m] = timeStr.split(':').map(Number);
    const [sh, sm] = WORK_START.split(':').map(Number);
    return h > sh || (h === sh && m > sm);
  }

  function durationBetween(start, end) {
    const [sh, sm] = start.split(':').map(Number);
    const [eh, em] = end.split(':').map(Number);
    let mins = eh * 60 + em - (sh * 60 + sm);
    if (mins < 0) mins = 0;
    const h = Math.floor(mins / 60);
    const m = mins % 60;
    return `${h} jam ${m} menit`;
  }

  function showToast(message) {
    let el = document.querySelector('.toast');
    if (!el) {
      el = document.createElement('div');
      el.className = 'toast';
      document.body.appendChild(el);
    }
    el.textContent = message;
    el.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => el.classList.remove('show'), 2800);
  }

  function bindClock(selector) {
    const el = document.querySelector(selector);
    if (!el) return;
    const tick = () => {
      el.textContent = formatTime();
    };
    tick();
    setInterval(tick, 1000);
  }

  function bindDate(selector) {
    const el = document.querySelector(selector);
    if (el) el.textContent = formatDateLong();
  }

  function bindSidebar() {
    const toggle = document.querySelector('[data-toggle-sidebar]');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    if (!toggle || !sidebar) return;

    const close = () => {
      sidebar.classList.remove('open');
      overlay?.classList.remove('show');
    };

    toggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay?.classList.toggle('show');
    });
    overlay?.addEventListener('click', close);
  }

  function bindLogout() {
    document.querySelectorAll('[data-logout]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        clearSession();
        window.location.href = 'login.html';
      });
    });
  }

  function fillUser() {
    const session = getSession();
    if (!session) return;
    document.querySelectorAll('[data-user-name]').forEach((el) => {
      el.textContent = session.nama;
    });
    document.querySelectorAll('[data-user-id]').forEach((el) => {
      el.textContent = session.id_karyawan;
    });
    document.querySelectorAll('[data-user-initials]').forEach((el) => {
      el.textContent = session.initials;
    });
  }

  /* ---- Page: Login ---- */
  function initLogin() {
    if (getSession()) {
      window.location.href = 'absen.html';
      return;
    }

    const form = document.getElementById('login-form');
    const alertBox = document.getElementById('login-alert');
    const toggle = document.querySelector('[data-toggle-password]');
    const passInput = document.getElementById('password');

    toggle?.addEventListener('click', () => {
      const isPass = passInput.type === 'password';
      passInput.type = isPass ? 'text' : 'password';
      toggle.setAttribute('aria-label', isPass ? 'Sembunyikan password' : 'Tampilkan password');
    });

    form?.addEventListener('submit', (e) => {
      e.preventDefault();
      const id = document.getElementById('id_karyawan').value.trim();
      const password = passInput.value;

      if (!id || !password) {
        alertBox.textContent = 'ID karyawan dan password wajib diisi.';
        alertBox.className = 'alert alert-error show';
        return;
      }

      if (id !== demoUser.id_karyawan || password !== demoUser.password) {
        alertBox.textContent = 'ID karyawan atau password salah.';
        alertBox.className = 'alert alert-error show';
        return;
      }

      setSession({
        id_karyawan: demoUser.id_karyawan,
        nama: demoUser.nama,
        role: demoUser.role,
        initials: demoUser.initials,
      });
      window.location.href = 'absen.html';
    });
  }

  /* ---- Page: Absensi ---- */
  function renderAbsensiState() {
    const record = getTodayRecord();
    const masukBtn = document.getElementById('btn-masuk');
    const pulangBtn = document.getElementById('btn-pulang');
    const statusEl = document.getElementById('status-hari-ini');
    const masukInfo = document.getElementById('info-masuk');
    const pulangInfo = document.getElementById('info-pulang');

    if (!record || !record.jam_masuk) {
      statusEl.innerHTML = '<span class="status-pill muted">Belum Absen</span>';
      masukBtn.disabled = false;
      pulangBtn.disabled = true;
      masukInfo.textContent = 'Belum tercatat';
      pulangInfo.textContent = 'Belum tersedia';
      return;
    }

    if (record.jam_masuk && !record.jam_pulang) {
      const badge =
        record.status === 'Terlambat'
          ? '<span class="status-pill warn">Terlambat</span>'
          : '<span class="status-pill ok">Tepat Waktu</span>';
      statusEl.innerHTML = `<span class="status-pill neutral">Sudah Absen Masuk</span> ${badge}`;
      masukBtn.disabled = true;
      pulangBtn.disabled = false;
      masukInfo.textContent = record.jam_masuk + ' WIB';
      pulangInfo.textContent = 'Siap absen pulang';
      return;
    }

    statusEl.innerHTML = '<span class="status-pill ok">Sudah Absen Pulang</span>';
    masukBtn.disabled = true;
    pulangBtn.disabled = true;
    masukInfo.textContent = record.jam_masuk + ' WIB';
    pulangInfo.textContent = record.jam_pulang + ' WIB · ' + (record.durasi || '');
  }

  function initAbsensi() {
    const session = requireAuth();
    if (!session) return;
    seedDemoHistory();
    fillUser();
    bindSidebar();
    bindLogout();
    bindClock('#live-clock');
    bindDate('#live-date');
    renderAbsensiState();

    document.getElementById('btn-masuk')?.addEventListener('click', () => {
      if (getTodayRecord()?.jam_masuk) {
        showToast('Absen masuk hari ini sudah tercatat.');
        return;
      }
      const jam = formatHM();
      const status = isLate(jam) ? 'Terlambat' : 'Tepat Waktu';
      upsertTodayRecord({ jam_masuk: jam, status, jam_pulang: null, durasi: null });
      renderAbsensiState();
      showToast(`Absen masuk berhasil · ${status}`);
    });

    document.getElementById('btn-pulang')?.addEventListener('click', () => {
      const record = getTodayRecord();
      if (!record?.jam_masuk) {
        showToast('Anda belum absen masuk hari ini.');
        return;
      }
      if (record.jam_pulang) {
        showToast('Absen pulang hari ini sudah tercatat.');
        return;
      }
      const jam = formatHM();
      const durasi = durationBetween(record.jam_masuk, jam);
      const updated = upsertTodayRecord({ jam_pulang: jam, durasi });

      const store = getAttendanceStore();
      store.history = store.history || [];
      store.history = [
        {
          tanggal: updated.tanggal,
          jam_masuk: updated.jam_masuk,
          jam_pulang: updated.jam_pulang,
          status: updated.status,
          durasi: updated.durasi,
        },
        ...store.history.filter((r) => r.tanggal !== updated.tanggal),
      ];
      saveAttendanceStore(store);

      renderAbsensiState();
      showToast('Absen pulang berhasil tersimpan.');
    });
  }

  /* ---- Page: Riwayat ---- */
  function monthName(i) {
    return [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ][i];
  }

  function initRiwayat() {
    const session = requireAuth();
    if (!session) return;
    seedDemoHistory();
    fillUser();
    bindSidebar();
    bindLogout();
    bindDate('#live-date');

    const now = new Date();
    const bulanSelect = document.getElementById('filter-bulan');
    const tahunSelect = document.getElementById('filter-tahun');

    for (let i = 0; i < 12; i++) {
      const opt = document.createElement('option');
      opt.value = String(i + 1);
      opt.textContent = monthName(i);
      if (i === now.getMonth()) opt.selected = true;
      bulanSelect.appendChild(opt);
    }

    for (let y = now.getFullYear(); y >= now.getFullYear() - 2; y--) {
      const opt = document.createElement('option');
      opt.value = String(y);
      opt.textContent = String(y);
      tahunSelect.appendChild(opt);
    }

    const PAGE_SIZE = 10;
    let page = 1;

    function collectRows() {
      const bulan = Number(bulanSelect.value);
      const tahun = Number(tahunSelect.value);
      const store = getAttendanceStore();
      const today = getTodayRecord();
      let rows = [...(store.history || [])];

      if (today?.jam_masuk) {
        rows = [
          {
            tanggal: today.tanggal,
            jam_masuk: today.jam_masuk,
            jam_pulang: today.jam_pulang || '-',
            status: today.status,
            durasi: today.durasi || '-',
          },
          ...rows.filter((r) => r.tanggal !== today.tanggal),
        ];
      }

      return rows
        .filter((r) => {
          const d = new Date(r.tanggal + 'T00:00:00');
          return d.getMonth() + 1 === bulan && d.getFullYear() === tahun;
        })
        .sort((a, b) => (a.tanggal < b.tanggal ? 1 : -1));
    }

    function render() {
      const rows = collectRows();
      const hadir = rows.length;
      const terlambat = rows.filter((r) => r.status === 'Terlambat').length;
      const tepat = rows.filter((r) => r.status === 'Tepat Waktu').length;

      document.getElementById('stat-hadir').textContent = `${hadir} hari`;
      document.getElementById('stat-tepat').textContent = `${tepat} kali`;
      document.getElementById('stat-terlambat').textContent = `${terlambat} kali`;

      const tbody = document.getElementById('riwayat-body');
      const empty = document.getElementById('riwayat-empty');
      const totalPages = Math.max(1, Math.ceil(rows.length / PAGE_SIZE));
      if (page > totalPages) page = totalPages;
      const slice = rows.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

      if (!rows.length) {
        tbody.innerHTML = '';
        empty.style.display = 'block';
      } else {
        empty.style.display = 'none';
        tbody.innerHTML = slice
          .map((r, idx) => {
            const d = new Date(r.tanggal + 'T00:00:00');
            const tanggal = d.toLocaleDateString('id-ID', {
              weekday: 'long',
              day: '2-digit',
              month: 'short',
              year: 'numeric',
            });
            const badge =
              r.status === 'Terlambat'
                ? '<span class="status-pill warn">Terlambat</span>'
                : '<span class="status-pill ok">Tepat Waktu</span>';
            return `<tr>
              <td>${(page - 1) * PAGE_SIZE + idx + 1}</td>
              <td>${tanggal}</td>
              <td>${r.jam_masuk}</td>
              <td>${r.jam_pulang || '-'}</td>
              <td>${r.durasi || '-'}</td>
              <td>${badge}</td>
            </tr>`;
          })
          .join('');
      }

      const pager = document.getElementById('pagination');
      pager.innerHTML = '';
      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = String(i);
        if (i === page) btn.classList.add('active');
        btn.addEventListener('click', () => {
          page = i;
          render();
        });
        pager.appendChild(btn);
      }
    }

    document.getElementById('btn-filter')?.addEventListener('click', () => {
      page = 1;
      render();
    });

    render();
  }

  /* ---- Page: Dashboard ---- */
  function initDashboard() {
    const session = requireAuth();
    if (!session) return;
    seedDemoHistory();
    fillUser();
    bindSidebar();
    bindLogout();
    bindClock('#live-clock');
    bindDate('#live-date');

    const store = getAttendanceStore();
    const history = store.history || [];
    const now = new Date();
    const monthRows = history.filter((r) => {
      const d = new Date(r.tanggal + 'T00:00:00');
      return d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
    });
    document.getElementById('dash-hadir').textContent = String(monthRows.length);
    document.getElementById('dash-terlambat').textContent = String(
      monthRows.filter((r) => r.status === 'Terlambat').length
    );

    const record = getTodayRecord();
    const statusEl = document.getElementById('dash-status');
    if (!record?.jam_masuk) statusEl.textContent = 'Belum absen hari ini';
    else if (!record.jam_pulang) statusEl.textContent = `Masuk ${record.jam_masuk} · Belum pulang`;
    else statusEl.textContent = `Selesai · ${record.jam_masuk} – ${record.jam_pulang}`;
  }

  const page = document.body.dataset.page;
  if (page === 'login') initLogin();
  if (page === 'absen') initAbsensi();
  if (page === 'riwayat') initRiwayat();
  if (page === 'dashboard') initDashboard();
})();
