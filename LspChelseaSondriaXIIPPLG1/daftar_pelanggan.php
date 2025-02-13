<?php
session_start();
include 'koneksi.php';

// Ambil data pelanggan dari database
$sql_pelanggan = "SELECT * FROM pelanggan";
$result_pelanggan = $conn->query($sql_pelanggan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Daftar Pelanggan</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Pelanggan ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Alamat</th>
                    <th>Nomor Telepon</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_pelanggan->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['PelangganID']; ?></td>
                        <td><?= $row['NamaPelanggan']; ?></td>
                        <td><?= $row['Alamat']; ?></td>
                        <td><?= $row['NomorTelepon']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="tambah_pelanggan.php" class="btn btn-primary">Tambah Pelanggan</a>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
</body>
</html>
