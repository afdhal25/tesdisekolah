<?php
session_start();

// Mengecek apakah pengguna memiliki role dalam session
if (isset($_SESSION['role'])) {
    // Dapatkan role pengguna
    $role = $_SESSION['role'];

    // Jika pengguna adalah admin atau petugas, proses logout
    if ($role == 'admin' || $role == 'petugas') {
        $_SESSION = []; // Kosongkan array session
        session_unset(); // Hapus semua variabel sesi
        session_destroy(); // Hapus sesi

        // Hapus cookie sesi jika ada
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Redirect ke halaman login
        header('Location: login.php');
        exit;
    } else {
        // Jika role tidak valid, redirect ke halaman login
        header('Location: login.php');
        exit;
    }
} else {
    // Jika session tidak ada atau role tidak ditemukan, redirect ke login
    header('Location: login.php');
    exit;
}
?>