<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id_produk = $_GET['id'];

$sql = "SELECT * FROM produk WHERE ProdukID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_produk);
$stmt->execute();
$result = $stmt->get_result();
$produk = $result->fetch_assoc();

if (!$produk) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = trim($_POST['nama_produk']);
    $harga = (int) $_POST['harga'];
    $stok = (int) $_POST['stok'];

    $sql = "UPDATE produk SET NamaProduk = ?, Harga = ?, Stok = ? WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $nama_produk, $harga, $stok, $id_produk);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Produk berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui produk.";
    }

    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Edit Produk</h2>
    
    <form action="" method="POST">
        <div class="mb-3">
            <label for="nama_produk" class="form-label">Nama Produk:</label>
            <input type="text" name="nama_produk" class="form-control" value="<?= $produk['NamaProduk']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="harga" class="form-label">Harga:</label>
            <input type="number" name="harga" class="form-control" value="<?= $produk['Harga']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok:</label>
            <input type="number" name="stok" class="form-control" value="<?= $produk['Stok']; ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

</body>
</html>
