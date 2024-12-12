<?php
// user_dashboard.php
// This view displays the user information and navigation links on the dashboard

// Ensure user data variables are set
if (!isset($username, $email, $phone, $address, $status, $created_at)) {
    echo "Error: User information is not available.";
    exit();
}
?>

<div class="user-info">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?></h2>
    
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
    <p><strong>Account Status:</strong> <?php echo htmlspecialchars($status); ?></p>
    <p><strong>Joined on:</strong> <?php echo htmlspecialchars($created_at); ?></p>
</div>

<div style="margin-top: 50px;"></div>
<!-- Quick links navigation section -->
<div class="quick-links">
    <a href="post_item.php">Post an Item</a> 
    <a href="match_trade.php">Find a Trade</a> 
    <a href="transaction_history.php">View Transaction History</a> 
    <a href="../controllers/userController.php?logout=1">Logout</a>
</div>
