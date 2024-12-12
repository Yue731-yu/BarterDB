<?php
// config.php
// Configuration file for BarterDB project

// Database configuration settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Database username, default for XAMPP
define('DB_PASS', '12345');         // Database password, replace with your own if different
// define('DB_PASS', ''); 
define('DB_NAME', 'BarterDB');      // Database name

// Start session management for user login persistence
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Set error reporting level
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
