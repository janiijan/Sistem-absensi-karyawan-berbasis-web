(function () {
  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function formatClock(date) {
    return `${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
  }

  function bindClock() {
    const el = document.getElementById('live-clock');
    if (!el) return;

    const serverTs = Number(el.dataset.serverTime || 0);
    const origin = serverTs > 0 ? serverTs * 1000 : Date.now();
    const started = Date.now();

    const tick = () => {
      const current = new Date(origin + (Date.now() - started));
      el.textContent = formatClock(current);
    };

    tick();
    setInterval(tick, 1000);
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

  function bindPasswordToggle() {
    const toggle = document.querySelector('[data-toggle-password]');
    const input = document.getElementById('password');
    if (!toggle || !input) return;

    toggle.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      toggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
    });
  }

  bindClock();
  bindSidebar();
  bindPasswordToggle();
})();
