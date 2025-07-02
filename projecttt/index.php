<?php
// =================================================================
// KONFIGURASI DATABASE
// Sesuaikan nilai di bawah ini dengan konfigurasi server Anda.
// =================================================================
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Ganti dengan username database Anda
define('DB_PASSWORD', ''); // Ganti dengan password database Anda
define('DB_NAME', 'db_perpustakaan'); // Pastikan nama database ini sudah dibuat

// =================================================================
// KONEKSI KE DATABASE
// =================================================================
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($mysqli->connect_error) {
    die("ERROR: Tidak dapat terhubung ke database. " . $mysqli->connect_error);
}

// =================================================================
// PROSES LOGIKA CRUD
// =================================================================

// Ambil aksi dari URL (untuk Edit dan Delete)
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Proses Tambah & Update Data (Hanya berjalan jika ada data yang dikirim via POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil aksi dari input 'hidden' di dalam form
    $action_post = $_POST['action'] ?? '';

    $nama_peminjam = $_POST['nama_peminjam'];
    $judul_buku = $_POST['judul_buku'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = !empty($_POST['tanggal_kembali']) ? $_POST['tanggal_kembali'] : NULL;
    $status = $_POST['status'];

    if ($action_post == 'create') {
        $sql = "INSERT INTO peminjaman (nama_peminjam, judul_buku, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssss", $nama_peminjam, $judul_buku, $tanggal_pinjam, $tanggal_kembali, $status);
    } elseif ($action_post == 'update') {
        // Ambil ID dari input 'hidden' di dalam form
        $id_post = $_POST['id'] ?? null;
        if ($id_post) {
            $sql = "UPDATE peminjaman SET nama_peminjam=?, judul_buku=?, tanggal_pinjam=?, tanggal_kembali=?, status=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssssi", $nama_peminjam, $judul_buku, $tanggal_pinjam, $tanggal_kembali, $status, $id_post);
        }
    }
    
    // Eksekusi statement jika sudah disiapkan
    if (isset($stmt) && $stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        // Tampilkan error jika ada
        echo "Terjadi kesalahan: " . $mysqli->error;
    }
    $stmt->close();
}

// Proses Hapus Data (Saat link Hapus di-klik)
if ($action == 'delete' && $id) {
    $sql = "DELETE FROM peminjaman WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat menghapus data.";
        }
        $stmt->close();
    }
}

// Ambil data untuk form edit (Saat link Edit di-klik)
$data_edit = null;
if ($action == 'edit' && $id) {
    $sql = "SELECT * FROM peminjaman WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id);
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
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Data Peminjaman Perpustakaan</title>
    <style>
        /* CSS tidak diubah, tetap sama */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f9;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #eaf5fc;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            border: none;
        }
        .btn-submit {
            background-color: #2ecc71;
        }
        .btn-submit:hover {
            background-color: #27ae60;
        }
        .btn-edit {
            background-color: #f39c12;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            margin-left: 5px;
        }
        .btn-cancel {
            background-color: #7f8c8d;
            margin-left: 10px;
        }
        .btn-cancel:hover {
            background-color: #6c7a7b;
        }
        .actions a {
            text-decoration: none;
        }
        .form-container {
            background-color: #ecf0f1;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Sistem Data Peminjaman Perpustakaan</h1>

    <!-- FORM UNTUK TAMBAH / EDIT DATA -->
    <div class="form-container">
        <h2><?php echo $data_edit ? 'Edit Data Peminjaman' : 'Tambah Peminjaman Baru'; ?></h2>
        <form action="index.php" method="post">
            <!-- Hidden inputs untuk menentukan aksi dan id -->
            <input type="hidden" name="action" value="<?php echo $data_edit ? 'update' : 'create'; ?>">
            <input type="hidden" name="id" value="<?php echo $data_edit['id'] ?? ''; ?>">

            <div class="form-group">
                <label for="nama_peminjam">Nama Peminjam</label>
                <input type="text" name="nama_peminjam" id="nama_peminjam" value="<?php echo htmlspecialchars($data_edit['nama_peminjam'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="judul_buku">Judul Buku</label>
                <input type="text" name="judul_buku" id="judul_buku" value="<?php echo htmlspecialchars($data_edit['judul_buku'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="<?php echo htmlspecialchars($data_edit['tanggal_pinjam'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali">Tanggal Kembali</label>
                <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="<?php echo htmlspecialchars($data_edit['tanggal_kembali'] ?? ''); ?>">
            </div>
             <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="Dipinjam" <?php echo (isset($data_edit['status']) && $data_edit['status'] == 'Dipinjam') ? 'selected' : ''; ?>>Dipinjam</option>
                    <option value="Dikembalikan" <?php echo (isset($data_edit['status']) && $data_edit['status'] == 'Dikembalikan') ? 'selected' : ''; ?>>Dikembalikan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-submit"><?php echo $data_edit ? 'Update Data' : 'Simpan Peminjaman'; ?></button>
            <?php if ($data_edit): ?>
                <a href="index.php" class="btn btn-cancel">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- TABEL UNTUK MENAMPILKAN DATA -->
    <h2>Daftar Peminjaman Buku</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil semua data peminjaman
            $sql = "SELECT * FROM peminjaman ORDER BY tanggal_pinjam DESC";
            $result = $mysqli->query($sql);
            
            if ($result && $result->num_rows > 0) {
                $no = 1;
                // Loop untuk menampilkan setiap baris data
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_peminjam']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['judul_buku']) . "</td>";
                    echo "<td>" . date("d-m-Y", strtotime($row['tanggal_pinjam'])) . "</td>";
                    echo "<td>" . ($row['tanggal_kembali'] ? date("d-m-Y", strtotime($row['tanggal_kembali'])) : 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td class='actions'>";
                    // Link untuk Edit, mengirimkan ?action=edit&id=...
                    echo "<a href='index.php?action=edit&id=" . $row['id'] . "' class='btn btn-edit'>Edit</a>";
                    // Link untuk Hapus, mengirimkan ?action=delete&id=...
                    echo "<a href='index.php?action=delete&id=" . $row['id'] . "' class='btn btn-delete' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data peminjaman.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>

<?php
// Tutup koneksi database
$mysqli->close();
?>
