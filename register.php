<?php
session_start();
require 'includes/koneksi.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data dari form
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $gmail = $_POST['gmail'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $id_kamar = $_POST['id_kamar'];
    $tgl_masuk = $_POST['tanggal_masuk'];
    $nik = $_POST['nik'];
    
    // Validasi upload bukti transfer
    $file_tmp = $_FILES['bukti']['tmp_name'];
    $file_name = uniqid() . '_' . $_FILES['bukti']['name'];
    $target = 'uploads/' . $file_name;

    // Validasi input dasar
    if (empty($nama) || empty($no_hp) || empty($gmail) || empty($username) || 
        empty($password) || empty($id_kamar) || empty($tgl_masuk) || empty($nik)) {
        $errors[] = "Semua field harus diisi";
    }

    if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }

    if (empty($errors)) {
        if (move_uploaded_file($file_tmp, $target)) {
            // Hitung tanggal selesai (30 hari)
            $tgl_selesai = date('Y-m-d', strtotime($tgl_masuk . ' +30 days'));

            $conn->begin_transaction();
            try {
                // 1. Simpan ke tabel penghuni
                $stmt = $conn->prepare("INSERT INTO penghuni (nama, nik, no_hp, gmail, username, password) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nama, $nik, $no_hp, $gmail, $username, $password);
                $stmt->execute();
                $id_penghuni = $conn->insert_id;

                // 2. Simpan ke kontrak sewa (status default 'pending')
                $stmt = $conn->prepare("INSERT INTO kontraksewa (id_penghuni, id_kamar, tanggal_mulai, tanggal_selesai, status) 
                                      VALUES (?, ?, ?, ?, 'pending')");
                $stmt->bind_param("iiss", $id_penghuni, $id_kamar, $tgl_masuk, $tgl_selesai);
                $stmt->execute();
                $id_kontrak = $conn->insert_id;

                // 3. Ambil harga kamar
                $stmt = $conn->prepare("SELECT harga FROM kamar WHERE id_kamar = ?");
                $stmt->bind_param("i", $id_kamar);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $jumlah = $row['harga'];

                // 4. Simpan pembayaran
                $bulan_tagihan = date('Y-m', strtotime($tgl_masuk));
                $stmt = $conn->prepare("INSERT INTO pembayaran 
                                    (id_kontrak, bulan, tanggal_bayar, jumlah, metode_pembayaran, status)
                                    VALUES (?, ?, CURDATE(), ?, ?, 'belum')");
                $stmt->bind_param("isds", $id_kontrak, $bulan_tagihan, $jumlah, $file_name);
                $stmt->execute();

                $conn->commit();
                $success = "Pendaftaran berhasil! Mohon tunggu konfirmasi dari pemilik kos.";
            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Gagal menyimpan data: " . $e->getMessage();
                
                // Hapus file yang sudah diupload jika gagal
                if (file_exists($target)) {
                    unlink($target);
                }
            }
        } else {
            $errors[] = "Gagal mengunggah bukti pembayaran. Pastikan file valid dan ukuran tidak terlalu besar.";
        }
    }
}

// Ambil kamar kosong
$kamar_result = $conn->query("SELECT * FROM kamar WHERE status = 'kosong'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pendaftaran Penghuni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .registration-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="registration-container">
        <h2 class="text-center mb-4">Form Pendaftaran Calon Penghuni</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="gmail" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required 
                            autocomplete="off" readonly
                            onfocus="this.removeAttribute('readonly')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required 
                            autocomplete="new-password" readonly
                            onfocus="this.removeAttribute('readonly')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Pilih Kamar</label>
                <select name="id_kamar" class="form-select" required>
                    <option value="">-- Pilih Kamar --</option>
                    <?php while ($row = $kamar_result->fetch_assoc()): ?>
                        <option value="<?= $row['id_kamar'] ?>">
                            <?= htmlspecialchars($row['nomor_kamar']) ?> 
                            (<?= htmlspecialchars($row['tipe']) ?>) - 
                            Rp <?= number_format($row['harga']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Bukti Transfer DP</label>
                <input type="file" name="bukti" class="form-control" accept="image/*,.pdf" required>
                <small class="text-muted">Upload bukti transfer DP (format: JPG, PNG, atau PDF)</small>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Daftar Sekarang</button>
            </div>
        </form>

        <div class="mt-4 text-center">
            <p>Sudah punya akun? <a href="login.php">Login disini</a></p>
            <p><a href="index.php">← Kembali ke Beranda</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Kosongkan field saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="username"]').value = '';
    document.querySelector('input[name="password"]').value = '';
    
    // Set tanggal minimal hari ini
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="tanggal_masuk"]').min = today;
});
</script>
</body>
</html>