<?php
require_once 'functions.php';

$user = null;

// 1. Retrieve the encrypted token from the cookie
if (isset($_COOKIE[SESSION_COOKIE])) {
    $token = $_COOKIE[SESSION_COOKIE];

    // 2. Decrypt the token
    $sessionData = decrypt_session_data($token);

    if ($sessionData) {
        // 3. Validate Session
        // Check if the session has expired
        if (isset($sessionData['expires']) && $sessionData['expires'] > time()) {
            $user = $sessionData;
        } else {
            // Session expired
            // Logic to handle expiration (e.g. force logout) handled by the redirect below
        }
    }
}

// Redirect to login if session is invalid or missing
if (!$user) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Sistem Aman</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($user['username']); ?>!</h1>

    <div style="border: 1px solid #ccc; padding: 15px; background: #f9f9f9;">
        <h3>Informasi Sesi Aman</h3>
        <p>Halaman ini dilindungi. Server menerima cookie sesi terenkripsi Anda, mendekripsinya, dan memverifikasi validitasnya.</p>
        <ul>
            <li><b>User ID:</b> <?php echo htmlspecialchars($user['user_id']); ?></li>
            <li><b>Sesi Dibuat:</b> <?php echo date('Y-m-d H:i:s', $user['created_at']); ?></li>
            <li><b>Sesi Berakhir:</b> <?php echo date('Y-m-d H:i:s', $user['expires']); ?></li>
        </ul>
    </div>

    <br>
    <a href="logout.php"><button>Logout</button></a>
</body>
</html>
