<?php
// transaction_history.php
// This view displays the transaction history for the logged-in user

// Check if the $transactions array is defined and has data
if (!isset($transactions) || empty($transactions)) {
    echo "<p>No transaction history available.</p>";
    return;
}
?>

<table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Hash Key</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Completed At</th>
            <th>Your Role</th>
            <th>Item P</th>
            <th>Item E</th>
            <th>Other Party</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                
                <!-- Display part of the hash key based on user's role -->
                <td>
                    <?php
                    if ($transaction['user_a'] === $_SESSION['username']) {
                        // Buyer: Show first 8 characters
                        echo htmlspecialchars(substr($transaction['hash_key'], 0, 8));
                    } elseif ($transaction['user_x'] === $_SESSION['username']) {
                        // Seller: Show last 8 characters
                        echo htmlspecialchars(substr($transaction['hash_key'], 8, 8));
                    }
                    ?>
                </td>
                
                <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                <td><?php echo htmlspecialchars($transaction['completed_at'] ?? 'Pending'); ?></td>
                
                <!-- Determine the user's role in the transaction -->
                <td>
                    <?php
                    if ($transaction['user_a'] === $_SESSION['username']) {
                        echo 'Buyer';
                    } elseif ($transaction['user_x'] === $_SESSION['username']) {
                        echo 'Seller';
                    }
                    ?>
                </td>

                <td><?php echo htmlspecialchars($transaction['item_p']); ?></td>
                <td><?php echo htmlspecialchars($transaction['item_e']); ?></td>

                <!-- Display the username of the other party in the transaction -->
                <td>
                    <?php
                    if ($transaction['user_a'] === $_SESSION['username']) {
                        echo htmlspecialchars($transaction['user_x']);
                    } else {
                        echo htmlspecialchars($transaction['user_a']);
                    }
                    ?>
                </td>

                <!-- Add link to view transaction details -->
                <td>
                    <a href="transaction_detail.php?transaction_id=<?php echo urlencode($transaction['transaction_id']); ?>">
                        View Details
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>