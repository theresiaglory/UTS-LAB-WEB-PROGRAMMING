<?php
$host = '203.175.9.20';
$db = 'todh8447_todo';           
$user = 'todh8447_Morgees';      
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
