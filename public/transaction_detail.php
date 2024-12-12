<?php
// transaction_detail.php
require_once '../controllers/transactionController.php';
// session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verify that a transaction ID is provided
if (!isset($_GET['transaction_id'])) {
    echo "Transaction ID is missing.";
    exit();
}

$transactionId = $_GET['transaction_id'];

// Fetch transaction details based on the transaction ID
$transaction = getTransactionDetails($transactionId);

// If the transaction does not exist, show an error message
if (!$transaction) {
    echo "Transaction not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Detail</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include '../views/header.php'; ?>

    <h2>Transaction Detail</h2>

    <?php include '../views/transaction_detail.php'; ?>

    <p><a href="transaction_history.php">Back to Transaction History</a></p>

    <?php include '../views/footer.php'; ?>
</body>
</html>
