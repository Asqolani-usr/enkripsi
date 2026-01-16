# Sistem Informasi Aman (PHP)

Ini adalah aplikasi PHP sederhana yang mendemonstrasikan prinsip kriptografi modern untuk autentikasi pengguna dan manajemen sesi.

## 1. Konsep Kriptografi

### Hashing vs. Enkripsi
*   **Hashing (Satu Arah):** Digunakan untuk **Password**.
    *   *Apa itu:* Fungsi matematika yang mengubah input (password) menjadi string karakter dengan panjang tetap (hash). Hash dirancang agar tidak dapat dibalikkan (irreversible).
    *   *Mengapa:* Meskipun database diretas, penyerang tidak dapat membalikkan hash untuk mendapatkan password asli.
    *   *Implementasi:* Kami menggunakan `password_hash()` (Bcrypt) yang secara otomatis menangani **Salting** (menambahkan data acak ke input sebelum di-hash) untuk mencegah serangan Rainbow Table.

*   **Enkripsi (Dua Arah):** Digunakan untuk **Token Sesi**.
    *   *Apa itu:* Proses penyandian informasi sehingga hanya pihak yang berwenang yang dapat mengaksesnya menggunakan kunci. Proses ini dapat dibalikkan (Dekripsi).
    *   *Mengapa:* Kita perlu membaca kembali data sesi untuk mengetahui siapa penggunanya, tetapi kita tidak ingin klien (atau penyadap) membaca atau memanipulasi data sesi di dalam cookie.
    *   *Implementasi:* Kami menggunakan **AES-256-CBC** (Advanced Encryption Standard) dengan kunci rahasia yang disimpan di server.

## 2. Alur Keamanan

1.  **Registrasi:**
    *   Pengguna mengirimkan Username dan Password.
    *   Server melakukan hashing password menggunakan Bcrypt (dengan salt).
    *   Server menyimpan `username` dan `password_hash` di database.

2.  **Login:**
    *   Pengguna mengirimkan Username dan Password.
    *   Server mengambil hash yang tersimpan untuk pengguna tersebut.
    *   Server memverifikasi password input dengan hash yang tersimpan menggunakan `password_verify()`.
    *   **Jika valid:**
        *   Server membuat Payload Sesi (User ID + Waktu Kadaluarsa).
        *   Server **Mengenkripsi** payload ini menggunakan AES-256.
        *   Server mengirimkan token terenkripsi sebagai cookie `HttpOnly` ke klien.

3.  **Validasi Sesi (Dashboard):**
    *   Browser mengirimkan cookie dengan setiap permintaan.
    *   Server mencoba **Mendekripsi** cookie menggunakan kunci rahasia.
    *   **Jika berhasil:**
        *   Server memeriksa apakah sesi telah kadaluarsa (`expires` > waktu saat ini).
        *   Akses diberikan.

4.  **Logout:**
    *   Server memerintahkan browser untuk menghapus cookie (dengan mengatur tanggal kadaluarsanya ke masa lalu).

## 3. Mitigasi Ancaman

*   **Pencurian Password (Kebocoran Database):**
    *   *Mitigasi:* Hashing dengan Salt. Jika DB dicuri, penyerang hanya melihat hash, bukan password asli. Bcrypt lambat, membuat serangan brute-force menjadi sulit.

*   **Pembajakan / Manipulasi Sesi:**
    *   *Mitigasi:* Enkripsi. Karena token sesi dienkripsi dengan kunci sisi-server, penyerang tidak dapat membuat token palsu yang valid atau memodifikasi yang sudah ada (misalnya, mengubah User ID dari 1 ke 2) tanpa merusak proses dekripsi.
    *   *Mitigasi:* Flag `HttpOnly`. Cookie tidak dapat diakses melalui JavaScript, mencegah pencurian token berbasis XSS.

*   **Serangan Replay (Parsial):**
    *   *Mitigasi:* Waktu Kadaluarsa. Token terenkripsi berisi timestamp kadaluarsa. Bahkan jika token lama dicuri dan diputar ulang, token tersebut akan tidak valid setelah waktu kadaluarsa berlalu. (Catatan: Perlindungan replay penuh akan memerlukan nonce atau state di sisi server).

## 4. Instalasi & Jalankan

1.  Pastikan PHP dan `php-sqlite3` telah terinstal.
2.  Inisialisasi database (jalankan `php init_db.php` untuk membuat tabel).
3.  Jalankan server:
    ```bash
    php -S localhost:8000
    ```
4.  Kunjungi `http://localhost:8000/register.php`.
