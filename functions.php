<?php
require_once 'config.php';

// Get Database Connection
function get_db_connection() {
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// --------------------------------------------------------------------------
// SYMMETRIC ENCRYPTION (AES-256-CBC)
// Used for Session Token Protection
// --------------------------------------------------------------------------

/**
 * Encrypts data using AES-256-CBC.
 *
 * @param mixed $data The data to encrypt (will be JSON encoded).
 * @return string Base64 encoded string containing IV and Encrypted Data.
 */
function encrypt_session_data($data) {
    $plaintext = json_encode($data);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);

    // Encrypt
    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-cbc',
        ENCRYPTION_KEY,
        OPENSSL_RAW_DATA,
        $iv
    );

    // Combine IV and Ciphertext to allow decryption later
    // IV is not secret, just needs to be unique.
    return base64_encode($iv . $ciphertext);
}

/**
 * Decrypts data using AES-256-CBC.
 *
 * @param string $token The base64 encoded token containing IV and Ciphertext.
 * @return mixed The original data (array/object) or null if failure.
 */
function decrypt_session_data($token) {
    $data = base64_decode($token);
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');

    if (strlen($data) < $ivLength) {
        return null;
    }

    $iv = substr($data, 0, $ivLength);
    $ciphertext = substr($data, $ivLength);

    $decrypted = openssl_decrypt(
        $ciphertext,
        'aes-256-cbc',
        ENCRYPTION_KEY,
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($decrypted === false) {
        return null;
    }

    return json_decode($decrypted, true);
}

// --------------------------------------------------------------------------
// HASHING (Bcrypt)
// Used for Password Storage
// --------------------------------------------------------------------------

/**
 * Registers a new user.
 *
 * @param string $username
 * @param string $password
 * @return bool|string True on success, error message string on failure.
 */
function register_user($username, $password) {
    $pdo = get_db_connection();

    // PASSWORD HASHING
    // password_hash() uses a strong, one-way hashing algorithm (currently Bcrypt by default).
    // It automatically generates a random cryptographically secure salt.
    // The salt is part of the returned hash string, so we don't need to store it separately.
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :hash)");

    try {
        $stmt->execute([':username' => $username, ':hash' => $passwordHash]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation (unique username)
            return "Username already exists.";
        }
        return "Registration error: " . $e->getMessage();
    }
}

/**
 * Authenticates a user.
 *
 * @param string $username
 * @param string $password
 * @return array|false User data array if success, false if failure.
 */
function login_user($username, $password) {
    $pdo = get_db_connection();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // HASH VERIFICATION
        // password_verify() checks the password against the hash.
        // It extracts the salt from the hash string and hashes the input password
        // with that salt to compare.
        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }
    }

    return false;
}
?>
