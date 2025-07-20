/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - db_klinik_aesthetic_test
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_klinik_aesthetic` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_klinik_aesthetic`;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

INSERT  INTO `migrations`(`id`,`migration`,`batch`) VALUES 
(1,'2014_10_12_000000_create_users_table',1),
(2,'2014_10_12_100000_create_password_reset_tokens_table',1),
(3,'2019_12_14_000001_create_personal_access_tokens_table',1),
(4,'2024_10_17_043202_create_tb_produk',1),
(5,'2024_10_19_165518_create_tb_promo_table',1),
(6,'2024_10_20_020356_create_dokter_table',1),
(7,'2024_10_20_043523_create_beautician_table',1),
(8,'2024_10_20_044213_create_treatment_table',1),
(9,'2024_10_25_020520_create_konsultasi_table',1),
(10,'2024_12_09_092117_create_tb_pembelian',1),
(11,'2024_12_09_092750_create_tb_detail_pembelian',1),
(12,'2025_01_12_191928_create_pembayaran_table',1),
(13,'2025_05_29_061940_create_tb_keranjang_pembelian',1);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `tb_absensi` */

DROP TABLE IF EXISTS `tb_absensi`;

CREATE TABLE `tb_absensi` (
  `id_absensi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(10) unsigned NOT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_absensi`),
  KEY `id_pegawai` (`id_pegawai`),
  CONSTRAINT `tb_absensi_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_absensi` */

/*Table structure for table `tb_beautician` */

DROP TABLE IF EXISTS `tb_beautician`;

CREATE TABLE `tb_beautician` (
  `id_beautician` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(10) unsigned NOT NULL,
  `nama_beautician` varchar(50) NOT NULL,
  `no_telp` varchar(50) NOT NULL,
  `email_beautician` varchar(50) NOT NULL,
  `NIP` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_beautician`),
  KEY `id_pegawai` (`id_pegawai`),
  CONSTRAINT `tb_beautician_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_beautician` */

/*Table structure for table `tb_booking_treatment` */

DROP TABLE IF EXISTS `tb_booking_treatment`;

CREATE TABLE `tb_booking_treatment` (
  `id_booking_treatment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `waktu_treatment` datetime NOT NULL,
  `id_dokter` int(10) unsigned DEFAULT NULL,
  `id_beautician` int(10) unsigned DEFAULT NULL,
  `status_booking_treatment` enum('Verifikasi','Berhasil dibooking','Dibatalkan','Selesai') DEFAULT 'Verifikasi',
  `harga_total` decimal(15,2) DEFAULT NULL,
  `id_promo` int(10) unsigned DEFAULT NULL,
  `potongan_harga` decimal(15,2) DEFAULT NULL,
  `besaran_pajak` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_akhir_treatment` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_booking_treatment`),
  KEY `tb_booking_treatment_id_user_foreign` (`id_user`),
  KEY `tb_booking_treatment_id_dokter_foreign` (`id_dokter`),
  KEY `tb_booking_treatment_id_beautician_foreign` (`id_beautician`),
  KEY `tb_booking_treatment_id_promo_foreign` (`id_promo`),
  CONSTRAINT `tb_booking_treatment_id_beautician_foreign` FOREIGN KEY (`id_beautician`) REFERENCES `tb_beautician` (`id_beautician`) ON DELETE SET NULL,
  CONSTRAINT `tb_booking_treatment_id_dokter_foreign` FOREIGN KEY (`id_dokter`) REFERENCES `tb_dokter` (`id_dokter`) ON DELETE SET NULL,
  CONSTRAINT `tb_booking_treatment_id_promo_foreign` FOREIGN KEY (`id_promo`) REFERENCES `tb_promo` (`id_promo`) ON DELETE CASCADE,
  CONSTRAINT `tb_booking_treatment_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_booking_treatment` */

/*Table structure for table `tb_detail_booking_treatment` */

DROP TABLE IF EXISTS `tb_detail_booking_treatment`;

CREATE TABLE `tb_detail_booking_treatment` (
  `id_detail_booking_treatment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_booking_treatment` int(10) unsigned NOT NULL,
  `id_treatment` int(10) unsigned NOT NULL,
  `biaya_treatment` decimal(15,2) NOT NULL,
  `id_kompensasi_diberikan` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_booking_treatment`),
  KEY `tb_detail_booking_treatment_id_booking_treatment_foreign` (`id_booking_treatment`),
  KEY `tb_detail_booking_treatment_id_treatment_foreign` (`id_treatment`),
  KEY `tb_detail_booking_treatment_id_kompensasi_diberikan_foreign` (`id_kompensasi_diberikan`),
  CONSTRAINT `tb_detail_booking_treatment_id_booking_treatment_foreign` FOREIGN KEY (`id_booking_treatment`) REFERENCES `tb_booking_treatment` (`id_booking_treatment`) ON DELETE CASCADE,
  CONSTRAINT `tb_detail_booking_treatment_id_kompensasi_diberikan_foreign` FOREIGN KEY (`id_kompensasi_diberikan`) REFERENCES `tb_kompensasi_diberikan` (`id_kompensasi_diberikan`) ON DELETE SET NULL,
  CONSTRAINT `tb_detail_booking_treatment_id_treatment_foreign` FOREIGN KEY (`id_treatment`) REFERENCES `tb_treatment` (`id_treatment`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_detail_booking_treatment` */

/*Table structure for table `tb_detail_konsultasi` */

DROP TABLE IF EXISTS `tb_detail_konsultasi`;

CREATE TABLE `tb_detail_konsultasi` (
  `id_detail_konsultasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_konsultasi` int(10) unsigned NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `saran_tindakan` text DEFAULT NULL,
  `id_treatment` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_konsultasi`),
  KEY `tb_detail_konsultasi_id_konsultasi_foreign` (`id_konsultasi`),
  KEY `tb_detail_konsultasi_id_treatment_foreign` (`id_treatment`),
  CONSTRAINT `tb_detail_konsultasi_id_konsultasi_foreign` FOREIGN KEY (`id_konsultasi`) REFERENCES `tb_konsultasi` (`id_konsultasi`) ON DELETE CASCADE,
  CONSTRAINT `tb_detail_konsultasi_id_treatment_foreign` FOREIGN KEY (`id_treatment`) REFERENCES `tb_treatment` (`id_treatment`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_detail_konsultasi` */

/*Table structure for table `tb_detail_penjualan_produk` */

DROP TABLE IF EXISTS `tb_detail_penjualan_produk`;

CREATE TABLE `tb_detail_penjualan_produk` (
  `id_detail_penjualan_produk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_penjualan_produk` int(10) unsigned NOT NULL,
  `id_produk` int(10) unsigned NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `harga_penjualan_produk` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail_penjualan_produk`),
  KEY `tb_detail_penjualan_produk_id_penjualan_produk_foreign` (`id_penjualan_produk`),
  KEY `tb_detail_penjualan_produk_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_detail_penjualan_produk_id_penjualan_produk_foreign` FOREIGN KEY (`id_penjualan_produk`) REFERENCES `tb_penjualan_produk` (`id_penjualan_produk`) ON DELETE CASCADE,
  CONSTRAINT `tb_detail_penjualan_produk_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_detail_penjualan_produk` */

/*Table structure for table `tb_dokter` */

DROP TABLE IF EXISTS `tb_dokter`;

CREATE TABLE `tb_dokter` (
  `id_dokter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(10) unsigned NOT NULL,
  `nama_dokter` varchar(50) NOT NULL,
  `no_telp` varchar(50) NOT NULL,
  `email_dokter` varchar(50) NOT NULL,
  `NIP` varchar(50) NOT NULL,
  `foto_dokter` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_dokter`),
  KEY `id_pegawai` (`id_pegawai`),
  CONSTRAINT `tb_dokter_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_dokter` */

/*Table structure for table `tb_feedback_konsultasi` */

DROP TABLE IF EXISTS `tb_feedback_konsultasi`;

CREATE TABLE `tb_feedback_konsultasi` (
  `id_feedback_konsultasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_konsultasi` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned DEFAULT NULL,
  `teks_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_feedback_konsultasi`),
  KEY `tb_feedback_konsultasi_id_konsultasi_foreign` (`id_konsultasi`),
  CONSTRAINT `tb_feedback_konsultasi_id_konsultasi_foreign` FOREIGN KEY (`id_konsultasi`) REFERENCES `tb_konsultasi` (`id_konsultasi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_feedback_konsultasi` */

/*Table structure for table `tb_feedback_treatment` */

DROP TABLE IF EXISTS `tb_feedback_treatment`;

CREATE TABLE `tb_feedback_treatment` (
  `id_feedback_treatment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_detail_booking_treatment` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned DEFAULT NULL,
  `teks_feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_feedback_treatment`),
  KEY `tb_feedback_treatment_id_detail_booking_treatment_foreign` (`id_detail_booking_treatment`),
  CONSTRAINT `tb_feedback_treatment_id_detail_booking_treatment_foreign` FOREIGN KEY (`id_detail_booking_treatment`) REFERENCES `tb_detail_booking_treatment` (`id_detail_booking_treatment`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_feedback_treatment` */

/*Table structure for table `tb_gaji` */

DROP TABLE IF EXISTS `tb_gaji`;

CREATE TABLE `tb_gaji` (
  `id_gaji` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_pegawai` int(10) unsigned NOT NULL,
  `periode_bulan` tinyint(3) unsigned NOT NULL,
  `periode_tahun` int(10) unsigned NOT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gaji_bonus` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gaji_kehadiran` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gaji_total` decimal(12,2) NOT NULL,
  `tanggal_pembayaran` date DEFAULT NULL,
  `status` enum('Terbayar','Belum Terbayar') NOT NULL DEFAULT 'Belum Terbayar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_gaji`),
  KEY `id_pegawai` (`id_pegawai`),
  CONSTRAINT `tb_gaji_ibfk_1` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`periode_bulan` between 1 and 12)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_gaji` */

/*Table structure for table `tb_hasil_seleksi` */

DROP TABLE IF EXISTS `tb_hasil_seleksi`;

CREATE TABLE `tb_hasil_seleksi` (
  `id_hasil_seleksi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_lowongan_pekerjaan` int(10) unsigned NOT NULL,
  `status` enum('diterima','ditolak','pending') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_hasil_seleksi`),
  KEY `id_user` (`id_user`),
  KEY `id_lowongan_pekerjaan` (`id_lowongan_pekerjaan`),
  CONSTRAINT `tb_hasil_seleksi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`),
  CONSTRAINT `tb_hasil_seleksi_ibfk_2` FOREIGN KEY (`id_lowongan_pekerjaan`) REFERENCES `tb_lowongan_pekerjaan` (`id_lowongan_pekerjaan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_hasil_seleksi` */

/*Table structure for table `tb_jadwal_praktik_beautician` */

DROP TABLE IF EXISTS `tb_jadwal_praktik_beautician`;

CREATE TABLE `tb_jadwal_praktik_beautician` (
  `id_jadwal_praktik_beautician` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_beautician` int(10) unsigned NOT NULL,
  `hari` enum('senin','selasa','rabu','kamis','jumat','sabtu') NOT NULL,
  `tgl_kerja` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal_praktik_beautician`),
  KEY `tb_jadwal_praktik_beautician_id_beautician_foreign` (`id_beautician`),
  CONSTRAINT `tb_jadwal_praktik_beautician_id_beautician_foreign` FOREIGN KEY (`id_beautician`) REFERENCES `tb_beautician` (`id_beautician`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_jadwal_praktik_beautician` */

/*Table structure for table `tb_jadwal_praktik_dokter` */

DROP TABLE IF EXISTS `tb_jadwal_praktik_dokter`;

CREATE TABLE `tb_jadwal_praktik_dokter` (
  `id_jadwal_praktik_dokter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_dokter` int(10) unsigned NOT NULL,
  `tgl_kerja` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal_praktik_dokter`),
  KEY `tb_jadwal_praktik_dokter_id_dokter_foreign` (`id_dokter`),
  CONSTRAINT `tb_jadwal_praktik_dokter_id_dokter_foreign` FOREIGN KEY (`id_dokter`) REFERENCES `tb_dokter` (`id_dokter`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_jadwal_praktik_dokter` */

/*Table structure for table `tb_jenis_treatment` */

DROP TABLE IF EXISTS `tb_jenis_treatment`;

CREATE TABLE `tb_jenis_treatment` (
  `id_jenis_treatment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_jenis_treatment` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jenis_treatment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_jenis_treatment` */

/*Table structure for table `tb_kategori` */

DROP TABLE IF EXISTS `tb_kategori`;

CREATE TABLE `tb_kategori` (
  `id_kategori` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_kategori` */

/*Table structure for table `tb_keranjang_pembelian` */

DROP TABLE IF EXISTS `tb_keranjang_pembelian`;

CREATE TABLE `tb_keranjang_pembelian` (
  `id_keranjang_pembelian` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_produk` int(10) unsigned NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_keranjang_pembelian`),
  KEY `tb_keranjang_pembelian_id_user_foreign` (`id_user`),
  KEY `tb_keranjang_pembelian_id_produk_foreign` (`id_produk`),
  CONSTRAINT `tb_keranjang_pembelian_id_produk_foreign` FOREIGN KEY (`id_produk`) REFERENCES `tb_produk` (`id_produk`) ON DELETE CASCADE,
  CONSTRAINT `tb_keranjang_pembelian_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_keranjang_pembelian` */

/*Table structure for table `tb_kompensasi` */

DROP TABLE IF EXISTS `tb_kompensasi`;

CREATE TABLE `tb_kompensasi` (
  `id_kompensasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_treatment` int(10) unsigned NOT NULL,
  `nama_kompensasi` varchar(255) NOT NULL,
  `deskripsi_kompensasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kompensasi`),
  KEY `tb_kompensasi_id_treatment_foreign` (`id_treatment`),
  CONSTRAINT `tb_kompensasi_id_treatment_foreign` FOREIGN KEY (`id_treatment`) REFERENCES `tb_treatment` (`id_treatment`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_kompensasi` */

/*Table structure for table `tb_kompensasi_diberikan` */

DROP TABLE IF EXISTS `tb_kompensasi_diberikan`;

CREATE TABLE `tb_kompensasi_diberikan` (
  `id_kompensasi_diberikan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_komplain` int(10) unsigned DEFAULT NULL,
  `id_kompensasi` int(10) unsigned DEFAULT NULL,
  `kode_kompensasi` varchar(255) DEFAULT NULL,
  `tanggal_berakhir_kompensasi` date DEFAULT NULL,
  `status_kompensasi` enum('Belum Digunakan','Sudah Digunakan','Sudah Kadaluwarsa') NOT NULL DEFAULT 'Belum Digunakan',
  `tanggal_pemakaian_kompensasi` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_kompensasi_diberikan`),
  UNIQUE KEY `tb_kompensasi_diberikan_kode_kompensasi_unique` (`kode_kompensasi`),
  KEY `tb_kompensasi_diberikan_id_komplain_foreign` (`id_komplain`),
  KEY `tb_kompensasi_diberikan_id_kompensasi_foreign` (`id_kompensasi`),
  CONSTRAINT `tb_kompensasi_diberikan_id_kompensasi_foreign` FOREIGN KEY (`id_kompensasi`) REFERENCES `tb_kompensasi` (`id_kompensasi`) ON DELETE CASCADE,
  CONSTRAINT `tb_kompensasi_diberikan_id_komplain_foreign` FOREIGN KEY (`id_komplain`) REFERENCES `tb_komplain` (`id_komplain`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_kompensasi_diberikan` */

/*Table structure for table `tb_komplain` */

DROP TABLE IF EXISTS `tb_komplain`;

CREATE TABLE `tb_komplain` (
  `id_komplain` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_booking_treatment` int(10) unsigned NOT NULL,
  `id_detail_booking_treatment` int(10) unsigned NOT NULL,
  `teks_komplain` text DEFAULT NULL,
  `gambar_komplain` text DEFAULT NULL,
  `balasan_komplain` text DEFAULT NULL,
  `pemberian_kompensasi` enum('Tidak ada pemberian','Sudah diberikan') NOT NULL DEFAULT 'Tidak ada pemberian',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_komplain`),
  KEY `tb_komplain_id_user_foreign` (`id_user`),
  KEY `tb_komplain_id_booking_treatment_foreign` (`id_booking_treatment`),
  KEY `tb_komplain_id_detail_booking_treatment_foreign` (`id_detail_booking_treatment`),
  CONSTRAINT `tb_komplain_id_booking_treatment_foreign` FOREIGN KEY (`id_booking_treatment`) REFERENCES `tb_booking_treatment` (`id_booking_treatment`) ON DELETE CASCADE,
  CONSTRAINT `tb_komplain_id_detail_booking_treatment_foreign` FOREIGN KEY (`id_detail_booking_treatment`) REFERENCES `tb_detail_booking_treatment` (`id_detail_booking_treatment`) ON DELETE CASCADE,
  CONSTRAINT `tb_komplain_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_komplain` */

/*Table structure for table `tb_konsultasi` */

DROP TABLE IF EXISTS `tb_konsultasi`;

CREATE TABLE `tb_konsultasi` (
  `id_konsultasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned DEFAULT NULL,
  `id_dokter` int(10) unsigned DEFAULT NULL,
  `waktu_konsultasi` datetime NOT NULL,
  `keluhan_pelanggan` text DEFAULT NULL,
  `status_booking_konsultasi` enum('Verifikasi','Berhasil dibooking','Dibatalkan','Selesai') DEFAULT 'Verifikasi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_konsultasi`),
  KEY `tb_konsultasi_id_user_foreign` (`id_user`),
  KEY `tb_konsultasi_id_dokter_foreign` (`id_dokter`),
  CONSTRAINT `tb_konsultasi_id_dokter_foreign` FOREIGN KEY (`id_dokter`) REFERENCES `tb_dokter` (`id_dokter`) ON DELETE CASCADE,
  CONSTRAINT `tb_konsultasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_konsultasi` */

/*Table structure for table `tb_lamaran_pekerjaan` */

DROP TABLE IF EXISTS `tb_lamaran_pekerjaan`;

CREATE TABLE `tb_lamaran_pekerjaan` (
  `id_lamaran_pekerjaan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_lowongan_pekerjaan` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `nama_pelamar` varchar(100) NOT NULL,
  `email_pelamar` varchar(100) NOT NULL,
  `NIK_pelamar` varchar(16) DEFAULT NULL,
  `telepon_pelamar` varchar(20) NOT NULL,
  `alamat_pelamar` text NOT NULL,
  `pendidikan_terakhir` varchar(50) NOT NULL,
  `CV` longblob DEFAULT NULL,
  `status_lamaran` enum('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `status_seleksi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_lamaran_pekerjaan`),
  KEY `id_lowongan_pekerjaan` (`id_lowongan_pekerjaan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `tb_lamaran_pekerjaan_ibfk_1` FOREIGN KEY (`id_lowongan_pekerjaan`) REFERENCES `tb_lowongan_pekerjaan` (`id_lowongan_pekerjaan`),
  CONSTRAINT `tb_lamaran_pekerjaan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_lamaran_pekerjaan` */

/*Table structure for table `tb_lowongan_pekerjaan` */

DROP TABLE IF EXISTS `tb_lowongan_pekerjaan`;

CREATE TABLE `tb_lowongan_pekerjaan` (
  `id_lowongan_pekerjaan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `judul_pekerjaan` varchar(100) NOT NULL,
  `id_posisi` int(10) unsigned NOT NULL,
  `jumlah_lowongan` int(10) unsigned NOT NULL,
  `pengalaman_minimal` varchar(50) DEFAULT NULL,
  `gaji_minimal` decimal(12,2) DEFAULT NULL,
  `gaji_maksimal` decimal(12,2) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `persyaratan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_lowongan_pekerjaan`),
  KEY `id_posisi` (`id_posisi`),
  CONSTRAINT `tb_lowongan_pekerjaan_ibfk_1` FOREIGN KEY (`id_posisi`) REFERENCES `tb_posisi` (`id_posisi`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`tanggal_selesai` >= `tanggal_mulai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_lowongan_pekerjaan` */

/*Table structure for table `tb_pegawai` */

DROP TABLE IF EXISTS `tb_pegawai`;

CREATE TABLE `tb_pegawai` (
  `id_pegawai` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `NIP` varchar(20) DEFAULT NULL,
  `NIK` varchar(16) DEFAULT NULL,
  `id_posisi` int(10) unsigned DEFAULT NULL,
  `agama` varchar(20) DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pegawai`),
  UNIQUE KEY `NIK` (`NIK`),
  KEY `id_user` (`id_user`),
  KEY `id_posisi` (`id_posisi`),
  CONSTRAINT `tb_pegawai_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`),
  CONSTRAINT `tb_pegawai_ibfk_2` FOREIGN KEY (`id_posisi`) REFERENCES `tb_posisi` (`id_posisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_pegawai` */

/*Table structure for table `tb_pelatihan` */

DROP TABLE IF EXISTS `tb_pelatihan`;

CREATE TABLE `tb_pelatihan` (
  `id_pelatihan` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `jenis_pelatihan` varchar(50) DEFAULT NULL,
  `jadwal_pelatihan` datetime DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `durasi` int(10) unsigned DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_pelatihan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_pelatihan` */

/*Table structure for table `tb_pembayaran` */

DROP TABLE IF EXISTS `tb_pembayaran`;

CREATE TABLE `tb_pembayaran` (
  `id_pembayaran` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_booking_treatment` int(10) unsigned DEFAULT NULL,
  `id_penjualan_produk` int(10) unsigned DEFAULT NULL,
  `metode_pembayaran` enum('Tunai','Non Tunai') NOT NULL DEFAULT 'Tunai',
  `uang` decimal(15,2) DEFAULT NULL,
  `kembalian` decimal(15,2) DEFAULT NULL,
  `status_pembayaran` enum('Belum Dibayar','Sudah Dibayar','Dibatalkan') NOT NULL DEFAULT 'Belum Dibayar',
  `waktu_pembayaran` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  KEY `tb_pembayaran_id_booking_treatment_foreign` (`id_booking_treatment`),
  KEY `tb_pembayaran_id_penjualan_produk_foreign` (`id_penjualan_produk`),
  CONSTRAINT `tb_pembayaran_id_booking_treatment_foreign` FOREIGN KEY (`id_booking_treatment`) REFERENCES `tb_booking_treatment` (`id_booking_treatment`) ON DELETE CASCADE,
  CONSTRAINT `tb_pembayaran_id_penjualan_produk_foreign` FOREIGN KEY (`id_penjualan_produk`) REFERENCES `tb_penjualan_produk` (`id_penjualan_produk`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_pembayaran` */

/*Table structure for table `tb_penjualan_produk` */

DROP TABLE IF EXISTS `tb_penjualan_produk`;

CREATE TABLE `tb_penjualan_produk` (
  `id_penjualan_produk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `tanggal_pembelian` datetime NOT NULL DEFAULT '2025-06-29 15:31:33',
  `harga_total` decimal(15,2) NOT NULL,
  `id_promo` int(10) unsigned DEFAULT NULL,
  `potongan_harga` decimal(15,2) DEFAULT 0.00,
  `besaran_pajak` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_akhir` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_penjualan_produk`),
  KEY `tb_penjualan_produk_id_user_foreign` (`id_user`),
  KEY `tb_penjualan_produk_id_promo_foreign` (`id_promo`),
  CONSTRAINT `tb_penjualan_produk_id_promo_foreign` FOREIGN KEY (`id_promo`) REFERENCES `tb_promo` (`id_promo`) ON DELETE CASCADE,
  CONSTRAINT `tb_penjualan_produk_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_penjualan_produk` */

/*Table structure for table `tb_posisi` */

DROP TABLE IF EXISTS `tb_posisi`;

CREATE TABLE `tb_posisi` (
  `id_posisi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_posisi` varchar(50) NOT NULL,
  `gaji_pokok` decimal(12,2) NOT NULL,
  `persen_bonus` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_posisi`),
  UNIQUE KEY `nama_posisi` (`nama_posisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_posisi` */

/*Table structure for table `tb_produk` */

DROP TABLE IF EXISTS `tb_produk`;

CREATE TABLE `tb_produk` (
  `id_produk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_kategori` int(10) unsigned NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi_produk` text DEFAULT NULL,
  `harga_produk` decimal(15,2) NOT NULL,
  `stok_produk` int(11) NOT NULL,
  `status_produk` enum('Tersedia','Habis') NOT NULL,
  `gambar_produk` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_produk`),
  KEY `tb_produk_id_kategori_foreign` (`id_kategori`),
  CONSTRAINT `tb_produk_id_kategori_foreign` FOREIGN KEY (`id_kategori`) REFERENCES `tb_kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_produk` */

/*Table structure for table `tb_promo` */

DROP TABLE IF EXISTS `tb_promo`;

CREATE TABLE `tb_promo` (
  `id_promo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_promo` varchar(255) NOT NULL,
  `jenis_promo` enum('Treatment','Produk') NOT NULL,
  `deskripsi_promo` text NOT NULL,
  `tipe_potongan` enum('Diskon','Rupiah') NOT NULL,
  `potongan_harga` decimal(15,2) NOT NULL,
  `minimal_belanja` decimal(15,2) DEFAULT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `gambar_promo` varchar(255) NOT NULL,
  `status_promo` enum('Aktif','Tidak Aktif') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_promo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_promo` */

/*Table structure for table `tb_treatment` */

DROP TABLE IF EXISTS `tb_treatment`;

CREATE TABLE `tb_treatment` (
  `id_treatment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_jenis_treatment` int(10) unsigned NOT NULL,
  `nama_treatment` varchar(255) NOT NULL,
  `deskripsi_treatment` text NOT NULL,
  `biaya_treatment` decimal(15,2) NOT NULL,
  `estimasi_treatment` time NOT NULL,
  `gambar_treatment` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_treatment`),
  KEY `tb_treatment_id_jenis_treatment_foreign` (`id_jenis_treatment`),
  CONSTRAINT `tb_treatment_id_jenis_treatment_foreign` FOREIGN KEY (`id_jenis_treatment`) REFERENCES `tb_jenis_treatment` (`id_jenis_treatment`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_treatment` */

/*Table structure for table `tb_user` */

DROP TABLE IF EXISTS `tb_user`;

CREATE TABLE `tb_user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(255) NOT NULL,
  `no_telp` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `role` enum('pelanggan','dokter','beautician','front office','kasir','admin', 'hrd') NOT NULL DEFAULT 'pelanggan',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `tb_user_no_telp_unique` (`no_telp`),
  UNIQUE KEY `tb_user_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tb_user` */

/*Table structure for table `tb_wawancara` */

DROP TABLE IF EXISTS `tb_wawancara`;

CREATE TABLE `tb_wawancara` (
  `id_wawancara` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_lamaran_pekerjaan` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `tanggal_wawancara` datetime NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `catatan` text DEFAULT NULL,
  `hasil` enum('diterima','ditolak','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_wawancara`),
  KEY `id_lamaran_pekerjaan` (`id_lamaran_pekerjaan`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `tb_wawancara_ibfk_1` FOREIGN KEY (`id_lamaran_pekerjaan`) REFERENCES `tb_lamaran_pekerjaan` (`id_lamaran_pekerjaan`),
  CONSTRAINT `tb_wawancara_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tb_wawancara` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
