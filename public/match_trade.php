
<?php
// match_trade.php
require_once '../controllers/userController.php';
require_once '../controllers/itemController.php';
require_once '../controllers/transactionController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$matches = [];
$partners = [];
$message = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemId = $_POST['item_id'] ?? null;
    $partnerId = $_POST['partner_id'] ?? null;

    if (!$itemId) {
        $message = "Please select an item to find matches.";
    } elseif ($partnerId) {
        $partnerAvailable = isUserAvailable($partnerId);
        if ($partnerAvailable) {
            $matches = suggestMatches($itemId, $userId, $partnerId);
            $message = empty($matches)
                ? "No matches found with this partner. <a href='match_trade.php'>Try a different partner.</a>"
                : "Matches found. Select an option to proceed.";
        } else {
            $message = "The selected partner is not available for trade.";
        }
    } else {
        $matches = suggestMatches($itemId, $userId);
        $message = empty($matches)
            ? "No matches found for this item. <a href='match_trade.php'>Try a different item.</a>"
            : "Matches found. Select an option to proceed.";
    }

    if (isset($_POST['match_item_id'])) {
        $itemIdP = $_POST['item_id'];
        $itemIdE = $_POST['match_item_id'];
        $partnerId = $_POST['partner_id'] ?? null;

        error_log("itemIdP: $itemIdP, itemIdE: $itemIdE, partnerId: " . ($partnerId ?: 'NULL'));

        $equivalenceDetails = getEquivalenceDetails($itemIdP, $itemIdE);
        if ($equivalenceDetails) {
            error_log("Equivalence details: " . print_r($equivalenceDetails, true));

            $hashKey = createTransaction(
                $itemIdP,
                $itemIdE,
                $userId,
                $partnerId,   
                null, null,   
                $equivalenceDetails['equivalence_rate'],
                $equivalenceDetails['cost_p_rate'],
                $equivalenceDetails['cost_e_rate']
            );

            if ($hashKey) {
                error_log("Transaction created successfully. HashKey: $hashKey");
                $message = "Transaction created successfully! Transaction Key: <strong>" . htmlspecialchars(substr($hashKey, 0, 8)) . "</strong>";
            } else {
                error_log("Failed to create transaction.");
                $message = "Failed to create transaction.";
            }
        } else {
            error_log("Failed to fetch equivalence details for itemIdP: $itemIdP, itemIdE: $itemIdE.");
            $message = "Failed to fetch equivalence details.";
        }
    }
}
updateLatestTransactionWithRandomUserIds();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Match a Trade</title>
    <link rel="stylesheet" href="../assets/css/match_trade.css">
    <script>
        // JavaScript function to handle countdown and form submission
        function startCountdown(formId, buttonId) {
            const button = document.getElementById(buttonId);
            const form = document.getElementById(formId);
            let countdown = 5; // 5 seconds countdown
            button.disabled = true; // Disable the button to prevent multiple clicks

            // Update button text with countdown
            const interval = setInterval(() => {
                button.textContent = `Processing (${countdown}s)`;
                countdown--;

                if (countdown < 0) {
                    clearInterval(interval); // Stop the countdown
                    form.submit(); // Submit the form
                }
            }, 1000);
        }
    </script>
</head>
<body>
    <!-- Include header -->
    <?php include '../views/header.php'; ?>

    <div class="container">
        <h2>Find a Trade Match</h2>

        <!-- Display messages -->
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Include form for selecting an item to find matches -->
        <?php include '../views/match_trade_form.php'; ?>

        <!-- Display available matches if found -->
        <?php if (!empty($matches)): ?>
            <h3>Available Matches</h3>
            <ul>
                <?php foreach ($matches as $index => $match): ?>
                    <li>
                        <form action="match_trade.php" method="POST" id="matchForm<?php echo $index; ?>">
                            Item: <?php echo htmlspecialchars($match['name']); ?>,
                            Description: <?php echo htmlspecialchars($match['description']); ?>,
                            Quantity: <?php echo htmlspecialchars($match['quantity']); ?>,
                            Value: <?php echo htmlspecialchars($match['value']); ?><br>
                            Equivalence Rate: <?php echo htmlspecialchars($match['equivalence_rate']); ?>,
                            Cost (P): <?php echo htmlspecialchars($match['cost_p_rate']); ?>,
                            Cost (E): <?php echo htmlspecialchars($match['cost_e_rate']); ?><br>
                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($itemId); ?>">
                            <input type="hidden" name="match_item_id" value="<?php echo htmlspecialchars($match['item_e_id']); ?>">
                            <input type="hidden" name="partner_id" value="<?php echo htmlspecialchars($partnerId ?? $userId); ?>">
                            <button type="button" id="submitButton<?php echo $index; ?>"
                                onclick="startCountdown('matchForm<?php echo $index; ?>', 'submitButton<?php echo $index; ?>')">
                                Select this Match
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Partner invitation logic -->
        <?php if (empty($matches) && empty($_POST['partner_id'])): ?>
            <h3>Invite a Partner</h3>
            <form action="match_trade.php" method="POST" id="invitePartnerForm">
                <label for="partner_id">Select a Partner:</label>
                <select name="partner_id" required>
                    <?php
                    $partners = getAvailablePartners($userId);
                    foreach ($partners as $partner): ?>
                        <option value="<?php echo htmlspecialchars($partner['user_id']); ?>">
                            <?php echo htmlspecialchars($partner['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($itemId ?? ''); ?>">
                <button type="button" id="inviteButton"
                    onclick="startCountdown('invitePartnerForm', 'inviteButton')">
                    Invite Partner
                </button>
            </form>
        <?php endif; ?>

        <p><a href="dashboard.php">Return to Dashboard</a></p>
    </div>

    <!-- Include footer -->
    <?php include '../views/footer.php'; ?>
</body>
</html>