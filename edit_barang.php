<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi sudah ada

if (!isset($_GET['id'])) {
    echo "<script>alert('ID barang tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

$id_barang = intval($_GET['id']); // Hindari SQL Injection

// Ambil data barang berdasarkan ID
$sql = "SELECT * FROM barang WHERE id_barang = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$result = $stmt->get_result();
$barang = $result->fetch_assoc();

if (!$barang) {
    echo "<script>alert('Barang tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang']);
    $harga = intval($_POST['harga']);
    $stok = intval($_POST['stok']);

    $sql = "UPDATE barang SET nama_barang = ?, harga = ?, stok = ? WHERE id_barang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $nama_barang, $harga, $stok, $id_barang);

    if ($stmt->execute()) {
        echo "<script>alert('Barang berhasil diperbarui!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui barang!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Barang</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Barang</h2>
    <form method="POST">
        <label>Nama Barang:</label>
        <input type="text" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']); ?>" required>
        <br>
        <label>Harga:</label>
        <input type="text" name="harga" value="<?= htmlspecialchars($barang['harga']); ?>" required>
        <br>
        <label>Stok:</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($barang['stok']); ?>" required>
        <br>
        <button type="submit">Simpan</button>
    </form>
    <br>
    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>
</html>
