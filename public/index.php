<?php
// index.php
session_start();

// Redirect to dashboard if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once '../includes/db_connect.php';

// Fetch announcements and trade updates from the database
$announcements = [
    ["title" => "Welcome to BarterDB", "content" => "Start your trading journey here!"],
    ["title" => "Post Accurate Descriptions", "content" => "Ensure accurate item descriptions to find better trade partners."],
    ["title" => "System Update", "content" => "New and improved matching algorithms are live!"],
    ["title" => "Security Reminder", "content" => "Keep your account secure by using a strong password."],
    ["title" => "Weekly Barter Tips", "content" => "Check our blog for weekly tips on successful bartering."]
];

$tradeUpdates = [];

// Query for trade updates
$tradeStmt = $conn->prepare("SELECT u.username AS user_a, i.name AS item_name, t.created_at AS trade_time
    FROM Transaction t
    JOIN User u ON t.user_a_id = u.user_id
    JOIN Item i ON t.item_p_id = i.item_id
    ORDER BY t.created_at DESC LIMIT 10");

$tradeStmt->execute();
$tradeResult = $tradeStmt->get_result();
while ($row = $tradeResult->fetch_assoc()) {
    $tradeUpdates[] = $row;
}
$tradeStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to BarterDB</title>
    <link rel="stylesheet" href="../assets/css/index.css">
</head>
<body>
    <?php include '../views/header.php'; ?>

    <!-- Welcome Section -->
    <div class="welcome-container">
        <h2>Welcome to BarterDB !</h2>
        <p>BarterDB is an innovative platform designed for anonymous, secure, and efficient item exchanges. Our goal is to bring people together through a modern barter system where you can trade items directly with others—without the need for money.</p>
        
        <div class="auth-buttons">
            <a href="login.php" class="button">Login</a>
            <a href="register.php" class="button button-secondary">Register</a>
        </div>
    </div>

    <!-- Announcements and Trade Updates -->
    <div class="info-container">
        <!-- Announcement Board (Manual Scroll) -->
        <div class="bulletin-board">
            <h3>Announcement Board</h3>
            <div class="bulletin-scroll">
                <ul>
                    <?php foreach ($announcements as $announcement): ?>
                        <li>
                            <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                            <p><?php echo htmlspecialchars($announcement['content']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Trade Updates (Auto Scroll) -->
        <div class="trade-updates">
            <h3>Recent Trade Updates</h3>
            <div class="trade-scroll">
                <ul id="trade-updates-list">
                    <?php foreach ($tradeUpdates as $update): ?>
                        <li>
                            <p><strong><?php echo htmlspecialchars($update['user_a']); ?></strong> traded <strong><?php echo htmlspecialchars($update['item_name']); ?></strong> at <?php echo htmlspecialchars($update['trade_time']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Collapsible Sections for Features, How It Works, and FAQ -->
    <button class="collapsible">Why Choose BarterDB?</button>
    <div class="content">
        <ul>
            <li><strong>Anonymous Exchanges:</strong> Your identity is protected throughout the transaction process.</li>
            <li><strong>Secure Transactions:</strong> BarterDB ensures all trades are processed safely with a unique transaction key.</li>
            <li><strong>Equivalence Table:</strong> Use our value equivalence table to find fair exchanges and balance the value of items being traded.</li>
            <li><strong>Partner Matching:</strong> Our matching system pairs users based on their needs and available items for seamless trades.</li>
            <li><strong>Transparent Costs:</strong> Know all transaction costs upfront, so you can trade with confidence.</li>
        </ul>
    </div>

    <button class="collapsible">How It Works</button>
    <div class="content">
        <p>Here's a quick overview of how to start trading on BarterDB:</p>
        <ol>
            <li>Sign up and create an account, then log in to access the trading dashboard.</li>
            <li>Post items you want to exchange and specify what you are looking for.</li>
            <li>Find matching offers or let our system suggest potential partners based on the Equivalence Table.</li>
            <li>When a match is found, complete the transaction securely through BarterDB's exchange system.</li>
        </ol>
    </div>

    <button class="collapsible">Frequently Asked Questions (FAQ)</button>
    <div class="content">
        <div class="faq-item">
            <h4>Is BarterDB free to use?</h4>
            <p>Yes, signing up and posting items is completely free! Only small transaction costs apply during exchanges, which are displayed upfront.</p>
        </div>
        <div class="faq-item">
            <h4>How is my privacy protected?</h4>
            <p>BarterDB ensures that users remain anonymous throughout transactions. Identifiable information is never shared with other users.</p>
        </div>
        <div class="faq-item">
            <h4>What if I don't find a match right away?</h4>
            <p>If no match is immediately available, your item will remain posted on the bulletin board. You can also adjust your desired item criteria to increase matching possibilities.</p>
        </div>
    </div>

    <?php include '../views/footer.php'; ?>
    <script src="../assets/js/collapsible.js"></script>
    <script src="../assets/js/scrolling.js"></script>
</body>
</html> 