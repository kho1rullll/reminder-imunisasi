<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
require __DIR__ . '/../Server/koneksi.php';
// 2. LOGIKA RESTORE SESSION DARI COOKIE (Sangat Penting untuk Vercel)
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

// Pastikan hanya admin yang bisa melakukan aksi ini
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = htmlspecialchars($_POST['nama']);
    $email    = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $role     = htmlspecialchars($_POST['role']);

    // 1. Cek apakah email sudah terdaftar sebelumnya
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan oleh pengguna lain!";
        header("Location: ../tambah_user.php");
        exit();
    }

    // 2. Enkripsi Password (SANGAT PENTING agar bisa login)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Simpan ke Database
    $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";

    if (mysqli_query($koneksi, $query)) {
        // Jika sukses, kembalikan ke dashboard admin
        echo "<script>
                alert('Akun berhasil ditambahkan!');
                window.location.href='../dashboard_admin.php';
            </script>";
        exit();
    } else {
        // Jika gagal karena masalah query
        $_SESSION['error'] = "Terjadi kesalahan: " . mysqli_error($koneksi);
        header("Location: ../tambah_user.php");
        exit();
    }
} else {
    header("Location: ../dashboard_admin.php");
    exit();
}
?>