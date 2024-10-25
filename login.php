<?php
session_start();
require 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT userID, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hash);
        $stmt->fetch();
        
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: dashboard.php");
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var passwordCheckbox = document.getElementById("showPassword");
            passwordField.type = passwordCheckbox.checked ? "text" : "password";
        }
    </script>
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <?php if($error): ?>
            <p style="color:red">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required> 
        <input type="password" id="password" name="password" placeholder="Password" required>
        <label>
            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
        </label>
        <button type="submit">Login</button>
        
        <p>Forgot password? <a href="forgot_password.php">Reset here</a></p>
        <p>Don't have an account? <a href="sign_up.php">Sign Up</a></p>
    </form>
</body>
</html>
