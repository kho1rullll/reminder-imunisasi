<?php
session_start();

// 1. Panggil koneksi database (mundur 1 folder, lalu masuk ke Server)
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

// 2. Proteksi Halaman: Pastikan yang melakukan aksi ini benar-benar Admin
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// 3. Menangkap 'id' user yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id_target = $_GET['id'];
    $id_admin_sekarang = $_SESSION['id'];

    // (Opsional/Keamanan) Mencegah Admin menghapus akunnya sendiri secara tidak sengaja
    if ($id_target == $id_admin_sekarang) {
        echo "<script>
                alert('Anda tidak dapat menghapus akun Anda sendiri!');
                window.location.href='../dashboard_admin.php';
                </script>";
        exit();
    }

    // 4. Query untuk menghapus data berdasarkan ID
    $query = "DELETE FROM users WHERE id = '$id_target'";

    // 5. Eksekusi query dan kembali ke dashboard_admin
    if (mysqli_query($koneksi, $query)) {
        header("Location: ../dashboard_admin.php");
        exit();
    } else {
        // Jika gagal, tampilkan pesan error
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    // Jika tidak ada ID yang dikirim, kembalikan ke dashboard
    header("Location: ../dashboard_admin.php");
    exit();
}
?>