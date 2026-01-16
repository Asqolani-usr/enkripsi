<?php
require_once 'functions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Verify credentials via hash comparison
    $user = login_user($username, $password);

    if ($user) {
        // SESSION MANAGEMENT
        // Instead of using default PHP sessions (which store data on server),
        // we are demonstrating a "Client-side Encrypted Session" pattern.

        // 1. Create a session payload
        $sessionPayload = [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'created_at' => time(),
            'expires' => time() + 3600 // 1 hour expiration
        ];

        // 2. Encrypt the session payload using Symmetric Encryption (AES)
        $encryptedToken = encrypt_session_data($sessionPayload);

        // 3. Store the encrypted token in a cookie
        // 'httponly' => true : Prevents JavaScript access (mitigates XSS)
        // 'samesite' => 'Strict' : Mitigates CSRF
        setcookie(SESSION_COOKIE, $encryptedToken, [
            'expires' => time() + 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Strict',
            // 'secure' => true // Uncomment this in production when using HTTPS
        ]);

        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Secure System</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h2>Login</h2>
    <p><i>Password is verified against the stored hash. Session token is generated, encrypted, and sent as a cookie.</i></p>

    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</body>
</html>
