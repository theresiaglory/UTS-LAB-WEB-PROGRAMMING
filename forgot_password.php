<?php
// Koneksi ke database
$host = "localhost";
$dbname = "todo";
$username = "root"; // sesuaikan dengan username MySQL Anda
$password = ""; // sesuaikan dengan password MySQL Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Bagian 1: Kirim token reset password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];
    
    // Cek apakah email terdaftar
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Buat token dan waktu kedaluwarsa
        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // token berlaku 1 jam

        // Simpan token ke database
        $stmt = $pdo->prepare("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
        $stmt->execute(['token' => $token, 'expiry' => $expiry, 'email' => $email]);

        // Kirim email (untuk keperluan demo, hanya cetak tautan)
        echo "Klik link ini untuk reset password: ";
        echo "<a href='forgot_password.php?token=$token'>Reset Password</a>";
    } else {
        echo "Email tidak ditemukan.";
    }
}

// Bagian 2: Proses reset password jika token ada
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Cari token di database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
            // Update password
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE reset_token = :token");
            $stmt->execute(['password' => $new_password, 'token' => $token]);

            echo "Password berhasil diubah!";
        } else {
            // Form untuk mengubah password
            echo "
                <form method='POST'>
                    <label>Password Baru:</label><br>
                    <input type='password' name='new_password' required><br>
                    <button type='submit'>Ubah Password</button>
                </form>
            ";
        }
    } else {
        echo "Token tidak valid atau kedaluwarsa.";
    }
}
?>

<!-- Bagian 1: Form Email -->
<h2>Forgot Password</h2>
<form method="POST">
    <label>Masukkan Email Anda:</label><br>
    <input type="email" name="email" required><br>
    <button type="submit">Kirim Link Reset Password</button>
</form>
