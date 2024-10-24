<!-- NO 3 - TO DO LIST MANAGEMENT (create) -->
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Create new to do list
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $todo = $_POST['todo'];

    $stmt = $conn->prepare("INSERT INTO todolist (userID, todo) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $todo);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create To-Do List</title>
    <link rel="stylesheet" href="create.css">
</head>
<body>
    <div class="container">
        <h2>Create New To-Do</h2>
        <form method="POST" action="">
            <input type="text" name="todo" placeholder="Enter your task here..." required>
            <button type="submit">Create</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
