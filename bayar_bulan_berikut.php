<?php
session_start();
include 'includes/koneksi.php';

if (!isset($_SESSION['id_penghuni']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: login.php");
    exit();
}

$id_penghuni = $_SESSION['id_penghuni'];

// Ambil info kontrak & kamar
$q = $conn->query("
    SELECT ks.id_kontrak, ks.tanggal_selesai, k.harga
    FROM kontraksewa ks
    JOIN kamar k ON ks.id_kamar = k.id_kamar
    WHERE ks.id_penghuni = $id_penghuni AND ks.status = 'aktif'
    LIMIT 1
");
$data = $q->fetch_assoc();

$id_kontrak = $data['id_kontrak'];
$tgl_selesai = $data['tanggal_selesai'];
$harga = $data['harga'];
$bulan_berikut = date('Y-m', strtotime($tgl_selesai . ' +1 month'));

// Cek apakah sudah bayar bulan berikut
$cek = $conn->query("SELECT * FROM pembayaran WHERE id_kontrak = $id_kontrak AND bulan = '$bulan_berikut'");
if ($cek->num_rows > 0) {
    echo "<script>alert('Anda sudah membayar bulan $bulan_berikut'); window.location='dashboard_penghuni.php';</script>";
    exit;
}

// Proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_tmp  = $_FILES['bukti']['tmp_name'];
    $file_name = uniqid() . '_' . $_FILES['bukti']['name'];
    $target    = 'uploads/' . $file_name;

    if (move_uploaded_file($file_tmp, $target)) {
        $stmt = $conn->prepare("INSERT INTO pembayaran 
            (id_kontrak, bulan, tanggal_bayar, jumlah, metode_pembayaran, status) 
            VALUES (?, ?, CURDATE(), ?, ?, 'lunas')");
        $stmt->bind_param("isds", $id_kontrak, $bulan_berikut, $harga, $file_name);
        $stmt->execute();
        // ✅ Perpanjang tanggal selesai kontrak 1 bulan
        $new_selesai = date('Y-m-d', strtotime($tgl_selesai . ' +1 month'));
        $conn->query("UPDATE kontraksewa SET tanggal_selesai = '$new_selesai' WHERE id_kontrak = $id_kontrak");



        echo "<script>alert('Pembayaran bulan $bulan_berikut berhasil!'); window.location='dashboard_penghuni.php';</script>";
        exit;
    } else {
        $error = "Gagal upload bukti pembayaran.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran Bulan <?= $bulan_berikut ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3>💳 Pembayaran Bulan <?= $bulan_berikut ?></h3>
    <p>Jumlah yang harus dibayar: <strong>Rp <?= number_format($harga, 0, ',', '.') ?></strong></p>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label>Upload Bukti Pembayaran:</label>
            <input type="file" name="bukti" class="form-control" accept="image/*,.pdf" required>
        </div>
        <button type="submit" class="btn btn-success">Kirim</button>
        <a href="dashboard_penghuni.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
