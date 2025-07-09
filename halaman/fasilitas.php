<?php
// fasilitas_kamar.php
include '../includes/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fasilitas Kamar - Sistem Kos</title>
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
        .room-type-card {
            border-radius: 10px;
            transition: transform 0.3s;
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .room-type-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .room-type-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 15px;
            color: white;
            font-weight: bold;
        }
        .standard {
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        .deluxe {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        }
        .vip {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        .facility-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: #0d6efd;
        }
        .price-tag {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        .section-title:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: #0d6efd;
        }
        .back-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-house-door"></i> Kosan Kita
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="daftar_kamar.php">
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
        <a href="../index.php" class="btn btn-primary back-btn">
            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
        </a>
        
        <h2 class="section-title"><i class="bi bi-list-check"></i> Fasilitas Kamar Berdasarkan Tipe</h2>
        
        <div class="row">
            <!-- Tipe Standar -->
            <div class="col-md-4 mb-4">
                <div class="card room-type-card h-100">
                    <div class="room-type-header standard">
                        <h4 class="text-center mb-0">STANDAR</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="price-tag">Rp 1.000.000 - 1.400.000/bulan</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar ukuran 4x4 meter
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kasur single
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Lemari pakaian
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Meja belajar
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar mandi luar
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Akses WiFi
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Listrik included
                            </li>
                        </ul>
                        <div class="text-center mt-3">
                            <a href="daftar_kamar.php?tipe=Standar" class="btn btn-primary">
                                Lihat Kamar Tersedia
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tipe Deluxe -->
            <div class="col-md-4 mb-4">
                <div class="card room-type-card h-100">
                    <div class="room-type-header deluxe">
                        <h4 class="text-center mb-0">DELUXE</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="price-tag">Rp 1.500.000 - 2.500.000/bulan</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar ukuran 6x6 meter
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kasur single premium
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Lemari pakaian besar
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Meja belajar + kursi ergonomis
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar mandi dalam
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Akses WiFi high speed
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Listrik included + AC
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kulkas mini
                            </li>
                        </ul>
                        <div class="text-center mt-3">
                            <a href="daftar_kamar.php?tipe=Deluxe" class="btn btn-primary">
                                Lihat Kamar Tersedia
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tipe VIP -->
            <div class="col-md-4 mb-4">
                <div class="card room-type-card h-100">
                    <div class="room-type-header vip">
                        <h4 class="text-center mb-0">VIP</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <span class="price-tag">Rp 2.600.000 - 3.000.000/bulan</span>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar ukuran 8x8 meter
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kasur double premium
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Lemari pakaian walk-in
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Meja kerja eksekutif
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kamar mandi dalam + water heater
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> WiFi dedicated
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Listrik included + AC
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Kulkas + microwave
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> TV LED 32"
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check-circle-fill facility-icon text-success"></i> Cleaning service mingguan
                            </li>
                        </ul>
                        <div class="text-center mt-3">
                            <a href="daftar_kamar.php?tipe=VIP" class="btn btn-primary">
                                Lihat Kamar Tersedia
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Fasilitas Umum -->
        <div class="card mt-4">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="bi bi-building"></i> Fasilitas Umum Kosan</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> Dapur bersama
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> Ruang tamu
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> Laundry service
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> Parkir motor & mobil
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> CCTV 24 jam
                            </li>
                            <li class="list-group-item">
                                <i class="bi bi-check2-circle text-primary"></i> Security
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>