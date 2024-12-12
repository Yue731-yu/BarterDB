<?php
// transactionController.php
// Controller for handling trade matching and transaction management

require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Function to suggest matching items based on equivalence table
function suggestMatches($itemId, $userId, $partnerId = null) {
    global $conn;

    if ($partnerId) {
        $stmt = $conn->prepare("
            SELECT e.item_e_id, i.name, i.description, i.quantity, i.value, e.equivalence_rate, e.cost_p_rate, e.cost_e_rate
            FROM Equivalence e
            JOIN Item i ON e.item_e_id = i.item_id
            WHERE e.item_p_id = ? AND i.user_id = ? AND i.status = 'available'
        ");
        $stmt->bind_param("ii", $itemId, $partnerId);
    } else {

        $stmt = $conn->prepare("
            SELECT e.item_e_id, i.name, i.description, i.quantity, i.value, e.equivalence_rate, e.cost_p_rate, e.cost_e_rate
            FROM Equivalence e
            JOIN Item i ON e.item_e_id = i.item_id
            WHERE e.item_p_id = ? AND i.user_id != ? AND i.status = 'available'
        ");
        $stmt->bind_param("ii", $itemId, $userId);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $matches = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $matches;
}

// Function to create a new transaction
function createTransaction(
    $itemIdP,
    $itemIdE,
    $userA,
    $userX,
    $userB = null,
    $userY = null,
    $equivalenceRate,
    $costPRate,
    $costERate
) {
    global $conn;

    // Generate hash_key, leading_8, and trailing_8
    $uuid = uniqid('', true);
    $hashKey = substr(sha1($uuid), 0, 16);
    $leading8 = substr($hashKey, 0, 8);
    $trailing8 = substr($hashKey, 8, 8);

    // Calculate costs for both items
    $costP = round($costPRate * $equivalenceRate, 2);
    $costE = round($costERate * $equivalenceRate, 2);

    // Check if $userB and $userY are NULL and prepare SQL accordingly
    if ($userB === null && $userY === null) {
        $stmt = $conn->prepare("
            INSERT INTO Transaction 
            (hash_key, item_p_id, item_e_id, user_a_id, user_x_id, status, cost_p, cost_e, leading_8, trailing_8)
            VALUES (?, ?, ?, ?, ?, 'initiated', ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "siiiiddss",
            $hashKey,     // hash_key
            $itemIdP,     // item_p_id
            $itemIdE,     // item_e_id
            $userA,       // user_a_id
            $userX,       // user_x_id
            $costP,       // cost_p
            $costE,       // cost_e
            $leading8,    // leading_8
            $trailing8    // trailing_8
        );
    } else {
        $stmt = $conn->prepare("
            INSERT INTO Transaction 
            (hash_key, item_p_id, item_e_id, user_a_id, user_x_id, user_b_id, user_y_id, status, cost_p, cost_e, leading_8, trailing_8)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'initiated', ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "siiiiiddss",
            $hashKey,     // hash_key
            $itemIdP,     // item_p_id
            $itemIdE,     // item_e_id
            $userA,       // user_a_id
            $userX,       // user_x_id
            $userB,       // user_b_id
            $userY,       // user_y_id
            $costP,       // cost_p
            $costE,       // cost_e
            $leading8,    // leading_8
            $trailing8    // trailing_8
        );
    }

    // Execute statement and return result
    if ($stmt->execute()) {
        $stmt->close();
        return $hashKey;
    } else {
        error_log("SQL Error: " . $stmt->error); // Debugging: Log SQL errors
        $stmt->close();
        return false;
    }
}



// Function to get transaction history for a specific user
function getUserTransactions($userId) {
    global $conn;

    // Query to fetch transactions where the user is involved
    $stmt = $conn->prepare("
        SELECT t.transaction_id, t.hash_key, t.status, t.created_at, t.completed_at, 
               i1.name AS item_p, i2.name AS item_e, u1.username AS user_a, u2.username AS user_x
        FROM Transaction t
        JOIN Item i1 ON t.item_p_id = i1.item_id
        JOIN Item i2 ON t.item_e_id = i2.item_id
        JOIN User u1 ON t.user_a_id = u1.user_id
        JOIN User u2 ON t.user_x_id = u2.user_id
        WHERE t.user_a_id = ? OR t.user_x_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch transactions and return as an array
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $transactions;
}


// Function to update the latest transaction 
function updateLatestTransactionWithRandomUserIds() {
    global $conn;

    $stmt = $conn->prepare("
        SELECT transaction_id 
        FROM Transaction 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $latestTransaction = $result->fetch_assoc();
    $stmt->close();

    if (!$latestTransaction) {
        return "No transactions found to update.";
    }

    $transactionId = $latestTransaction['transaction_id'];

    $randomNumbers = array_rand(array_flip(range(1, 100)), 3);

    $stmt = $conn->prepare("
        UPDATE Transaction 
        SET user_x_id = ?, user_b_id = ?, user_y_id = ?
        WHERE transaction_id = ?
    ");
    $stmt->bind_param("iiii", $randomNumbers[0], $randomNumbers[1], $randomNumbers[2], $transactionId);

    if ($stmt->execute()) {
        $stmt->close();
        return "Transaction ID {$transactionId} updated successfully with random user IDs.";
    } else {
        error_log("SQL Error: " . $stmt->error); // Log SQL error for debugging
        $stmt->close();
        return "Failed to update the latest transaction.";
    }
}

// Function to retrieve transaction details by transaction ID
function getTransactionDetails($transactionId) {
    global $conn;

    // Prepare SQL statement to retrieve transaction details by transaction_id
    $stmt = $conn->prepare("
        SELECT t.transaction_id, t.hash_key, t.item_p_id, t.item_e_id, t.user_a_id, t.user_x_id, t.user_b_id, t.user_y_id, 
               t.status, t.cost_p, t.cost_e, t.created_at, t.leading_8, t.trailing_8,
               i1.name AS item_p_name, i1.description AS item_p_description, i1.quantity AS item_p_quantity, i1.value AS item_p_value,
               i2.name AS item_e_name, i2.description AS item_e_description, i2.quantity AS item_e_quantity, i2.value AS item_e_value,
               u1.username AS user_a, u2.username AS user_x
        FROM Transaction t
        JOIN Item i1 ON t.item_p_id = i1.item_id
        JOIN Item i2 ON t.item_e_id = i2.item_id
        JOIN User u1 ON t.user_a_id = u1.user_id
        JOIN User u2 ON t.user_x_id = u2.user_id
        WHERE t.transaction_id = ?
    ");
    $stmt->bind_param("i", $transactionId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch transaction details and return as associative array
    $transaction = $result->fetch_assoc();
    $stmt->close();
    return $transaction;
}

// Function to update transaction status
function updateTransactionStatus($transactionId, $newStatus) {
    global $conn;

    // Prepare SQL statement to update transaction status
    $stmt = $conn->prepare("UPDATE Transaction SET status = ? WHERE transaction_id = ?");
    $stmt->bind_param("si", $newStatus, $transactionId);

    // Execute and return result
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}
?>
