<?php
// Database connection settings
$servername = "localhost"; // Change to your database server (e.g., 127.0.0.1)
$username = "root";        // Change to your database username
$password = "";            // Change to your database password
$dbname = "aapp";          // Database name as shown in the ER diagram

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
