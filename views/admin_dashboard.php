<?php
// Ensure $users and $transactions are defined
if (!isset($users) || !is_array($users)) {
    $users = [];
}

if (!isset($transactions) || !is_array($transactions)) {
    $transactions = [];
}
?>

<h3>Manage Users</h3>
<table>
    <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['phone_number']); ?></td>
            <td><?php echo htmlspecialchars($user['address']); ?></td>
            <td><?php echo htmlspecialchars($user['status']); ?></td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td>
                <!-- Update Status Form -->
                <form action="dashboard.php" method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                    <select name="status">
                        <option value="active" <?php if ($user['status'] === 'active') echo 'selected'; ?>>Active</option>
                        <option value="suspended" <?php if ($user['status'] === 'suspended') echo 'selected'; ?>>Suspended</option>
                        <option value="deleted" <?php if ($user['status'] === 'deleted') echo 'selected'; ?>>Deleted</option>
                    </select>
                    <button type="submit" name="update_status" class="button">Update</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<div style="margin-top: 50px;"></div>
<h3>Transaction Records</h3>
<table>
    <tr>
        <th>Transaction ID</th>
        <th>Hash Key</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Completed At</th>
        <th>User A</th>
        <th>User X</th>
        <th>Item P</th>
        <th>Item E</th>
    </tr>
    <?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
            <td><?php echo htmlspecialchars($transaction['hash_key']); ?></td>
            <td><?php echo htmlspecialchars($transaction['status']); ?></td>
            <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
            <td><?php echo htmlspecialchars($transaction['completed_at']); ?></td>
            <td><?php echo htmlspecialchars($transaction['user_a_id']); ?></td>
            <td><?php echo htmlspecialchars($transaction['user_x_id']); ?></td>
            <td><?php echo htmlspecialchars($transaction['item_p_id']); ?></td>
            <td><?php echo htmlspecialchars($transaction['item_e_id']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>