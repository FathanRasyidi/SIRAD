-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2025 at 12:04 AM
-- Server version: 8.0.41-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sr`
--
CREATE DATABASE IF NOT EXISTS `sr` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `sr`;

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `id` int NOT NULL,
  `tanggal` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `rekmed` int NOT NULL,
  `nama_pasien` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_lahir` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_periksa` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `image` longblob NOT NULL,
  `expertise` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`id`, `tanggal`, `rekmed`, `nama_pasien`, `tanggal_lahir`, `alamat`, `jenis_periksa`, `image`, `expertise`) VALUES
(17, '14/07/2024', 123456, 'Athar', '14/07/2024', 'Banguntapan', 'VERT.THORACHAL AP/LAT', 0x75706c6f6164732f3132333435365f363661356638663562636461385f646170612e706e67, 'batuk'),
(21, '17/02/2025', 76578, 'Rian Subur', '16/02/2025', 'Kledokan', 'THORAX PA', 0x75706c6f6164732f37363537385f363762333138363239346164375f4c61796f757420312e706e67, ''),
(22, '18/02/2025', 859834, 'Adriano', '17/02/2025', 'Bekasi', 'PEDIS', 0x75706c6f6164732f3835393833345f363762343439353234306664305f53637265656e73686f742066726f6d20323032352d30322d31352031332d34332d34342e706e67, '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `nama` varchar(35) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `akses` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `dibuat` varchar(35) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `akses`, `dibuat`) VALUES
(5, 'Hanif', 'mustafa', 'MTIz', 'radiologi', ''),
(12, 'fathan', 'fathanras', 'MTIz', 'dokter', ''),
(17, 'Admin', 'admin', 'MTIz', 'admin', '14/07/2024'),
(25, 'Athar', '123456', 'MTQvMDcvMjAyNA==', 'pasien', '14/07/2024'),
(29, 'Rian Subur', '76578', 'MTYvMDIvMjAyNQ==', 'pasien', '17/02/2025'),
(30, 'Adriano', '859834', 'MTcvMDIvMjAyNQ==', 'pasien', '18/02/2025');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rekmed` (`rekmed`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
