-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 12, 2025 at 10:58 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sippinda`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telp` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('superadmin','admin','umum','kadis') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('diajukan','diverifikasi','ditolak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `no_telp`, `role`, `status`) VALUES
(7, 'superadmin', '$2y$10$X6aW6XAVE4/FXAyyJyL6iO8etO73GwPEPmFF7vU5k6Ab/gRWmxQV6', 'irwanfirdaus508@gmail.com', '081234567890', 'superadmin', 'diverifikasi'),
(8, 'admin', '$2y$10$CzcXMxfqzo2NLhFtuEylJebWsaKsrP2sIuCV41QDjkmrFbP0NMmAy', 'admin@gmail.com', '081234567890', 'admin', 'diverifikasi'),
(10, 'mmaulina', '$2y$10$3MaVpfwJVId8NGTAV4KGq.na.kRmDSZH3NJSCn1pSrtzXtso3wO5a', 'mayamaulina16@gmail.com', '08115128607', 'umum', 'diverifikasi'),
(11, 'irwan12', '$2y$10$TDVhbL9ZFs.T14CSrEfHCu5rs2G2wtpZ0biR8lhQi.4vqHVYrPD9m', 'irwa@gmail.com', '081234567890', 'umum', 'diverifikasi'),
(12, 'yanda', '$2y$10$qL/rBi.t8BU0as3GawlFQed6ey9SGH5Up2dKfxgW39uHrNYDeHR4y', 'yanda@gmail.com', '081234567890', 'umum', 'diverifikasi'),
(15, 'perusahaan1', '$2y$10$RuRabyDmMxtXSDLbE8yTM.ZXxQds80q5CxI/i9J17.VgtZELGbV/q', 'perusahaan1@gmail.com', '081234567890', 'umum', 'diverifikasi'),
(16, 'kadis', '$2y$10$Z9a6rOOX9J0a1XUH.Fhfyu3yrGOt3ggpGY3CDpFbQXWzEkeNuw9aK', 'kadis@gmail.com', '081234567890', 'kadis', 'diverifikasi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
