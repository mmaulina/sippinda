-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2025 at 09:12 AM
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
  `nama_penanda_tangan_laporan` varchar(100) NOT NULL,
  `jabatan` varchar(225) NOT NULL,
  `nama_perusahaan_induk` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
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

--
-- Dumping data for table `data_umum`
--

INSERT INTO `data_umum` (`id`, `id_user`, `nama_perusahaan`, `periode_laporan`, `nilai_investasi_mesin`, `nilai_investasi_lainnya`, `modal_kerja`, `investasi_tanpa_tanah_bangunan`, `status`, `menggunakan_maklon`, `menyediakan_maklon`) VALUES
(2, 1, 'WASAKA CODE DIGITAL DEVELOPMENT', 'apa', 'apa', 'apa lo', 'lo apa', 'lo yg apa', 'jomblo', 'iya', 'iya');

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
-- Table structure for table `konten_dilihat`
--

CREATE TABLE `konten_dilihat` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `konten_id` int NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konten_dilihat`
--

INSERT INTO `konten_dilihat` (`id`, `id_user`, `konten_id`, `tanggal`) VALUES
(7389, 1, 15, '2025-06-18 14:17:48'),
(7390, 2, 15, '2025-06-19 08:47:34'),
(7391, 1, 15, '2025-06-19 08:48:06'),
(7392, 1, 15, '2025-06-19 08:53:45'),
(7393, 1, 16, '2025-06-19 08:56:40'),
(7394, 1, 16, '2025-06-19 08:57:48'),
(7395, 1, 16, '2025-06-19 08:59:28');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `id_title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_konten` enum('gambar','file','link','kosong') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `konten` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `caption` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `id_title`, `title`, `jenis_konten`, `konten`, `caption`, `tanggal`) VALUES
(16, 'TTL-001', 'link google', 'link', 'https://www.google.com/', 'google', '2025-06-19 08:57:43');

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
  `jenis_laporan` enum('KKPR','PERSETUJUAN LINGKUNGAN','SURAT LAIK FUNGSI','PERSETUJUAN BANGUNAN DAN GEDUNG','NOMOR INDUK BERUSAHA','NPWP','PERIZINAN USAHA SEKTOR INDUSTRI','SERTIFIKAT HALAL','TKDN','SNI','SERTIFIKAT INDUSTRI HIJAU','PELAPORAN S1 S2 SINAS','KEPEMILIKAN AKUN SINAS','KESESUAIAN KEGIATAN USAHA DENGAN BIDANG USAHA PERIZINAN PERUSAHAAN','KESESUAIAN FASILITAS PRODUKSI DAN KAPASITAS SESUAI DENGAN PERIZINAN PERUSAHAAN','IZIN USAHA INDUSTRI','IZIN PERLUASAN INDUSTRI','IZIN KAWASAN INDUSTRI','IZIN PERLUASAN KAWASAN INDUSTRI') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `no_izin` varchar(100) NOT NULL,
  `tgl_dokumen` date NOT NULL,
  `upload_berkas` varchar(225) NOT NULL,
  `verifikasi` enum('diterima','diajukan','dikembalikan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `keterangan` varchar(225) NOT NULL,
  `tgl_verif` date DEFAULT NULL
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

--
-- Dumping data for table `profil_perusahaan`
--

INSERT INTO `profil_perusahaan` (`id`, `id_user`, `nama_perusahaan`, `alamat_kantor`, `alamat_pabrik`, `no_telpon`, `no_fax`, `jenis_lokasi_pabrik`, `jenis_kuisioner`) VALUES
(3, 1, 'WASAKA CODE DIGITAL DEVELOPMENT', 'Rumah Maya', 'tidak ada pabrik', '0812345678910', '2134342124', 'Tidak ada pabrik', 'apa tuh');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `username` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telp` varchar(15) NOT NULL,
  `role` enum('superadmin','admin','umum') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('diajukan','diverifikasi','ditolak') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `email`, `no_telp`, `role`, `status`) VALUES
(1, 'superadmin', 'superadmin', 'superadmin@gmail.com', '081234567890', 'superadmin', 'diajukan'),
(2, 'umum', 'umum', 'umum@gmail.com', '', 'umum', 'diajukan'),
(3, 'admin', 'admin', 'admin@gmail.com', '', 'admin', 'diajukan');

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
-- Indexes for table `konten_dilihat`
--
ALTER TABLE `konten_dilihat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `data_khusus`
--
ALTER TABLE `data_khusus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `data_umum`
--
ALTER TABLE `data_umum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `investasi`
--
ALTER TABLE `investasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `konten_dilihat`
--
ALTER TABLE `konten_dilihat`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7396;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pekerja`
--
ALTER TABLE `pekerja`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `perizinan`
--
ALTER TABLE `perizinan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `profil_perusahaan`
--
ALTER TABLE `profil_perusahaan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
