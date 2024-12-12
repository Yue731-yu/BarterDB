<?php
// adminController.php
// Controller for handling admin functions such as managing users and viewing transactions

require_once '../includes/db_connect.php';
// session_start();

// Check if user is an admin
function checkAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../public/login.php");
        exit();
    }
}

// Function to get a list of all users
function getAllUsers() {
    global $conn;

    // Query to fetch user information
    $query = "SELECT user_id, username, email, phone_number, address, created_at, status FROM User";
    $result = $conn->query($query);

    // Fetch all users and return as an array
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get a list of all transactions
function getAllTransactions() {
    global $conn;

    // Query to fetch transaction information
    $query = "
        SELECT t.transaction_id, t.hash_key, t.status, t.created_at, t.completed_at, 
               u1.username AS user_a, u2.username AS user_x, i1.name AS item_p, i2.name AS item_e
        FROM Transaction t
        JOIN User u1 ON t.user_a_id = u1.user_id
        JOIN User u2 ON t.user_x_id = u2.user_id
        JOIN Item i1 ON t.item_p_id = i1.item_id
        JOIN Item i2 ON t.item_e_id = i2.item_id
        ORDER BY t.created_at DESC
    ";
    $result = $conn->query($query);

    // Fetch all transactions and return as an array
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to update user status (e.g., approve, suspend, delete)
function updateUserStatus($userId, $status) {
    global $conn;

    // Prepare SQL statement to update user status
    $stmt = $conn->prepare("UPDATE User SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $status, $userId);

    // Execute and return result
    return $stmt->execute();
}

// Function to delete a user by ID
function deleteUser($userId) {
    global $conn;

    // Prepare SQL statement to delete a user
    $stmt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $userId);

    // Execute and return result
    return $stmt->execute();
}
?>
