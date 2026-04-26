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

// Proteksi: Hanya Admin yang bisa merubah status
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status_baru = $_GET['status'];

    // Update status di database
    $query = "UPDATE jadwal_imunisasi SET status = '$status_baru' WHERE id = '$id'";

    if (mysqli_query($koneksi, $query)) {
        // Berhasil, kembali ke dashboard admin
        header("Location: ../dashboard_admin.php");
        exit();
    } else {
        echo "Gagal memperbarui status: " . mysqli_error($koneksi);
    }
} else {
    header("Location: ../dashboard_admin.php");
    exit();
}