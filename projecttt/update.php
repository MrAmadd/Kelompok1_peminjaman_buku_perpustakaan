<?php
/*
 * File: update.php
 * Deskripsi: File ini memiliki dua fungsi:
 * 1. Menampilkan form edit dengan data yang sudah ada (saat diakses via GET).
 * 2. Memproses perubahan data ke database (saat form di-submit via POST).
 */

// Memanggil file koneksi
require_once 'koneksi.php';

// --- PROSES 1: SAAT FORM DI-SUBMIT (METHOD POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $id = $_POST['id'];
    $nama_peminjam = $_POST['nama_peminjam'];
    $judul_buku = $_POST['judul_buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = !empty($_POST['tanggal_kembali']) ? $_POST['tanggal_kembali'] : NULL;
    $status = $_POST['status'];

    // Siapkan perintah SQL untuk update
    $sql = "UPDATE peminjaman SET nama_peminjam=?, judul_buku=?, tanggal_pinjam=?, tanggal_kembali=?, status=? WHERE id=?";
    
    if ($stmt = $mysqli->prepare($sql)) {
        // Ikat semua parameter
        $stmt->bind_param("sssssi", $nama_peminjam, $judul_buku, $tanggal_pinjam, $tanggal_kembali, $status, $id);
        
        // Eksekusi
        if ($stmt->execute()) {
            // Jika berhasil, kembali ke halaman utama
            header("Location: index.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat memperbarui data.";
        }
        $stmt->close();
    }
}

// --- PROSES 2: SAAT HALAMAN DI-LOAD (METHOD GET) ---
// Ambil ID dari URL untuk menampilkan data yang akan diedit
$id_get = $_GET['id'] ?? null;
$data_edit = null;

if ($id_get) {
    $sql = "SELECT * FROM peminjaman WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id_get);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $data_edit = $result->fetch_assoc();
            } else {
                echo "Data tidak ditemukan.";
                exit();
            }
        }
        $stmt->close();
    }
} else {
    // Jika tidak ada ID, kembali ke halaman utama
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Peminjaman</title>
    <!-- Salin CSS dari file index.php agar tampilan konsisten -->
    <style>
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background-color:#f4f7f9;color:#333;line-height:1.6;margin:0;padding:20px}.container{max-width:1000px;margin:0 auto;background:#fff;padding:25px 30px;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,.1)}h1,h2{color:#2c3e50;border-bottom:2px solid #3498db;padding-bottom:10px;margin-bottom:20px}.form-group{margin-bottom:15px}.form-group label{display:block;margin-bottom:5px;font-weight:700;color:#555}.form-group input[type=text],.form-group input[type=date],.form-group select{width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box}.btn{display:inline-block;padding:10px 15px;border-radius:4px;text-decoration:none;color:#fff;font-weight:700;text-align:center;cursor:pointer;border:none}.btn-submit{background-color:#2ecc71}.btn-submit:hover{background-color:#27ae60}.btn-cancel{background-color:#7f8c8d;margin-left:10px}.btn-cancel:hover{background-color:#6c7a7b}.form-container{background-color:#ecf0f1;padding:20px;border-radius:5px;margin-bottom:30px}
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h2>Edit Data Peminjaman</h2>
        <!-- Form ini akan mengirim data ke halaman ini sendiri (update.php) -->
        <form action="update.php" method="post">
            <!-- Input 'hidden' untuk menyimpan ID, ini sangat penting -->
            <input type="hidden" name="id" value="<?php echo $data_edit['id']; ?>">

            <div class="form-group">
                <label for="nama_peminjam">Nama Peminjam</label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" value="<?php echo htmlspecialchars($data_edit['nama_peminjam']); ?>" required>
            </div>
            <div class="form-group">
                <label for="judul_buku">Judul Buku</label>
                <input type="text" name="judul_buku" id="judul_buku" value="<?php echo htmlspecialchars($data_edit['judul_buku']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="<?php echo htmlspecialchars($data_edit['tanggal_pinjam']); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali">Tanggal Kembali</label>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="<?php echo htmlspecialchars($data_edit['tanggal_kembali'] ?? ''); ?>">
            </div>
             <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="Dipinjam" <?php echo ($data_edit['status'] == 'Dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                    <option value="Dikembalikan" <?php echo ($data_edit['status'] == 'Dikembalikan') ? 'selected' : ''; ?>>Dikembalikan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-submit">Update Data</button>
            <a href="index.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</div>
</body>
</html>
