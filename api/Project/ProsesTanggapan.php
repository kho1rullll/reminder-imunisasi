<?php
session_start();
require __DIR__ . '/../Server/koneksi.php';

// Proteksi akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $laporan_id      = $_POST['laporan_id'];
    $user_id         = $_POST['user_id']; // ID orang tua yang melaporkan
    $pesan_tanggapan = htmlspecialchars($_POST['pesan_tanggapan']);
    $nama_admin      = $_SESSION['nama'];
    
    // 1. Update status laporan menjadi 'Selesai'
    $update_laporan = mysqli_query($koneksi, "UPDATE laporan_keluhan SET status_laporan = 'Selesai' WHERE id = '$laporan_id'");
    
    // 2. Tembakkan notifikasi ke tabel notifikasi user tersebut
    $judul_notif = "Tanggapan Keluhan Medis";
    $pesan_notif = "Dokter/Admin ($nama_admin) merespon keluhan Anda: \"" . $pesan_tanggapan . "\"";
    $tipe_notif  = "Informasi"; // Menggunakan ENUM Informasi yang sudah dibuat sebelumnya
    
    $insert_notif = mysqli_query($koneksi, "INSERT INTO notifikasi (user_id, judul, pesan, tipe) VALUES ('$user_id', '$judul_notif', '$pesan_notif', '$tipe_notif')");
    
    if ($update_laporan && $insert_notif) {
        echo "<script>
                alert('Tanggapan berhasil dikirim! Notifikasi otomatis diteruskan ke akun pengguna.');
                window.location.href='../dashboard_admin.php';
            </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>