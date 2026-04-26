<?php
session_start();
require __DIR__ . '/../Server/koneksi.php';

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