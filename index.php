<?php
session_start();

// Validasi: Jika session user_id sudah ada, langsung lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    // Jika tidak ada session, langsung redirect ke halaman login
    header("Location: login.php");
    exit();
}
?>