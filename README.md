# Secure Information System (PHP)

This is a simple PHP application demonstrating modern cryptographic principles for user authentication and session management.

## 1. Cryptography Concepts

### Hashing vs. Encryption
*   **Hashing (One-way):** Used for **Passwords**.
    *   *What it is:* A mathematical function that converts an input (password) into a fixed-size string of characters (hash). It is designed to be irreversible.
    *   *Why:* Even if the database is compromised, the attacker cannot reverse the hash to get the original password.
    *   *Implementation:* We use `password_hash()` (Bcrypt) which automatically handles **Salting** (adding random data to the input before hashing) to prevent Rainbow Table attacks.

*   **Encryption (Two-way):** Used for **Session Tokens**.
    *   *What it is:* A process of encoding information so that only authorized parties can access it using a key. It is reversible (Decryption).
    *   *Why:* We need to read the session data back to know who the user is, but we don't want the client (or an interceptor) to read or tamper with the session data inside the cookie.
    *   *Implementation:* We use **AES-256-CBC** (Advanced Encryption Standard) with a secret key stored on the server.

## 2. Security Flow

1.  **Registration:**
    *   User submits Username and Password.
    *   Server hashes the password using Bcrypt (with salt).
    *   Server stores `username` and `password_hash` in the database.

2.  **Login:**
    *   User submits Username and Password.
    *   Server retrieves the stored hash for that user.
    *   Server verifies the input password against the stored hash using `password_verify()`.
    *   **If valid:**
        *   Server generates a Session Payload (User ID + Expiry Time).
        *   Server **Encrypts** this payload using AES-256.
        *   Server sends the encrypted token as a `HttpOnly` cookie to the client.

3.  **Session Validation (Dashboard):**
    *   Browser sends the cookie with the request.
    *   Server attempts to **Decrypt** the cookie using the secret key.
    *   **If successful:**
        *   Server checks if the session has expired (`expires` > current time).
        *   Access is granted.

4.  **Logout:**
    *   Server instructs the browser to delete the cookie (by setting its expiration date to the past).

## 3. Threat Mitigation

*   **Password Theft (Database Leak):**
    *   *Mitigation:* Hashing with Salt. If the DB is stolen, attackers only see hashes, not real passwords. Bcrypt is slow, making brute-force attacks difficult.

*   **Session Hijacking / Tampering:**
    *   *Mitigation:* Encryption. Since the session token is encrypted with a server-side key, an attacker cannot generate a valid fake token or modify an existing one (e.g., changing User ID from 1 to 2) without corrupting the decryption process.
    *   *Mitigation:* `HttpOnly` Flag. The cookie cannot be accessed via JavaScript, preventing XSS-based token theft.

*   **Replay Attacks (Partial):**
    *   *Mitigation:* Expiration Time. The encrypted token contains an expiry timestamp. Even if an old token is stolen and replayed, it will be invalid after the expiration time passes. (Note: Full replay protection would require nonces or server-side state).

## 4. Setup & Run

1.  Ensure PHP and `php-sqlite3` are installed.
2.  Initialize the database (automatically done on first run of `init_db.php` if you were running manually, but `init_db.php` has been run).
3.  Start the server:
    ```bash
    php -S localhost:8000
    ```
4.  Visit `http://localhost:8000/register.php`.
