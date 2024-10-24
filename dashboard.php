<!-- NO 2 - DASHBOARD -->
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// NO 4 - TASK SEARCHING AND FILTERING
$filter = 'all';
$search = '';

if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
}
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

$query = "SELECT todolistID, todo, completion FROM todolist WHERE userID = ?";
if ($filter === 'completed') {
    $query .= " AND completion = 1";
} elseif ($filter === 'incomplete') {
    $query .= " AND completion = 0";
}

// Search tasks
if (!empty($search)) {
    $query .= " AND todo LIKE ?";
}

$stmt = $conn->prepare($query);

if (!empty($search)) {
    $search_term = '%' . $search . '%'; // Use LIKE wildcard for search
    $stmt->bind_param("is", $user_id, $search_term);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET['toggle_id'])) {
    $todolistID = $_GET['toggle_id'];

    $stmt = $conn->prepare("SELECT completion FROM todolist WHERE todolistID = ? AND userID = ?");
    $stmt->bind_param("ii", $todolistID, $user_id);
    $stmt->execute();
    $stmt->bind_result($completion);
    $stmt->fetch();
    $stmt->close();

    $new_status = $completion == 1 ? 0 : 1;

    $stmt = $conn->prepare("UPDATE todolist SET completion = ? WHERE todolistID = ? AND userID = ?");
    $stmt->bind_param("iii", $new_status, $todolistID, $user_id);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating status: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css"> 
</head>
<body>

<div class="container">
    <h2>Your To-Do Lists</h2>
    
    <a href="create_list.php" class="new-todo">Create New To-Do</a>

    <a href="view_profile.php" class="profile-link">View Profile</a>

    <a href="edit_profile.php" class="profile-link">Edit Profile</a>

    <!-- Search -->
    <form class="search-bar" method="POST" action="dashboard.php">
        <input type="text" name="search" placeholder="Search tasks..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Filter -->
    <form class="filter-bar" method="POST" action="dashboard.php">
        <button type="submit" name="filter" value="all">Show All</button>
        <button type="submit" name="filter" value="completed">Show Completed</button>
        <button type="submit" name="filter" value="incomplete">Show Incomplete</button>
    </form>

    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="<?= $row['completion'] ? 'complete' : 'incomplete' ?>">
                <?= htmlspecialchars($row['todo']) ?> 
                <div>
                    <a href="dashboard.php?toggle_id=<?= $row['todolistID'] ?>">
                        <button><?= $row['completion'] ? 'Mark Incomplete' : 'Mark Complete' ?></button>
                    </a>
                    <button class="delete" onclick="openModal('delete_list.php?id=<?= $row['todolistID'] ?>')">Delete</button>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="logout.php" class="logout">Logout</a>
</div>

<div id="confirmModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <p>Are you sure you want to delete this to-do item?</p>
        <button id="confirmDelete">Yes, delete it</button>
        <button id="cancelDelete" onclick="closeModal()">Cancel</button>
    </div>
</div>

<script>
let modal = document.getElementById("confirmModal");
let currentDeleteUrl = '';

function openModal(url) {
    currentDeleteUrl = url;
    modal.style.display = "flex";
}

function closeModal() {
    modal.style.display = "none";
}

document.getElementById("confirmDelete").onclick = function() {
    window.location.href = currentDeleteUrl;
    closeModal();
}

window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
