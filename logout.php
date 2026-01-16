<?php
require_once 'config.php';

// Logout Logic
// To logout, we simply invalidate the client-side token by expiring the cookie.
// Since we are not storing server-side session state for this token mechanism,
// clearing the cookie is sufficient to "destroy" the session from the client's perspective.

setcookie(SESSION_COOKIE, '', [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Redirect to login page
header("Location: login.php");
exit;
?>
