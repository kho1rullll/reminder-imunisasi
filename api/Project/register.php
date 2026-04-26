<?php
session_start();

// Panggil koneksi database (mundur 1 folder, lalu masuk ke Server)
require __DIR__ . '/../Server/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menangkap inputan dari form
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Cek apakah email sudah terdaftar di database
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan! Silakan gunakan email lain.";
        // Ingat, kembalinya ke register.php yang ada di luar (root)
        header("Location: ../register.php");
        exit();
    }

    // Hashing Password demi keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ==========================================
    // LOGIKA PENENTUAN ROLE (Semi-Otomatis)
    // ==========================================
    $role = 'user'; // Secara default, semua yang daftar adalah user biasa

    // Jika email yang didaftarkan mengandung domain kampus, jadikan admin
    if (strpos($email, '@admin.imunisasiku.ac.id') !== false) {
        $role = 'admin';
    }

    // Insert data dengan role yang sudah divalidasi di atas
    $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan masuk dengan akun Anda.";
        header("Location: ../login.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan sistem. Silakan coba lagi.";
        header("Location: ../register.php");
    }
}
?>