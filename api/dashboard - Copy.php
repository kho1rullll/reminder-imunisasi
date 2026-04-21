<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard – ImunisasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
        theme: {
            extend: {
            fontFamily: { poppins: ['Poppins', 'sans-serif'] },
            colors: {
                'brand-blue': '#2563eb',
                'brand-sky':  '#0ea5e9',
                'brand-deep': '#1a3a8f',
            }
            }
        }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar-link { transition: all 0.3s; position: relative; overflow: hidden; }
        .sidebar-link::before {
        content: '';
        position: absolute; inset: 0; border-radius: 12px;
        background: linear-gradient(100deg, rgba(255,255,255,0.2), rgba(186,230,253,0.12));
        opacity: 0;
        transform: translateX(-100%) skewX(-6deg);
        transition: opacity 0.3s, transform 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        .sidebar-link:hover::before, .sidebar-link.active::before { opacity: 1; transform: translateX(0) skewX(0deg); }
        .sidebar-link:hover, .sidebar-link.active { color: white !important; }
        .sidebar-link.active::after {
        content: '';
        position: absolute; left: 0; top: 22%; bottom: 22%;
        width: 3px; border-radius: 4px;
        background: #38bdf8;
        }
        .stat-card { transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(14,165,233,0.18); }
        .vaccine-item { transition: all 0.3s; }
        .vaccine-item:hover { transform: translateX(5px); border-color: #0ea5e9; }
        .progress-bar { transition: width 1s ease; }
        .sidebar-bg { background: linear-gradient(170deg, #0f2878 0%, #2563eb 55%, #0ea5e9 100%); }
        .sidebar-blob1 { position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.07); pointer-events:none; }
        .sidebar-blob2 { position:absolute; bottom:80px; left:-60px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none; }
        .dropdown-menu { display:none; position:absolute; right:0; top:calc(100% + 8px); min-width:180px; background:white; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,0.12); border:1px solid rgba(14,165,233,0.12); z-index:50; overflow:hidden; }
        .dropdown-menu.open { display:block; animation:dropIn 0.2s ease; }
        @keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    </style>
    </head>
    <body class="bg-gray-50 h-screen flex overflow-hidden">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar-bg w-64 flex-shrink-0 flex flex-col z-50 relative overflow-hidden" id="dashSidebar">
        <div class="sidebar-blob1"></div>
        <div class="sidebar-blob2"></div>

        <!-- Brand -->
        <div class="flex items-center gap-3 p-6 pb-5 border-b border-white/12 relative z-10">
        <div class="w-11 h-11 bg-white/20 border border-white/30 rounded-2xl flex items-center justify-center text-2xl backdrop-blur flex-shrink-0">💉</div>
        <div>
            <div class="font-bold text-white text-base leading-tight">ImunisasiKu</div>
            <div class="text-white/50 text-xs uppercase tracking-wider">Reminder System</div>
        </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto p-4 relative z-10">
        <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mb-3 mt-1">Menu Utama</p>
        <ul class="space-y-1 list-none p-0">
            <li>
            <a href="#" class="sidebar-link active flex items-center gap-3 px-3 py-3 rounded-xl text-white/80 text-sm font-medium no-underline" data-tab="beranda">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">🏠</span>
                <span class="relative z-10">Beranda</span>
            </a>
            </li>
            <li>
            <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium no-underline" data-tab="jadwal">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">📅</span>
                <span class="relative z-10">Jadwal Vaksin</span>
            </a>
            </li>
            <li>
            <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium no-underline" data-tab="riwayat">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">📋</span>
                <span class="relative z-10">Riwayat Imunisasi</span>
            </a>
            </li>
            <li>
            <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium no-underline" data-tab="notifikasi">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">🔔</span>
                <span class="relative z-10">Notifikasi</span>
                <span class="ml-auto bg-red-400 text-white text-xs font-bold rounded-full px-2 py-0.5 relative z-10">3</span>
            </a>
            </li>
            <li>
            <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium no-underline" data-tab="konsultasi">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">👨‍⚕️</span>
                <span class="relative z-10">Konsultasi Dokter</span>
            </a>
            </li>
            <li>
            <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium no-underline" data-tab="profil">
                <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base flex-shrink-0 relative z-10">👤</span>
                <span class="relative z-10">Profil Saya</span>
            </a>
            </li>
        </ul>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-white/12 relative z-10">
        <a href="index.php" class="flex items-center gap-3 px-3 py-3 rounded-xl text-white/60 hover:text-white hover:bg-white/10 text-sm font-medium no-underline transition-all duration-200">
            <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-base flex-shrink-0">🚪</span>
            Keluar
        </a>
        </div>
    </aside>

    <!-- ===== MAIN AREA ===== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between shadow-sm z-40">
        <div>
            <h1 class="text-lg font-bold text-gray-800" id="pageTitle">Dashboard</h1>
            <p class="text-xs text-gray-400" id="pageDate"></p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Search -->
            <div class="relative hidden sm:block">
            <input type="text" placeholder="Cari vaksin, jadwal..."
                class="pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl w-52 focus:outline-none focus:border-brand-sky focus:ring-2 focus:ring-sky-100 transition-all" />
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
            </div>
            <!-- Notif bell -->
            <button class="relative w-10 h-10 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-lg hover:bg-blue-50 transition-colors">
            🔔
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <!-- Profile dropdown -->
            <div class="relative">
            <button id="profileBtn" class="flex items-center gap-2 cursor-pointer bg-gradient-to-r from-brand-blue to-brand-sky rounded-xl px-3 py-2 text-white text-sm font-semibold hover:opacity-90 transition-opacity border-0">
                <span>👤</span>
                <span class="hidden sm:block">Budi S.</span>
                <span class="text-white/70 text-xs">▾</span>
            </button>
            <div class="dropdown-menu" id="profileMenu">
                <a href="#" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 no-underline" onclick="switchTab('profil')">👤 Profil Saya</a>
                <a href="#" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 no-underline border-t border-gray-100">⚙️ Pengaturan</a>
                <a href="index.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-500 hover:bg-red-50 no-underline border-t border-gray-100">🚪 Keluar</a>
            </div>
            </div>
        </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 p-8 overflow-y-auto">

        <!-- ===== BERANDA TAB ===== -->
        <div id="tab-beranda" class="tab-content">
            <!-- Welcome banner -->
            <div class="bg-gradient-to-r from-brand-deep via-brand-blue to-brand-sky rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-[-50px] right-[-50px] w-48 h-48 rounded-full bg-white/8"></div>
            <div class="absolute bottom-[-40px] left-[40%] w-36 h-36 rounded-full bg-white/6"></div>
            <div class="relative z-10">
                <p class="text-white/65 text-sm mb-1">Selamat datang kembali 👋</p>
                <h2 class="text-2xl font-bold mb-2">Halo, Budi Santoso!</h2>
                <p class="text-white/75 text-sm max-w-lg">Ada <strong class="text-white">2 jadwal vaksin</strong> yang akan datang dalam 7 hari ke depan. Pastikan Anda tidak melewatkannya.</p>
            </div>
            </div>

            <!-- Stats row -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-xl mb-4">💉</div>
                <p class="text-2xl font-bold text-gray-800">12</p>
                <p class="text-xs text-gray-500 mt-1">Total Vaksin Selesai</p>
            </div>
            <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center text-xl mb-4">📅</div>
                <p class="text-2xl font-bold text-gray-800">2</p>
                <p class="text-xs text-gray-500 mt-1">Jadwal Mendatang</p>
            </div>
            <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-xl mb-4">✅</div>
                <p class="text-2xl font-bold text-gray-800">85%</p>
                <p class="text-xs text-gray-500 mt-1">Progres Imunisasi</p>
            </div>
            <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center text-xl mb-4">👨‍⚕️</div>
                <p class="text-2xl font-bold text-gray-800">3</p>
                <p class="text-xs text-gray-500 mt-1">Konsultasi Aktif</p>
            </div>
            </div>

            <!-- Two column content -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Upcoming vaccines -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800">📅 Jadwal Mendatang</h3>
                <button class="text-xs text-brand-sky font-semibold hover:underline" onclick="switchTab('jadwal')">Lihat Semua</button>
                </div>
                <ul class="space-y-3 list-none p-0">
                <li class="vaccine-item flex items-center gap-4 p-4 bg-orange-50 border border-orange-100 rounded-xl cursor-pointer">
                    <div class="w-11 h-11 bg-orange-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">💉</div>
                    <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">Vaksin MMR</p>
                    <p class="text-xs text-gray-500">Anak: Dina · Selasa, 15 Apr 2025</p>
                    </div>
                    <span class="text-xs font-bold text-orange-600 bg-orange-100 px-3 py-1 rounded-full flex-shrink-0">7 hari</span>
                </li>
                <li class="vaccine-item flex items-center gap-4 p-4 bg-blue-50 border border-blue-100 rounded-xl cursor-pointer">
                    <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🛡️</div>
                    <div class="flex-1">
                    <p class="font-semibold text-sm text-gray-800">Vaksin Influenza</p>
                    <p class="text-xs text-gray-500">Dewasa · Sabtu, 19 Apr 2025</p>
                    </div>
                    <span class="text-xs font-bold text-brand-blue bg-blue-100 px-3 py-1 rounded-full flex-shrink-0">11 hari</span>
                </li>
                </ul>
            </div>

            <!-- Progress immunization -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800">📊 Progres Imunisasi Anak</h3>
                </div>
                <p class="text-sm text-gray-500 mb-4">Dina · Usia 15 Bulan</p>
                <ul class="space-y-3 list-none p-0" id="progressList">
                <li>
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-1"><span>Hepatitis B</span><span class="text-green-600">Selesai</span></div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full bg-green-400 rounded-full" style="width:0%" data-width="100%"></div></div>
                </li>
                <li>
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-1"><span>DPT-HB-Hib</span><span class="text-green-600">Selesai</span></div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full bg-green-400 rounded-full" style="width:0%" data-width="100%"></div></div>
                </li>
                <li>
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-1"><span>MMR</span><span class="text-orange-500">Belum</span></div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full bg-orange-400 rounded-full" style="width:0%" data-width="30%"></div></div>
                </li>
                <li>
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-1"><span>Polio</span><span class="text-green-600">Selesai</span></div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full bg-green-400 rounded-full" style="width:0%" data-width="100%"></div></div>
                </li>
                <li>
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-1"><span>BCG</span><span class="text-green-600">Selesai</span></div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="progress-bar h-full bg-green-400 rounded-full" style="width:0%" data-width="100%"></div></div>
                </li>
                </ul>
            </div>
            </div>
        </div>

        <!-- ===== JADWAL TAB ===== -->
        <div id="tab-jadwal" class="tab-content hidden">
        </div>

        <!-- ===== RIWAYAT TAB ===== -->
        <div id="tab-riwayat" class="tab-content hidden">
        </div>

        <!-- ===== NOTIFIKASI TAB ===== -->
        <div id="tab-notifikasi" class="tab-content hidden">
        </div>

        <!-- ===== KONSULTASI TAB ===== -->
        <div id="tab-konsultasi" class="tab-content hidden">
        </div>

        <!-- ===== PROFIL TAB ===== -->
        <div id="tab-profil" class="tab-content hidden">
            <h2 class="text-xl font-bold text-gray-800 mb-6">👤 Profil Saya</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile card -->
            <div class="bg-gradient-to-br from-brand-blue to-brand-sky rounded-2xl p-6 text-white text-center">
                <div class="w-20 h-20 bg-white/20 border-2 border-white/30 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">👤</div>
                <h3 class="font-bold text-lg">Khoirulbarie Kholifatul A.</h3>
                <p class="text-white/65 text-sm mt-1">userimunisasiku@gmail.com</p>
                <div class="mt-4 bg-white/15 rounded-xl p-3 text-left">
                <p class="text-white/60 text-xs">No. Telepon</p>
                <p class="font-semibold text-sm">+62 812-3456-7890</p>
                </div>
            </div>
            <!-- Edit form -->
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-5">Edit Informasi Profil</h3>
                <form class="space-y-4" onsubmit="event.preventDefault(); alert('Profil berhasil diperbarui!')">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Depan</label>
                    <input type="text" value="Budi" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                    </div>
                    <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Belakang</label>
                    <input type="text" value="Santoso" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                    <input type="email" value="user@imunisasiku.id" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon</label>
                    <input type="tel" value="+62 812-3456-7890" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                    <textarea class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky resize-none" rows="2">Jl. Kemerdekaan No. 5, Bangil, Jawa Timur</textarea>
                </div>
                <button type="submit" class="bg-gradient-to-r from-brand-blue to-brand-sky text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity border-0 cursor-pointer">Simpan Perubahan</button>
                </form>
            </div>
            </div>
        </div>

        </main>
    </div>

    <script>
        // Set date
        const now = new Date();
        document.getElementById('pageDate').textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        // Tab switching
        function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tab).classList.remove('hidden');
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.classList.remove('active');
            if (link.dataset.tab === tab) link.classList.add('active');
        });
        const titles = { beranda:'Dashboard', jadwal:'Jadwal Vaksin', riwayat:'Riwayat Imunisasi', notifikasi:'Notifikasi', konsultasi:'Konsultasi Dokter', profil:'Profil Saya' };
        document.getElementById('pageTitle').textContent = titles[tab] || 'Dashboard';
        document.getElementById('profileMenu').classList.remove('open');
        }

        document.querySelectorAll('.sidebar-link').forEach(link => {
        link.addEventListener('click', e => { e.preventDefault(); switchTab(link.dataset.tab); });
        });

        // Profile dropdown
        const profileBtn  = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');
        profileBtn.addEventListener('click', e => { e.stopPropagation(); profileMenu.classList.toggle('open'); });
        document.addEventListener('click', () => profileMenu.classList.remove('open'));

        // Animate progress bars on load
        setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            bar.style.width = bar.dataset.width;
        });
        }, 400);
    </script>
    </body>
    </html>
