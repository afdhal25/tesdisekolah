<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$servername = "localhost";
$dbname     = "kasir";
$dbuser     = "root";
$dbpass     = "";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = trim($_POST['username']);
    $inputPassword = trim($_POST['password']);

    // Cek apakah username ada di database
    $sql = "SELECT UserID, password, role FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare statement error: " . $conn->error);
    }
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($UserID, $dbPassword, $role);
        $stmt->fetch();

        // Debug: tampilkan nilai yang diambil
        error_log("Input Password: " . $inputPassword);
        error_log("Database Password: [" . trim($dbPassword) . "]");
        error_log("Role dari database (sebelum trim): [" . $role . "]");
        error_log("Role dari database (setelah trim): [" . trim($role) . "]");

        // Gunakan trim() untuk menghilangkan spasi ekstra
        if ($inputPassword === trim($dbPassword)) {
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);
            $_SESSION['UserID'] = $UserID;
            $_SESSION['role']   = trim($role);

            // Arahkan pengguna berdasarkan role
            if (strtolower(trim($role)) === 'admin') {
                header('Location: dashboard.php');
                exit;
            } elseif (strtolower(trim($role)) === 'petugas') {
                header('Location: dashboard_petugas.php');
                exit;
            } else {
                $_SESSION['error'] = "Role tidak dikenal!";
            }
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = "Username tidak ditemukan!";
    }

    $stmt->close();
    // Redirect kembali ke halaman login untuk menampilkan pesan error (jika ada)
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php 
        if (isset($_SESSION['error'])) { 
            echo "<p style='color:red;'>" . $_SESSION['error'] . "</p>"; 
            unset($_SESSION['error']);
        } 
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
    <href
</body>
</html>