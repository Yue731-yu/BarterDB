<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarterDB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header>
    <div class="header-container">
        <!-- Left section for logo -->
        <div class="header-left">
            <a href="https://www.uidaho.edu/" class="logo-link">
                <img src="../assets/images/logo.png" alt="BarterDB Logo" class="logo">
            </a>
        </div>
        
        <!-- Center section for title, now linking to index.php -->
        <div class="header-center">
            <a href="../public/index.php" class="title-link">
                <h1>Barter Database Management System</h1>
            </a>
        </div>
        
        <!-- Right section for navigation -->
        <div class="header-right">
            <nav>
                <ul>
                    <?php
                    // Only start session if it has not already been started
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Check user role and display appropriate navigation
                    if (isset($_SESSION['role'])) {
                        $role = $_SESSION['role'];
                        if ($role === 'admin') {
                            // Admin view: only Logout
                            echo '<li><a href="../controllers/userController.php?logout=1">Logout</a></li>';
                        } elseif ($role === 'user') {
                            // User view: full navigation
                            echo '
                                <li><a href="../public/dashboard.php">Dashboard</a></li>
                                <li><a href="../public/post_item.php">Post Item</a></li>
                                <li><a href="../public/match_trade.php">Find Trade</a></li>
                                <li><a href="../public/transaction_history.php">Transaction History</a></li>
                                <li><a href="../controllers/userController.php?logout=1">Logout</a></li>
                            ';
                        }
                    } else {
                        // Guest view: redirect to login
                        header("Location: ../public/login.php");
                        exit();
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
</header>

<main>