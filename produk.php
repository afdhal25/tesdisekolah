<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Ambil data produk dari database
$sql = "SELECT * FROM produk";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Daftar Produk</h2>

    <!-- Tombol Tambah Produk -->
    <a href="tambah_produk.php" class="btn btn-primary mb-3">Tambah Produk</a>

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
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['ProdukID']; ?></td>
                    <td><?= $row['NamaProduk']; ?></td>
                    <td>Rp<?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                    <td><?= $row['Stok']; ?></td>
                    <td>
                        <a href="edit_produk.php?id=<?= $row['ProdukID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus_produk.php?id=<?= $row['ProdukID']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');" class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>