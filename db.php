<?php
$host = 'localhost';
$db = 'todo';           
$user = 'root';      
$pass = '';        

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>
