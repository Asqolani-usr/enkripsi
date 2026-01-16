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
            $message = "Registrasi berhasil! <a href='login.php'>Login di sini</a>";
        } else {
            $message = "Error: " . htmlspecialchars($result);
        }
    } else {
        $message = "Harap isi semua kolom.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - Sistem Aman</title>
    <style>body { font-family: sans-serif; padding: 20px; }</style>
</head>
<body>
    <h2>Registrasi Pengguna</h2>
    <p><i>Password di-hash (Bcrypt) sebelum disimpan.</i></p>

    <?php if ($message): ?>
        <p style="color: blue;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br><br>
        <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>
