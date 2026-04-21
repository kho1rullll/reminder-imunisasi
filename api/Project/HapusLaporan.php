<?php
session_start();
require '../Server/koneksi.php';

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