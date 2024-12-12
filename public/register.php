<?php
// register.php
require_once '../controllers/userController.php';

$error = "";  // Variable to hold error messages

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match. Please try again.";
    } else {
        // Attempt to register the user
        if (registerUser($username, $password, $email, $phone, $address)) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Username or email might already exist.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Register</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required>
            
            <label for="email">Email:</label>
            <input type="email" name="email" required>
            
            <label for="phone">Phone:</label>
            <input type="text" name="phone">
            
            <label for="address">Address:</label>
            <input type="text" name="address">
            
            <button type="submit">Register</button>
        </form>
        
        <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
