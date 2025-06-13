-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 13, 2025 at 01:41 PM
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
-- Table structure for table `bidang_perusahaan`
--

CREATE TABLE `bidang_perusahaan` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `bidang` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_khusus`
--

CREATE TABLE `data_khusus` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `jabatan` varchar(225) NOT NULL,
  `nama_perusahaan_induk` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_sinas`
--

CREATE TABLE `data_sinas` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `upload` varchar(225) NOT NULL,
  `status` enum('diterima','diajukan','dikembalikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `data_umum`
--

CREATE TABLE `data_umum` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `periode_laporan` varchar(100) NOT NULL,
  `nilai_investasi_mesin` varchar(225) NOT NULL,
  `nilai_investasi_lainnya` varchar(225) NOT NULL,
  `modal_kerja` varchar(225) NOT NULL,
  `investasi_tanpa_tanah_bangunan` varchar(225) NOT NULL,
  `status` varchar(225) NOT NULL,
  `menggunakan_maklon` enum('iya','tidak') NOT NULL,
  `menyediakan_maklon` enum('iya','tidak') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investasi`
--

CREATE TABLE `investasi` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `pemerintah_pusat` varchar(225) NOT NULL,
  `pemerintah_daerah` varchar(225) NOT NULL,
  `swasta_nasional` varchar(225) NOT NULL,
  `asing` varchar(225) NOT NULL,
  `negara_asal` varchar(225) NOT NULL,
  `nilai_investasi_tanah` varchar(225) NOT NULL,
  `nilai_investasi_bangunan` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pekerja`
--

CREATE TABLE `pekerja` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `laki_laki_pro_tetap` int NOT NULL,
  `perempuan_pro_tetap` int NOT NULL,
  `laki_laki_pro_tidak_tetap` int NOT NULL,
  `perempuan_pro_tidak_tetap` int NOT NULL,
  `laki_laki_lainnya` int NOT NULL,
  `perempuan_lainnya` int NOT NULL,
  `sd` int NOT NULL,
  `smp` int NOT NULL,
  `sma` int NOT NULL,
  `d1_d2_d3` int NOT NULL,
  `s1_d4` int NOT NULL,
  `s2` int NOT NULL,
  `s3` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perizinan`
--

CREATE TABLE `perizinan` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `jenis_laporan` enum('KKPR','PERSETUJUAN ???','SLF','PBS','NIIS','NPWP','PERIZINAN BERUSAHA SEKTOR INDUSTRI','SERTIFIKAT HALAL','TKDN','SNI','SERTIFIKAT INDUSTRI HIJAU','PELAPORAN S1 S2 SINAS','KEPEMILIKAN AKUN SINAS','KESESUAIAN KEGIATAN USAHA','KESESUAIAN FASILITAS','IZIN USAHA INDUSTRI','IZIN PERLUASAN INDUSTRI','IZIN KAWASAN INDUSTRI','IZIN PERLUASAN KAWASAN INDUSTRI') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_izin` varchar(100) NOT NULL,
  `tgl_dokumen` date NOT NULL,
  `upload_berkas` varchar(225) NOT NULL,
  `verifikasi` enum('diterima','diajukan','dikembalikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tgl_verif` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profil_perusahaan`
--

CREATE TABLE `profil_perusahaan` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_perusahaan` varchar(225) NOT NULL,
  `alamat_kantor` varchar(225) NOT NULL,
  `alamat_pabrik` varchar(225) NOT NULL,
  `no_telpon` varchar(15) NOT NULL,
  `no_fax` varchar(15) NOT NULL,
  `jenis_lokasi_pabrik` varchar(225) NOT NULL,
  `jenis_kuisioner` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('superadmin','admin','umum') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `role`) VALUES
(1, 'superadmin', 'superadmin', '', 'superadmin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bidang_perusahaan`
--
ALTER TABLE `bidang_perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_khusus`
--
ALTER TABLE `data_khusus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `data_umum`
--
ALTER TABLE `data_umum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investasi`
--
ALTER TABLE `investasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pekerja`
--
ALTER TABLE `pekerja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perizinan`
--
ALTER TABLE `perizinan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil_perusahaan`
--
ALTER TABLE `profil_perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bidang_perusahaan`
--
ALTER TABLE `bidang_perusahaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_khusus`
--
ALTER TABLE `data_khusus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_umum`
--
ALTER TABLE `data_umum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `investasi`
--
ALTER TABLE `investasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pekerja`
--
ALTER TABLE `pekerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profil_perusahaan`
--
ALTER TABLE `profil_perusahaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
