<?php
session_start();
include 'includes/koneksi.php';

// Cek apakah user sudah login dan memiliki role yang benar
if (!isset($_SESSION['id_penghuni']) || $_SESSION['role'] !== 'penghuni') {
    header("Location: login.php");
    exit();
}

// Set header untuk mencegah caching halaman
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$id_penghuni = $_SESSION['id_penghuni'];

// Initialize $riwayat variable
$riwayat = null;
$riwayat_count = 0;

// Ambil data kontrak aktif
$kontrakQ = $conn->query("
    SELECT ks.*, k.nomor_kamar, k.tipe, k.harga
    FROM kontraksewa ks
    JOIN kamar k ON ks.id_kamar = k.id_kamar
    WHERE ks.id_penghuni = $id_penghuni AND ks.status = 'aktif'
    LIMIT 1
");
$kontrak = $kontrakQ->fetch_assoc();

if (!$kontrak) {
    echo "<h3 class='text-danger'>Anda tidak memiliki kontrak aktif saat ini.</h3>";
    exit;
}

$id_kontrak = $kontrak['id_kontrak'];
$tgl_mulai = strtotime($kontrak['tanggal_mulai']);
$tgl_selesai = strtotime($kontrak['tanggal_selesai']);
$hari_tersisa = ceil(($tgl_selesai - time()) / 86400);
$bulan_berikut = date('Y-m', strtotime('+1 month', $tgl_selesai));

// Cek apakah sudah bayar bulan berikutnya
$cekPembayaranLanjutan = $conn->query("
    SELECT * FROM pembayaran 
    WHERE id_kontrak = $id_kontrak AND bulan = '$bulan_berikut' AND status = 'lunas'
");
$bolehBayarBulanBerikut = ($cekPembayaranLanjutan->num_rows === 0);

// Ambil data penghuni
$penghuni = $conn->query("SELECT * FROM penghuni WHERE id_penghuni = $id_penghuni")->fetch_assoc();

// Ambil riwayat pembayaran
$riwayat = $conn->query("SELECT * FROM pembayaran WHERE id_kontrak = $id_kontrak ORDER BY bulan ASC");
if ($riwayat) {
    $riwayat_count = $riwayat->num_rows;
} else {
    $riwayat_count = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penghuni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/dashboard_penghuni.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Mencegah halaman di-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
<div class="container mt-4">

    <h2><i class="fas fa-user me-2"></i>Halo, <?= htmlspecialchars($penghuni['nama']) ?>!</h2>
    
    <!-- Sisa waktu -->
    <?php if ($hari_tersisa >= 0): ?>
        <div class="alert alert-info">
            <i class="fas fa-clock me-2"></i>Sisa waktu ngekos Anda: <strong><?= $hari_tersisa ?> hari</strong>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>Masa kos Anda telah <strong>berakhir</strong>.
        </div>
    <?php endif; ?>

    <!-- Tombol bayar bulan berikut -->
    <?php if ($bolehBayarBulanBerikut): ?>
        <a href="bayar_bulan_berikut.php" class="btn btn-primary mb-3">
            <i class="fas fa-credit-card me-2"></i>Bayar Bulan <?= $bulan_berikut ?>
        </a>
    <?php endif; ?>

    <h4><i class="fas fa-home me-2"></i>Informasi Kamar</h4>
    <div class="card mb-4">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nomor Kamar:</strong> <?= $kontrak['nomor_kamar'] ?></li>
                <li class="list-group-item"><strong>Tipe:</strong> <?= $kontrak['tipe'] ?></li>
                <li class="list-group-item"><strong>Harga:</strong> Rp <?= number_format($kontrak['harga']) ?></li>
            </ul>
        </div>
    </div>

    <h4><i class="fas fa-file-contract me-2"></i>Detail Kontrak</h4>
    <div class="card mb-4">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Mulai:</strong> <?= date('d/m/Y', $tgl_mulai) ?></li>
                <li class="list-group-item"><strong>Selesai:</strong> <?= date('d/m/Y', $tgl_selesai) ?></li>
                <li class="list-group-item"><strong>Status:</strong> 
                    <span class="badge bg-<?= $kontrak['status'] === 'aktif' ? 'success' : 'danger' ?>">
                        <?= $kontrak['status'] ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>

    <h4><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h4>
    <?php if ($riwayat_count > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Bulan</th>
                        <th>Tgl Bayar</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $riwayat->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['bulan'] ?? '-' ?></td>
                        <td><?= $row['tanggal_bayar'] ? date('d/m/Y', strtotime($row['tanggal_bayar'])) : '-' ?></td>
                        <td>Rp <?= number_format($row['jumlah']) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $row['status'] === 'lunas' ? 'success' : 
                                ($row['status'] === 'terlambat' ? 'warning' : 'secondary') 
                            ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['metode_pembayaran']): ?>
                                <a href="uploads/<?= $row['metode_pembayaran'] ?>" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye me-1"></i>Lihat Bukti
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle me-2"></i>Belum ada pembayaran yang tercatat.
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-danger" onclick="confirmLogout()">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mencegah halaman di-cache dan kembali setelah logout
(function() {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
})();

// Fungsi konfirmasi logout
function confirmLogout() {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        // Hapus cache dan history
        if (typeof(Storage) !== "undefined") {
            localStorage.clear();
            sessionStorage.clear();
        }
        
        // Redirect ke logout.php
        window.location.replace('logout.php');
    }
}

// Mencegah akses kembali setelah logout
window.addEventListener('beforeunload', function(e) {
    // Hapus cache saat meninggalkan halaman
    if (typeof(Storage) !== "undefined") {
        localStorage.clear();
        sessionStorage.clear();
    }
});
</script>
</body>
</html>