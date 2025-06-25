<?php
// Database configuration
$host = 'localhost';       // Server hostname or IP address
$dbName = 'hms_db'; // Replace with your database name
$username = 'root';    // Replace with your database username
$password = '';    // Replace with your database password

// Create connection
$conn = new mysqli($host, $username, $password, $dbName);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Uncomment this for debugging successful connection
// echo "Connected successfully";

?>
