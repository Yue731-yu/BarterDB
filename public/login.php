<?php
// login.php
require_once '../controllers/userController.php';

$error = "";  // Variable to hold error messages

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Attempt to log in the user
    if (loginUser($username, $password)) {
        // Redirect to dashboard on successful login
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Login failed. Please check your username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Login</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        
        <p class="auth-footer">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
