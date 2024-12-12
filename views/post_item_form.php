<?php
// post_item_form.php
// Form for posting a new item
?>

<form action="post_item.php" method="POST">
    <div>
        <label for="name">Item Name:</label>
        <input type="text" name="name" id="name" placeholder="Enter item name" required>
    </div>
    
    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" placeholder="Enter a brief description" required></textarea>
    </div>
    
    <div>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" step="0.01" placeholder="Enter quantity" required>
    </div>
    
    <div>
        <label for="value">Value:</label>
        <input type="number" name="value" id="value" step="0.01" placeholder="Enter value" required>
    </div>
    
    <div>
        <label for="unit">Unit:</label>
        <input type="text" name="unit" id="unit" placeholder="Enter unit (e.g., kg, pcs)" required>
    </div>
    
    <button type="submit">Post Item</button>
</form>