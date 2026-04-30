<?php
session_start();
require '../Server/koneksi.php';

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