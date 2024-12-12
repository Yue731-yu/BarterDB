<?php
// dashboard.php
session_start();
require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Get user information
$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle role-specific data
if ($role === 'user') {
    // Fetch user-specific information
    $stmt = $conn->prepare("SELECT username, email, phone_number, address, created_at, status FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username, $email, $phone, $address, $created_at, $status);
    $stmt->fetch();
    $stmt->close();

} elseif ($role === 'admin') {
    // Fetch admin-specific information
    $stmt = $conn->prepare("SELECT username, email, created_at, status FROM Admin WHERE admin_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($username, $email, $created_at, $status);
    $stmt->fetch();
    $stmt->close();

    // Fetch all users
    $userStmt = $conn->prepare("SELECT user_id, username, email, phone_number, address, status, created_at FROM User");
    $userStmt->execute();
    $result = $userStmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $userStmt->close();

    // Fetch all transactions
    $transactionStmt = $conn->prepare("SELECT transaction_id, hash_key, status, created_at, completed_at, user_a_id, user_x_id, item_p_id, item_e_id FROM Transaction");
    $transactionStmt->execute();
    $result = $transactionStmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $transactionStmt->close();
} else {
    // Unknown role, logout for safety
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle POST requests for admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'admin') {
    if (isset($_POST['update_status']) && isset($_POST['user_id']) && isset($_POST['status'])) {
        // Update user status
        $userId = intval($_POST['user_id']);
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE User SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $status, $userId);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: dashboard.php");
        exit();
    }

    if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
        // Delete user
        $userId = intval($_POST['user_id']);
        $stmt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();

        // Redirect to avoid form resubmission
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <!-- Include header -->
    <?php include '../views/header.php'; ?>

    <!-- Dashboard content container -->
    <div class="dashboard-container">
        <?php
        if ($role === 'user') {
            include '../views/user_dashboard.php';
        } elseif ($role === 'admin') {
            include '../views/admin_dashboard.php';
        }
        ?>
    </div>

    <!-- Include footer -->
    <?php include '../views/footer.php'; ?>
</body>
</html>