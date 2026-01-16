<?php
require 'config.php';

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    // id: Auto-increment primary key
    // username: Unique identifier
    // password_hash: Stores the hashed password (e.g., via bcrypt).
    //                Note: Salt is included in the hash string by standard PHP functions.
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($query);
    echo "Database initialized successfully at " . DB_PATH . "\n";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
