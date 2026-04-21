<?php
    session_start();
    require '../Server/koneksi.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($koneksi, $query);

        // Cek apakah email ditemukan
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            // Verifikasi password
            if (password_verify($password, $row['password'])) {
                // Set Session
                $_SESSION['id'] = $row['id'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['email'] = $row['email']; // Tambahan untuk halaman Profil
                $_SESSION['role'] = $row['role'];

                // Arahkan berdasarkan Role
                if ($row['role'] == 'admin') {
                    header("Location: ../dashboard_admin.php");
                } else {
                    header("Location: ../dashboard.php");
                }
                exit();
            } else {
                $_SESSION['error'] = "Password yang Anda masukkan salah!";
                header("Location: ../login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Email tidak ditemukan!";
            header("Location: ../login.php");
            exit();
        }
    }
?>