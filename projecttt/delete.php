<?php
/*
 * File: delete.php
 * Deskripsi: File ini menangani proses penghapusan data dari database.
 */

// Memanggil file koneksi untuk terhubung ke database
require_once 'koneksi.php';

// Ambil ID dari URL, pastikan ID ada dan valid
$id = $_GET['id'] ?? null;

// Jika ID ada, lanjutkan proses hapus
if ($id) {
    // Siapkan perintah SQL untuk menghapus data berdasarkan ID
    $sql = "DELETE FROM peminjaman WHERE id = ?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        // Ikat parameter ID ke perintah SQL
        $stmt->bind_param("i", $id);
        
        // Eksekusi perintah
        if ($stmt->execute()) {
            // Jika berhasil, alihkan kembali ke halaman utama
            header("Location: index.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat mencoba menghapus data.";
        }
        // Tutup statement
        $stmt->close();
    }
} else {
    // Jika tidak ada ID di URL, alihkan ke halaman utama
    header("Location: index.php");
    exit();
}

// Tutup koneksi database
$mysqli->close();
?>
