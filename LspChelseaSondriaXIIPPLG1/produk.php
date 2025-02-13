<?php
$host = "localhost";     // Atur host database Anda
$username = "root";      // Atur username database Anda
$password = "";          // Atur password database Anda
$dbname = "nama_database"; // Ganti dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

// Proses tambah produk jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form
    $nama_produk = $_POST['NamaProduk'];
    $stok = (int) $_POST['Stok'];
    $harga = (float) $_POST['Harga'];

    // Validasi input
    if (empty($nama_produk) || $stok <= 0 || $harga <= 0) {
        $_SESSION['error'] = "Semua kolom harus diisi dengan benar!";
    } else {
        // Query untuk menambahkan data produk ke database
        $sql = "INSERT INTO produk (NamaProduk, Stok, Harga) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nama_produk, $stok, $harga);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Produk berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan produk! Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Tambah Produk</h2>

    <!-- Tampilkan Pesan -->
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php } elseif (isset($_SESSION['error'])) { ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php } ?>

    <!-- Form Tambah Produk -->
    <form action="add_product.php" method="POST" class="mb-4">
        <div class="mb-3">
            <label for="NamaProduk" class="form-label">Nama Produk</label>
            <input type="text" name="NamaProduk" id="NamaProduk" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="Stok" class="form-label">Stok</label>
            <input type="number" name="Stok" id="Stok" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="Harga" class="form-label">Harga</label>
            <input type="number" name="Harga" id="Harga" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Tambah Produk</button>
    </form>

    <!-- Tabel Data Produk -->
    <h3>Daftar Produk</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Stok</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil data produk dari database
            $sql = "SELECT ProdukID, NamaProduk, Stok, Harga FROM produk";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['ProdukID']; ?></td>
                        <td><?= $row['NamaProduk']; ?></td>
                        <td><?= $row['Stok']; ?></td>
                        <td>Rp<?= number_format($row['Harga'], 0, ',', '.'); ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data produk.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
