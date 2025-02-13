<?php
session_start();

// Memastikan bahwa role sudah diset dan sesuai (case-insensitive)
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header('Location: login.php'); // Arahkan jika bukan petugas
    exit;
}

echo "<h1>Dashboard Admin</h1>";
echo "<p>Selamat datang, Petugas! Anda hanya dapat melihat informasi yang Anda perlukan di sini.</p>";
?>
