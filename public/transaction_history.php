<?php
// transaction_history.php
require_once '../controllers/transactionController.php';
// session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch transaction history for the logged-in user
$transactions = getUserTransactions($userId);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../views/header.php'; ?>

    <h2>Transaction History</h2>

    <?php if (empty($transactions)): ?>
        <p>No transaction history available.</p>
    <?php else: ?>
        <?php include '../views/transaction_history.php'; ?>
    <?php endif; ?>

    <?php include '../views/footer.php'; ?>
</body>
</html>
