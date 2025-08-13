-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 11:59 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_gaji`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_jasa_pegawai`
--

CREATE TABLE `detail_jasa_pegawai` (
  `id` int(11) NOT NULL,
  `pegawai_id` int(11) DEFAULT NULL,
  `jasa_id` int(11) DEFAULT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `jumlah_unit` int(11) DEFAULT 0,
  `total_jasa` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hrd`
--

CREATE TABLE `hrd` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `hrd`
--

INSERT INTO `hrd` (`id`, `nama`, `jabatan`, `kontak`, `email`) VALUES
(1, 'Hanafi', 'HRM', '08516151654', 'info@nafiim.com');

-- --------------------------------------------------------

--
-- Table structure for table `jasa_pelayanan`
--

CREATE TABLE `jasa_pelayanan` (
  `id` int(11) NOT NULL,
  `kode_jasa` varchar(10) NOT NULL,
  `nama_jasa` varchar(100) NOT NULL,
  `tarif_per_unit` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jasa_pelayanan`
--

INSERT INTO `jasa_pelayanan` (`id`, `kode_jasa`, `nama_jasa`, `tarif_per_unit`, `created_at`) VALUES
(1, 'ECG', 'Elektrokardiogram', '2000.00', '2025-08-12 17:38:25'),
(2, 'USG', 'Ultrasonografi', '150000.00', '2025-08-12 17:38:25'),
(3, 'LAB', 'Pemeriksaan Laboratorium', '0.00', '2025-08-12 17:38:25'),
(4, 'XRAY', 'Rontgen', '10000.00', '2025-08-12 17:38:25'),
(5, 'KONSUL', 'Konsultasi Spesialis', '200000.00', '2025-08-12 17:38:25'),
(6, 'TERAPI', 'Fisioterapi', '80000.00', '2025-08-12 17:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL,
  `tunjangan_jabatan` decimal(12,2) DEFAULT 0.00,
  `tunjangan_makan` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id`, `nip`, `nama`, `jabatan`, `gaji_pokok`, `tunjangan_jabatan`, `tunjangan_makan`, `created_at`) VALUES
(1, 'PEG001', 'Dr. Ahmad Wijaya', 'Dokter Spesialis', '8000000.00', '2000000.00', '500000.00', '2025-08-12 17:38:25'),
(2, 'PEG002', 'Siti Nurhaliza', 'Perawat Senior', '4500000.00', '800000.00', '400000.00', '2025-08-12 17:38:25'),
(3, 'PEG003', 'Budi Santoso1', 'Teknisi Radiologi', '3500000.00', '500000.00', '350000.00', '2025-08-12 17:38:25'),
(4, 'PEG004', 'Maria Sari', 'Perawat Junior', '3800000.00', '600000.00', '350000.00', '2025-08-12 17:38:25'),
(5, 'PEG005', 'Andi Prasetyo', 'Administrasi', '2500000.00', '0.00', '0.00', '2025-08-12 17:38:25');

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan`
--

CREATE TABLE `pemasukan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_invoice` varchar(20) DEFAULT 'Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `nama_klinik` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_telp` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_klinik`, `alamat`, `no_telp`, `email`, `logo`) VALUES
(1, 'Klinik Medika Batulicin', 'Batulicin', '0851334165', 'john.doe@email.com', 'logo_klinik_1755021708.png');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_invoice` varchar(20) DEFAULT 'Lunas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `penggajian`
--

CREATE TABLE `penggajian` (
  `id` int(11) NOT NULL,
  `pegawai_id` int(11) DEFAULT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL,
  `tunjangan_jabatan` decimal(12,2) DEFAULT 0.00,
  `tunjangan_makan` decimal(12,2) DEFAULT 0.00,
  `total_jasa_pelayanan` decimal(12,2) DEFAULT 0.00,
  `gross_salary` decimal(12,2) NOT NULL,
  `potongan_pajak` decimal(12,2) DEFAULT 0.00,
  `potongan_bpjs_kesehatan` decimal(12,2) DEFAULT 0.00,
  `potongan_bpjs_tk` decimal(12,2) DEFAULT 0.00,
  `potongan_lain` decimal(12,2) DEFAULT 0.00,
  `keterangan_potongan_lain` varchar(255) DEFAULT NULL,
  `total_potongan` decimal(12,2) DEFAULT 0.00,
  `gaji_bersih` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `nama`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$OWV1SC16lkvrf8mW8AXsy.B2d5R0KBpukET5B3xOqLpJp5WhvrAr.', 'Administrator', 'admin', '2025-08-12 19:44:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_neraca`
--

CREATE TABLE `user_neraca` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_neraca`
--

INSERT INTO `user_neraca` (`id`, `username`, `password`, `nama`, `created_at`) VALUES
(1, 'admin', '$2y$10$WxkBA/PFydNlmZ/MuiaGguP6YnQppnRGgL8QVoM2aAguYfVKxWjZ2', 'Hanafi', '2025-08-13 02:58:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_jasa_pegawai`
--
ALTER TABLE `detail_jasa_pegawai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`),
  ADD KEY `jasa_id` (`jasa_id`);

--
-- Indexes for table `hrd`
--
ALTER TABLE `hrd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jasa_pelayanan`
--
ALTER TABLE `jasa_pelayanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_jasa` (`kode_jasa`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nip` (`nip`);

--
-- Indexes for table `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_neraca`
--
ALTER TABLE `user_neraca`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_jasa_pegawai`
--
ALTER TABLE `detail_jasa_pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hrd`
--
ALTER TABLE `hrd`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jasa_pelayanan`
--
ALTER TABLE `jasa_pelayanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pemasukan`
--
ALTER TABLE `pemasukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `penggajian`
--
ALTER TABLE `penggajian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_neraca`
--
ALTER TABLE `user_neraca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_jasa_pegawai`
--
ALTER TABLE `detail_jasa_pegawai`
  ADD CONSTRAINT `detail_jasa_pegawai_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_jasa_pegawai_ibfk_2` FOREIGN KEY (`jasa_id`) REFERENCES `jasa_pelayanan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penggajian`
--
ALTER TABLE `penggajian`
  ADD CONSTRAINT `penggajian_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `pegawai` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
