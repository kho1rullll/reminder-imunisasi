<?php
session_start();
require 'Server/koneksi.php';

if (!isset($_SESSION['id']) && isset($_COOKIE['login_email'])) {
    $cookie_email = $_COOKIE['login_email'];
    $query_cookie = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$cookie_email'");
    
    if (mysqli_num_rows($query_cookie) === 1) {
        $row = mysqli_fetch_assoc($query_cookie);
        $_SESSION['id'] = $row['id'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
    }
}

// Proteksi Halaman: Pastikan hanya Admin yang bisa masuk
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// 1. Ambil data semua pengguna
$query_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY created_at DESC");

// 2. Ambil data semua jadwal (beserta nama ortu)
$query_jadwal = mysqli_query($koneksi, "SELECT jadwal_imunisasi.*, users.nama as nama_ortu 
                                        FROM jadwal_imunisasi 
                                        JOIN users ON jadwal_imunisasi.user_id = users.id 
                                        ORDER BY tanggal_jadwal ASC");

// 3. Ambil data laporan keluhan
$query_laporan = mysqli_query($koneksi, "SELECT laporan_keluhan.*, users.nama as nama_pelapor 
                                        FROM laporan_keluhan 
                                        JOIN users ON laporan_keluhan.user_id = users.id 
                                        ORDER BY created_at DESC");

// 4. Hitung Statistik untuk Beranda
$q_total_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$total_user = mysqli_fetch_assoc($q_total_user)['total'];

$q_jadwal_minggu = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE status = 'Mendatang' AND WEEK(tanggal_jadwal) = WEEK(CURDATE())");
$jadwal_minggu = mysqli_fetch_assoc($q_jadwal_minggu)['total'];

$q_total_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jadwal_imunisasi WHERE status = 'Selesai'");
$total_selesai = mysqli_fetch_assoc($q_total_selesai)['total'];

$q_laporan_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM laporan_keluhan WHERE status_laporan = 'Belum Ditangani'");
$laporan_pending = mysqli_fetch_assoc($q_laporan_pending)['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel – ImunisasiKu</title>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar-bg { background: linear-gradient(170deg, #0f2878 0%, #2563eb 55%, #0ea5e9 100%); }
        .sidebar-link { transition: all 0.3s; position: relative; overflow: hidden; }
        .sidebar-link.active { color: white !important; background: rgba(255,255,255,0.15); }
        .sidebar-link.active::after {
            content: ''; position: absolute; left: 0; top: 22%; bottom: 22%;
            width: 3px; border-radius: 4px; background: #38bdf8;
        }
        .dropdown-menu { display:none; position:absolute; right:0; top:calc(100% + 8px); min-width:180px; background:white; border-radius:14px; box-shadow:0 10px 40px rgba(0,0,0,0.12); border:1px solid rgba(14,165,233,0.12); z-index:50; }
        .dropdown-menu.open { display:block; animation:dropIn 0.2s ease; }
        @keyframes dropIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden">

    <aside class="sidebar-bg w-64 flex-shrink-0 flex flex-col z-50 relative overflow-hidden">
        <div class="flex items-center gap-3 p-6 pb-5 border-b border-white/12">
            <div class="w-11 h-11 bg-white/20 border border-white/30 rounded-2xl flex items-center justify-center text-2xl backdrop-blur">💉</div>
            <div>
                <div class="font-bold text-white text-base leading-tight">Admin Panel</div>
                <div class="text-white/50 text-xs uppercase tracking-wider">ImunisasiKu</div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto p-4">
            <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mb-3 mt-1">Dashboard</p>
            <ul class="space-y-1 list-none p-0 mb-6">
                <li>
                    <a href="#" class="sidebar-link active flex items-center gap-3 px-3 py-3 rounded-xl text-white/80 text-sm font-medium" data-tab="beranda">
                        <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base">🏠</span>
                        <span>Beranda Utama</span>
                    </a>
                </li>
            </ul>

            <p class="text-white/40 text-xs font-semibold uppercase tracking-widest px-3 mb-3 mt-1">Manajemen Data</p>
            <ul class="space-y-1 list-none p-0">
                <li>
                    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium hover:bg-white/10" data-tab="pengguna">
                        <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base">👥</span>
                        <span>Kelola Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium hover:bg-white/10" data-tab="jadwal">
                        <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base">📅</span>
                        <span>Semua Jadwal</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium hover:bg-white/10" data-tab="vaksin">
                        <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base">💉</span>
                        <span>Manajemen Vaksin</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-xl text-white/70 text-sm font-medium hover:bg-white/10" data-tab="laporan">
                        <span class="w-8 h-8 bg-white/12 rounded-lg flex items-center justify-center text-base">🆘</span>
                        <span>Laporan Masuk</span>
                        <?php if($laporan_pending > 0): ?>
                            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $laporan_pending; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-white/12">
            <a href="Project/logout.php" class="flex items-center gap-3 px-3 py-3 rounded-xl text-white/60 hover:text-white hover:bg-white/10 text-sm font-medium no-underline transition-all">
                <span class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-base">🚪</span> Keluar
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">

        <header class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between shadow-sm z-40">
            <div>
                <h1 class="text-lg font-bold text-gray-800" id="pageTitle">Beranda Admin</h1>
                <p class="text-xs text-gray-400" id="pageDate"></p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button id="profileBtn" class="flex items-center gap-2 bg-gradient-to-r from-brand-blue to-brand-sky rounded-xl px-3 py-2 text-white text-sm font-semibold hover:opacity-90 border-0">
                        <span>👨‍💻</span>
                        <span class="hidden sm:block"><?php echo $_SESSION['nama']; ?></span>
                        <span class="text-white/70 text-xs">▾</span>
                    </button>
                    <div class="dropdown-menu" id="profileMenu">
                        <a href="Project/logout.php" class="flex items-center gap-2 px-4 py-3 text-sm text-red-500 hover:bg-red-50 no-underline">🚪 Keluar</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-8 overflow-y-auto">
            
            <div id="tab-beranda" class="tab-content">
                <div class="bg-gradient-to-r from-brand-deep to-brand-sky rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-white/65 text-sm mb-1">Status Admin: Online</p>
                        <h2 class="text-2xl font-bold mb-2">Halo, <?php echo $_SESSION['nama']; ?>!</h2>
                        <p class="text-white/75 text-sm">Anda memiliki akses penuh untuk mengelola data sistem ImunisasiKu.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:-translate-y-1 transition-transform">
                        <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-4">👥</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_user; ?></p>
                        <p class="text-xs text-gray-500 mt-1">Total Pengguna Aktif</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:-translate-y-1 transition-transform">
                        <div class="w-11 h-11 bg-orange-50 text-orange-500 rounded-xl flex items-center justify-center text-xl mb-4">📅</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $jadwal_minggu; ?></p>
                        <p class="text-xs text-gray-500 mt-1">Jadwal Minggu Ini</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:-translate-y-1 transition-transform">
                        <div class="w-11 h-11 bg-green-50 text-green-500 rounded-xl flex items-center justify-center text-xl mb-4">✅</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $total_selesai; ?></p>
                        <p class="text-xs text-gray-500 mt-1">Vaksinasi Selesai</p>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:-translate-y-1 transition-transform border-l-4 border-l-red-500">
                        <div class="w-11 h-11 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-xl mb-4">🆘</div>
                        <p class="text-2xl font-bold text-gray-800"><?= $laporan_pending; ?></p>
                        <p class="text-xs text-gray-500 mt-1">Laporan Belum Dicek</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl p-8 shadow-sm mb-8">
                    <h3 class="font-bold text-gray-800 mb-6 text-xl">📊 Statistik Imunisasi Wilayah (Data BPS)</h3>
                    <div class="relative h-[450px] w-full">
                        <canvas id="adminChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="tab-pengguna" class="tab-content hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">👥 Manajemen Pengguna</h2>
                    <button class="bg-gradient-to-r from-brand-blue to-brand-sky text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90">+ Tambah Akun</button>
                </div>
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-semibold">ID</th>
                                    <th class="px-6 py-4 font-semibold">Nama Lengkap</th>
                                    <th class="px-6 py-4 font-semibold">Email</th>
                                    <th class="px-6 py-4 font-semibold">Role</th>
                                    <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php 
                                mysqli_data_seek($query_users, 0); 
                                while($row = mysqli_fetch_assoc($query_users)) : 
                                ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-500">#<?= $row['id']; ?></td>
                                    <td class="px-6 py-4 font-bold text-gray-800"><?= $row['nama']; ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= $row['email']; ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase <?= $row['role'] == 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600' ?>">
                                            <?= $row['role']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="edit_data.php?id=<?= $row['id']; ?>" class="p-2 bg-blue-50 text-blue-500 rounded-lg hover:bg-blue-500 hover:text-white transition-all">✏️</a>
                                            <a href="Project/HapusUser.php?id=<?= $row['id']; ?>" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" 
                                                class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all">🗑️</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-jadwal" class="tab-content hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">📅 Semua Jadwal Imunisasi</h2>
                    <input type="text" placeholder="Cari nama pasien atau ID..." class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky w-64" />
                </div>
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-3">Nama Pasien</th>
                                    <th class="px-6 py-3">Jenis Vaksin</th>
                                    <th class="px-6 py-3">Jadwal Suntik</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php while($jadwal = mysqli_fetch_assoc($query_jadwal)) : ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-semibold text-gray-800"><?= htmlspecialchars($jadwal['nama_pasien']); ?> <br><span class="text-xs font-normal text-gray-500">Ortu: <?= htmlspecialchars($jadwal['nama_ortu']); ?></span></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($jadwal['jenis_vaksin']); ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= date('d M Y', strtotime($jadwal['tanggal_jadwal'])); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold 
                                            <?= $jadwal['status'] == 'Selesai' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' ?>">
                                            <?= htmlspecialchars($jadwal['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2 items-center">
                                            <?php if($jadwal['status'] == 'Mendatang') : ?>
                                                <a href="Project/UpdateStatusJadwal.php?id=<?= $jadwal['id']; ?>&status=Selesai" 
                                                    onclick="return confirm('Konfirmasi bahwa imunisasi ini telah selesai dilakukan?')"
                                                    class="text-[10px] bg-green-500 text-white px-2 py-1.5 rounded-lg font-bold hover:bg-green-600 no-underline transition-all">
                                                    ✅ Tandai Selesai
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-vaksin" class="tab-content hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-800">💉 Manajemen Data Vaksin</h2>
                    <button class="bg-gradient-to-r from-brand-blue to-brand-sky text-white px-4 py-2 rounded-xl text-sm font-semibold hover:opacity-90">+ Tambah Vaksin</button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-gray-800">DPT-HB-Hib</h3>
                            <span class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-1 rounded-md">Tersedia</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Vaksin kombinasi untuk mencegah Difteri, Pertusis, Tetanus, Hepatitis B, dan infeksi Hib.</p>
                        <div class="flex gap-2">
                            <button class="flex-1 text-xs py-2 bg-gray-50 text-gray-600 font-semibold rounded-lg hover:bg-gray-100">Edit Data</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tab-laporan" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-6">🆘 Laporan Keluhan Pasien</h2>
                
                <div class="space-y-4">
                    <?php 
                    mysqli_data_seek($query_laporan, 0);
                    if(mysqli_num_rows($query_laporan) > 0) :
                        while($laporan = mysqli_fetch_assoc($query_laporan)) : 
                    ?>
                    <div class="bg-white border-l-4 <?= $laporan['status_laporan'] == 'Belum Ditangani' ? 'border-l-red-500' : 'border-l-green-500' ?> rounded-r-2xl border-t border-r border-b border-gray-100 p-5 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-bold text-gray-800 text-sm">Keluhan: <?= htmlspecialchars($laporan['jenis_gejala']); ?></p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Pelapor: <?= htmlspecialchars($laporan['nama_pelapor']); ?> · Vaksin: <?= htmlspecialchars($laporan['nama_vaksin']); ?>
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                <?= $laporan['status_laporan'] == 'Belum Ditangani' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' ?>">
                                <?= htmlspecialchars($laporan['status_laporan']); ?>
                            </span>
                        </div>
                        <div class="mt-3 p-3 bg-gray-50 rounded-lg text-sm text-gray-600 border border-gray-100">
                            "<?= htmlspecialchars($laporan['detail_laporan']); ?>"
                        </div>
                        <div class="mt-4 flex gap-3">
                            <?php if($laporan['status_laporan'] == 'Belum Ditangani') : ?>
                                <button type="button" onclick="bukaModalTanggapan('<?= $laporan['id']; ?>', '<?= $laporan['user_id']; ?>', '<?= addslashes($laporan['nama_vaksin']); ?>')" class="text-xs bg-gradient-to-r from-brand-blue to-brand-sky text-white px-4 py-2 rounded-lg font-semibold border-0 cursor-pointer hover:opacity-90">Tanggapi Laporan</button>
                            <?php endif; ?>
                            <a href="Project/HapusLaporan.php?id=<?= $laporan['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data laporan ini secara permanen?')" class="text-xs bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 no-underline transition-colors">Hapus/Arsip</a>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    else:
                        echo '<p class="text-sm text-gray-500">Belum ada laporan masuk.</p>';
                    endif;
                    ?>
                </div>
            </div>

            <div id="modalTanggapan" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all scale-95" id="modalTanggapanContent">
                    <div class="bg-gradient-to-r from-brand-blue to-brand-sky p-5 text-white flex justify-between items-center">
                        <h3 class="font-bold text-lg">💬 Tanggapi Keluhan Medis</h3>
                        <button onclick="tutupModalTanggapan()" class="text-white/80 hover:text-white text-2xl border-0 bg-transparent cursor-pointer">&times;</button>
                    </div>
                    <form action="Project/ProsesTanggapan.php" method="POST" class="p-6 space-y-4">
                        <input type="hidden" name="laporan_id" id="inputLaporanId">
                        <input type="hidden" name="user_id" id="inputUserId">
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Vaksin Terkait</label>
                            <input type="text" id="inputVaksinTanggapan" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold text-gray-700 cursor-not-allowed" readonly />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pesan untuk Orang Tua (Akan masuk ke notifikasi)</label>
                            <textarea name="pesan_tanggapan" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-brand-sky outline-none resize-none" rows="4" placeholder="Contoh: Silakan berikan obat penurun panas dan kompres air hangat..." required></textarea>
                        </div>
                        <div class="flex justify-end gap-3 mt-4">
                            <button type="button" onclick="tutupModalTanggapan()" class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold border-0 cursor-pointer">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-brand-blue to-brand-sky text-white rounded-xl text-sm font-semibold hover:opacity-90 shadow-lg shadow-blue-100 border-0 cursor-pointer">Kirim Tanggapan</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

    <script>
        // 1. Logika Tanggal
        const now = new Date();
        const dateEl = document.getElementById('pageDate');
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        }

        // 2. Logika Pindah Tab
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            const targetTab = document.getElementById('tab-' + tab);
            if(targetTab) targetTab.classList.remove('hidden');
            
            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.classList.remove('active');
                if (link.dataset.tab === tab) link.classList.add('active');
            });
            
            // Ganti Judul
            const titles = { 
                beranda: 'Beranda Admin', 
                pengguna: 'Manajemen Pengguna', 
                jadwal: 'Semua Jadwal Imunisasi', 
                vaksin: 'Manajemen Vaksin', 
                laporan: 'Laporan Masuk' 
            };
            
            const titleEl = document.getElementById('pageTitle');
            if(titleEl) titleEl.textContent = titles[tab] || 'Beranda Admin';
        }

        // 3. Trigger Klik Menu Sidebar
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', e => { 
                e.preventDefault(); 
                switchTab(link.dataset.tab); 
            });
        });

        // 4. Logika Dropdown Profil
        const profileBtn  = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        if(profileBtn && profileMenu) {
            profileBtn.addEventListener('click', e => { 
                e.stopPropagation(); 
                profileMenu.classList.toggle('open'); 
            });
            document.addEventListener('click', () => profileMenu.classList.remove('open'));
        }

        // ====== FUNGSI MODAL TANGGAPAN ADMIN ======
        function bukaModalTanggapan(laporanId, userId, namaVaksin) {
            document.getElementById('inputLaporanId').value = laporanId;
            document.getElementById('inputUserId').value = userId;
            document.getElementById('inputVaksinTanggapan').value = namaVaksin;
            
            const modal = document.getElementById('modalTanggapan');
            const modalContent = document.getElementById('modalTanggapanContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => { 
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }

        function tutupModalTanggapan() {
            const modal = document.getElementById('modalTanggapan');
            const modalContent = document.getElementById('modalTanggapanContent');
            
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => { 
                modal.classList.add('hidden'); 
            }, 200);
        }

        // Ganti bagian fetch BPS dengan kode sakti ini
        fetch('Server/api_bps_admin.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === "OK") {
                    // 1. Ambil tahun sebagai label (BPS simpan di vervar)
                    const labels = data.vervar.map(item => item.label);
                    
                    // 2. Ambil nilai data asli dari datacontent
                    // Data BPS model 'list' biasanya berbentuk object dengan key string ID
                    // Kita ambil semua value-nya dan bersihkan jika ada karakter non-angka
                    const values = Object.values(data.datacontent).map(val => parseFloat(val) || 0);

                    // 3. Render Grafik
                    const ctx = document.getElementById('adminChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Persentase Imunisasi Dasar Lengkap (%)',
                                data: values,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: '#2563eb',
                                pointRadius: 5,
                                fill: true,
                                tension: 0.3
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
                } else {
                    throw new Error("Data BPS tidak valid");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const container = document.getElementById('adminChart').parentElement;
                container.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-red-500 italic">
                                        <span>⚠️ Gagal memuat data asli BPS</span>
                                        <span class="text-[10px]">Periksa API Key atau koneksi internet Anda</span>
                                    </div>`;
            });
    </script>
</body>
</html>