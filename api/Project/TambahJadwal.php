<?php
session_start();

// Mundur 1 folder untuk memanggil koneksi
require '../../Server/koneksi.php'; 

// Pastikan hanya user yang bisa melakukan aksi ini
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $user_id        = $_SESSION['id']; // Diambil dari session, bukan diinput manual
    $nama_pasien    = htmlspecialchars($_POST['nama_pasien']);
    $jenis_vaksin   = htmlspecialchars($_POST['jenis_vaksin']);
    $tanggal_jadwal = $_POST['tanggal_jadwal'];
    
    // Status default untuk jadwal baru pasti 'Mendatang'
    $status = 'Mendatang';

    // Validasi sederhana (opsional, tapi bagus buat keamanan)
    if(empty($nama_pasien) || empty($jenis_vaksin) || empty($tanggal_jadwal)) {
        $_SESSION['error'] = "Semua kolom wajib diisi!";
        header("Location: ../tambah_jadwal.php");
        exit();
    }

    // Query untuk menyimpan ke tabel jadwal_imunisasi
    $query = "INSERT INTO jadwal_imunisasi (user_id, nama_pasien, jenis_vaksin, tanggal_jadwal, status) 
            VALUES ('$user_id', '$nama_pasien', '$jenis_vaksin', '$tanggal_jadwal', '$status')";

    if (mysqli_query($koneksi, $query)) {
        // Jika sukses, lempar balik ke dashboard
        header("Location: ../dashboard.php");
        exit();
    } else {
        // Jika gagal karena masalah database
        $_SESSION['error'] = "Gagal menyimpan jadwal: " . mysqli_error($koneksi);
        header("Location: ../tambah_jadwal.php");
        exit();
    }
} else {
    // Jika ada yang mencoba akses file ini langsung dari URL
    header("Location: ../dashboard.php");
    exit();
}
?>