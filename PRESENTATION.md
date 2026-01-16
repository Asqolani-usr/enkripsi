# Panduan Presentasi Sistem Informasi Aman

Dokumen ini berisi penjelasan mendetail mengenai setiap bagian kode dan alur kerja sistem untuk keperluan presentasi Anda.

---

## 1. Pendahuluan

**Judul Proyek:** Sistem Informasi Sederhana dengan Implementasi Kriptografi Modern (PHP)

**Tujuan:**
Mendemonstrasikan penerapan keamanan data pengguna menggunakan teknik kriptografi yang tepat:
1.  **Hashing** untuk melindungi password (satu arah).
2.  **Enkripsi** untuk melindungi sesi pengguna (dua arah).

---

## 2. Penjelasan Kode (Code Breakdown)

Berikut adalah penjelasan fungsi dari setiap file dalam proyek ini:

### a. `config.php` (Konfigurasi)
*   **Fungsi:** Menyimpan pengaturan dasar yang digunakan di seluruh aplikasi.
*   **Poin Penting:**
    *   `DB_PATH`: Lokasi file database SQLite.
    *   `ENCRYPTION_KEY`: Kunci rahasia untuk algoritma AES-256. Dalam presentasi, tekankan bahwa di sistem nyata, kunci ini harus disimpan di environment server yang aman, bukan di hardcode.
    *   `SESSION_COOKIE`: Nama cookie yang akan disimpan di browser pengguna.

### b. `init_db.php` (Inisialisasi Database)
*   **Fungsi:** Membuat struktur database jika belum ada.
*   **Poin Penting:**
    *   Membuat tabel `users`.
    *   Kolom `password_hash` digunakan untuk menyimpan password yang sudah diacak (hash), bukan password asli (plaintext).

### c. `functions.php` (Inti Logika Kriptografi)
Ini adalah "otak" dari sistem keamanan.
*   **`get_db_connection()`**: Menangani koneksi ke database SQLite.
*   **`encrypt_session_data($data)`**:
    *   Mengubah data sesi (User ID, waktu login) menjadi format JSON.
    *   Membuat **IV (Initialization Vector)** acak agar enkripsi selalu unik meskipun datanya sama.
    *   Mengenkripsi data menggunakan algoritma **AES-256-CBC**.
    *   Hasilnya adalah string acak yang aman.
*   **`decrypt_session_data($token)`**:
    *   Kebalikan dari enkripsi. Menerima token acak, menggunakan kunci rahasia untuk membukanya kembali menjadi data asli (JSON).
*   **`register_user($username, $password)`**:
    *   Menggunakan fungsi `password_hash()` (algoritma Bcrypt).
    *   **Fitur Keamanan:** Otomatis menambahkan "Salt" (data acak tambahan) sehingga dua user dengan password "rahasia" akan memiliki hash yang berbeda.
*   **`login_user($username, $password)`**:
    *   Menggunakan fungsi `password_verify()`.
    *   Fungsi ini mencocokkan password yang diketik saat login dengan hash yang tersimpan di database.

### d. `register.php` (Halaman Pendaftaran)
*   **Fungsi:** Menerima input user baru.
*   **Alur:** Menerima form POST -> Panggil `register_user` -> Simpan ke DB.

### e. `login.php` (Halaman Masuk)
*   **Fungsi:** Memverifikasi identitas pengguna dan membuat sesi aman.
*   **Alur Kunci:**
    1.  Cek username & password.
    2.  Jika benar, buat data sesi (isi: ID user, waktu expired).
    3.  **Enkripsi** data sesi tersebut menjadi token.
    4.  Kirim token ke browser sebagai **Cookie**.

### f. `dashboard.php` (Halaman Terproteksi)
*   **Fungsi:** Halaman yang hanya bisa diakses jika sudah login.
*   **Alur Kunci:**
    1.  Ambil Cookie dari browser.
    2.  **Dekripsi** token.
    3.  Cek apakah token valid dan belum kadaluarsa.
    4.  Jika valid, tampilkan konten. Jika tidak, tendang ke login.

### g. `logout.php` (Keluar)
*   **Fungsi:** Menghapus akses.
*   **Cara kerja:** Menghapus/meng-expire cookie sesi di browser pengguna.

---

## 3. Alur Sistem (Workflow)

Gunakan alur ini saat menjelaskan demo aplikasi:

**Langkah 1: Registrasi**
*   User memasukkan: `Budi` / `password123`.
*   Sistem memproses `password123` dengan Hashing.
*   Database menyimpan: `Budi` / `$2y$10$e8D...` (String acak).
*   *Keamanan:* Admin database pun tidak tahu password asli Budi.

**Langkah 2: Login**
*   User memasukkan: `Budi` / `password123`.
*   Sistem mencocokkan input dengan hash di database.
*   Jika cocok, Sistem membuat tiket sesi: `{"id": 1, "expires": "jam 10"}`.
*   Sistem **Mengenkripsi** tiket ini menjadi: `a8f92b...`.
*   Tiket terenkripsi ini disimpan di browser user (Cookie).

**Langkah 3: Akses Dashboard**
*   Setiap kali user klik menu, browser mengirim tiket `a8f92b...`.
*   Sistem membuka (Dekripsi) tiket tersebut.
*   Sistem membaca: "Oh, ini User ID 1, dan tiketnya masih berlaku."
*   Sistem mengizinkan akses.

**Langkah 4: Logout**
*   Sistem menghancurkan tiket di browser.
*   User tidak bisa lagi mengakses dashboard tanpa login ulang.

---

## 4. Kesimpulan untuk Presentasi

*   Aplikasi ini **tidak menyimpan password asli**, meminimalisir risiko jika database bocor.
*   Aplikasi ini **mengamankan sesi** dengan enkripsi standar industri (AES), mencegah pemalsuan identitas pengguna (Session Hijacking).
