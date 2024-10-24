<!-- NO 3 - TO DO LIST MANAGEMENT (delete) -->
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $todolistID = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM todolist WHERE todolistID = ? AND userID = ?");
    $stmt->bind_param("ii", $todolistID, $user_id);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: dashboard.php");
    exit();
}

$conn->close();
?>
