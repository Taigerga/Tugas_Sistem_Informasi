-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 03:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kosan`
--

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe` enum('Standar','Deluxe','VIP') NOT NULL,
  `harga` decimal(12,2) DEFAULT NULL,
  `status` enum('kosong','terisi') DEFAULT 'kosong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id_kamar`, `nomor_kamar`, `tipe`, `harga`, `status`) VALUES
(1, 'K011', 'Standar', 1000000.00, 'terisi'),
(2, 'K012', 'Standar', 1000000.00, 'terisi'),
(3, 'K013', 'Deluxe', 1500000.00, 'terisi'),
(4, 'K014', 'Standar', 1000000.00, 'terisi'),
(5, 'K015', 'Standar', 1000000.00, 'terisi'),
(6, 'K016', 'Standar', 1000000.00, 'terisi'),
(7, 'K017', 'VIP', 2000000.00, 'terisi'),
(8, 'K018', 'Standar', 1000000.00, 'terisi'),
(9, 'K019', 'Standar', 1000000.00, 'terisi'),
(10, 'K020', 'Standar', 1000000.00, 'terisi'),
(11, 'K021', 'Standar', 1000000.00, 'terisi'),
(12, 'K022', 'Standar', 1000000.00, 'terisi'),
(13, 'K023', 'Standar', 1000000.00, 'terisi'),
(14, 'K024', 'Standar', 1000000.00, 'terisi'),
(15, 'K025', 'Standar', 1000000.00, 'kosong'),
(16, 'K026', 'Standar', 1000000.00, 'kosong'),
(17, 'K027', 'Standar', 1000000.00, 'kosong'),
(18, 'K028', 'Deluxe', 1500000.00, 'kosong');

-- --------------------------------------------------------

--
-- Table structure for table `kontraksewa`
--

CREATE TABLE `kontraksewa` (
  `id_kontrak` int(11) NOT NULL,
  `id_penghuni` int(11) NOT NULL,
  `id_kamar` int(11) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `id_pemilik` int(11) DEFAULT NULL,
  `status` enum('pending','aktif','nonaktif') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontraksewa`
--

INSERT INTO `kontraksewa` (`id_kontrak`, `id_penghuni`, `id_kamar`, `tanggal_mulai`, `tanggal_selesai`, `id_pemilik`, `status`) VALUES
(2, 2, 1, '2025-06-30', '2025-07-04', 1, 'aktif'),
(4, 4, 2, '2025-06-30', '2025-07-04', 1, 'aktif'),
(5, 5, 3, '2025-06-30', '2025-07-05', 1, 'aktif'),
(10, 10, 4, '2025-06-30', '2025-08-30', 1, 'aktif'),
(11, 11, 5, '2025-06-30', '2025-07-30', 1, 'aktif'),
(14, 12, 6, '2025-07-03', '2025-08-02', 1, 'aktif'),
(16, 14, 8, '2025-07-04', '2025-08-03', 1, 'aktif'),
(18, 16, 7, '2025-07-04', '2025-08-03', 1, 'aktif'),
(19, 17, 10, '2025-07-04', '2025-09-03', 1, 'aktif'),
(20, 18, 9, '2025-07-04', '2025-08-03', 1, 'aktif'),
(21, 19, 11, '2025-07-04', '2025-09-03', 1, 'aktif'),
(22, 20, 12, '2025-07-04', '2025-11-03', 1, 'aktif'),
(23, 21, 13, '2025-07-05', '2025-08-04', 1, 'aktif'),
(24, 22, 14, '2025-07-07', '2025-08-06', NULL, 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_kontrak` int(11) NOT NULL,
  `bulan` varchar(7) DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `jumlah` decimal(12,2) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status` enum('belum','lunas','terlambat') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_kontrak`, `bulan`, `tanggal_bayar`, `jumlah`, `metode_pembayaran`, `status`) VALUES
(4, 2, '2025-08', '2025-06-30', 1000000.00, '68624dd6bc829_220px-Azathoth.jpg', 'lunas'),
(5, 2, '2025-07', '2025-06-30', 1000000.00, '68624f13b9e67_04l5I8TqdzF9WDMJ.png', 'lunas'),
(7, 4, '2025-06', '2025-06-30', 1000000.00, 'uploads/686253c38907f_ERD_Tiket_KAI.png', 'lunas'),
(8, 5, '2025-06', '2025-06-30', 1500000.00, 'uploads/68628e4bd8ea0_naive-bayes-class.png', 'lunas'),
(9, 5, '2025-08', '2025-06-30', 1500000.00, '68628ecf8696f_maxresdefault.jpg', 'lunas'),
(10, 5, '2025-09', '2025-06-30', 1500000.00, '68628ede9f5ea_va.PNG', 'lunas'),
(11, 5, '2025-10', '2025-06-30', 1500000.00, '68628fcea68d8_maxresdefault.jpg', 'lunas'),
(16, 10, '2025-06', '2025-06-30', 1000000.00, '6862962aabdf8_naive-bayes-class.png', 'lunas'),
(17, 10, '2025-08', '2025-06-30', 1000000.00, '686297fa68d97_va.PNG', 'lunas'),
(18, 11, '2025-06', '2025-06-30', 1000000.00, '686298f6c54e5_va.PNG', 'lunas'),
(21, 14, '2025-07', '2025-07-03', 1000000.00, '6866a87c79ec0_04l5I8TqdzF9WDMJ.png', 'lunas'),
(23, 16, '2025-07', '2025-07-04', 1000000.00, '68676c335a689_ccna-introduction-to-networks.png', 'lunas'),
(25, 18, '2025-07', '2025-07-04', 2000000.00, '686789bf8978d_ERD_Tiket_KAI.png', 'lunas'),
(26, 19, '2025-07', '2025-07-04', 1000000.00, '68678b4a7d135_ERD_Tiket_KAI.png', 'lunas'),
(27, 20, '2025-07', '2025-07-04', 1000000.00, '68678c1ee27fa_ERD_Tiket_KAI.png', 'lunas'),
(28, 21, '2025-07', '2025-07-04', 1000000.00, '68678cf767da1_sigma-sign-vector-24417187.jpg', 'lunas'),
(29, 21, '2025-09', '2025-07-04', 1000000.00, '68678d643c2a0_sigma-sign-vector-24417187.jpg', 'lunas'),
(30, 22, '2025-07', '2025-07-04', 1000000.00, '6867dc4118b89_images.jpeg', 'lunas'),
(31, 22, '2025-09', '2025-07-04', 1000000.00, '6867dcdc4aea5_images.jpeg', 'lunas'),
(32, 23, '2025-07', '2025-07-05', 1000000.00, '6869256fccae0_1.PNG', 'lunas'),
(33, 24, '2025-07', '2025-07-07', 1000000.00, '686bb722b1c41_6862962aabdf8_naive-bayes-class.png', 'lunas'),
(35, 19, '2025-09', '2025-07-09', 1000000.00, '686e3ea122cbe_cobadk1.drawio.png', 'lunas'),
(36, 22, '2025-10', '2025-07-09', 1000000.00, '686e5cb94a909_cobadk1.drawio.png', 'lunas'),
(37, 22, '2025-11', '2025-07-09', 1000000.00, '686e6b11bb72e_DFD_Level2_Pemesanan.png', 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `id_pemilik` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `gmail` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pemilik') DEFAULT 'pemilik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`id_pemilik`, `nama`, `no_hp`, `gmail`, `username`, `password`, `role`) VALUES
(1, 'Yanto', '0912581051', 'uiasbgawgo@hgasga', 'sigma', '12345', 'pemilik');

-- --------------------------------------------------------

--
-- Table structure for table `penghuni`
--

CREATE TABLE `penghuni` (
  `id_penghuni` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `gmail` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('penghuni') DEFAULT 'penghuni'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penghuni`
--

INSERT INTO `penghuni` (`id_penghuni`, `nama`, `nik`, `no_hp`, `gmail`, `username`, `password`, `role`) VALUES
(2, 'Raga', '1012', '0512421', 'iluminati270306@gmail.com', 'Akira', '12345', 'penghuni'),
(4, 'eshhawhe', '41253112412', '0512421', 'iluminati270306@gmail.com', 'Grifit', '12345', 'penghuni'),
(5, 'alibaba', '2151241', '0512421', 'iluminati270306@gmail.com', 'Mrabucin', '12345', 'penghuni'),
(6, 'Raga', '1414141412', '0512421', 'iluminati270306@gmail.com', 'riz', '12345', 'penghuni'),
(7, 'awsgag', '41253112412', '0512421', 'iluminati270306@gmail.com', 'ligma', '12345', 'penghuni'),
(8, 'wfa', '1414141412', '0512421', 'iluminati270306@gmail.com', 'gr', '12345', 'penghuni'),
(9, 'alibaba', '1414141412', '0512421', 'iluminati270306@gmail.com', 'yant', '12345', 'penghuni'),
(10, 'wfa', '131231', '12313123', 'iluminati270306@gmail.com', 'gtr', '12345', 'penghuni'),
(11, 'GAD', '41253112412', '0512421', 'iluminati270306@gmail.com', 'GAD', '12345', 'penghuni'),
(12, 'Rizki', '151251', '12415141', 'iluminati270306@gmail.com', 'Taigerga', '12345', 'penghuni'),
(14, 'Gbafax', '12345', '24142', 'Suzukautako2434@gmail.com', 'Gfa', '12345', 'penghuni'),
(16, 'r1241', '12412512515', '124112125', 'mrizkialiansyah19@gmail.com', 'mra', '12345', 'penghuni'),
(17, 'Ryu', '124214', '1241541', 'mrizkialiansyah19@gmail.com', 'Riz27', '12345', 'penghuni'),
(18, 'qr12r', '1412441', '123512', 'mrizkialiansyah19@gmail.com', 'Grtza', '12345', 'penghuni'),
(19, 'Rizki251', '1235415', '1231414', 'iluminati270306@gmail.com', 'Taigergax', '12345', 'penghuni'),
(20, 'Nauval', '5124215123', '124121241', 'lordblackstar22@gmail.com', 'Nauval', '12345', 'penghuni'),
(21, 'Wa ode', '3253471252', '15126214161', 'Cchaca1318@gmail.com', 'Chaca', '12345', 'penghuni'),
(22, 'Akuto', '12515141', '4125124', 'lordblackstar22@gmail.com', 'Nauga', '12345', 'penghuni'),
(23, 'asgawgawg', '124214124', '4125214123', 'lordblackstar22@gmail.com', 'Goga', '12345', 'penghuni');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`id_kamar`);

--
-- Indexes for table `kontraksewa`
--
ALTER TABLE `kontraksewa`
  ADD PRIMARY KEY (`id_kontrak`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_kamar` (`id_kamar`),
  ADD KEY `fk_kontraksewa_pemilik` (`id_pemilik`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_kontrak` (`id_kontrak`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`id_pemilik`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `penghuni`
--
ALTER TABLE `penghuni`
  ADD PRIMARY KEY (`id_penghuni`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kamar`
--
ALTER TABLE `kamar`
  MODIFY `id_kamar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `kontraksewa`
--
ALTER TABLE `kontraksewa`
  MODIFY `id_kontrak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `pemilik`
--
ALTER TABLE `pemilik`
  MODIFY `id_pemilik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penghuni`
--
ALTER TABLE `penghuni`
  MODIFY `id_penghuni` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kontraksewa`
--
ALTER TABLE `kontraksewa`
  ADD CONSTRAINT `fk_kontraksewa_pemilik` FOREIGN KEY (`id_pemilik`) REFERENCES `pemilik` (`id_pemilik`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `kontraksewa_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `penghuni` (`id_penghuni`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kontraksewa_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_kontrak`) REFERENCES `kontraksewa` (`id_kontrak`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
