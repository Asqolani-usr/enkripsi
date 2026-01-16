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
        $message = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistem Aman</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h2>Login</h2>
    <p><i>Password diverifikasi dengan hash yang tersimpan. Token sesi dibuat, dienkripsi, dan dikirim sebagai cookie.</i></p>

    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit">Masuk</button>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</body>
</html>
