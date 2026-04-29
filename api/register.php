<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register – ImunisasiKu</title>
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
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .input-field { outline: none; transition: all 0.2s; }
        .input-field:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.18); }
        .btn-gradient { background: linear-gradient(90deg, #2563eb, #0ea5e9); }
        .btn-gradient:hover { background: linear-gradient(90deg, #1d4ed8, #0284c7); }
        .left-panel { background: linear-gradient(165deg, #0f2878 0%, #2563eb 55%, #0ea5e9 100%); }
        .strength-bar { transition: width 0.4s ease; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-sky-50 to-white flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex min-h-[600px]">

        <div class="left-panel hidden md:flex flex-col justify-between w-5/12 p-10 relative overflow-hidden text-white">
            <div class="absolute top-[-80px] right-[-80px] w-72 h-72 rounded-full bg-white/8"></div>
            <div class="absolute bottom-[-60px] left-[-60px] w-56 h-56 rounded-full bg-white/6"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 p-6 pb-5 border-b border-white/12 relative z-10">
                    <div class="w-11 h-11 bg-white` /20 border border-white/30 rounded-2xl flex items-center justify-center backdrop-blur flex-shrink-0 overflow-hidden">
                        <img src="../../images/Favicon.png" alt="Logo ImunisasiKu" class="w-full h-full object-contain p-1.5">
                    </div>
                    <div>
                        <div class="font-bold text-lg leading-tight">ImunisasiKu</div>
                        <div class="text-xs text-white/55 uppercase tracking-wider">Reminder System</div>
                    </div>
                </div>
                <h2 class="font-bold text-3xl leading-snug mb-4">Bergabung<br>Bersama Kami!</h2>
                <p class="text-white/75 text-sm leading-relaxed">Daftar sekarang dan mulai lindungi keluarga Anda dengan jadwal vaksin yang teratur.</p>
            </div>
            <div class="relative z-10 space-y-3">
                <div class="flex items-center gap-3"><div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center text-base">✅</div><span class="text-sm text-white/85">Notifikasi otomatis jadwal vaksin</span></div>
                <div class="flex items-center gap-3"><div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center text-base">📋</div><span class="text-sm text-white/85">Riwayat imunisasi digital</span></div>
            </div>
        </div>

        <div class="flex-1 flex flex-col justify-center p-8 md:p-12 overflow-y-auto">
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Buat Akun Baru</h3>
            <p class="text-sm text-gray-500 mb-7">Sudah punya akun? <a href="login.php" class="text-brand-sky font-semibold hover:underline">Masuk di sini</a></p>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-50 text-red-600 border border-red-200 text-sm px-4 py-3 rounded-xl mb-5">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form action="Project/register.php" method="POST">
                <div class="mb-4">
                    <label for="nama" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" placeholder="Budi Santoso" class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white" required />
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@contoh.com" class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white" required />
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Minimal 8 karakter" class="input-field w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white" required />
                        <span class="cursor-pointer absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg" id="eyeReg">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn-gradient w-full py-3.5 rounded-xl text-white font-semibold text-sm tracking-wide shadow-lg shadow-blue-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                    Daftar Sekarang
                </button>
            </form>
        </div>
    </div>

    <script src="JS/register.js"></script>
</body>
</html>