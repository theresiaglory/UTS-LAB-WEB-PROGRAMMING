<!-- NO 5 - USER PROFILE MANAGEMENT (edit) -->
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT username, email FROM users WHERE userID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
$stmt->fetch();
$stmt->close();

$error = "";

// Edit profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE userID = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_password_hash, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE userID = ?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: view_profile.php");
        exit();
    } else {
        $error = "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($current_username) ?>" required>
            <br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($current_email) ?>" required>
            <br>
            <label for="password">New Password (optional):</label>
            <input type="password" name="password">
            <br>
            <button type="submit">Update Profile</button>
        </form>

        <a href="view_profile.php">Back to Profile</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
