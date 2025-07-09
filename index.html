<?php
include 'includes/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kosan Campuran - Tempat Nyaman untuk Tinggal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/index.css"/>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Kosan Campuran</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link active" href="halaman/daftar_kamar.php">Kamar</a></li>
        <li class="nav-item"><a class="nav-link" href="halaman/fasilitas.php">Fasilitas</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">Tentang Kami</a></li>
      </ul>
      <a href="login.php" class="btn btn-primary">
        <i class="fas fa-sign-in-alt me-1"></i> Login
      </a>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero-section text-center py-5 bg-light">
  <div class="container">
    <h1 class="display-4 fw-bold">Temukan Kamar Kos Nyaman</h1>
    <p class="lead">Lokasi strategis, fasilitas lengkap, dan harga terjangkau</p>
    <div class="mt-4">
      <a href="register.php" class="btn btn-primary btn-lg">
        <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
      </a>
    </div>
  </div>
</section>

<!-- Daftar Kamar -->
<section id="rooms" class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-5">Daftar Kamar Tersedia</h2>
    <div class="row">
      <?php
      $query = "SELECT id_kamar, nomor_kamar, tipe, harga, status FROM Kamar WHERE status = 'kosong' ORDER BY RAND() LIMIT 3";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $statusBadge = ($row['status'] == 'kosong') ? 'success' : 'danger';
          $statusText = ucfirst($row['status']);
          echo '
          <div class="col-md-4 mb-4">
            <div class="card room-card h-100">
              <img src="https://via.placeholder.com/300x200" class="card-img-top" alt="Kamar">
              <div class="card-body">
                <h5 class="card-title">Kamar No. ' . htmlspecialchars($row['nomor_kamar']) . '</h5>
                <p class="card-text">
                  <i class="fas fa-ruler-combined me-2"></i>Tipe: ' . htmlspecialchars($row['tipe']) . '<br>
                  <i class="fas fa-money-bill-wave me-2"></i>Harga: Rp ' . number_format($row['harga'], 0, ',', '.') . '
                </p>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-primary">Rp ' . number_format($row['harga'], 0, ',', '.') . '/bulan</span>
                  <span class="badge bg-' . $statusBadge . '">' . $statusText . '</span>
                </div>
              </div>
            </div>
          </div>';
        }
      } else {
        echo '<p class="text-center">Belum ada data kamar tersedia.</p>';
      }
      ?>
    </div>
  </div>
</section>

<!-- Fasilitas -->
<section id="facilities" class="py-5">
  <div class="container">
    <h2 class="text-center mb-5">Fasilitas Kosan</h2>
    <div class="row text-center">
      <div class="col-md-3 mb-4"><i class="fas fa-wifi fa-2x text-primary"></i><p>WiFi Cepat</p></div>
      <div class="col-md-3 mb-4"><i class="fas fa-utensils fa-2x text-primary"></i><p>Dapur Bersama</p></div>
      <div class="col-md-3 mb-4"><i class="fas fa-tint fa-2x text-primary"></i><p>Air Bersih 24 Jam</p></div>
      <div class="col-md-3 mb-4"><i class="fas fa-shield-alt fa-2x text-primary"></i><p>Keamanan 24 Jam</p></div>
    </div>
  </div>
</section>

<!-- Tentang Kami -->
<section id="about" class="py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <h2>Tentang Kosan Campuran</h2>
        <p class="lead">Menyediakan tempat tinggal nyaman dengan harga terjangkau untuk mahasiswa dan pekerja.</p>
        <p>Lokasi strategis di pusat kota, dekat kampus & pusat perbelanjaan. Lingkungan nyaman dan aman.</p>
        <p><i class="fas fa-map-marker-alt me-2"></i>Jl. Tubagus Ismail Dalam No.54A/C, Bandung</p>
        <p><i class="fas fa-phone me-2"></i>0812-3456-7890</p>
      </div>
      <div class="col-md-6">
        <img src="https://via.placeholder.com/600x400" class="img-fluid rounded" alt="Kos Kami">
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
  <div class="container text-center">
    <p class="mb-0">&copy; <?= date('Y') ?> Kosan Campuran. All rights reserved.</p>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
