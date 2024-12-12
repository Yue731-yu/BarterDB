<?php
// userController.php
require_once '../includes/db_connect.php';

// User registration function
function registerUser($username, $password, $email, $phone, $address) {
    global $conn;
    
    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Prepare SQL statement to insert user data
    $stmt = $conn->prepare("INSERT INTO User (username, password_hash, email, phone_number, address, status) VALUES (?, ?, ?, ?, ?, 'active')");
    $stmt->bind_param("sssss", $username, $hashedPassword, $email, $phone, $address);
    
    // Execute statement and check if successful
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// User login function
function loginUser($username, $password) {
    global $conn;

    // Try to login as a regular user
    $stmt = $conn->prepare("SELECT user_id, password_hash, 'user' AS role FROM User WHERE username = ? AND status = 'active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $storedHash, $role);
        $stmt->fetch();

        // Verify the password using bcrypt
        if (password_verify($password, $storedHash)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Save role in session
            $stmt->close();
            return true;
        }
    }

    // Try to login as an admin if user login fails
    $stmt = $conn->prepare("SELECT admin_id, password_hash, 'admin' AS role FROM Admin WHERE username = ? AND status = 'active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($adminId, $storedHash, $role);
        $stmt->fetch();

        // Verify the password using bcrypt
        if (password_verify($password, $storedHash)) {
            $_SESSION['user_id'] = $adminId; // Save admin ID as user_id
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role; // Save role in session
            $stmt->close();
            return true;
        }
    }

    // Close the statement and return false for login failure
    $stmt->close();
    return false;
}

// Logout function
function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: ../public/login.php");
}

// Check if a user is available for trade (mock availability check)
function isUserAvailable($userId) {
    global $conn;

    // Query to check user status
    $stmt = $conn->prepare("SELECT status FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    // Return true if the user is active
    return $status === 'active';
}

// Fetch all available users to invite as partners
function getAvailablePartners($excludeUserId) {
    global $conn;

    // Query to fetch all active users except the current user
    $stmt = $conn->prepare("
        SELECT user_id, username 
        FROM User 
        WHERE user_id != ? AND status = 'active'
    ");
    $stmt->bind_param("i", $excludeUserId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch and return as an array
    $partners = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $partners;
}

// Update user status (e.g., active, suspended, or deleted)
function updateUserStatus($userId, $newStatus) {
    global $conn;

    // Prepare SQL statement to update user status
    $stmt = $conn->prepare("UPDATE User SET status = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newStatus, $userId);

    // Execute and return result
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Handle logout request if it is set in the URL
if (isset($_GET['logout'])) {
    logoutUser();
}
?>