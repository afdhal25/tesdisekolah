<?php
session_start();
include 'koneksi.php';

// Aktifkan error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan UserID ada di sesi
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Proses jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan data yang dibutuhkan ada
    if (!empty($_POST['PenjualanID']) && !empty($_POST['ProdukID']) && !empty($_POST['JumlahProduk'])) {
        $penjualan_id = $_POST['PenjualanID'];
        $produk_id = $_POST['ProdukID'];
        $jumlah_produk = $_POST['JumlahProduk'];

        // Cek apakah PenjualanID ada di tabel penjualan
        $cek_penjualan = $conn->query("SELECT * FROM penjualan WHERE PenjualanID = '$penjualan_id'");
        if ($cek_penjualan && $cek_penjualan->num_rows > 0) {
            // Cek apakah produk ada dan stok cukup
            $cek_stok = $conn->query("SELECT Stok, Harga FROM produk WHERE ProdukID = '$produk_id'");
            if ($cek_stok && $cek_stok->num_rows > 0) {
                $produk = $cek_stok->fetch_assoc();
                $stok = $produk['Stok'];
                $harga_produk = $produk['Harga'];

                // Cek apakah stok mencukupi
                if ($stok >= $jumlah_produk) {
                    // Kurangi stok
                    $new_stok = $stok - $jumlah_produk;
                    $update_stok = $conn->query("UPDATE produk SET Stok = '$new_stok' WHERE ProdukID = '$produk_id'");

                    if ($update_stok) {
                        // Insert ke detail penjualan
                        $subtotal = $jumlah_produk * $harga_produk;
                        $sql = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal, UserID) 
                                VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iiiii", $penjualan_id, $produk_id, $jumlah_produk, $subtotal, $_SESSION['UserID']);
                        if ($stmt->execute()) {
                            $_SESSION['success'] = "Detail penjualan berhasil ditambahkan!";
                        } else {
                            $_SESSION['error'] = "Gagal menambahkan detail penjualan.";
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['error'] = "Gagal mengupdate stok produk.";
                    }
                } else {
                    $_SESSION['error'] = "Stok tidak mencukupi!";
                }
            } else {
                $_SESSION['error'] = "Produk tidak ditemukan!";
            }
        } else {
            $_SESSION['error'] = "PenjualanID tidak ditemukan di tabel penjualan!";
        }
    } else {
        $_SESSION['error'] = "Data tidak lengkap!";
    }
}

// Ambil data detail penjualan berdasarkan PenjualanID
if (isset($_GET['PenjualanID'])) {
    $penjualan_id = $_GET['PenjualanID'];
    $sql = "SELECT * FROM detailpenjualan WHERE PenjualanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $penjualan_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Detail Penjualan</h2>

    <!-- Tampilkan Pesan -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php } elseif (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php } ?>

    <!-- Form Input Detail Penjualan -->
    <form action="" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="PenjualanID" class="form-label">ID Penjualan</label>
            <input type="text" name="PenjualanID" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ProdukID" class="form-label">Pilih Produk</label>
            <select name="ProdukID" class="form-control" required>
                <option value="">-- Pilih Produk --</option>
                <?php
                $produk_result = $conn->query("SELECT ProdukID, NamaProduk FROM produk");
                while ($row = $produk_result->fetch_assoc()) {
                    echo "<option value='{$row['ProdukID']}'>{$row['NamaProduk']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="JumlahProduk" class="form-label">Jumlah Produk</label>
            <input type="number" name="JumlahProduk" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Detail Penjualan</button>
    </form>

    <!-- Tabel Data Detail Penjualan -->
    <?php if ($result && $result->num_rows > 0) { ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Penjualan</th>
                    <th>ID Produk</th>
                    <th>Jumlah Produk</th>
                    <th>Subtotal</th>
                    <th>User ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['PenjualanID']; ?></td>
                        <td><?= $row['ProdukID']; ?></td>
                        <td><?= $row['JumlahProduk']; ?></td>
                        <td>Rp<?= number_format($row['Subtotal'], 0, ',', '.'); ?></td>
                        <td><?= $row['UserID']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-center">Tidak ada detail penjualan untuk ID penjualan ini.</p>
    <?php } ?>

</div>

</body>
</html>
