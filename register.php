<?php
require_once 'functions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Register the user.
        // Password will be hashed inside register_user() before storage.
        $result = register_user($username, $password);
        if ($result === true) {
            $message = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "Error: " . htmlspecialchars($result);
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Secure System</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h2>Register User</h2>
    <p><i>Passwords are hashed (Bcrypt) before storage.</i></p>

    <?php if ($message): ?>
        <p style="color: blue;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
