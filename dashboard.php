<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Ambil data produk
$sql_produk = "SELECT * FROM produk";
$result_produk = $conn->query($sql_produk);

// Ambil data penjualan
$sql_penjualan = "SELECT PenjualanID, TanggalPenjualan, TotalHarga, PelangganID FROM penjualan";
$result_penjualan = $conn->query($sql_penjualan);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="container">
    <h2>Dashboard Kasir</h2>
    
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="tambah_pelanggan.php">Pelanggan</a></li>
            <li><a href="penjualan.php">Penjualan</a></li>
            <li><a href="tambah_pelanggan.php">Tambah Pelanggan</a></li>
            <li><a href="daftar_pelanggan.php">Daftar Pelanggan</a></li>
            <!-- Menu User untuk Registrasi -->
            <li><a href="registrasi.php">User (Registrasi)</a></li>
            <li><a href="logout.php" class="text-danger">Logout</a></li>
        </ul>
    </nav>

    <!-- ====== Tabel Produk ====== -->
    <h3 class="mt-4">Daftar Produk</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_produk->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['ProdukID']; ?></td>
                    <td><?= $row['NamaProduk']; ?></td>
                    <td>Rp<?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                    <td><?= $row['Stok']; ?></td>
                    <td>
                        <a href="edit_produk.php?id=<?= $row['ProdukID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus_produk.php?id=<?= $row['ProdukID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="container mt-4">
        <h3>Tambah Produk</h3>
        <?php 
        if (isset($_SESSION['success'])) {
            echo "<p style='color: green;'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }
        
        if (isset($_SESSION['error'])) {
            echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>
        <form action="tambah_produk.php" method="POST">
            <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk:</label>
                <input type="text" name="nama_produk" id="nama_produk" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga:</label>
                <input type="number" name="harga" id="harga" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok:</label>
                <input type="number" name="stok" id="stok" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Produk</button>
        </form>
    </div>  

    <h3 class="mt-4">Daftar Penjualan</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Penjualan</th>
                <th>Tanggal Penjualan</th>
                <th>Total Harga</th>
                <th>ID Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($result_penjualan->num_rows > 0) {
                while ($row = $result_penjualan->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['PenjualanID']; ?></td>
                        <td><?= $row['TanggalPenjualan']; ?></td>
                        <td>Rp<?= number_format($row['TotalHarga'], 0, ',', '.'); ?></td>
                        <td><?= $row['PelangganID']; ?></td>
                    </tr>
                <?php } 
            } else { ?>
                <tr><td colspan="4">Tidak ada data penjualan.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
