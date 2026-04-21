<?php
    session_start();
    session_destroy(); // Menghapus session persis seperti kode dosenmu
    header("Location: ../index.php");
    exit();
?>