<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login – ImunisasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="icon" type="image" href="images/Favicon.png">
    <script>
        tailwind.config = {
        theme: {
            extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] }, colors: { 'brand-blue': '#2563eb', 'brand-sky': '#0ea5e9', 'brand-deep': '#1a3a8f', } }
        }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .input-field:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.18); }
        .btn-gradient { background: linear-gradient(90deg, #2563eb, #0ea5e9); }
        .btn-gradient:hover { background: linear-gradient(90deg, #1d4ed8, #0284c7); }
        .left-panel { background: linear-gradient(165deg, #0f2878 0%, #2563eb 55%, #0ea5e9 100%); }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-sky-50 to-white flex items-center justify-center p-4">

    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex min-h-[560px]">

        <div class="left-panel hidden md:flex flex-col justify-between w-5/12 p-10 relative overflow-hidden text-white">
            <div class="absolute top-[-80px] right-[-80px] w-72 h-72 rounded-full bg-white/8"></div>
            <div class="absolute bottom-[-60px] left-[-60px] w-56 h-56 rounded-full bg-white/6"></div>
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-11 h-11 bg-white/20 border border-white/30 rounded-2xl flex items-center justify-center text-2xl backdrop-blur">💉</div>
                    <div>
                        <div class="font-bold text-lg leading-tight">ImunisasiKu</div>
                        <div class="text-xs text-white/55 uppercase tracking-wider">Reminder System</div>
                    </div>
                </div>
                <h2 class="font-bold text-3xl leading-snug mb-4">Selamat<br>Datang Kembali!</h2>
                <p class="text-white/75 text-sm leading-relaxed">Masuk untuk mengakses jadwal vaksin dan riwayat imunisasi keluarga Anda.</p>
            </div>
        </div>

        <div class="flex-1 flex flex-col justify-center p-8 md:p-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-1">Masuk ke Akun</h3>
            <p class="text-sm text-gray-500 mb-7">Belum punya akun? <a href="register.php" class="text-brand-sky font-semibold hover:underline">Daftar sekarang</a></p>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-50 text-red-600 border border-red-200 text-sm px-4 py-3 rounded-xl mb-5">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-50 text-green-600 border border-green-200 text-sm px-4 py-3 rounded-xl mb-5">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="Project/login.php" method="POST">
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@contoh.com" class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white" required />
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" class="input-field w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-xl text-sm bg-gray-50 focus:bg-white" required />
                        <span class="cursor-pointer absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-lg" id="eyeToggle">👁️</span>
                    </div>
                </div>

                <button type="submit" class="btn-gradient w-full py-3.5 mt-2 rounded-xl text-white font-semibold text-sm tracking-wide shadow-lg shadow-blue-200 transition-all duration-300 hover:shadow-xl hover:-translate-y-0.5">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    <script src="JS/login.js"></script>
</body>
</html>