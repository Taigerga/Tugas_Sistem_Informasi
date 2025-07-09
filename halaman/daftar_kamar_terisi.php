<?php
include '../includes/koneksi.php';

$limit = 20; // Jumlah kamar per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM kamar WHERE status = 'terisi' ORDER BY nomor_kamar LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

$total = $conn->query("SELECT COUNT(*) as total FROM kamar WHERE status = 'terisi'")->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kamar Terisi - Sistem Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Gunakan style yang sama dengan daftar_kamar.php */
    </style>
</head>
<body>
    <!-- Navbar sama dengan daftar_kamar.php -->
    
    <div class="container container-main">
        <a href="daftar_kamar.php" class="btn btn-primary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        
        <h2 class="text-center mb-4"><i class="bi bi-door-closed"></i> Daftar Lengkap Kamar Terisi</h2>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No. Kamar</th>
                        <th>Tipe</th>
                        <th>Harga</th>
                        <th>Status</th>  
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nomor_kamar']) ?></td>
                                <td><?= htmlspecialchars($row['tipe']) ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?>/bulan</td>
                                <td><span class="badge bg-secondary">Terisi</span></td>  
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada kamar yang tersedia</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>