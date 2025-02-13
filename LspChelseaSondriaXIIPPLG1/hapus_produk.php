<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];   

    $sql = "DELETE FROM produk WHERE ProdukID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produk);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Produk berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus produk.";
    }

    $stmt->close();
    $conn->close();
}

header("Location: dashboard.php");
exit;
?>