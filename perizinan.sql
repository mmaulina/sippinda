-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 17, 2025 at 01:21 PM
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
-- Table structure for table `perizinan`
--

CREATE TABLE `perizinan` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `jenis_laporan` enum('KKPR','PERSETUJUAN','SLF','PBS','NIIS','NPWP','PERIZINAN BERUSAHA SEKTOR INDUSTRI','SERTIFIKAT HALAL','TKDN','SNI','SERTIFIKAT INDUSTRI HIJAU','PELAPORAN S1 S2 SINAS','KEPEMILIKAN AKUN SINAS','KESESUAIAN KEGIATAN USAHA','KESESUAIAN FASILITAS','IZIN USAHA INDUSTRI','IZIN PERLUASAN INDUSTRI','IZIN KAWASAN INDUSTRI','IZIN PERLUASAN KAWASAN INDUSTRI') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_izin` varchar(100) NOT NULL,
  `tgl_dokumen` date NOT NULL,
  `upload_berkas` varchar(225) NOT NULL,
  `verifikasi` enum('diterima','diajukan','dikembalikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `keterangan` varchar(225) NOT NULL,
  `tgl_verif` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `perizinan`
--
ALTER TABLE `perizinan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
