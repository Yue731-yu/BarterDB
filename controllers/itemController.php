<?php
// itemController.php
// Controller for handling item posting, management, and matching

require_once '../includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Function to add a new item
function addItem($userId, $name, $description, $quantity, $value, $unit) {
    global $conn;

    // Prepare SQL statement to insert new item data
    $stmt = $conn->prepare("INSERT INTO Item (user_id, name, description, quantity, value, unit, status) VALUES (?, ?, ?, ?, ?, ?, 'available')");
    $stmt->bind_param("issdds", $userId, $name, $description, $quantity, $value, $unit);

    // Execute and return result
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Function to get all items posted by a user
function getUserItems($userId) {
    global $conn;

    // Prepare SQL statement to fetch items by user ID
    $stmt = $conn->prepare("SELECT item_id, name, description, quantity, value, unit, status FROM Item WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all items and return as an array
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $items;
}

// Function to delete an item by item ID
function deleteItem($itemId, $userId) {
    global $conn;

    // Prepare SQL statement to delete an item if it belongs to the user
    $stmt = $conn->prepare("DELETE FROM Item WHERE item_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $userId);

    // Execute and return result
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// Function to update an existing item
function updateItem($itemId, $userId, $name, $description, $quantity, $value, $unit) {
    global $conn;

    // Prepare SQL statement to update item data
    $stmt = $conn->prepare("UPDATE Item SET name = ?, description = ?, quantity = ?, value = ?, unit = ?, status = 'available' WHERE item_id = ? AND user_id = ?");
    $stmt->bind_param("ssddsii", $name, $description, $quantity, $value, $unit, $itemId, $userId);

    // Execute and return result
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

// // Function to suggest matching items for trade
// function suggestMatches($itemId, $userId) {
//     global $conn;

//     // Prepare SQL query to find equivalent items based on Equivalence table
//     $stmt = $conn->prepare("
//         SELECT e.item_e_id, i.name, i.description, i.quantity, i.value, e.equivalence_rate, e.cost_p_rate, e.cost_e_rate 
//         FROM Equivalence e
//         JOIN Item i ON e.item_e_id = i.item_id
//         WHERE e.item_p_id = ? AND i.user_id != ? AND i.status = 'available'
//     ");
//     $stmt->bind_param("ii", $itemId, $userId);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     // Fetch all matching items and return as an array
//     $matches = $result->fetch_all(MYSQLI_ASSOC);
//     $stmt->close();
//     return $matches;
// }

function getAvailableItems($userId) {
    global $conn;

    $query = "
        SELECT item_id, name, quantity, status 
        FROM Item 
        WHERE user_id = ? AND status = 'available'
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $items;
}


// Function to get equivalence details for a specific pair of items
function getEquivalenceDetails($itemIdP, $itemIdE) {
    global $conn;

    // Prepare SQL statement to fetch equivalence details
    $stmt = $conn->prepare("
        SELECT equivalence_rate, cost_p_rate, cost_e_rate 
        FROM Equivalence 
        WHERE item_p_id = ? AND item_e_id = ?
    ");
    $stmt->bind_param("ii", $itemIdP, $itemIdE);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch equivalence details and return as an associative array
    $details = $result->fetch_assoc();
    $stmt->close();
    return $details;
}
?>