<?php
session_start();

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

// Proteksi Halaman: Pastikan hanya User biasa yang bisa masuk
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
    <title>Tambah Jadwal Vaksin – ImunisasiKu</title>
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
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    <div class="absolute top-[-100px] right-[-100px] w-96 h-96 bg-blue-200/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-[-100px] left-[-100px] w-80 h-80 bg-sky-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl overflow-hidden relative z-10 border border-gray-100">
        <div class="bg-gradient-to-r from-brand-deep via-brand-blue to-brand-sky p-8 text-white text-center relative">
            <a href="dashboard.php" class="absolute left-6 top-8 text-white/80 hover:text-white transition-colors">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <div class="w-14 h-14 bg-white/20 border-2 border-white/30 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3 backdrop-blur-sm">📅</div>
            <h2 class="text-2xl font-bold">Tambah Jadwal</h2>
            <p class="text-white/75 text-sm mt-1">Buat pengingat jadwal imunisasi anak</p>
        </div>

        <form action="Project/TambahJadwal.php" method="POST" class="p-8 space-y-5">
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-50 text-red-600 border border-red-100 text-sm px-4 py-3 rounded-xl">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Anak (Penerima Vaksin)</label>
                <input type="text" name="nama_pasien" placeholder="Contoh: Dina" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky focus:ring-2 focus:ring-sky-100 transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Vaksin</label>
                <select name="jenis_vaksin" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky focus:ring-2 focus:ring-sky-100 transition-all cursor-pointer" required>
                    <option value="">-- Pilih Jenis Vaksin --</option>
                    <option value="Hepatitis B (HB-0)">Hepatitis B (HB-0)</option>
                    <option value="BCG">BCG</option>
                    <option value="Polio 1">Polio 1</option>
                    <option value="DPT-HB-Hib 1">DPT-HB-Hib 1</option>
                    <option value="Polio 2">Polio 2</option>
                    <option value="Campak/Rubella (MR)">Campak/Rubella (MR)</option>
                    <option value="Vaksin MMR">Vaksin MMR</option>
                    <option value="Vaksin Influenza">Vaksin Influenza</option>
                    <option value="Lainnya">Lainnya...</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Jadwal</label>
                <input type="date" name="tanggal_jadwal" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-brand-sky focus:ring-2 focus:ring-sky-100 transition-all cursor-pointer" required>
                <p class="text-xs text-gray-400 mt-1.5 ml-1">Kamu akan mendapatkan notifikasi sebelum tanggal ini.</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-brand-blue to-brand-sky text-white font-semibold rounded-xl text-sm shadow-lg shadow-blue-200 hover:opacity-90 hover:-translate-y-0.5 transition-all">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

</body>
</html>