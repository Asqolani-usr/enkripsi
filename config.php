<?php
// Configuration for the application

// Database file path
define('DB_PATH', __DIR__ . '/database.sqlite');

// Encryption Key for Session Management (AES-256 requires 32 bytes)
// In a real production environment, this should be stored in an environment variable outside the web root.
// We are using a hardcoded key here for demonstration purposes.
define('ENCRYPTION_KEY', hex2bin('200aea919e1ecc08ed71bfc2932e95134c7301c8eedc6fd9ddd691ed765d3a39'));

// Session Cookie Name
define('SESSION_COOKIE', 'secure_session');
?>
