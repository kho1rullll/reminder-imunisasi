<?php
session_start();
require '../../Server/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id        = $_POST['user_id'];
    $riwayat_id     = $_POST['riwayat_id'];
    $nama_vaksin    = htmlspecialchars($_POST['nama_vaksin']);
    $jenis_gejala   = htmlspecialchars($_POST['jenis_gejala']);
    $detail_laporan = htmlspecialchars($_POST['detail_laporan']);

    $query = "INSERT INTO laporan_keluhan (user_id, riwayat_id, nama_vaksin, jenis_gejala, detail_laporan, status_laporan) 
            VALUES ('$user_id', '$riwayat_id', '$nama_vaksin', '$jenis_gejala', '$detail_laporan', 'Belum Ditangani')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>
                alert('Laporan berhasil dikirim. Admin akan segera meninjau keluhan Anda.');
                window.location.href='../dashboard.php';
            </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>