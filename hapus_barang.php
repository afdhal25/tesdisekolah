<?php
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID barang tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

$id_barang = intval($_GET['id']); // Hindari SQL Injection

// Hapus data barang
$sql = "DELETE FROM barang WHERE id_barang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_barang);

if ($stmt->execute()) {
    echo "<script>alert('Barang berhasil dihapus!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus barang!'); window.location='dashboard.php';</script>";
}

$stmt->close();
$conn->close();
?>