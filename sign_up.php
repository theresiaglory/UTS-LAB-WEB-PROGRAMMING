<?php
require 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
        <h2>Register</h2>
        <?php if ($error): ?>
            <p style="color:red"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <label>
            <input type="checkbox" id="showPassword" onclick="togglePassword()"> Show Password
        </label>
        <button type="submit">Sign Up</button>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</body>
</html>
