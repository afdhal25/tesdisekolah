<?php
session_start();
include 'koneksi.php';

// Cek jika pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Ambil data produk dan penjualan dari database
$sql_produk = "SELECT * FROM produk";
$result_produk = $conn->query($sql_produk);

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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <h3>Dashboard</h3>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="penjualan.php">Penjualan</a></li>
                <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                <li class="nav-item"><a class="nav-link" href="pelanggan.php">Pelanggan</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 main-content">
            <h2>Selamat Datang, <?= $_SESSION['NamaUser']; ?></h2>
            
            <!-- Tabel Produk -->
            <h3 class="mt-4">Daftar Produk</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_produk->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['ProdukID']; ?></td>
                            <td><?= $row['NamaProduk']; ?></td>
                            <td>Rp<?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                            <td><?= $row['Stok']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Tabel Penjualan -->
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
                    <?php while ($row = $result_penjualan->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $row['PenjualanID']; ?></td>
                            <td><?= $row['TanggalPenjualan']; ?></td>
                            <td>Rp<?= number_format($row['TotalHarga'], 0, ',', '.'); ?></td>
                            <td><?= $row['PelangganID']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
