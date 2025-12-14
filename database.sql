-- Database Schema for Zentori Inventory System

SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_users` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_users`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `nama`, `email`, `password`, `role`, `status`, `created_at`) VALUES
('U001', 'Budi Santoso', 'budi@beautyshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', '2024-01-01 02:00:00'),
('U002', 'Sari Dewi', 'sari@beautyshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', 'active', '2024-01-02 03:00:00'),
('U003', 'Rina Melati', 'rina@beautyshop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', '2024-01-03 04:00:00'),
('U004', 'Bella Sari', 'bella@gmail.com', '$2y$10$H8KMQJxDRReDzdCmbftPMeO68LgCJrJ1djH3ehRDlbah4GNbs/u.y', 'admin', 'active', '2025-01-01 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id_supplier` varchar(50) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `kontak` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id_supplier`, `nama_supplier`, `kontak`, `email`, `alamat`, `status`, `created_at`) VALUES
('S001', 'PT Beauty Indonesia', '08123456789', 'beauty@email.com', 'Jl. Kemang Raya No. 123, Jakarta', 'active', '2024-01-15 03:00:00'),
('S002', 'CV Glow Skincare', '08234567890', 'glow@email.com', 'Jl. Pasteur No. 45, Bandung', 'active', '2024-01-16 04:00:00'),
('S003', 'Toko Makeup Lux', '08345678901', 'lux@email.com', 'Jl. Tunjungan No. 67, Surabaya', 'active', '2024-01-17 05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Skincare'),
(2, 'Makeup'),
(3, 'Body Care'),
(4, 'Hair Care'),
(5, 'Fragrance');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang` (
  `id_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `satuan` varchar(50) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `harga_beli` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `status_stok` enum('available','low','empty') DEFAULT 'available',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_barang`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `id_kategori`, `satuan`, `stok`, `harga_beli`, `harga_jual`, `status_stok`, `status`, `created_at`) VALUES
('B001', 'Serum Vitamin C', 1, 'pcs', 30, 85000.00, 120000.00, 'available', 'active', '2024-01-20 03:00:00'),
('B002', 'Foundation Matte', 2, 'pcs', 50, 65000.00, 95000.00, 'available', 'active', '2024-01-20 03:10:00'),
('B003', 'Body Lotion Vanilla', 3, 'pcs', 100, 35000.00, 50000.00, 'available', 'active', '2024-01-20 03:20:00'),
('B004', 'Shampoo Anti Dandruff', 4, 'botol', 80, 45000.00, 65000.00, 'available', 'active', '2024-01-20 03:30:00'),
('B005', 'Parfum Floral', 5, 'pcs', 40, 120000.00, 180000.00, 'available', 'active', '2024-01-20 03:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `stok_masuk`
--

DROP TABLE IF EXISTS `stok_masuk`;
CREATE TABLE `stok_masuk` (
  `id_stokin` varchar(50) NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `id_supplier` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `tanggal_masuk` datetime NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `expired_date` date DEFAULT NULL,
  `id_user` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stokin`),
  KEY `id_barang` (`id_barang`),
  KEY `id_supplier` (`id_supplier`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `stok_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  CONSTRAINT `stok_masuk_ibfk_2` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id_supplier`) ON DELETE CASCADE,
  CONSTRAINT `stok_masuk_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_users`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stok_masuk`
--

INSERT INTO `stok_masuk` (`id_stokin`, `id_barang`, `id_supplier`, `jumlah`, `harga_beli`, `tanggal_masuk`, `total_harga`, `expired_date`, `id_user`, `created_at`) VALUES
('IN001', 'B001', 'S001', 30, 85000.00, '2025-02-01 09:00:00', 2550000.00, '2025-06-30', 'U001', '2025-02-01 02:00:00'),
('IN002', 'B002', 'S003', 50, 65000.00, '2025-02-02 10:00:00', 3250000.00, '2025-08-15', 'U002', '2025-02-02 03:00:00'),
('IN003', 'B003', 'S004', 100, 35000.00, '2024-02-03 11:00:00', 3500000.00, '2026-03-20', 'U003', '2024-02-03 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `stok_keluar`
--

DROP TABLE IF EXISTS `stok_keluar`;
CREATE TABLE `stok_keluar` (
  `id_stokout` varchar(50) NOT NULL,
  `id_barang` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tipe_keluar` varchar(50) NOT NULL,
  `keterangan` text,
  `tanggal_keluar` datetime NOT NULL,
  `total_harga` decimal(15,2) NOT NULL,
  `id_user` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_stokout`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `stok_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  CONSTRAINT `stok_keluar_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_users`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `stok_keluar`
--

INSERT INTO `stok_keluar` (`id_stokout`, `id_barang`, `jumlah`, `tipe_keluar`, `keterangan`, `tanggal_keluar`, `total_harga`, `id_user`, `created_at`) VALUES
('OUT01', 'B001', 5, 'penjualan', 'Penjualan retail', '2025-02-11 14:00:00', 600000.00, 'U002', '2024-02-11 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `log_expired`
--

DROP TABLE IF EXISTS `log_expired`;
CREATE TABLE `log_expired` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_stokin` varchar(50) DEFAULT NULL,
  `id_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `expired_date` date NOT NULL,
  `tanggal_bersih` date NOT NULL,
  `alasan` varchar(100) NOT NULL,
  `keterangan` text,
  `id_user` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`),
  KEY `id_barang` (`id_barang`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `log_expired_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  CONSTRAINT `log_expired_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_users`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;
