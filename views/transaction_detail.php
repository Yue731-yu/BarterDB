<?php
// transaction_detail.php
// This view displays detailed information for a specific transaction

require_once '../controllers/transactionController.php';

// Get the transaction ID from the query string
$transactionId = $_GET['transaction_id'] ?? null;

if (!$transactionId) {
    echo "<p>Transaction ID is missing.</p>";
    return;
}

// Fetch transaction details using the provided ID
$transaction = getTransactionDetails($transactionId);

// Check if transaction data is available
if (!$transaction) {
    echo "<p>Transaction details are not available.</p>";
    return;
}
?>

<h3>Information</h3>

<table>
    <tr>
        <th>Transaction ID</th>
        <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
    </tr>
    <tr>
        <th>Hash Key</th>
        <td>
            <?php
            if ($transaction['user_a'] === $_SESSION['username']) {
                echo htmlspecialchars(substr($transaction['hash_key'], 0, 8)); 
            } elseif ($transaction['user_x'] === $_SESSION['username']) {
                echo htmlspecialchars(substr($transaction['hash_key'], 8, 8)); 
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
    </tr>
    <tr>
        <th>Created At</th>
        <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
    </tr>
    <tr>
        <th>Completed At</th>
        <td><?php echo htmlspecialchars($transaction['completed_at'] ?? 'Pending'); ?></td>
    </tr>
</table>

<h3>Items in Transaction</h3>

<table>
    <tr>
        <th>Item (P)</th>
        <td><?php echo htmlspecialchars($transaction['item_p_name']); ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?php echo htmlspecialchars($transaction['item_p_description']); ?></td>
    </tr>
    <tr>
        <th>Quantity</th>
        <td><?php echo htmlspecialchars($transaction['item_p_quantity']); ?></td>
    </tr>
    <tr>
        <th>Value</th>
        <td><?php echo htmlspecialchars($transaction['item_p_value']); ?></td>
    </tr>
</table>

<table>
    <tr>
        <th>Item (E)</th>
        <td><?php echo htmlspecialchars($transaction['item_e_name']); ?></td>
    </tr>
    <tr>
        <th>Description</th>
        <td><?php echo htmlspecialchars($transaction['item_e_description']); ?></td>
    </tr>
    <tr>
        <th>Quantity</th>
        <td><?php echo htmlspecialchars($transaction['item_e_quantity']); ?></td>
    </tr>
    <tr>
        <th>Value</th>
        <td><?php echo htmlspecialchars($transaction['item_e_value']); ?></td>
    </tr>
</table>

<h3>Participants</h3>

<table>
    <tr>
        <th>Buyer (User A)</th>
        <td><?php echo htmlspecialchars($transaction['user_a']); ?></td>
    </tr>
    <tr>
        <th>Seller (User X)</th>
        <td>secret</td>
    </tr>
</table>

<h3>Transaction Costs</h3>

<table>
    <tr>
        <th>Cost for Item P</th>
        <td><?php echo htmlspecialchars($transaction['cost_p']); ?></td>
    </tr>
    <tr>
        <th>Cost for Item E</th>
        <td><?php echo htmlspecialchars($transaction['cost_e']); ?></td>
    </tr>
</table>