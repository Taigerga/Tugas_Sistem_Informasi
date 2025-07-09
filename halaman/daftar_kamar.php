<?php
// daftar_kamar.php
include '../includes/koneksi.php';

// Konfigurasi pagination
$limit = 10; // Jumlah kamar per halaman
$page_kosong = isset($_GET['page_kosong']) ? (int)$_GET['page_kosong'] : 1;
$page_terisi = isset($_GET['page_terisi']) ? (int)$_GET['page_terisi'] : 1;
$offset_kosong = ($page_kosong - 1) * $limit;
$offset_terisi = ($page_terisi - 1) * $limit;

// Query kamar kosong
$query_kosong = "SELECT * FROM kamar WHERE status = 'kosong' ORDER BY nomor_kamar LIMIT $limit OFFSET $offset_kosong";
$result_kosong = $conn->query($query_kosong);

// Hitung total kamar kosong
$total_kosong = $conn->query("SELECT COUNT(*) as total FROM kamar WHERE status = 'kosong'")->fetch_assoc()['total'];
$total_pages_kosong = ceil($total_kosong / $limit);

// Query kamar terisi
$query_terisi = "SELECT k.*, p.nama AS penghuni 
                FROM kamar k
                LEFT JOIN kontraksewa ks ON k.id_kamar = ks.id_kamar
                LEFT JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
                WHERE k.status = 'terisi' 
                ORDER BY k.nomor_kamar LIMIT $limit OFFSET $offset_terisi";
$result_terisi = $conn->query($query_terisi);

// Hitung total kamar terisi
$total_terisi = $conn->query("SELECT COUNT(*) as total FROM kamar WHERE status = 'terisi'")->fetch_assoc()['total'];
$total_pages_terisi = ceil($total_terisi / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kamar - Sistem Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-custom {
            background-color: #343a40;
        }
        .container-main {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .room-card {
            border-radius: 10px;
            transition: transform 0.3s;
            margin-bottom: 20px;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.1);
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .room-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 10px;
            color: white;
            font-weight: bold;
        }
        .room-body {
            padding: 15px;
        }
        .available {
            background-color: #28a745;
        }
        .occupied {
            background-color: #dc3545;
        }
        .section-title {
            border-left: 4px solid;
            padding-left: 10px;
            margin-bottom: 20px;
        }
        .available-title {
            border-left-color: #28a745;
            color: #28a745;
        }
        .occupied-title {
            border-left-color: #dc3545;
            color: #dc3545;
        }
        .view-all {
            margin-top: 15px;
            text-align: center;
        }
        .pagination .page-item.active .page-link {
            background-color: #343a40;
            border-color: #343a40;
        }
        .pagination .page-link {
            color: #343a40;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="bi bi-house-door"></i> Kosan Campuran
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="daftar_kamar.php">
                            <i class="bi bi-door-closed"></i> Daftar Kamar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="fasilitas.php">
                            <i class="bi bi-list-check"></i> Fasilitas Kamar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container container-main">
        <h2 class="text-center mb-4"><i class="bi bi-door-closed"></i> Daftar Kamar Kos</h2>
        
        <div class="row">
            <!-- Kamar Kosong -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="section-title available-title">
                        <i class="bi bi-check-circle"></i> Kamar Tersedia
                    </h4>
                    <small class="text-muted">Total: <?= $total_kosong ?> kamar</small>
                </div>
                
                <div class="row">
                    <?php if ($result_kosong->num_rows > 0): ?>
                        <?php while ($row = $result_kosong->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card room-card h-100">
                                    <div class="room-header available">
                                        Kamar <?= htmlspecialchars($row['nomor_kamar']) ?> - <?= htmlspecialchars($row['tipe']) ?>
                                    </div>
                                    <div class="room-body">
                                        <p><strong>Harga:</strong> Rp <?= number_format($row['harga'], 0, ',', '.') ?>/bulan</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-success">Tersedia</span>
                                            <a href="login.php" class="btn btn-sm btn-outline-primary">Sewa Sekarang</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Semua kamar sudah terisi.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination dan View All -->
                <?php if ($total_kosong > $limit): ?>
                    <nav aria-label="Page navigation kamar kosong">
                        <ul class="pagination pagination-sm justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages_kosong; $i++): ?>
                                <li class="page-item <?= $i == $page_kosong ? 'active' : '' ?>">
                                    <a class="page-link" href="?page_kosong=<?= $i ?>&page_terisi=<?= $page_terisi ?>#kamar-kosong"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <div class="view-all">
                        <a href="daftar_kamar_kosong.php" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-list-ul"></i> Lihat Semua Kamar Kosong
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Kamar Terisi -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="section-title occupied-title">
                        <i class="bi bi-x-circle"></i> Kamar Terisi
                    </h4>
                    <small class="text-muted">Total: <?= $total_terisi ?> kamar</small>
                </div>
                
                <div class="row">
                    <?php if ($result_terisi->num_rows > 0): ?>
                        <?php while ($row = $result_terisi->fetch_assoc()): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card room-card h-100">
                                    <div class="room-header occupied">
                                        Kamar <?= htmlspecialchars($row['nomor_kamar']) ?> - <?= htmlspecialchars($row['tipe']) ?>
                                    </div>
                                    <div class="room-body">
                                        <p><strong>Harga:</strong> Rp <?= number_format($row['harga'], 0, ',', '.') ?>/bulan</p>
                                        <?php if (!empty($row['penghuni'])): ?>
                                            <p><strong>Penghuni:</strong> <?= htmlspecialchars($row['penghuni']) ?></p>
                                        <?php endif; ?>
                                        <span class="badge bg-danger">Terisi</span>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Tidak ada kamar yang terisi saat ini.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination dan View All -->
                <?php if ($total_terisi > $limit): ?>
                    <nav aria-label="Page navigation kamar terisi">
                        <ul class="pagination pagination-sm justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages_terisi; $i++): ?>
                                <li class="page-item <?= $i == $page_terisi ? 'active' : '' ?>">
                                    <a class="page-link" href="?page_kosong=<?= $page_kosong ?>&page_terisi=<?= $i ?>#kamar-terisi"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <div class="view-all">
                        <a href="daftar_kamar_terisi.php" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-list-ul"></i> Lihat Semua Kamar Terisi
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>