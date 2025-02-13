<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Ambil daftar pelanggan untuk dropdown
$pelanggan_result = $conn->query("SELECT PelangganID, NamaPelanggan FROM pelanggan");

// Proses tambah penjualan jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form
    $tanggal_penjualan = $_POST['TanggalPenjualan'];
    $total_harga = (int) $_POST['TotalHarga'];
    $pelanggan_id = trim($_POST['PelangganID']);
    $produk_id = $_POST['ProdukID']; // Produk yang dibeli
    $jumlah_produk = $_POST['JumlahProduk']; // Jumlah produk yang dibeli

    // Cek apakah PelangganID ada di tabel pelanggan
    $cek_pelanggan = $conn->query("SELECT * FROM pelanggan WHERE PelangganID = '$pelanggan_id'");
    if ($cek_pelanggan->num_rows > 0) {
        // Cek stok produk
        $cek_stok = $conn->query("SELECT Stok, Harga FROM produk WHERE ProdukID = '$produk_id'");
        $produk = $cek_stok->fetch_assoc();
        
        if ($produk) {
            $stok = $produk['Stok'];
            $harga_produk = $produk['Harga'];

            if ($stok >= $jumlah_produk) {
                // Kurangi stok produk
                $new_stok = $stok - $jumlah_produk;
                $update_stok = $conn->query("UPDATE produk SET Stok = '$new_stok' WHERE ProdukID = '$produk_id'");

                if ($update_stok) {
                    // Query untuk menambahkan data penjualan ke database
                    $sql = "INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID) 
                            VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sii", $tanggal_penjualan, $total_harga, $pelanggan_id);

                    if ($stmt->execute()) {
                        // Ambil PenjualanID yang baru saja di-generate
                        $penjualan_id = $stmt->insert_id;

                        $_SESSION['success'] = "Penjualan berhasil ditambahkan!";

                        // Simpan detail penjualan (produk yang terjual)
                        $detail_sql = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal, UserID) 
                                       VALUES (?, ?, ?, ?, ?)";
                        $stmt_detail = $conn->prepare($detail_sql);
                        $subtotal = $jumlah_produk * $harga_produk; // Harga produk dikalikan jumlah
                        $stmt_detail->bind_param("iiiii", $penjualan_id, $produk_id, $jumlah_produk, $subtotal, $_SESSION['UserID']);
                        $stmt_detail->execute();
                        $stmt_detail->close();
                    } else {
                        $_SESSION['error'] = "Gagal menambahkan penjualan.";
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
        $_SESSION['error'] = "Pelanggan dengan ID tersebut tidak ditemukan!";
    }
}

// Ambil data penjualan dari database
$sql = "SELECT PenjualanID, TanggalPenjualan, TotalHarga, PelangganID FROM penjualan";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Data Penjualan</h2>

    <!-- Tampilkan Pesan -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php } elseif (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php } ?>

    <!-- Form Tambah Penjualan -->
    <form action="" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="TanggalPenjualan" class="form-label">Tanggal Penjualan</label>
            <input type="date" name="TanggalPenjualan" id="TanggalPenjualan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="TotalHarga" class="form-label">Total Harga</label>
            <input type="number" name="TotalHarga" id="TotalHarga" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="PelangganID" class="form-label">Pilih Pelanggan</label>
            <select name="PelangganID" id="PelangganID" class="form-control" required>
                <option value="">-- Pilih Pelanggan --</option>
                <?php while ($row = $pelanggan_result->fetch_assoc()) { ?>
                    <option value="<?= $row['PelangganID']; ?>"><?= $row['NamaPelanggan']; ?> (ID: <?= $row['PelangganID']; ?>)</option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="ProdukID" class="form-label">Pilih Produk</label>
            <select name="ProdukID" id="ProdukID" class="form-control" required>
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
            <input type="number" name="JumlahProduk" id="JumlahProduk" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Penjualan</button>
    </form>

    <!-- Tabel Data Penjualan -->
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
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['PenjualanID']; ?></td>
                        <td><?= $row['TanggalPenjualan']; ?></td>
                        <td>Rp<?= number_format($row['TotalHarga'], 0, ',', '.'); ?></td>
                        <td><?= $row['PelangganID']; ?></td>
                    </tr>
                <?php } 
            } else { ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data penjualan.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>