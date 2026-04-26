<?php
session_start();
require __DIR__ . '/../Server/koneksi.php';

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

// Proteksi akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Hapus data dari tabel
    $query = "DELETE FROM laporan_keluhan WHERE id = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../dashboard_admin.php");
        exit();
    } else {
        echo "Gagal menghapus laporan: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../dashboard_admin.php");
}
?>