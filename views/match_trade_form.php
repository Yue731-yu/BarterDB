<?php
// match_trade_form.php
// Form for selecting an item to find trade matches

// Get user items
$userItems = getUserItems($userId);
?>

<form action="match_trade.php" method="POST">
    <label for="item_id">Select an Item to Trade:</label>
    <select name="item_id" required>
        <?php foreach ($userItems as $item): ?>
            <option value="<?php echo htmlspecialchars($item['item_id']); ?>">
                <?php echo htmlspecialchars($item['name']) . " - " . htmlspecialchars($item['description']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Find Matches</button>
</form>