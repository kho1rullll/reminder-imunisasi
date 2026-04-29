<?php
session_start();
require 'Server/koneksi.php';

if (!isset($_SESSION['id']) && isset($_COOKIE['login_email'])) {
    $cookie_email = $_COOKIE['login_email'];
    $query_cookie = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$cookie_email'");
    
    if (mysqli_num_rows($query_cookie) === 1) {
        $row = mysqli_fetch_assoc($query_cookie);
        // Bangun ulang Session-nya
        $_SESSION['id'] = $row['id'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
    }
}

// Proteksi Halaman: Pastikan hanya User biasa yang bisa masuk
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$query_stat_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE user_id = '$user_id' AND status = 'Selesai'");
$stat_selesai = mysqli_fetch_assoc($query_stat_selesai)['total'];

$query_stat_mendatang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE user_id = '$user_id' AND status != 'Selesai'");
$stat_mendatang = mysqli_fetch_assoc($query_stat_mendatang)['total'];

$query_jadwal = mysqli_query($koneksi, "SELECT * FROM jadwal_imunisasi WHERE user_id = '$user_id' AND status != 'Selesai' ORDER BY tanggal_jadwal ASC");

$query_riwayat = mysqli_query($koneksi, "SELECT * FROM jadwal_imunisasi WHERE user_id = '$user_id' AND status = 'Selesai' ORDER BY tanggal_jadwal DESC");

$query_notif = mysqli_query($koneksi, "SELECT * FROM notifikasi WHERE user_id = '$user_id' ORDER BY created_at DESC");

$query_riwayat = mysqli_query($koneksi, "SELECT * FROM jadwal_imunisasi 
                                        WHERE user_id = '$user_id' 
                                        AND status = 'Selesai' 
                                        ORDER BY tanggal_jadwal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard – ImunisasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="icon" type="image" href="../../images/Favicon.png">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            content: ''; position: absolute; left: 0; top: 22%; bottom: 22%;
            width: 3px; border-radius: 4px; background: #38bdf8;
        }
        .stat-card { transition: transform 0.3s, box-shadow 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(14,165,233,0.18); }
        .vaccine-item { transition: all 0.3s; }
        .vaccine-item:hover { transform: translateX(5px); border-color: #0ea5e9; }
        .progress-bar { transition: width 1s ease; }
        .sidebar-bg { background: linear-gradient(170deg, #0f2878 0%, #2563eb 55%, #0ea5e9 100%); }
        .sidebar-blob1 { position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.07); pointer-events:none; }
        .sidebar-blob2 { position:absolute; bottom:80px; left:-60px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,0.05); pointer-events:none; }
        .dropdown-menu { display:none; position:absolute; right:0; top:calc(100% + 8px); min-width:180px; background:white; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,0.12); border:1px solid rgba(14,165,233,0.12); z-index:50; }
        .dropdown-menu.open { display:block; animation:dropIn 0.2s ease; }
        @keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden">

    <aside id="dashSidebar" class="sidebar-bg w-64 flex-shrink-0 flex flex-col z-[60] fixed inset-y-0 left-0 transform -translate-x-full transition-transform duration-300 md:relative md:translate-x-0 overflow-hidden">
        <div class="sidebar-blob1"></div>
        <div class="sidebar-blob2"></div>

        <div class="flex items-center gap-3 p-6 pb-5 border-b border-white/12 relative z-10">
            <div class="w-11 h-11 bg-white/20 border border-white/30 rounded-2xl flex items-center justify-center backdrop-blur flex-shrink-0 overflow-hidden">
                <img src="../../images/Favicon.png" alt="Logo ImunisasiKu" class="w-full h-full object-contain p-1.5">
            </div>
            <div>
                <div class="font-bold text-white text-base leading-tight">ImunisasiKu</div>
                <div class="text-white/50 text-xs uppercase tracking-wider">Reminder System</div>
            </div>
        </div>

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

        <div class="p-4 border-t border-white/12 relative z-10">
            <a href="Project/logout.php" class="flex items-center gap-3 px-3 py-3 rounded-xl text-white/60 hover:text-white hover:bg-white/10 text-sm font-medium transition-all">
                <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-base flex-shrink-0">🚪</span> Keluar
            </a>
        </div>
    </aside>

    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/50 z-50 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b border-gray-100 px-4 sm:px-8 py-4 flex items-center justify-between shadow-sm z-40">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-brand-blue focus:outline-none text-2xl border-0 bg-transparent cursor-pointer">
                    ☰
                </button>
                <div>
                    <h1 class="text-lg font-bold text-gray-800" id="pageTitle">Dashboard</h1>
                    <p class="text-xs text-gray-400" id="pageDate"></p>
                </div>
            <div class="flex items-center gap-4">
                <div class="relative hidden sm:block">
                    <input type="text" placeholder="Cari vaksin, jadwal..." class="pl-9 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl w-52 focus:outline-none focus:border-brand-sky focus:ring-2 focus:ring-sky-100 transition-all" />
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
                </div>
                <button class="relative w-10 h-10 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center text-lg hover:bg-blue-50 transition-colors">
                    🔔<span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                <div class="relative">
                    <button id="profileBtn" class="flex items-center gap-2 cursor-pointer bg-gradient-to-r from-brand-blue to-brand-sky rounded-xl px-3 py-2 text-white text-sm font-semibold hover:opacity-90 transition-opacity border-0">
                        <span>👤</span>
                        <span class="hidden sm:block"><?php echo $_SESSION['nama']; ?></span>
                        <span class="text-white/70 text-xs">▾</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <a href="#" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 no-underline" onclick="switchTab('profil')">👤 Profil Saya</a>
                        <a href="#" class="flex items-center gap-2 px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 no-underline border-t border-gray-100">⚙️ Pengaturan</a>
                        <a href="Project/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-500 hover:bg-red-50 no-underline border-t border-gray-100">🚪 Keluar</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-8 overflow-y-auto">

            <div id="tab-beranda" class="tab-content">
                <div class="bg-gradient-to-r from-brand-deep via-brand-blue to-brand-sky rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
                    <div class="absolute top-[-50px] right-[-50px] w-48 h-48 rounded-full bg-white/8"></div>
                    <div class="absolute bottom-[-40px] left-[40%] w-36 h-36 rounded-full bg-white/6"></div>
                    <div class="relative z-10">
                        <p class="text-white/65 text-sm mb-1">Selamat datang kembali 👋</p>
                        <h2 class="text-2xl font-bold mb-2">Halo, <?php echo $_SESSION['nama']; ?>!</h2>
                        <p class="text-white/75 text-sm max-w-lg">Ada <strong class="text-white"><?= $stat_mendatang; ?> jadwal vaksin</strong> yang akan datang. Pastikan Anda tidak melewatkannya.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                    <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                        <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-xl mb-4">💉</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $stat_selesai; ?></p>
                        <p class="text-xs text-gray-500 mt-1">Total Vaksin Selesai</p>
                    </div>
                    <div class="stat-card bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                        <div class="w-11 h-11 bg-orange-50 rounded-xl flex items-center justify-center text-xl mb-4">📅</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $stat_mendatang; ?></p>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="font-bold text-gray-800">📅 Jadwal Mendatang</h3>
                            <button class="text-xs text-brand-sky font-semibold hover:underline" onclick="switchTab('jadwal')">Lihat Semua</button>
                        </div>
                        <ul class="space-y-3 list-none p-0">
                            <?php 
                            $limit = 0;
                            mysqli_data_seek($query_jadwal, 0); // Reset pointer
                            while($row = mysqli_fetch_assoc($query_jadwal)) : 
                                if($limit >= 3) break; // Cuma nampilin 3 jadwal teratas
                                
                                // Hitung sisa hari
                                $tanggal_jadwal = new DateTime($row['tanggal_jadwal']);
                                $sekarang = new DateTime();
                                $selisih = $sekarang->diff($tanggal_jadwal);
                                $sisa_hari = $selisih->invert == 0 ? $selisih->days . " hari lagi" : "Terlewat";
                            ?>
                            <li class="vaccine-item flex items-center gap-4 p-4 bg-orange-50 border border-orange-100 rounded-xl cursor-pointer">
                                <div class="w-11 h-11 bg-orange-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">💉</div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-800"><?= htmlspecialchars($row['jenis_vaksin']); ?></p>
                                    <p class="text-xs text-gray-500">Anak: <?= htmlspecialchars($row['nama_pasien']); ?> · <?= date('d M Y', strtotime($row['tanggal_jadwal'])); ?></p>
                                </div>
                                <span class="text-xs font-bold text-orange-600 bg-orange-100 px-3 py-1 rounded-full flex-shrink-0"><?= $sisa_hari; ?></span>
                            </li>
                            <?php 
                                $limit++;
                            endwhile; 
                            
                            if($limit == 0) echo '<p class="text-sm text-gray-400 italic">Tidak ada jadwal mendatang.</p>';
                            ?>
                        </ul>
                    </div>

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
                        </ul>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm mb-8 mt-8">
                        <h3 class="font-bold text-gray-800 mb-6 text-xl">📊 Statistik Anak Umur 12-23 Bulan yang Menerima Imunisasi Dasar</h3>
                        <div class="relative h-[500px] w-full">
                            <canvas id="userChart"></canvas>
                        </div>
                    </div>
            </div>

            <div id="tab-jadwal" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">📅 Jadwal Vaksin</h2>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <p class="font-semibold text-gray-700">Daftar Jadwal Imunisasi</p>
                        <a href="tambah_jadwal.php" class="text-sm bg-gradient-to-r from-brand-blue to-brand-sky text-white px-4 py-2 rounded-xl font-semibold hover:opacity-90 transition-opacity border-0 cursor-pointer no-underline block">
                            + Tambah Jadwal
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Vaksin</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Penerima</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php 
                                mysqli_data_seek($query_jadwal, 0); // Reset pointer
                                while($row = mysqli_fetch_assoc($query_jadwal)) : 
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4"><span class="font-semibold text-gray-800"><?= htmlspecialchars($row['jenis_vaksin']); ?></span></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($row['nama_pasien']); ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= date('d F Y', strtotime($row['tanggal_jadwal'])); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="bg-orange-100 text-orange-600 text-xs font-bold px-3 py-1 rounded-full"><?= htmlspecialchars($row['status']); ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="edit_jadwal.php?id=<?= $row['id']; ?>" class="text-brand-sky text-xs font-semibold hover:underline no-underline mr-2">Edit</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-riwayat" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">📋 Riwayat Imunisasi</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                    <?php while($row = mysqli_fetch_assoc($query_riwayat)) : ?>
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 hover:border-brand-sky transition-colors">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">✅</div>
                        <div class="flex-1">
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($row['jenis_vaksin']); ?></p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Penerima: <?= htmlspecialchars($row['nama_pasien']); ?> · Tanggal: <?= date('d F Y', strtotime($row['tanggal_jadwal'])); ?>
                            </p>
                        </div>
                        <button onclick="bukaModalLapor('<?= htmlspecialchars($row['jenis_vaksin']); ?>', '<?= $row['id']; ?>')" 
                                class="text-xs font-semibold px-4 py-2 bg-white border border-red-200 text-red-500 rounded-lg hover:bg-red-50 transition-all cursor-pointer">
                            ⚠️ Lapor Keluhan
                        </button>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div id="tab-notifikasi" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">🔔 Notifikasi</h2>
                <div class="space-y-4">
                    <?php while($notif = mysqli_fetch_assoc($query_notif)) : ?>
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                        <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                            <?= $notif['tipe'] == 'Peringatan' ? '⚠️' : '📅'; ?>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($notif['judul']); ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($notif['pesan']); ?></p>
                            <p class="text-xs text-gray-400 mt-2"><?= date('d M Y H:i', strtotime($notif['created_at'])); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div id="tab-konsultasi" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">👨‍⚕️ Konsultasi Dokter</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:border-brand-sky transition-colors cursor-pointer">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 bg-gradient-to-br from-brand-blue to-brand-sky rounded-2xl flex items-center justify-center text-2xl text-white">👨‍⚕️</div>
                            <div>
                                <p class="font-bold text-gray-800">Dr. Ahmad Rizki, Sp.A</p>
                                <p class="text-xs text-gray-500">Dokter Spesialis Anak</p>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="text-yellow-400 text-xs">⭐⭐⭐⭐⭐</span>
                                    <span class="text-xs text-gray-400">(4.9)</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mb-4 leading-relaxed">Spesialis imunisasi anak dengan pengalaman lebih dari 10 tahun. Tersedia untuk konsultasi online dan tatap muka.</p>
                        <button class="w-full py-2.5 bg-gradient-to-r from-brand-blue to-brand-sky text-white text-sm font-semibold rounded-xl hover:opacity-90 transition-opacity border-0 cursor-pointer">Buat Janji</button>
                    </div>
                </div>
            </div>

            <div id="tab-profil" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">👤 Profil Saya</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-8">
                    <div class="bg-gradient-to-br from-brand-blue to-brand-sky rounded-2xl p-6 text-white text-center">
                        <div class="w-20 h-20 bg-white/20 border-2 border-white/30 rounded-full flex items-center justify-center text-4xl mx-auto mb-4">👤</div>
                        <h3 class="font-bold text-lg"><?php echo $_SESSION['nama']; ?></h3>
                        <p class="text-white/65 text-sm mt-1"><?php echo $_SESSION['email']; ?></p>
                        <div class="mt-4 bg-white/15 rounded-xl p-3 text-left">
                            <p class="text-white/60 text-xs">Status Akun</p>
                            <p class="font-semibold text-sm uppercase"><?php echo $_SESSION['role']; ?></p>
                        </div>
                    </div>
                    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                        <h3 class="font-bold text-gray-800 mb-5">Edit Informasi Profil</h3>
                        <form class="space-y-4" onsubmit="event.preventDefault(); alert('Profil berhasil diperbarui!')">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap</label>
                                <input type="text" value="<?php echo $_SESSION['nama']; ?>" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                                <input type="email" value="<?php echo $_SESSION['email']; ?>" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">No. Telepon</label>
                                <input type="tel" placeholder="Tambahkan nomor telepon..." class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat</label>
                                <textarea class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky resize-none" rows="2" placeholder="Tambahkan alamat lengkap..."></textarea>
                            </div>
                            <button type="submit" class="bg-gradient-to-r from-brand-blue to-brand-sky text-white px-6 py-2.5 rounded-xl font-semibold text-sm hover:opacity-90 transition-opacity border-0 cursor-pointer">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modalLapor" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all scale-95" id="modalLaporContent">
                    <div class="bg-gradient-to-r from-red-500 to-rose-500 p-5 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg">⚠️ Lapor Keluhan Medis</h3>
                        <button onclick="tutupModalLapor()" class="text-white/80 hover:text-white text-2xl">&times;</button>
                    </div>
                    <form action="Project/ProsesLaporan.php" method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['id']; ?>">
                        <input type="hidden" name="riwayat_id" id="inputRiwayatId">
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Vaksin Terkait</label>
                            <input type="text" name="nama_vaksin" id="inputNamaVaksin" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis Gejala / Keluhan</label>
                            <select name="jenis_gejala" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-red-400 outline-none" required>
                                <option value="">-- Pilih --</option>
                                <option value="Demam Tinggi">Demam Tinggi</option>
                                <option value="Bengkak/Kemerahan">Bengkak/Kemerahan</option>
                                <option value="Anak Rewel">Anak Sangat Rewel</option>
                                <option value="Lainnya">Lainnya...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Detail Laporan</label>
                            <textarea name="detail_laporan" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-red-400 outline-none resize-none" rows="3" placeholder="Ceritakan kondisi anak..." required></textarea>
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" onclick="tutupModalLapor()" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 shadow-lg shadow-red-100">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        // 1. Logika Tanggal di Topbar
        const now = new Date();
        const dateEl = document.getElementById('pageDate');
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        }

        // 2. Logika Pindah Tab (Beranda, Jadwal, dll)
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            const targetTab = document.getElementById('tab-' + tab);
            if(targetTab) targetTab.classList.remove('hidden');
            
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
                if (link.dataset.tab === tab) link.classList.add('active');
            });
            
            // Ganti Judul Topbar
            const titles = { 
                beranda: 'Dashboard', 
                jadwal: 'Jadwal Vaksin', 
                riwayat: 'Riwayat Imunisasi', 
                notifikasi: 'Notifikasi', 
                konsultasi: 'Konsultasi Dokter', 
                profil: 'Profil Saya' 
            };
            
            const titleEl = document.getElementById('pageTitle');
            if(titleEl) titleEl.textContent = titles[tab] || 'Dashboard';
        }

        // Trigger Tab saat menu sidebar diklik
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', e => { 
                e.preventDefault(); 
                switchTab(link.dataset.tab); 
            });
        });

        // 3. Logika Dropdown Profil (Kanan Atas)
        const profileBtn  = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        if(profileBtn && profileMenu) {
            profileBtn.addEventListener('click', e => { 
                e.stopPropagation(); 
                profileMenu.classList.toggle('open'); 
            });
            document.addEventListener('click', () => profileMenu.classList.remove('open'));
        }

        // ==========================================
        // 4. LOGIKA MODAL LAPORAN KELUHAN (YANG BIKIN ERROR TADI)
        // ==========================================
        function bukaModalLapor(namaVaksin, idRiwayat) {
            // Isi otomatis input readonly di dalam modal
            document.getElementById('inputNamaVaksin').value = namaVaksin;
            document.getElementById('inputRiwayatId').value = idRiwayat;
            
            const modal = document.getElementById('modalLapor');
            const modalContent = document.getElementById('modalLaporContent');
            
            // Munculkan background
            modal.classList.remove('hidden');
            
            // Efek pop-up animasi
            setTimeout(() => { 
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function tutupModalLapor() {
            const modal = document.getElementById('modalLapor');
            const modalContent = document.getElementById('modalLaporContent');
            
            // Animasi mengecil
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            // Sembunyikan setelah animasi selesai
            setTimeout(() => { 
                modal.classList.add('hidden'); 
            }, 200);
        }

        // 5. Animasi Progress Bar Beranda
        setTimeout(() => {
            document.querySelectorAll('.progress-bar').forEach(bar => {
                if(bar.dataset.width) {
                    bar.style.width = bar.dataset.width;
                }
            });
        }, 400);

        // Mengambil data dari file PHP yang kita buat tadi
        fetch('Server/api_bps_user.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "OK") {
                    const labels = data.vervar.map(item => item.label);
                    const values = Object.values(data.datacontent).map(val => parseFloat(val) || 0);

                    const ctx = document.getElementById('userChart').getContext('2d'); // ID kanvas kamu di dashboard.php juga adminChart kan?
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Rata-rata Nasional (%)',
                                data: values,
                                backgroundColor: 'rgba(14, 165, 233, 0.7)',
                                borderRadius: 8,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // WAJIB false agar mengikuti tinggi div [450px] tadi
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom' // Pindahkan legenda ke bawah agar grafik lebih lebar
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxRotation: 45, // Memiringkan tulisan label (Aceh, Riau, dll) agar tidak tumpang tindih
                                        minRotation: 45
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => console.error('Error:', error));

            // ====== LOGIKA TOGGLE SIDEBAR MOBILE ======
            function toggleSidebar() {
                const sidebar = document.getElementById('dashSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                // Geser sidebar masuk/keluar
                sidebar.classList.toggle('-translate-x-full');
                // Tampilkan/sembunyikan overlay gelap
                overlay.classList.toggle('hidden');
            }
    </script>
</body>
</html>