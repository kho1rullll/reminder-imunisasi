<?php
session_start();
require 'Server/koneksi.php';

// Proteksi: Hanya Admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit();
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$id'");
$user_data = mysqli_fetch_assoc($query);

if (!$user_data) {
    die("Data tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna – ImunisasiKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-sky-500 p-6 text-white text-center">
            <h2 class="text-xl font-bold">Edit Informasi Pengguna</h2>
            <p class="text-white/70 text-sm mt-1">Ubah data untuk ID #<?= $user_data['id']; ?></p>
        </div>

        <form action="Project/EditUser.php" method="POST" class="p-8 space-y-5">
            <input type="hidden" name="id" value="<?= $user_data['id']; ?>">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $user_data['nama']; ?>" 
                    class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-blue-500 outline-none transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                <input type="email" name="email" value="<?= $user_data['email']; ?>" 
                    class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-blue-500 outline-none transition-all" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Role / Hak Akses</label>
                <select name="role" class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-blue-500 outline-none transition-all">
                    <option value="user" <?= $user_data['role'] == 'user' ? 'selected' : ''; ?>>User (Orang Tua)</option>
                    <option value="admin" <?= $user_data['role'] == 'admin' ? 'selected' : ''; ?>>Admin (Petugas)</option>
                </select>
            </div>

            <div class="pt-2 flex gap-3">
                <a href="dashboard_admin.php" class="flex-1 text-center py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition-all">Batal</a>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-semibold text-sm shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">Update Data</button>
            </div>
        </form>
    </div>

</body>
</html>