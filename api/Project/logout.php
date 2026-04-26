<?php
    session_start();
    session_destroy(); // Menghapus session persis seperti kode dosenmu
    setcookie("login_email", "", time() - 3600, "/");
    setcookie("login_role", "", time() - 3600, "/");
    header("Location: ../../index.html");
    exit();
?>