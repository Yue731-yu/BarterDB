<?php
// db_connect.php
// Database connection script for BarterDB project

// Include the configuration file to get database credentials
require_once 'config.php';

// Create a new MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for a connection error and display a message if it fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for better compatibility with special characters
$conn->set_charset("utf8mb4");
?>
