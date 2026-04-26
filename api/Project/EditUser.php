<?php
session_start();
require __DIR__ . '/../Server/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id    = $_POST['id'];
    $nama  = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $role  = $_POST['role'];

    // Update data di tabel users
    $query = "UPDATE users SET nama='$nama', email='$email', role='$role' WHERE id='$id'";

    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, balik ke dashboard_admin
        header("Location: ../dashboard_admin.php");
        exit();
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>