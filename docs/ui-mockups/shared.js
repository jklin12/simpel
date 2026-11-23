/* Mockup UI Simpel (mobile-first). Dipakai semua halaman di folder ini.
   - Mengatur konfigurasi Tailwind (sama dengan layouts/landing.blade.php)
   - Menyuntik navbar ringkas + drawer menu dan footer ringkas
   - Ini hanya mockup statis, bukan kode produksi. */

tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Outfit', 'sans-serif'] },
      colors: {
        primary: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a' },
        secondary: { 400: '#facc15', 500: '#eab308', 600: '#ca8a04' },
      },
    },
  },
};

const ICON_PATHS = {
  menu: 'M3 12h18M3 6h18M3 18h18',
  x: 'M18 6 6 18M6 6l12 12',
  doc: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8',
  status: 'M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11',
  map: 'M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4zM8 2v16M16 6v16',
  download: 'M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3',
  search: 'M21 21l-4.35-4.35M11 19a8 8 0 1 0 0-16 8 8 0 0 0 0 16z',
  down: 'M6 9l6 6 6-6',
  right: 'M9 18l6-6-6-6',
  arrow: 'M5 12h14M12 5l7 7-7 7',
  users: 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75',
  user: 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z',
  help: 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20zM9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01',
  pin: 'M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0zM12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z',
  mail: 'M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2zM22 6l-10 7L2 6',
  chat: 'M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 3.5 11.5 8.5 8.5 0 0 1 12 3a8.4 8.4 0 0 1 9 8.5z',
  news: 'M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2M18 14h-8M15 18h-5M10 6h8v4h-8z',
  home: 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22V12h6v10',
  building: 'M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h.01M9 13h.01M9 17h.01',
  check: 'M20 6 9 17l-5-5',
  clock: 'M12 6v6l4 2M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0z',
};

window.icon = (name, cls = 'w-5 h-5') =>
  `<svg class="${cls}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="${ICON_PATHS[name]}"/></svg>`;

const LOGO = '../../public/images/logo_simpel.png';

const NAV = [
  ['home', 'Beranda', 'beranda.html'],
  ['doc', 'Daftar Layanan Surat', 'daftar-layanan.html'],
  ['status', 'Cek Status Permohonan', '#'],
  ['download', 'Download Template Surat', 'download-template.html'],
  ['users', 'Struktur Organisasi', 'struktur-organisasi.html'],
  ['news', 'Berita & Pengumuman', '#'],
  ['map', 'Peta Wilayah', '#'],
  ['help', 'FAQ', '#'],
];

function renderTopbar(active) {
  const items = NAV.map(([ic, label, href]) => {
    const on = href === active;
    return `<a href="${href}" class="flex items-center gap-3 px-4 h-12 text-[15px] ${on ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-gray-700'}">${icon(ic, 'w-5 h-5 ' + (on ? 'text-primary-600' : 'text-gray-400'))}${label}</a>`;
  }).join('');
  return `
  <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <input type="checkbox" id="navtoggle" class="peer hidden">
    <div class="flex items-center justify-between h-14 px-4">
      <a href="beranda.html" class="flex items-center gap-2">
        <img src="${LOGO}" alt="SiMPEL" class="h-8 w-auto">
        <span class="leading-none"><span class="block text-[15px] font-bold text-gray-900">SiMPEL</span><span class="block text-[9px] font-medium tracking-wider text-gray-500 mt-0.5">LANDASAN ULIN</span></span>
      </a>
      <label for="navtoggle" class="w-11 h-11 -mr-2 flex items-center justify-center text-gray-700 cursor-pointer" aria-label="Menu">${icon('menu', 'w-6 h-6')}</label>
    </div>
    <nav class="hidden peer-checked:block absolute left-0 right-0 top-14 bg-white border-b border-gray-200 shadow-lg pb-3">
      <div class="py-1">${items}</div>
      <div class="px-4 pt-2"><a href="daftar-layanan.html" class="flex items-center justify-center gap-2 h-12 rounded-xl bg-primary-600 text-white font-semibold">Ajukan Surat ${icon('arrow', 'w-4 h-4')}</a></div>
    </nav>
  </header>`;
}

function renderFooter() {
  return `
  <footer class="bg-white border-t border-gray-200 mt-10">
    <div class="px-4 pt-6 pb-4">
      <div class="flex items-center gap-2">
        <img src="${LOGO}" alt="" class="h-8 w-auto">
        <span class="text-[15px] font-bold text-gray-900">SiMPEL Landasan Ulin</span>
      </div>
      <p class="text-[13px] text-gray-500 mt-2 leading-relaxed">Sistem Informasi Pelayanan Administrasi Kecamatan Landasan Ulin. Mengurus surat secara digital.</p>
      <div class="mt-4 space-y-2.5 text-[13px] text-gray-600">
        <p class="flex gap-2.5">${icon('pin', 'w-4 h-4 mt-0.5 text-primary-600 flex-none')}<span>Jl. Kenanga, RT.06/RW.09, Kel. Landasan Ulin Timur, Kec. Landasan Ulin, Kota Banjarbaru 70724</span></p>
        <p class="flex gap-2.5">${icon('mail', 'w-4 h-4 text-primary-600 flex-none')}<span>kecamatan@landasanulin.id</span></p>
      </div>
    </div>
    <div class="border-t border-gray-100">
      <details class="border-b border-gray-100"><summary class="flex items-center justify-between h-12 px-4 text-sm font-semibold text-gray-800 list-none">Navigasi ${icon('down', 'w-4 h-4 text-gray-400')}</summary>
        <div class="px-4 pb-3 grid grid-cols-2 gap-y-2 text-[13px] text-gray-600"><a href="beranda.html">Beranda</a><a href="daftar-layanan.html">Ajukan Surat</a><a href="#">Cek Status</a><a href="#">FAQ</a></div></details>
      <details><summary class="flex items-center justify-between h-12 px-4 text-sm font-semibold text-gray-800 list-none">Layanan ${icon('down', 'w-4 h-4 text-gray-400')}</summary>
        <div class="px-4 pb-3 grid grid-cols-2 gap-y-2 text-[13px] text-gray-600"><a href="daftar-layanan.html">Daftar Layanan</a><a href="download-template.html">Download Template</a><a href="struktur-organisasi.html">Struktur Organisasi</a></div></details>
    </div>
    <p class="text-center text-[11px] text-gray-400 py-4 border-t border-gray-100">© 2026 SiMPEL Landasan Ulin · Privacy Policy · Terms of Service</p>
  </footer>`;
}

document.addEventListener('DOMContentLoaded', () => {
  const t = document.querySelector('[data-topbar]');
  if (t) t.outerHTML = renderTopbar(t.dataset.active || '');
  const f = document.querySelector('[data-footer]');
  if (f) f.outerHTML = renderFooter();
  if (new URLSearchParams(location.search).has('menu')) {
    const c = document.getElementById('navtoggle');
    if (c) c.checked = true;
  }
  // dipakai skrip render PNG untuk mengukur tinggi halaman penuh
  setTimeout(() => { document.documentElement.dataset.h = document.documentElement.scrollHeight; }, 1500);
});
