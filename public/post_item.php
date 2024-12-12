<?php
// post_item.php
require_once '../controllers/itemController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = "";  // Variable to hold success or error messages

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $quantity = (float)$_POST['quantity'];
    $value = (float)$_POST['value'];
    $unit = trim($_POST['unit']);

    // Validate input
    if ($quantity <= 0 || $value <= 0) {
        $message = "Quantity and Value must be greater than zero.";
    } elseif (empty($name) || empty($description) || empty($unit)) {
        $message = "All fields are required.";
    } else {
        // Attempt to add item
        if (addItem($userId, $name, $description, $quantity, $value, $unit)) {
            $message = "Item posted successfully!";
        } else {
            $message = "Failed to post item. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post an Item</title>
    <link rel="stylesheet" href="../assets/css/item.css">
</head>
<body>
    <?php include '../views/header.php'; ?>

    <div class="container">
        <h2>Post a New Item</h2>

        <!-- Display success or error message -->
        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <!-- Include form -->
        <?php include '../views/post_item_form.php'; ?>

        <!-- Return to Dashboard -->
        <!-- <p class="return-dashboard">
            <a href="dashboard.php">Return to Dashboard</a>
        </p> -->
        <p style="margin-top: 30px; text-align: center;">
            <a href="dashboard.php">Return to Dashboard</a>
        </p>
    </div>

    <?php include '../views/footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector("form");

            form.addEventListener("submit", (e) => {
                const quantity = parseFloat(document.getElementById("quantity").value);
                const value = parseFloat(document.getElementById("value").value);

                if (quantity <= 0 || value <= 0) {
                    e.preventDefault();
                    alert("Quantity and Value must be greater than zero.");
                }
            });
        });
    </script>
</body>
</html>