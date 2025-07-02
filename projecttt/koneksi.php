<?php
/*
 * File: koneksi.php
 * Deskripsi: File ini berisi konfigurasi dan inisialisasi koneksi ke database.
 * File ini akan dipanggil oleh file lain yang membutuhkan akses ke database.
 */

// =================================================================
// KONFIGURASI DATABASE
// Sesuaikan nilai di bawah ini dengan konfigurasi server Anda.
// =================================================================
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Ganti dengan username database Anda
define('DB_PASSWORD', '');     // Ganti dengan password database Anda
define('DB_NAME', 'db_perpustakaan'); // Pastikan nama database ini sudah dibuat

// =================================================================
// KONEKSI KE DATABASE MENGGUNAKAN MYSQLI
// =================================================================
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($mysqli->connect_error) {
    // Hentikan eksekusi dan tampilkan pesan error jika koneksi gagal
    die("ERROR: Tidak dapat terhubung ke database. " . $mysqli->connect_error);
}
?>
