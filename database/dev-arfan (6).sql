-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 06:19 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev-arfan`
--

-- --------------------------------------------------------

--
-- Table structure for table `gejala`
--

CREATE TABLE `gejala` (
  `id_gejala` int(11) NOT NULL,
  `kode_gejala` varchar(5) NOT NULL,
  `nama_gejala` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gejala`
--

INSERT INTO `gejala` (`id_gejala`, `kode_gejala`, `nama_gejala`, `created_at`, `updated_at`) VALUES
(1, 'G01', 'Sulit Berbicara', '2025-05-27 19:41:46', '2025-06-07 18:01:07'),
(2, 'G02', 'Tidak paham ketika ditanya', '2025-05-27 19:57:18', '2025-05-31 18:54:04'),
(3, 'G03', 'Lemas di salah satu bagian badan', '2025-05-31 18:55:22', '2025-05-31 19:48:31'),
(4, 'G04', 'Kebas di salah satu bagian muka', '2025-05-31 18:55:41', NULL),
(5, 'G05', 'Kebas di salah satu bagian badan', '2025-05-31 18:55:53', '2025-05-31 19:48:47'),
(6, 'G06', 'Kehilangan keseimbangan', '2025-05-31 18:56:04', NULL),
(7, 'G07', 'Pusing', '2025-05-31 18:56:16', NULL),
(8, 'G08', 'Sakit kepala hebat (vertigo, migrain, atau sakit kepala tidak seperti biasanya)', '2025-05-31 18:56:27', NULL),
(9, 'G09', 'Gangguan penglihatan di salah satu atau kedua mata', '2025-05-31 18:56:46', NULL),
(10, 'G10', 'Durasi stroke', '2025-05-31 18:57:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gejala_pasien`
--

CREATE TABLE `gejala_pasien` (
  `id_gejala_pasien` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL,
  `nilai_bobot` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gejala_pasien`
--

INSERT INTO `gejala_pasien` (`id_gejala_pasien`, `id_pasien`, `id_gejala`, `nilai_bobot`, `created_at`, `updated_at`) VALUES
(66, 13, 1, 0.6, '2025-06-12 18:07:42', '2025-06-12 18:07:42'),
(67, 13, 2, 0.6, '2025-06-12 18:07:42', '2025-06-12 18:07:42'),
(68, 13, 3, 0.6, '2025-06-12 18:07:42', '2025-06-12 18:07:42'),
(104, 15, 1, 0.6, '2025-06-13 20:42:09', '2025-06-13 20:42:09'),
(105, 15, 2, 0.6, '2025-06-13 20:42:09', '2025-06-13 20:42:09'),
(106, 15, 3, 0.6, '2025-06-13 20:42:09', '2025-06-13 20:42:09'),
(107, 15, 9, 1, '2025-06-13 20:42:09', '2025-06-13 20:42:09'),
(108, 15, 9, 1, '2025-06-13 20:42:09', '2025-06-13 20:42:09'),
(109, 14, 1, 1, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(110, 14, 7, 0.4, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(111, 14, 10, 1, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(112, 14, 7, 0.8, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(113, 14, 10, 0.2, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(114, 14, 5, 0.2, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(115, 14, 5, 1, '2025-06-13 20:56:50', '2025-06-13 20:56:50'),
(118, 9, 3, 0.6, '2025-06-14 16:10:56', '2025-06-14 16:10:56'),
(119, 9, 5, 0.6, '2025-06-14 16:10:56', '2025-06-14 16:10:56'),
(120, 9, 7, 0.4, '2025-06-14 16:10:56', '2025-06-14 16:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `hasil_diagnosa`
--

CREATE TABLE `hasil_diagnosa` (
  `id_hasil` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `nama_pasien` varchar(250) NOT NULL,
  `kode_penyakit` varchar(50) NOT NULL,
  `tanggal_diagnosa` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nilai_cf` float NOT NULL,
  `diagnosa` varchar(250) NOT NULL,
  `keterangan` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hasil_diagnosa`
--

INSERT INTO `hasil_diagnosa` (`id_hasil`, `user_id`, `id_pasien`, `nama_pasien`, `kode_penyakit`, `tanggal_diagnosa`, `nilai_cf`, `diagnosa`, `keterangan`, `created_at`) VALUES
(94, 1, 13, 'Ahmad Rizki Hidayat', 'P01', '2025-06-14 10:49:56', 0.272064, 'Stroke', 'Rangkuman Gejala Stroke', '2025-06-14 10:49:56'),
(95, 1, 14, 'Jatmiko', 'P01', '2025-06-14 10:49:56', 0.521393, 'Stroke', 'Rangkuman Gejala Stroke', '2025-06-14 10:49:56'),
(96, 5, 15, 'Deni', 'P01', '2025-06-14 10:49:56', 0.410372, 'Stroke', 'Rangkuman Gejala Stroke', '2025-06-14 10:49:56'),
(97, 1, 9, 'Ananta', 'P01', '2025-06-14 16:11:01', 0.205888, 'Stroke', 'Rangkuman Gejala Stroke', '2025-06-14 16:11:01');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `nik` varchar(30) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `usia` int(11) DEFAULT NULL,
  `alamat` varchar(250) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `user_id`, `nama_pasien`, `nik`, `jenis_kelamin`, `tanggal_lahir`, `usia`, `alamat`, `no_hp`, `created_at`, `updated_at`) VALUES
(9, 1, 'Ananta', '3173085', 'Laki-laki', '1994-02-09', 31, 'qweqwe', '000000000', '2025-06-10 18:57:07', '2025-06-13 16:31:00'),
(13, 1, 'Ahmad Rizki Hidayat', '317308', 'Laki-laki', '1985-11-13', 39, '1231231', '12312', '2025-06-11 18:42:33', '2025-06-13 16:31:24'),
(14, 1, 'Jatmiko', '317308123', 'Laki-laki', '1998-08-22', 26, 'qweqwe', '000000000', '2025-06-13 17:19:17', NULL),
(15, 5, 'Deni', '317308123123', 'Laki-laki', '1996-07-01', 28, 'qqweqwe', '089509310263', '2025-06-13 17:33:20', '2025-06-13 20:52:52');

-- --------------------------------------------------------

--
-- Table structure for table `penyakit`
--

CREATE TABLE `penyakit` (
  `id_penyakit` int(11) NOT NULL,
  `kode_penyakit` varchar(10) NOT NULL,
  `nama_penyakit` varchar(250) NOT NULL,
  `deskripsi` varchar(250) NOT NULL,
  `solusi` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyakit`
--

INSERT INTO `penyakit` (`id_penyakit`, `kode_penyakit`, `nama_penyakit`, `deskripsi`, `solusi`, `created_at`, `updated_at`) VALUES
(1, 'P01', 'Stroke', 'Rangkuman Gejala Stroke', 'Konsultasi dengan dokter saraf melakukan uji lab darah, CT scan dan MRI.', '2025-05-28 19:29:55', '2025-05-30 14:34:02');

-- --------------------------------------------------------

--
-- Table structure for table `rule`
--

CREATE TABLE `rule` (
  `id_rule` int(11) NOT NULL,
  `kode_rule` varchar(10) NOT NULL,
  `id_penyakit` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL,
  `nilai_mb` float NOT NULL,
  `nilai_md` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rule`
--

INSERT INTO `rule` (`id_rule`, `kode_rule`, `id_penyakit`, `id_gejala`, `nilai_mb`, `nilai_md`, `created_at`, `updated_at`) VALUES
(8, 'R01', 1, 1, 0.4, 0.2, '2025-05-31 19:33:24', '2025-06-10 17:10:31'),
(9, 'R02', 1, 2, 0.3, 0.2, '2025-05-31 19:45:22', NULL),
(10, 'R03', 1, 3, 0.5, 0.3, '2025-05-31 19:45:58', '2025-06-10 17:11:02'),
(11, 'R04', 1, 4, 0.3, 0.1, '2025-05-31 19:49:06', '2025-06-10 17:11:21'),
(12, 'R05', 1, 5, 0.3, 0.2, '2025-05-31 19:49:31', '2025-05-31 19:55:03'),
(13, 'R06', 1, 6, 0.4, 0.3, '2025-05-31 19:49:48', '2025-06-10 17:12:02'),
(14, 'R07', 1, 7, 0.3, 0.2, '2025-05-31 19:52:03', NULL),
(15, 'R08', 1, 8, 0.4, 0.3, '2025-05-31 19:52:16', '2025-06-10 17:12:36'),
(16, 'R09', 1, 9, 0.3, 0.2, '2025-05-31 19:52:34', NULL),
(19, 'R10', 1, 10, 0.3, 0.1, '2025-06-10 17:13:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `nama` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `role` enum('Admin','Perawat') NOT NULL,
  `status` enum('Aktif','Tidak Aktif') NOT NULL,
  `avatar` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `nama`, `email`, `password`, `role`, `status`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'arfan.jumelar', 'Arfan Jumelar Subangkit', 'arfan@admin.com', '$2y$10$clpkfMyLXNuBVtgDOHAzZOJNbLv2H0HzNp45Vj8RLXYyAJhvPArVm', 'Admin', 'Aktif', '683f44000d64d.png', '2025-05-25 17:57:23', '2025-05-28 15:18:30'),
(5, 'arfan2', 'Arfan 2', 'admin@admin.com', '$2y$10$k2cImVSYXaLqJTClx1qisOdOY.CMUpG/WcrT2Sn/x9SYtoZHRqILq', 'Perawat', 'Aktif', '6834b464b6117.', '2025-05-26 18:35:16', '2025-06-14 07:13:37'),
(6, 'arfan3', 'Arfan 3', 'admin@admin.com', '$2y$10$0PUWL./uBLCnDUVcA3j6velyIwOGbJiCYTGBcHR3RJ9J/Eit6qVN2', 'Perawat', 'Tidak Aktif', '68389556121d4.', '2025-05-29 17:11:50', '2025-06-14 07:14:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gejala`
--
ALTER TABLE `gejala`
  ADD PRIMARY KEY (`id_gejala`);

--
-- Indexes for table `gejala_pasien`
--
ALTER TABLE `gejala_pasien`
  ADD PRIMARY KEY (`id_gejala_pasien`),
  ADD KEY `fk_pasien` (`id_pasien`),
  ADD KEY `fk_gejala` (`id_gejala`);

--
-- Indexes for table `hasil_diagnosa`
--
ALTER TABLE `hasil_diagnosa`
  ADD PRIMARY KEY (`id_hasil`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `penyakit`
--
ALTER TABLE `penyakit`
  ADD PRIMARY KEY (`id_penyakit`);

--
-- Indexes for table `rule`
--
ALTER TABLE `rule`
  ADD PRIMARY KEY (`id_rule`),
  ADD UNIQUE KEY `kode_rule` (`kode_rule`),
  ADD KEY `fk_penyakit` (`id_penyakit`),
  ADD KEY `fk_gejala2` (`id_gejala`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gejala`
--
ALTER TABLE `gejala`
  MODIFY `id_gejala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `gejala_pasien`
--
ALTER TABLE `gejala_pasien`
  MODIFY `id_gejala_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `hasil_diagnosa`
--
ALTER TABLE `hasil_diagnosa`
  MODIFY `id_hasil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `penyakit`
--
ALTER TABLE `penyakit`
  MODIFY `id_penyakit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rule`
--
ALTER TABLE `rule`
  MODIFY `id_rule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gejala_pasien`
--
ALTER TABLE `gejala_pasien`
  ADD CONSTRAINT `fk_gejala` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id_gejala`),
  ADD CONSTRAINT `fk_pasien` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`);

--
-- Constraints for table `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rule`
--
ALTER TABLE `rule`
  ADD CONSTRAINT `fk_gejala2` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id_gejala`),
  ADD CONSTRAINT `fk_penyakit` FOREIGN KEY (`id_penyakit`) REFERENCES `penyakit` (`id_penyakit`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
