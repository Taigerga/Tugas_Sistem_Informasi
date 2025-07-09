<?php
session_start();
include 'includes/koneksi.php';
require_once 'notifikasi_email.php';

if (!isset($_SESSION['id_pemilik']) || $_SESSION['role'] !== 'pemilik') {
    header("Location: login.php");
    exit();
}

// Cegah cache agar tidak bisa back setelah logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Tambah kamar
if (isset($_POST['tambah_kamar'])) {
    $nomor = $_POST['nomor_kamar'];
    $tipe = $_POST['tipe'];
    $harga = $_POST['harga'];

    $stmt = $conn->prepare("INSERT INTO kamar (nomor_kamar, tipe, harga, status) VALUES (?, ?, ?, 'kosong')");
    $stmt->bind_param("ssd", $nomor, $tipe, $harga);
    $stmt->execute();
    header("Location: dashboard_pemilik.php#kamar");
    exit();
}

// Hapus kamar
if (isset($_GET['hapus_kamar'])) {
    $id = $_GET['hapus_kamar'];
    $conn->query("DELETE FROM kamar WHERE id_kamar = $id");
    header("Location: dashboard_pemilik.php#kamar");
    exit();
}

// Edit kamar
if (isset($_POST['edit_kamar'])) {
    $id = $_POST['id_kamar'];
    $nomor = $_POST['edit_nomor_kamar'];
    $tipe = $_POST['edit_tipe'];
    $harga = $_POST['edit_harga'];
    $status = $_POST['edit_status'];
    
    $stmt = $conn->prepare("UPDATE kamar SET nomor_kamar=?, tipe=?, harga=?, status=? WHERE id_kamar=?");
    $stmt->bind_param("ssdsi", $nomor, $tipe, $harga, $status, $id);
    $stmt->execute();
    
    header("Location: dashboard_pemilik.php#kamar");
    exit();
}

// Tambah kontrak sewa
if (isset($_POST['tambah_kontrak'])) {
    $id_penghuni = $_POST['id_penghuni'];
    $id_kamar = $_POST['id_kamar'];
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];

    $conn->query("INSERT INTO kontraksewa (id_penghuni, id_kamar, tanggal_mulai, tanggal_selesai, status) VALUES ($id_penghuni, $id_kamar, '$mulai', '$selesai', 'pending')");
    header("Location: dashboard_pemilik.php#kontrak");
    exit();
}

// Hapus kontrak
if (isset($_GET['hapus_kontrak'])) {
    $id = $_GET['hapus_kontrak'];
    $conn->query("DELETE FROM kontraksewa WHERE id_kontrak = $id");
    header("Location: dashboard_pemilik.php#kontrak");
    exit();
}

// Edit kontrak
if (isset($_POST['edit_kontrak'])) {
    $id_kontrak = $_POST['id_kontrak'];
    $tanggal_mulai = $_POST['edit_tanggal_mulai'];
    $tanggal_selesai = $_POST['edit_tanggal_selesai'];
    $status = $_POST['edit_status_kontrak'];

    $stmt = $conn->prepare("UPDATE kontraksewa SET tanggal_mulai=?, tanggal_selesai=?, status=? WHERE id_kontrak=?");
    $stmt->bind_param("sssi", $tanggal_mulai, $tanggal_selesai, $status, $id_kontrak);
    $stmt->execute();
    header("Location: dashboard_pemilik.php#kontrak");
    exit();
}

// Hapus penghuni
if (isset($_GET['hapus_penghuni'])) {
    $id = $_GET['hapus_penghuni'];
    $conn->query("DELETE FROM penghuni WHERE id_penghuni = $id");
    header("Location: dashboard_pemilik.php#penghuni");
    exit();
}

// Edit penghuni
if (isset($_POST['edit_penghuni'])) {
    $id = $_POST['id_penghuni'];
    $nama = $_POST['edit_nama'];
    $nik = $_POST['edit_nik'];
    $no_hp = $_POST['edit_no_hp'];
    $gmail = $_POST['edit_gmail'];

    $stmt = $conn->prepare("UPDATE penghuni SET nama=?, nik=?, no_hp=?, gmail=? WHERE id_penghuni=?");
    $stmt->bind_param("ssssi", $nama, $nik, $no_hp, $gmail, $id);
    $stmt->execute();
    header("Location: dashboard_pemilik.php#penghuni");
    exit();
}

// Tambah penghuni
if (isset($_POST['tambah_penghuni'])) {
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $no_hp = $_POST['no_hp'];
    $gmail = $_POST['gmail'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO penghuni (nama, nik, no_hp, gmail, username, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nama, $nik, $no_hp, $gmail, $username, $password);
    $stmt->execute();
    header("Location: dashboard_pemilik.php#penghuni");
    exit();
}

// Hapus pembayaran
if (isset($_GET['hapus_pembayaran'])) {
    $id = $_GET['hapus_pembayaran'];
    $conn->query("DELETE FROM pembayaran WHERE id_pembayaran = $id");
    header("Location: dashboard_pemilik.php#pembayaran");
    exit();
}

// Setujui calon penghuni
if (isset($_GET['approve'])) {
    $id_kontrak = $_GET['approve'];

    // Update status kontrak
    $conn->query("UPDATE kontraksewa SET status = 'aktif' WHERE id_kontrak = $id_kontrak");

    // Ambil data kontrak
    $res = $conn->query("SELECT id_kamar, id_penghuni FROM kontraksewa WHERE id_kontrak = $id_kontrak");
    if ($row = $res->fetch_assoc()) {
        $id_kamar = $row['id_kamar'];
        $id_penghuni = $row['id_penghuni'];
        
        // Ubah status kamar ke 'terisi'
        $conn->query("UPDATE kamar SET status = 'terisi' WHERE id_kamar = $id_kamar");

        // Update status pembayaran jadi lunas jika ada bukti pembayaran
        $cek_bukti = $conn->query("SELECT id_pembayaran FROM pembayaran WHERE id_kontrak = $id_kontrak AND metode_pembayaran IS NOT NULL");
        if ($cek_bukti->num_rows > 0) {
            $conn->query("UPDATE pembayaran SET status = 'lunas' WHERE id_kontrak = $id_kontrak");
        }

        // Kirim notifikasi email
        $result = $conn->query("SELECT gmail FROM penghuni WHERE id_penghuni = $id_penghuni");
        $data = $result->fetch_assoc();
        $email = $data['gmail'];

        $subjek = "Pendaftaran Kosan Disetujui";
        $pesan = "<p>Selamat! Pendaftaran Anda telah <strong>disetujui</strong>. Silakan login ke sistem untuk melihat detail kontrak sewa.</p>";
        kirimEmail($email, $subjek, $pesan);
    }

    header("Location: dashboard_pemilik.php#calon");
    exit();
}

// Tolak calon penghuni
if (isset($_GET['reject'])) {
    $id_kontrak = $_GET['reject'];

    // Ambil data kontrak untuk mendapatkan email
    $res = $conn->query("SELECT p.gmail 
                        FROM kontraksewa ks
                        JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
                        WHERE ks.id_kontrak = $id_kontrak");
    $data = $res->fetch_assoc();
    $email = $data['gmail'];

    // Hapus pembayaran terkait
    $conn->query("DELETE FROM pembayaran WHERE id_kontrak = $id_kontrak");

    // Hapus kontrak
    $conn->query("DELETE FROM kontraksewa WHERE id_kontrak = $id_kontrak");

    // Kirim notifikasi email
    $subjek = "Pendaftaran Kosan Ditolak";
    $pesan = "<p>Mohon maaf, pendaftaran Anda <strong>ditolak</strong>. Silakan hubungi admin untuk informasi lebih lanjut.</p>";
    kirimEmail($email, $subjek, $pesan);

    header("Location: dashboard_pemilik.php#calon");
    exit();
}

// Search functionality
$search_calon = isset($_GET['search_calon']) ? $_GET['search_calon'] : '';
$search_kamar = isset($_GET['search_kamar']) ? $_GET['search_kamar'] : '';
$search_kontrak = isset($_GET['search_kontrak']) ? $_GET['search_kontrak'] : '';
$search_pembayaran = isset($_GET['search_pembayaran']) ? $_GET['search_pembayaran'] : '';
$search_penghuni = isset($_GET['search_penghuni']) ? $_GET['search_penghuni'] : '';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik - Sistem Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/dashboard_pemilik.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
<div class="container container-main">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-house-door"></i> Dashboard Pemilik Kos</h2>
        <a href="logout.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- TAB NAVIGATION -->
    <ul class="nav nav-tabs mb-4" id="pemilikTabs" role="tablist">
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#calon">Calon Penghuni</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#kamar">Kamar</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#kontrak">Kontrak Sewa</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pembayaran">Pembayaran</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#penghuni">Penghuni Aktif</a></li>
    </ul>

    <div class="tab-content">
        <!-- === Calon Penghuni === -->
        <div class="tab-pane fade" id="calon">
            <div class="d-flex justify-content-between align-items-center mb-4">
            <h4><i class="bi bi-people-fill"></i> Daftar Calon Penghuni</h4>
                <form class="d-flex" method="GET" action="#calon">
                    <input type="text" name="search_calon" class="form-control me-2" placeholder="Cari nama..." value="<?= htmlspecialchars($search_calon) ?>">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                    <?php if($search_calon): ?>
                        <a href="dashboard_pemilik.php#calon" class="btn btn-outline-secondary ms-2">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            <?php
                $limit = 10;
                $page = isset($_GET['hal_calon']) ? (int)$_GET['hal_calon'] : 1;
                $offset = ($page - 1) * $limit;

                // Query for calon penghuni with search
                $calon_query = "SELECT 
                                p.id_penghuni, 
                                p.nama, 
                                p.nik, 
                                p.gmail, 
                                p.no_hp,
                                k.nomor_kamar, 
                                k.tipe, 
                                k.harga,
                                ks.id_kontrak,
                                ks.tanggal_mulai,
                                pb.jumlah, 
                                pb.metode_pembayaran,
                                pb.status as status_pembayaran
                            FROM kontraksewa ks
                            JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
                            JOIN kamar k ON ks.id_kamar = k.id_kamar
                            LEFT JOIN pembayaran pb ON pb.id_kontrak = ks.id_kontrak
                            WHERE ks.status = 'pending'";
                
                if ($search_calon) {
                    $calon_query .= " AND p.nama LIKE '%$search_calon%'";
                }
                
                $calon_query .= " GROUP BY ks.id_kontrak LIMIT $limit OFFSET $offset";
                
                $calon = $conn->query($calon_query);
                
                // Count total with search
                $totalQuery = $conn->query("SELECT COUNT(DISTINCT ks.id_kontrak) as total 
                                          FROM kontraksewa ks
                                          JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
                                          WHERE ks.status = 'pending'" . 
                                          ($search_calon ? " AND p.nama LIKE '%$search_calon%'" : ""));
                $totalData = $totalQuery->fetch_assoc()['total'];
                $totalPages = ceil($totalData / $limit);

            ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kamar</th>
                            <th>Harga</th>
                            <th>DP</th>
                            <th>Status DP</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($calon->num_rows > 0) {
                        while ($row = $calon->fetch_assoc()): 
                            $status_dp_class = '';
                            if ($row['status_pembayaran'] === 'lunas') {
                                $status_dp_class = 'bg-success text-white';
                            } elseif ($row['status_pembayaran'] === 'terlambat') {
                                $status_dp_class = 'bg-danger text-white';
                            } else {
                                $status_dp_class = 'bg-dark text-white';
                            }
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['nik']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($row['nomor_kamar']) ?></span>
                                    <?= htmlspecialchars($row['tipe']) ?>
                                </td>
                                <td class="text-end">Rp <?= number_format($row['harga']) ?></td>
                                <td class="text-end">
                                    <?= $row['jumlah'] ? 'Rp ' . number_format($row['jumlah']) : '-' ?>
                                </td>
                                <td>
                                    <?php if ($row['jumlah']): ?>
                                        <span class="badge <?= $status_dp_class ?>">
                                            <?= ucfirst($row['status_pembayaran'] ?? 'belum') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['metode_pembayaran'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($row['metode_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-file-earmark-image"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="?approve=<?= $row['id_kontrak'] ?>" class="btn btn-sm btn-success">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </a>
                                        <a href="?reject=<?= $row['id_kontrak'] ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle"></i> Tolak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada calon penghuni</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="dashboard_pemilik.php#calon" onclick="goToPageCalon(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>                
            </div>
        </div>

        <!-- === Kamar === -->
        <div class="tab-pane fade" id="kamar">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-door-closed-fill"></i> Manajemen Kamar</h4>
                <div>
                    <form class="d-inline me-2" method="GET" action="#kamar">
                        <input type="text" name="search_kamar" class="form-control d-inline-block" style="width: 200px;" placeholder="Cari nomor/tipe..." value="<?= htmlspecialchars($search_kamar) ?>">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                        <?php if($search_kamar): ?>
                            <a href="dashboard_pemilik.php#kamar" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKamarModal">
                        <i class="bi bi-plus-circle"></i> Tambah Kamar
                    </button>
                </div>
            </div>

            <!-- Modal Tambah Kamar -->
            <div class="modal fade" id="tambahKamarModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Kamar Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nomor_kamar" class="form-label">Nomor Kamar</label>
                                    <input type="text" class="form-control" id="nomor_kamar" name="nomor_kamar" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tipe" class="form-label">Tipe Kamar</label>
                                    <select class="form-select" id="tipe" name="tipe" required>
                                        <option value="Standar">Standar</option>
                                        <option value="Deluxe">Deluxe</option>
                                        <option value="VIP">VIP</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga" name="harga" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="tambah_kamar" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php 
            $limit = 10;
            $page = isset($_GET['hal_kamar']) ? (int)$_GET['hal_kamar'] : 1;
            $offset = ($page - 1) * $limit;

            // Query for kamar with search
            $kamar_query = "SELECT * FROM kamar";
            if ($search_kamar) {
                $kamar_query .= " WHERE nomor_kamar LIKE '%$search_kamar%' OR tipe LIKE '%$search_kamar%'";
            }
            $kamar_query .= " ORDER BY nomor_kamar LIMIT $limit OFFSET $offset";
            
            $kamar = $conn->query($kamar_query);
            
            // Count total with search
            $total_query = $conn->query("SELECT COUNT(*) AS total FROM kamar" . 
                                      ($search_kamar ? " WHERE nomor_kamar LIKE '%$search_kamar%' OR tipe LIKE '%$search_kamar%'" : ""));
            $total_kamar = $total_query->fetch_assoc()['total'];
            $total_pages = ceil($total_kamar / $limit);
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor</th>
                            <th>Tipe</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($kamar->num_rows > 0) {
                        while ($k = $kamar->fetch_assoc()): 
                            $status_class = $k['status'] === 'terisi' ? 'bg-danger' : 'bg-success';
                        ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="id_kamar" value="<?= $k['id_kamar'] ?>">
                                    <td>
                                        <input type="text" name="edit_nomor_kamar" value="<?= htmlspecialchars($k['nomor_kamar']) ?>" class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <select name="edit_tipe" class="form-select form-select-sm">
                                            <option value="Standar" <?= $k['tipe'] === 'Standar' ? 'selected' : '' ?>>Standar</option>
                                            <option value="Deluxe" <?= $k['tipe'] === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                                            <option value="VIP" <?= $k['tipe'] === 'VIP' ? 'selected' : '' ?>>VIP</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="edit_harga" value="<?= $k['harga'] ?>" class="form-control">
                                        </div>
                                    </td>
                                    <td>
                                    <select name="edit_status" class="form-select form-select-sm">
                                    <option value="kosong" <?= $k['status'] === 'kosong' ? 'selected' : '' ?>>Kosong</option>
                                    <option value="terisi" <?= $k['status'] === 'terisi' ? 'selected' : '' ?>>Terisi</option>
                                    </select>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="edit_kamar" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <a href="?hapus_kamar=<?= $k['id_kamar'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kamar ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data kamar</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="dashboard_pemilik.php#kamar" onclick="goToPageKamar(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- === Kontrak Sewa === -->
        <div class="tab-pane fade" id="kontrak">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-file-earmark-text-fill"></i> Manajemen Kontrak Sewa</h4>
                <div>
                    <form class="d-inline me-2" method="GET" action="#kontrak">
                        <input type="text" name="search_kontrak" class="form-control d-inline-block" style="width: 200px;" placeholder="Cari nama/no kamar..." value="<?= htmlspecialchars($search_kontrak) ?>">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                        <?php if($search_kontrak): ?>
                            <a href="dashboard_pemilik.php#kontrak" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKontrakModal">
                        <i class="bi bi-plus-circle"></i> Tambah Kontrak
                    </button>
                </div>
            </div>

            <!-- Modal Tambah Kontrak -->
            <div class="modal fade" id="tambahKontrakModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Buat Kontrak Sewa Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="id_penghuni" class="form-label">Penghuni</label>
                                    <select class="form-select" id="id_penghuni" name="id_penghuni" required>
                                        <option value="">-- Pilih Penghuni --</option>
                                        <?php
                                        $penghuni_list = $conn->query("SELECT * FROM penghuni");
                                        while ($p = $penghuni_list->fetch_assoc()): ?>
                                            <option value="<?= $p['id_penghuni'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_kamar" class="form-label">Kamar</label>
                                    <select class="form-select" id="id_kamar" name="id_kamar" required>
                                        <option value="">-- Pilih Kamar --</option>
                                        <?php
                                        $kamar_list = $conn->query("SELECT * FROM kamar WHERE status = 'kosong'");
                                        while ($k = $kamar_list->fetch_assoc()): ?>
                                            <option value="<?= $k['id_kamar'] ?>">
                                                <?= htmlspecialchars($k['nomor_kamar']) ?> (<?= htmlspecialchars($k['tipe']) ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="tambah_kontrak" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php
                $limit = 10;
                $page = isset($_GET['hal_kontrak']) ? (int)$_GET['hal_kontrak'] : 1;
                $offset = ($page - 1) * $limit;

                // Query for kontrak with search
                $kontrak_query = "SELECT ks.*, p.nama AS penghuni, k.nomor_kamar, k.tipe 
                                FROM kontraksewa ks 
                                JOIN penghuni p ON ks.id_penghuni = p.id_penghuni 
                                JOIN kamar k ON ks.id_kamar = k.id_kamar";
                
                if ($search_kontrak) {
                    $kontrak_query .= " WHERE p.nama LIKE '%$search_kontrak%' OR k.nomor_kamar LIKE '%$search_kontrak%'";
                }
                
                $kontrak_query .= " ORDER BY ks.status, ks.tanggal_mulai DESC LIMIT $limit OFFSET $offset";
                
                $kontrak = $conn->query($kontrak_query);
                
                // Count total with search
                $total_query = $conn->query("SELECT COUNT(*) AS total 
                                           FROM kontraksewa ks 
                                           JOIN penghuni p ON ks.id_penghuni = p.id_penghuni" . 
                                           ($search_kontrak ? " WHERE p.nama LIKE '%$search_kontrak%'" : ""));
                $total_kontrak = $total_query->fetch_assoc()['total'];
                $total_pages = ceil($total_kontrak / $limit);
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Penghuni</th>
                            <th>Kamar</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($kontrak->num_rows > 0) {
                        while ($ks = $kontrak->fetch_assoc()): 
                            $status_class = 'bg-secondary';
                            if ($ks['status'] === 'aktif') $status_class = 'bg-success';
                            elseif ($ks['status'] === 'pending') $status_class = 'bg-warning';
                        ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="id_kontrak" value="<?= $ks['id_kontrak'] ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($ks['penghuni']) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($ks['nomor_kamar']) ?></span>
                                        <?= htmlspecialchars($ks['tipe']) ?>
                                    </td>
                                    <td>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="date" name="edit_tanggal_mulai" value="<?= $ks['tanggal_mulai'] ?>" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="date" name="edit_tanggal_selesai" value="<?= $ks['tanggal_selesai'] ?>" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select name="edit_status_kontrak" class="form-select form-select-sm">
                                            <option value="pending" <?= $ks['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="aktif" <?= $ks['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="nonaktif" <?= $ks['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="edit_kontrak" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <a href="?hapus_kontrak=<?= $ks['id_kontrak'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kontrak ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data kontrak</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="dashboard_pemilik.php#kontrak" onclick="goToPageKontrak(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
               
            </div>
        </div>

        <!-- === Pembayaran === -->
        <div class="tab-pane fade" id="pembayaran">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-cash-stack"></i> Riwayat Pembayaran</h4>
                <form class="d-flex" method="GET" action="#pembayaran">
                    <input type="text" name="search_pembayaran" class="form-control me-2" placeholder="Cari nama/tanggal..." value="<?= htmlspecialchars($search_pembayaran) ?>">
                    <button class="btn btn-outline-primary" type="submit">Cari</button>
                    <?php if($search_pembayaran): ?>
                        <a href="dashboard_pemilik.php#pembayaran" class="btn btn-outline-secondary ms-2">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <?php
            $limit = 10;
            $page = isset($_GET['hal_bayar']) ? (int)$_GET['hal_bayar'] : 1;
            $offset = ($page - 1) * $limit;

            // Query for pembayaran with search
            $bayar_query = "SELECT pb.*, p.nama AS penghuni, k.nomor_kamar
                          FROM pembayaran pb
                          JOIN kontraksewa ks ON pb.id_kontrak = ks.id_kontrak
                          JOIN penghuni p ON ks.id_penghuni = p.id_penghuni
                          JOIN kamar k ON ks.id_kamar = k.id_kamar";
            
            if ($search_pembayaran) {
                $bayar_query .= " WHERE p.nama LIKE '%$search_pembayaran%' OR pb.tanggal_bayar LIKE '%$search_pembayaran%'";
            }
            
            $bayar_query .= " ORDER BY pb.tanggal_bayar DESC LIMIT $limit OFFSET $offset";
            
            $bayar = $conn->query($bayar_query);
            
            // Count total with search
            $total_query = $conn->query("SELECT COUNT(*) AS total 
                                       FROM pembayaran pb
                                       JOIN kontraksewa ks ON pb.id_kontrak = ks.id_kontrak
                                       JOIN penghuni p ON ks.id_penghuni = p.id_penghuni" . 
                                       ($search_pembayaran ? " WHERE p.nama LIKE '%$search_pembayaran%'" : ""));
            $total_bayar = $total_query->fetch_assoc()['total'];
            $total_pages = ceil($total_bayar / $limit);
            ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Penghuni</th>
                            <th>Kamar</th>
                            <th>Bulan</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($bayar->num_rows > 0) {
                        while ($b = $bayar->fetch_assoc()): 
                            switch ($b['status']) {
                                case 'lunas':
                                    $status_class = 'bg-success text-white';
                                    break;
                                case 'terlambat':
                                    $status_class = 'bg-danger text-white';
                                    break;
                                case 'belum':
                                default:
                                    $status_class = 'bg-dark text-white';
                                    break;
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($b['penghuni']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($b['nomor_kamar']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($b['bulan']) ?></td>
                                <td><?= date('d M Y', strtotime($b['tanggal_bayar'])) ?></td>
                                <td class="text-end">Rp <?= number_format($b['jumlah']) ?></td>
                                <td>
                                    <span class="badge <?= $status_class ?>">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($b['metode_pembayaran'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($b['metode_pembayaran']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-receipt"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?hapus_pembayaran=<?= $b['id_pembayaran'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data pembayaran ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data pembayaran</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="dashboard_pemilik.php#pembayaran" onclick="goToPagePembayaran(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- === Penghuni Aktif === -->
        <div class="tab-pane fade" id="penghuni">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-people-fill"></i> Manajemen Penghuni</h4>
                <div>
                    <form class="d-inline me-2" method="GET" action="#penghuni">
                        <input type="text" name="search_penghuni" class="form-control d-inline-block" style="width: 200px;" placeholder="Cari nama..." value="<?= htmlspecialchars($search_penghuni) ?>">
                        <button class="btn btn-outline-primary" type="submit">Cari</button>
                        <?php if($search_penghuni): ?>
                            <a href="dashboard_pemilik.php#penghuni" class="btn btn-outline-secondary">Reset</a>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPenghuniModal">
                        <i class="bi bi-plus-circle"></i> Tambah Penghuni
                    </button>
                </div>
            </div>

            <!-- Modal Tambah Penghuni -->
            <div class="modal fade" id="tambahPenghuniModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Penghuni Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                                </div>
                                <div class="mb-3">
                                    <label for="gmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="gmail" name="gmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" name="tambah_penghuni" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php 
            $limit = 10;
            $page = isset($_GET['hal_penghuni']) ? (int)$_GET['hal_penghuni'] : 1;
            $offset = ($page - 1) * $limit;

            // Query for penghuni with search
            $penghuni_query = "SELECT * FROM penghuni";
            if ($search_penghuni) {
                $penghuni_query .= " WHERE nama LIKE '%$search_penghuni%' OR username LIKE '%$search_penghuni%' OR nik LIKE '%$search_penghuni%'";
            }
            $penghuni_query .= " ORDER BY nama LIMIT $limit OFFSET $offset";
            
            $penghuni = $conn->query($penghuni_query);
            
            // Count total with search
            $total_query = $conn->query("SELECT COUNT(*) AS total FROM penghuni" . 
                                      ($search_penghuni ? " WHERE nama LIKE '%$search_penghuni%' OR username LIKE '%$search_penghuni%'" : ""));
            $total_penghuni = $total_query->fetch_assoc()['total'];
            $total_pages = ceil($total_penghuni / $limit);
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Identitas</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if ($penghuni->num_rows > 0) {
                        while ($p = $penghuni->fetch_assoc()): ?>
                            <tr>
                                <form method="POST">
                                    <input type="hidden" name="id_penghuni" value="<?= $p['id_penghuni'] ?>">
                                    <td>
                                        <input type="text" name="edit_nama" value="<?= htmlspecialchars($p['nama']) ?>" class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input type="text" name="edit_no_hp" value="<?= htmlspecialchars($p['no_hp']) ?>" class="form-control form-control-sm" placeholder="No HP">
                                            </div>
                                            <div class="col-12">
                                                <input type="email" name="edit_gmail" value="<?= htmlspecialchars($p['gmail']) ?>" class="form-control form-control-sm" placeholder="Email">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="edit_nik" value="<?= htmlspecialchars($p['nik']) ?>" class="form-control form-control-sm" placeholder="NIK">
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($p['username']) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="edit_penghuni" class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                            <a href="?hapus_penghuni=<?= $p['id_penghuni'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus penghuni ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        <?php endwhile;
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data penghuni</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="dashboard_pemilik.php#penghuni" onclick="goToPagePenghuni(<?= $i ?>)"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const hash = window.location.hash;

    if (hash) {
        const triggerTab = document.querySelector(`a[href="${hash}"]`);
        if (triggerTab) {
            const tab = new bootstrap.Tab(triggerTab);
            tab.show();
        }
    }

    // Tangkap perubahan hash saat user klik tab
    const navLinks = document.querySelectorAll('#pemilikTabs a.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('shown.bs.tab', function (event) {
            const selectedTab = event.target.getAttribute('href');
            history.replaceState(null, null, selectedTab);
        });
    });
});

function goToPageCalon(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('hal_calon', page);
    if ('<?= $search_calon ?>') {
        url.searchParams.set('search_calon', '<?= $search_calon ?>');
    }
    url.hash = 'calon';
    window.location.href = url;
}

function goToPageKamar(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('hal_kamar', page);
    if ('<?= $search_kamar ?>') {
        url.searchParams.set('search_kamar', '<?= $search_kamar ?>');
    }
    url.hash = 'kamar';
    window.location.href = url;
}

function goToPageKontrak(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('hal_kontrak', page);
    if ('<?= $search_kontrak ?>') {
        url.searchParams.set('search_kontrak', '<?= $search_kontrak ?>');
    }
    url.hash = 'kontrak';
    window.location.href = url;
}

function goToPagePembayaran(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('hal_bayar', page);
    if ('<?= $search_pembayaran ?>') {
        url.searchParams.set('search_pembayaran', '<?= $search_pembayaran ?>');
    }
    url.hash = 'pembayaran';
    window.location.href = url;
}

function goToPagePenghuni(page) {
    const url = new URL(window.location.href);
    url.searchParams.set('hal_penghuni', page);
    if ('<?= $search_penghuni ?>') {
        url.searchParams.set('search_penghuni', '<?= $search_penghuni ?>');
    }
    url.hash = 'penghuni';
    window.location.href = url;
}
</script>
</body>
</html>