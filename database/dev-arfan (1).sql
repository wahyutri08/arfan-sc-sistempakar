-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 09:23 PM
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
(1, 'G01', 'Tester24', '2025-05-27 19:41:46', '2025-05-27 20:08:34'),
(2, 'G02', 'Tester21', '2025-05-27 19:57:18', '2025-05-27 20:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `gejala_pasien`
--

CREATE TABLE `gejala_pasien` (
  `id_gejala_pasien` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL,
  `cf_user` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hasil_diagnosa`
--

CREATE TABLE `hasil_diagnosa` (
  `id_hasil` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `nama_pasien` varchar(250) NOT NULL,
  `kode_penyakit` varchar(10) NOT NULL,
  `tanggal_diagnosa` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nilai_cf` float NOT NULL,
  `diagnosa` varchar(250) NOT NULL,
  `keterangan` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 5, 'Ahmad Rizki Hidayat', '317308', 'Perempuan', '2025-05-12', 12, 'Wkwkwk', '000000000', '2025-05-26 19:45:32', '2025-05-29 18:14:53'),
(6, 5, 'Ahmad Rizki Hidayat', '3173081', 'Laki-laki', '2025-05-12', 12, 'JALAN TERSERAH1', '000000000', '2025-05-26 19:50:54', '2025-05-27 18:10:09');

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
(1, 'P01', 'Stroke', 'Rangkuman Gejala Stroke', 'Konsultasi dengan dokter saraf melakukan uji lab darah, CT scan dan MRI.', '2025-05-28 19:29:55', '2025-05-29 18:26:04');

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
(6, 'R01', 1, 2, 0.2, 0.05, '2025-05-29 19:21:04', NULL),
(7, 'R02', 1, 1, 0.15, 0.05, '2025-05-29 19:21:58', NULL);

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
(1, 'arfan.jumelar', 'Arfan Jumelar Subangkit', 'arfan@admin.com', '$2y$10$02/T7m79lUr8uGiB7QAIauwaeZpUCbKKnHrY0XDyX4QTkMAV0uL5q', 'Admin', 'Aktif', '68389bce4cbec.jpg', '2025-05-25 17:57:23', '2025-05-28 15:18:30'),
(5, 'arfan2', 'Arfan2', 'admin@admin.com', '$2y$10$ypFCurTTXHtzUC5lRap37.7NRH/j3hU8RFzYsrj7Va8IkauB3ZAwO', 'Perawat', 'Aktif', '6834b464b6117.', '2025-05-26 18:35:16', '2025-05-28 15:18:56'),
(6, 'arfan3', 'Arfan Jumelar Subangkit', 'admin@admin.com', '$2y$10$RnJQ7tKjsHXOhaELpstE4e2.o0IUQfItiMJ7Rd0CVgfTtn.dLK6lG', 'Perawat', 'Aktif', '68389556121d4.', '2025-05-29 17:11:50', NULL);

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
  MODIFY `id_gejala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gejala_pasien`
--
ALTER TABLE `gejala_pasien`
  MODIFY `id_gejala_pasien` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hasil_diagnosa`
--
ALTER TABLE `hasil_diagnosa`
  MODIFY `id_hasil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `penyakit`
--
ALTER TABLE `penyakit`
  MODIFY `id_penyakit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rule`
--
ALTER TABLE `rule`
  MODIFY `id_rule` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
