-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: plpi_public
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `app_settings`
--

DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `header_logo_path` varchar(255) DEFAULT NULL,
  `login_logo_path` varchar(255) DEFAULT NULL,
  `public_logo_path` varchar(255) DEFAULT NULL,
  `favicon_path` varchar(255) DEFAULT NULL,
  `app_timezone` varchar(64) NOT NULL DEFAULT 'Asia/Jakarta',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `smtp_host` varchar(191) DEFAULT NULL,
  `smtp_port` int(5) DEFAULT NULL,
  `smtp_user` varchar(191) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_crypto` varchar(10) DEFAULT NULL,
  `mail_from_email` varchar(191) DEFAULT NULL,
  `mail_from_name` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_settings`
--

LOCK TABLES `app_settings` WRITE;
/*!40000 ALTER TABLE `app_settings` DISABLE KEYS */;
INSERT INTO `app_settings` VALUES (1,'uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/favicon-1782915329_dbe7ceac17294e8ab2d0.png','Asia/Makassar','2026-04-21 15:36:45','2026-07-02 02:32:48',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `app_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `article_categories`
--

DROP TABLE IF EXISTS `article_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `article_categories`
--

LOCK TABLES `article_categories` WRITE;
/*!40000 ALTER TABLE `article_categories` DISABLE KEYS */;
INSERT INTO `article_categories` VALUES (1,'Penulisan Ilmiah','penulisan-ilmiah','Panduan penulisan naskah ilmiah.',1,1,'2026-07-01 07:59:09','2026-07-01 07:59:09'),(2,'Publikasi','publikasi','Informasi proses dan strategi publikasi.',1,2,'2026-07-01 07:59:09','2026-07-01 07:59:09'),(3,'Struktur Artikel','struktur-artikel',NULL,1,2,'2026-07-01 08:24:05','2026-07-01 08:24:05');
/*!40000 ALTER TABLE `article_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `editor_reviewer_applications`
--

DROP TABLE IF EXISTS `editor_reviewer_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editor_reviewer_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint(20) unsigned NOT NULL,
  `application_code` varchar(80) NOT NULL,
  `full_name` varchar(191) NOT NULL,
  `institution` varchar(191) NOT NULL,
  `role_requested` varchar(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `google_scholar_id` varchar(100) NOT NULL,
  `sinta_id` varchar(100) NOT NULL,
  `scopus_id` varchar(100) DEFAULT NULL,
  `orcid_id` varchar(100) DEFAULT NULL,
  `expertise` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'baru',
  `notification_sent_at` datetime DEFAULT NULL,
  `notification_error` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `application_code` (`application_code`),
  KEY `journal_id` (`journal_id`),
  KEY `role_requested` (`role_requested`),
  CONSTRAINT `editor_reviewer_applications_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `editor_reviewer_applications`
--

LOCK TABLES `editor_reviewer_applications` WRITE;
/*!40000 ALTER TABLE `editor_reviewer_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `editor_reviewer_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `educational_articles`
--

DROP TABLE IF EXISTS `educational_articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `educational_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `cover_path` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `educational_articles_category_id_foreign` (`category_id`),
  CONSTRAINT `educational_articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `educational_articles`
--

LOCK TABLES `educational_articles` WRITE;
/*!40000 ALTER TABLE `educational_articles` DISABLE KEYS */;
INSERT INTO `educational_articles` VALUES (1,1,'Cara Menulis Artikel Ilmiah yang Baik dan Sistematis','cara-menulis-artikel-ilmiah-yang-baik','Panduan ringkas memahami tahapan awal dalam menyusun artikel ilmiah, mulai dari pemilihan topik hingga penyusunan struktur naskah.','<p>Artikel ilmiah merupakan karya tulis akademik yang disusun berdasarkan kaidah ilmiah, data, argumentasi, dan rujukan yang dapat dipertanggungjawabkan.</p><p>Langkah pertama dalam menulis artikel ilmiah adalah menentukan topik yang jelas, spesifik, dan relevan dengan bidang kajian. Topik yang terlalu luas akan menyulitkan penulis dalam membangun fokus pembahasan.</p><p>Setelah topik ditentukan, penulis perlu menyusun kerangka artikel. Struktur umum artikel ilmiah biasanya meliputi judul, abstrak, pendahuluan, metode, hasil dan pembahasan, kesimpulan, serta daftar pustaka.</p><p>Artikel yang baik tidak hanya menyajikan informasi, tetapi juga menunjukkan hubungan logis antara masalah, teori, metode, temuan, dan simpulan.</p><blockquote><p>Artikel ilmiah yang baik tidak hanya benar secara struktur, tetapi juga jelas, etis, mudah dipahami, dan relevan dengan pembaca sasaran.</p></blockquote>',NULL,'Meja belajar dengan buku dan catatan untuk menulis artikel ilmiah','published','2026-06-27 09:00:00',1,'2026-07-01 08:24:05','2026-07-01 08:24:05'),(2,3,'Memahami Struktur IMRAD dalam Artikel Ilmiah','memahami-struktur-imrad-dalam-artikel-ilmiah','Mengenal struktur Introduction, Methods, Results, and Discussion sebagai format umum dalam penulisan artikel ilmiah.','<p>IMRAD merupakan singkatan dari Introduction, Methods, Results, and Discussion. Struktur ini banyak digunakan dalam artikel ilmiah karena memudahkan pembaca memahami alur penelitian.</p><p>Bagian Introduction berisi latar belakang, masalah, gap penelitian, dan tujuan. Bagian Methods menjelaskan pendekatan, subjek, instrumen, prosedur, dan teknik analisis data.</p><p>Bagian Results menyajikan temuan penelitian secara objektif, sedangkan Discussion menafsirkan temuan tersebut dengan mengaitkannya pada teori atau hasil penelitian terdahulu.</p><p>Dengan struktur IMRAD, artikel menjadi lebih runtut, sistematis, dan mudah dievaluasi oleh editor maupun reviewer.</p>',NULL,'Laptop terbuka untuk menyusun struktur artikel ilmiah','published','2026-06-27 08:00:00',2,'2026-07-01 08:24:05','2026-07-01 08:24:05'),(3,2,'Tips Memilih Jurnal yang Tepat untuk Publikasi','tips-memilih-jurnal-yang-tepat','Hal-hal penting yang perlu diperhatikan penulis sebelum mengirimkan artikel ke jurnal ilmiah.','<p>Memilih jurnal yang tepat merupakan langkah penting dalam proses publikasi ilmiah. Penulis perlu memastikan bahwa ruang lingkup artikel sesuai dengan fokus dan cakupan jurnal.</p><p>Sebelum submit, baca terlebih dahulu template, pedoman penulis, frekuensi terbit, informasi biaya, dan etika publikasi yang berlaku pada jurnal tersebut.</p><p>Hindari mengirim artikel yang sama ke lebih dari satu jurnal secara bersamaan karena hal tersebut bertentangan dengan etika publikasi.</p><p>Jurnal yang tepat akan membantu artikel diproses secara lebih relevan, baik dari sisi editor, reviewer, maupun pembaca sasaran.</p>',NULL,'Ruang kerja penulis dengan laptop dan buku referensi','published','2026-06-26 09:00:00',3,'2026-07-01 08:24:05','2026-07-01 08:24:05');
/*!40000 ALTER TABLE `educational_articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_messages`
--

DROP TABLE IF EXISTS `email_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipient_name` varchar(191) DEFAULT NULL,
  `recipient_email` varchar(191) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `sent_by` varchar(191) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_messages_template_id_foreign` (`template_id`),
  CONSTRAINT `email_messages_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `whatsapp_templates` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_messages`
--

LOCK TABLES `email_messages` WRITE;
/*!40000 ALTER TABLE `email_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `email_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_jurnal`
--

DROP TABLE IF EXISTS `invoice_jurnal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_jurnal` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_invoice` varchar(120) NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `judul_artikel` text NOT NULL,
  `nama_penulis` varchar(255) NOT NULL,
  `institusi_penulis` varchar(255) DEFAULT NULL,
  `nama_jurnal` varchar(255) NOT NULL,
  `jumlah_tagihan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` enum('Belum Dibayar','Menunggu Pembayaran','Lunas') NOT NULL DEFAULT 'Belum Dibayar',
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_invoice` (`nomor_invoice`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_jurnal`
--

LOCK TABLES `invoice_jurnal` WRITE;
/*!40000 ALTER TABLE `invoice_jurnal` DISABLE KEYS */;
INSERT INTO `invoice_jurnal` VALUES (1,'INV-JRN-0001/2026','2026-05-16','2026-05-17','STRATEGI PESANTREN DALAM MENJAGA MUTU KEDISIPLINAN SANTRI','Silvester Jenahut','Universitas San Pedro','LEKSIKON: Jurnal Pendidikan Bahasa, Sastra, & Budaya',250000.00,'Lunas',NULL,'2026-05-16 17:02:19','2026-06-29 14:00:11','2026-06-29 14:00:11');
/*!40000 ALTER TABLE `invoice_jurnal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `journals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `publisher_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(80) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `issn` varchar(50) DEFAULT NULL,
  `e_issn` varchar(50) DEFAULT NULL,
  `p_issn` varchar(50) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `commitment_statement_url` varchar(255) DEFAULT NULL,
  `recruitment_intro` text DEFAULT NULL,
  `default_stamp_path` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `default_signer_name` varchar(191) DEFAULT NULL,
  `default_signer_title` varchar(191) DEFAULT NULL,
  `default_signature_path` varchar(255) DEFAULT NULL,
  `pdf_sig_left_px` int(11) DEFAULT NULL,
  `pdf_sig_top_px` int(11) DEFAULT NULL,
  `pdf_sig_height_px` int(11) DEFAULT NULL,
  `pdf_sig_scale_percent` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `slug` (`slug`),
  KEY `journals_publisher_id_foreign` (`publisher_id`),
  CONSTRAINT `journals_publisher_id_foreign` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journals`
--

LOCK TABLES `journals` WRITE;
/*!40000 ALTER TABLE `journals` DISABLE KEYS */;
INSERT INTO `journals` VALUES (1,2,'Abdi Nusantara: Jurnal Pengabdian Kepada Masyarakat','ABDI NUSANTARA','abdi-nusantara-jurnal-pengabdian-kepada-masyarakat',NULL,'3089-5111 ','3089-512X','https://ejurnal.edumedia.or.id/abdinusantara',NULL,NULL,NULL,'journals/logos/1776866479_898fca6e886684e6bd2d.png','Osniman Paulina Maure, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1776859116_2f02527f75b16743ed69.png',20,10,85,125,'2026-04-21 09:58:20','2026-05-28 18:12:59'),(2,1,'Abdi Unisap: Jurnal Pengabdian Kepada Masyarakat','ABDIUNISAP','abdi-unisap-jurnal-pengabdian-kepada-masyarakat',NULL,'2987-9175',' 2987-9183','https://ejurnal-unisap.ac.id/index.php/abdiunisap',NULL,'<p style=\"text-align: justify;\">ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat diterbitkan oleh UPT Publikasi dan Penerbitan Universitas San Pedro. ABDI UNISAP menerima naskah artikel yang merupakan diseminasi dari luaran program kegiatan pengabdian kepada masyarakat (PKM). Adapun kegiatan PKM yang dapat dipublikasikan, yaitu kegiatan PKM yang merupakan bagian dari penerapan hasil penelitian yang dan pengembangan IPTEKS yang sesuai dengan kebutuhan masyarakat untuk memajukan kesejahteraan masyarakat dan mencerdaskan kehidupan bangsa. Kegiatan PkM yang dapat dipublikasikan dapat berupa Layanan kepada masyarakat, Penerapan IPTEKS, Peningkatan kapasitas masyarakat, dan Pemberdayaan masyarakat.</p><p style=\"text-align: justify;\">Kami menyadari bahwa untuk mencapai kualitas dan mutu sebuah jurnal ilmiah, tentunya membutuhkan waktu yang cukup lama. Tidak hanya itu, kerja sama tim redaksi khususnya para Reviewer sangat diperlukan untuk bisa mencapai tujuan yang dimaksud.&nbsp;</p><p style=\"text-align: justify;\">Berkaitan dengan hal di atas, maka kami Tim Redaksi ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat mengundang Bapak/Ibu/Saudara-Saudari sekalian untuk bergabung bersama kami sebagai Editor atau Reviewer&nbsp; di jurnal yang sedang kami kelola saat ini. Harapannya, kehadiran Bapak/Ibu sebagai Editor/ Reviewer dapat memberikan kontribusi yang sangat berarti demi kemajuan Jurnal ini.&nbsp;</p><p style=\"text-align: justify;\">Bagi Bapak/Ibu yang bersedia untuk bergabung bersama kami sebagai&nbsp; Editor/Reviewer, bisa mengisi formulir yang sudah persiapkan.</p><p style=\"text-align: justify;\">Demikian penyampaian kami, atas perhatian dan kerja samanya, kami ucapkan terima kasih.&nbsp;</p><p style=\"text-align: justify;\"><b>Salam hormat kami,&nbsp;</b></p><p style=\"text-align: justify;\">ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat</p>',NULL,'journals/logos/1776866353_d9674ac331c0846ce0c7.png','Konradus Silvester Jenahut, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1776859049_a7a7154eb1cd6f260126.png',20,9,100,100,'2026-04-21 09:59:44','2026-06-11 21:00:37'),(3,1,'SIBERNETIK: Jurnal Pendidikan dan Pembelajaran','SIBERNETIK','sibernetik-jurnal-pendidikan-dan-pembelajaran',NULL,'2988-0823 ','2988-0858','https://ejurnal-unisap.ac.id/index.php/sibernetik',NULL,NULL,NULL,'journals/logos/1776866177_223d356ea296088da75a.png','Konradus Silvester Jenahut, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1776756640_612f14412cd0f534d1c3.jpg',20,10,85,125,'2026-04-21 10:00:33','2026-05-18 22:52:07'),(5,1,'LEKSIKON: Jurnal Pendidikan Bahasa, Sastra, & Budaya','LEKSIKON','leksikon-jurnal-pendidikan-bahasa-sastra-budaya',NULL,'3025-1516','3025-1249','https://ejurnal-unisap.ac.id/index.php/leksikon',NULL,NULL,NULL,'journals/logos/1776866687_9ec2787cfeeaa263d73b.png','Konradus Silvester Jenahut, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1776866687_10cec1c186c291a0c671.png',20,10,85,NULL,'2026-04-22 22:04:47','2026-04-22 22:04:47'),(6,1,'EDUKASI TEMATIK: Jurnal Pendidikan Sekolah Dasar','EDUKASI-TEMATIK','edukasi-tematik-jurnal-pendidikan-sekolah-dasar',NULL,'2746-8011','','https://ejurnal.unisap.ac.id/edukasitematik',NULL,NULL,NULL,'journals/logos/1776866813_9e8379127094568972e1.png','Konradus Silvester Jenahut, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1776866814_6f81fdbc5717d2fc99bf.jpg',20,10,85,NULL,'2026-04-22 22:06:54','2026-04-22 22:06:54'),(7,4,'Leibniz: Jurnal Matematika','Leibniz','leibniz-jurnal-matematika',NULL,'2775-2356 ','','','https://ejurnal.unisap.ac.id/leibniz/index','<p><br></p>',NULL,'journals/logos/1782396118_b2911d5acdefe1c39828.png','Osniman Paulina Maure, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1782396144_2a63fa14763f872617e9.png',20,10,85,100,'2026-06-25 22:01:59','2026-06-25 22:02:24');
/*!40000 ALTER TABLE `journals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loa_letters`
--

DROP TABLE IF EXISTS `loa_letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loa_letters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint(20) unsigned NOT NULL,
  `loa_request_id` bigint(20) unsigned NOT NULL,
  `loa_number` varchar(120) NOT NULL,
  `article_url` varchar(255) NOT NULL,
  `article_id_external` varchar(100) DEFAULT NULL,
  `title` text NOT NULL,
  `authors_json` longtext NOT NULL,
  `corresponding_email` varchar(191) NOT NULL,
  `affiliations_json` longtext DEFAULT NULL,
  `volume` varchar(20) DEFAULT NULL,
  `issue_number` varchar(20) DEFAULT NULL,
  `published_year` varchar(4) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `verification_hash` varchar(191) NOT NULL,
  `public_token` varchar(191) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `revoked_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loa_number` (`loa_number`),
  UNIQUE KEY `verification_hash` (`verification_hash`),
  UNIQUE KEY `public_token` (`public_token`),
  KEY `loa_letters_journal_id_foreign` (`journal_id`),
  KEY `loa_letters_loa_request_id_foreign` (`loa_request_id`),
  CONSTRAINT `loa_letters_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `loa_letters_loa_request_id_foreign` FOREIGN KEY (`loa_request_id`) REFERENCES `loa_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_letters`
--

LOCK TABLES `loa_letters` WRITE;
/*!40000 ALTER TABLE `loa_letters` DISABLE KEYS */;
INSERT INTO `loa_letters` VALUES (1,1,1,'001/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','https://ejurnal.edumedia.or.id/abdinusantara',NULL,'Kuliah Kerja Nyata Merajut untuk Meningkatkan Kreativitas Ibu Rumah Tangga di Cipayung','[{\"name\":\"Mirza Savita\"},{\"name\":\"Sri Lestari Handayani\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','2201035004@uhamka.ac.id','[\"Universitas Muhammadiyah Prof. Dr. Hamka\"]','2','1','2026','published','49dc33a50a60a1f9685d56dff5fceaddd851f96a5d9b35d9339bd1ab824c87fa','TvKc7GYXMA1Agtkp5IVnxAUP29QSwjpc',NULL,'2026-02-21 15:07:34',NULL,NULL,'2026-02-21 15:07:34','2026-05-28 18:12:59'),(2,1,2,'002/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','',NULL,'MENJADI ORANG TUA YANG DI RINDUKAN ANAK MELALUI PROGRAM KKN : PENYULUHAN PSIKOLOG KELUARGA BAHAGIA DESA CIPEUCANG KAB.BOGOR','[{\"name\":\"Rr Ulfah Sholihah\"},{\"name\":\"Aisha Agustina\"},{\"name\":\"Dina Septiana\"},{\"name\":\"Nadya Lutfiah Azizah\"},{\"name\":\"Ade Falah\"},{\"name\":\"Paramita Emi Astuti\"}]','rrulfahsholihah93@gmail.com','[\"Manajemen Pendidikan Islam\",\"Pendidikan Guru Madrasah Ibtidaiyah\",\"STIT Fatahillah Cileungsi Kab.Bogor\"]','2','01','2026','published','d033c6f7a2d9900da1469abd7ac431d6774ecefb804002b4f845723e11818c14','C5n5qYEGpek3onpS07l5YkuDW7FrBhSu',NULL,'2026-02-21 15:07:58',NULL,NULL,'2026-02-21 15:07:58','2026-05-28 18:12:59'),(3,1,3,'003/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','',NULL,'Workshop dan Training Implementasi Integrasi Odoo dengan Khanza untuk Sistem Payroll Jasa Medis Dokter di Rumah Sakit','[{\"name\":\"Hasan\"}]','hasansulistyo96@gmail.com','[\"Sekolah Tinggi Teknologi Informatika Sony Sugema\"]','2','1','2026','published','480fd690270877bd5d64b1cc06bf8ca95a98377d379cc59f18b12725d15ae958','8X3t7GQx7pjIJFTLPBrHiLQvIy6ap6md',NULL,'2026-02-22 06:42:13',NULL,NULL,'2026-02-22 06:42:13','2026-05-28 18:12:59'),(4,1,4,'004/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','',NULL,'Deep Learning Manifestation for TEYL at PPA IO-0133 Krammer Hilina\'a Kota Gunungsitoli','[{\"name\":\"Yasminar Amaerita Telaumbanua\"},{\"name\":\"Meniati Zebua\"},{\"name\":\"Dari Hati Gulo\"},{\"name\":\"Pujawati Waruwu\"},{\"name\":\"Yaredi Waruwu\"},{\"name\":\"Hasna Zebua\"},{\"name\":\"Zun Kelvin Eferistus Albers Halawa\"}]','meizebua323@gmail.com','[\"Universitas Nias\"]','2','1','2026','published','62298d98c19039266eb622546ebb729ee1db3610af28ad11d16f5abde132ceba','JzgDimSgG7RgzyvrriBy2PMfT4PeGz7N',NULL,'2026-02-22 11:03:01',NULL,NULL,'2026-02-22 11:03:01','2026-05-28 18:12:59'),(5,1,5,'005/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','https://ejurnal.edumedia.or.id/abdinusantara',NULL,'MENDAUR ULANG BAHAN BEKAS SEPERTI KARDUS DAN TUTUP BOTOL MENJADI KALENDER BERSAMA ANAK USIA DINI','[{\"name\":\"Savna Ratu Abidah\"},{\"name\":\"Merina\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','savnaratu03@gmail.com','[\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Sejarah, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Biologi, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\"]','2','1','2026','published','a448170d5b1ea96865ece2b5aaec32f9ff5e33d974d2ca383e77d678b3c3f540','nehys8rQIu38WunwtNeN2DJBCpJH6dhW',NULL,'2026-02-24 03:40:45',NULL,NULL,'2026-02-24 03:40:45','2026-05-28 18:12:59'),(6,1,6,'006/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/II/2026','',NULL,'Eksplorasi Warna Primer Dalam Kegiatan Finger Painting Untuk Mengembangkan Kreativitas Anak Di Cipayung Jakarta Timur','[{\"name\":\"Vyona Angreini Agus\"},{\"name\":\"Merina\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','2201035026@uhamka.ac.id','[\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu\",\"Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka.\",\"Pendidikan Sejarah,\",\"Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr.\",\"Hamka.\",\"Pendidikan Biologi, Fakultas Keguruan dan Ilmu Pendidikan, Universitas\",\"Muhammadiyah Prof. Dr. Hamka.\",\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr.\",\"Hamka.\"]','2','1','2026','published','0f1641eb9454e7143627df1fb2d4cf2148eee08aa0a23bcc0eec5faef14fdde1','tSepWHq5S7Rcc5vw3RHWXZDhlpHkE45v',NULL,'2026-02-25 10:42:14',NULL,NULL,'2026-02-25 10:42:14','2026-05-28 18:12:59'),(8,3,8,'001/LOA/SIBERNETIK/UPT-UNISAP/III/2026','',NULL,'PENGARUH MEDIA MANIPULATIF BLOCK TERHADAP KEMAMPUAN REPRESENTASI MATEMATIS SISWA KELAS II SD PADA MATERI PENGURANGAN','[{\"name\":\"Ratih Shafira Wati1\"},{\"name\":\"Meiliana Nurfitriani\"},{\"name\":\"Dr. N Leni Sri Mulyani\"}]','ratihshafirawati@gmail.com','[\"Universitas Muhammadiyah Tasikmalaya\",\"Universitas Muhammadiyah Tasikmalaya\",\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','published','7bc4e1672193ca95cc909fdfcf4e227c85112c905fb00aac02cdc2822e713db3','UHs87qIshPk3qWP7jnqn94QgipQmsExi',NULL,'2026-03-13 11:27:14',NULL,NULL,'2026-03-13 11:27:14','2026-05-18 22:52:07'),(9,3,9,'002/LOA/SIBERNETIK/UPT-UNISAP/IV/2026','',NULL,'Persepsi Generasi Z Laki-laki Terhadap Minat Menjadi Guru TK','[{\"name\":\"Priska Akwila Buana\"},{\"name\":\"Ahmad Samawi\"}]','priska.akwila.2201536@students.um.ac.id','[\"Universitas Negeri Malang\",\"Universitas Negeri Malang\"]','4','1','2026','published','34839c3fea8ba0acbdd99646bf4465de06db7bd70bda34653d697e4cb02bf44d','6jXuJcdKWe2yzMUV7A2joGNPOCEs1enG',NULL,'2026-04-07 09:07:47',NULL,NULL,'2026-04-07 09:07:47','2026-05-18 22:52:07'),(10,3,10,'003/LOA/SIBERNETIK/UPT-UNISAP/IV/2026','',NULL,'HUBUNGAN TINGKAT KEBUGARAN JASMANI DENGAN HASIL BELAJAR PJOK SISWA KELAS V SDN SUKARAJA','[{\"name\":\"Nurifti Tahusarriroh\"},{\"name\":\"Rahmat Permana\"},{\"name\":\"Yopa Taufik Saleh\"}]','nurifti.t@gmail.com','[\"Pendidikan Guru Sekolah Dasar, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Tasikmalaya, Tasikmalaya, Indonesia\"]','4','1','2026','published','c79595b6fda8f348ab7a72c003ef46516f55404e70cb7ad2d97ba1941438b8de','9J8wn5CWCGT8VVAGbkoYgvIBS8PlPFDQ',NULL,'2026-04-07 09:21:38',NULL,NULL,'2026-04-07 09:21:38','2026-05-18 22:52:07'),(11,2,11,'001/LOA/ABDIUNISAP/UPT-UNISAP/IV/2026','',NULL,'Upaya Pencegahan Masalah Gigi dan Mulut melalui Edukasi dan Pemeriksaan Gigi pada Anak Usia Dini','[{\"name\":\"Desi Andriyani\"},{\"name\":\"Lies Elina P\"}]','desiandriyani2212@gmail.com','[\"Poltekkes Kemenkes Tanjung Karang\"]','4','1','2026','published','23dc7c356b94bec9041688262ad991a2ea5233a66ddd0e85f846dca9fb58df14','0yRpEh0ab2ci0fTysUI7CsKY3VN7FgqM',NULL,'2026-04-08 08:16:52',NULL,NULL,'2026-04-08 08:16:52','2026-06-11 21:00:37'),(12,2,12,'002/LOA/ABDIUNISAP/UPT-UNISAP/IV/2026','',NULL,'Edukasi Kesehatan Gigi dan Mulut terkait Dampak Merokok pada Anak Binaan LPKA Kelas II Bandar Lampung','[{\"name\":\"Ketua: Lies Elina\"},{\"name\":\"Anggota 1: Desi Andriyani\"},{\"name\":\"Anggota 2: Erni Gultom\"}]','desiandriyani2212@gmail.com','[\"Poltekkes Kemenkes Tanjung Karang\"]','4','1','2026','published','ffb64ecc5c38d4b6ca55b03dc4f24321aba3374239ba7be01b88254a875eda8d','JLpvBuSy2h5fOnTVsduvtoZC95tG6Bqn',NULL,'2026-04-08 08:18:08',NULL,NULL,'2026-04-08 08:18:08','2026-06-11 21:00:37'),(13,3,13,'004/LOA/SIBERNETIK/UPT-UNISAP/IV/2026','',NULL,'Pengaruh Model Pembelajaran Kooperatif Tipe STAD Berbantuan Media Educaplay Terhadap Hasil Belajar IPAS Pada Siswa Kelas V SDN Cicariu','[{\"name\":\"Ketua: Riki Abdul Hak\"},{\"name\":\"Anggota 1: Yopa Taufik Saleh\"},{\"name\":\"Anggota 2: N Leni Sri Mulyani\"}]','rikiabdulhak14@gmail.com','[\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','published','699b9cbd6546c2f41af48c42eed189f91c3f525e8b3d90fe758e6257ec05c9d2','gCOjX8nhY3okGIJDVUAAtHILt5hoZ1DY',NULL,'2026-04-09 08:37:27',NULL,NULL,'2026-04-09 08:37:27','2026-05-18 22:52:07'),(14,2,14,'003/LOA/ABDIUNISAP/UPT-UNISAP/IV/2026','',NULL,'PENGENALAN LAPANGAN PERSEKOLAHAN: PENGABDIAN KEPADA MASYARAKAT DALAM BIDANG PENGELOLAAN DI UPA. PERPUSTAKAAN INSTITUT AGAMA ISLAM HIDAYATULLAH BATAM','[{\"name\":\"Fatimah\"},{\"name\":\"Nurpaika\"},{\"name\":\"Halima Molakana\"},{\"name\":\"Sri Ayu Ulumando\"},{\"name\":\"Hadijah\"}]','fatimahfath1954@gmail.com','[\"Institut Agama Islam Hidayatullah Batam\"]','4','1','2026','published','ad08e5c8a49efd37e6fd9cf88ffd031aafd4512f31a875002594476a43dc3ac7','CeL3YFA6BWmLVvptabqmcNvingtrvSGH',NULL,'2026-04-20 12:46:48',NULL,NULL,'2026-04-20 12:46:48','2026-06-11 21:00:37'),(15,2,15,'004/LOA/ABDIUNISAP/UPT-UNISAP/IV/2026','https://ejurnal-unisap.ac.id/index.php/abdiunisap/article/view/468','468','Panduan Deployment PLPI ke Hosting Production','[{\"name\":\"Ketua: Konradus\"}]','silvesterjenahut@gmail.com','[\"Universitas San Pedro\"]','','','','published','806f477f3925944a6ffbb39c410a5bf3e5655aa7869834aefd55ffdcff1a5ccb','7e77ab390a7fa942e5fac27164582d90',NULL,'2026-04-21 15:02:46',NULL,NULL,'2026-04-21 15:02:46','2026-06-11 21:00:37'),(16,3,16,'005/LOA/SIBERNETIK/UPT-UNISAP/IV/2026','',NULL,'Pengaruh Permainan Ca’throw Terhadap Ketepatan Melempar Pada Pelajaran PJOK Siswa Kelas V SDI An-Nahl','[{\"name\":\"Ketua: Wafa Tsabita Rahmani\"},{\"name\":\"Anggota 1: Yopa Taufik Saleh\"},{\"name\":\"Anggota 2: Rahmat Permana\"}]','wafatsabita4@gmail.com','[\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','published','5427203331a99c8f2843441c4384bed1c407f11a02ed297f301569833b1d2d07','8d7d6ff43afa9ecaf33374df9dd09569','loa/LoA-005_LOA_SIBERNETIK_UPT-UNISAP_IV_2026.pdf','2026-04-22 19:40:31',NULL,NULL,'2026-04-22 19:40:31','2026-06-04 19:55:26'),(17,2,17,'005/LOA/ABDIUNISAP/UPT-UNISAP/IV/2026','',NULL,'LAPORAN HASIL KEGIATAN PENGENALAN LAPANGAN PERSEKOLAHAN (PLP) SDII 01 LUQMAN AL-HAKIM 01 HIDAYATULLAH BATAM BATAM','[{\"name\":\"Ketua: Muji sebagai Dosen pembimbing\"},{\"name\":\"Anggota 1: Saharuddin\"},{\"name\":\"Anggota 2: Muhammad Kibar\"},{\"name\":\"Anggota 3: Syahrul Basirun\"}]','muhammadkibar528@gmail.com','[\"Institut Agama Islam Hidayatullah Batam\"]','4','1','2026','published','9a8117d68ca89438d71ee4af0b7c1523821ab2115d40cb87344c8607d93059a7','8c4f0f1cee60553ced6427e92e2040f9',NULL,'2026-04-23 09:26:11',NULL,NULL,'2026-04-23 09:26:11','2026-06-11 21:00:37'),(18,3,24,'006/LOA/SIBERNETIK/UPT-UNISAP/V/2026','',NULL,'Pengaruh Pembelajaran Learning Station Method Terhadap KebugaranJasmani Siswa Kelas V SDN 1 Sindanggalih','[{\"name\":\"Ketua: Alvin Alviani\"},{\"name\":\"Anggota 1: Rahmat Permana\"},{\"name\":\"Anggota 2: Yopa Taufik Saleh\"}]','alvianialvin53@gmail.com','[\"Universitas Muhamadiyah Tasikmalaya\"]','7','3','2016','published','1ffe28edb7be2a639d4e4d879a36765f7523b480fbd7ca9eeba7191f3ff344e2','48cf402e62d605ded415c1d819856eef',NULL,'2026-05-07 13:45:25',NULL,NULL,'2026-05-07 13:45:25','2026-05-18 22:52:07'),(20,2,26,'006/LOA/ABDIUNISAP/UPT-UNISAP/V/2026','',NULL,'Digitalisasi UMKM: Penguatan Identitas Usaha dan Sistem Pembayaran Pada Naong Tenda Gebang Bangkalan','[{\"name\":\"Ketua: Moh. Rizky Abdillah\"},{\"name\":\"Anggota 1: Sudarmiatin\"},{\"name\":\"Anggota 2: Yuli Soesetio\"}]','m.rizkyabdillah10@gmail.com','[\"Universitas Negeri Malang\"]','4','1','2026','published','14ca96082a46f79a5363d0f3b2d10afbc15f586a63f7369a95ccd28feaac20c0','be6edc95026fe0a9329ec07fb75f5495',NULL,'2026-05-13 17:24:09',NULL,NULL,'2026-05-13 17:24:09','2026-06-11 21:00:37'),(21,1,27,'007/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/V/2026','https://ejurnal.edumedia.or.id/abdinusantara/article/view/68','68','Pendampingan Kelompok Kolaboratif dalam Penguatan Kemampuan Pemecahan Masalah Matematika Siswa SMP','[{\"name\":\"Ketua: Nedya Salaputa\"},{\"name\":\"Anggota 1: Nurul Azizah\"}]','nedyasalaputa05@gmail.com','[\"Universitas Muhammadiyah Makassar\"]','2','2','2026','published','b55d763651da78e2566340bd232c3c21a79272d1571bd66ca907a740750cf0b9','bae378d76c67ffa44f2213f7e7f98f9c',NULL,'2026-05-17 19:28:52',NULL,NULL,'2026-05-17 19:28:52','2026-05-28 18:12:59'),(22,3,28,'007/LOA/SIBERNETIK/UPT-UNISAP/V/2026','https://ejurnal-unisap.ac.id/index.php/sibernetik/index',NULL,'Relasi Pendidikan Kejuruan, Etos Kerja, dan Nilai Keagamaan terhadap Aspirasi Karier Siswa di SMK Pesantren Al Kausar','[{\"name\":\"Ketua: Haelah Kajalakee\"},{\"name\":\"Anggota 1: Dr. Muh. Hanif\"}]','244110402077@mhs.uinsaizu.ac.id','[\"Universitas Islam Negeri Profesor Kiai Haji Saifuddin Zuhri Purwokerto\"]','4','2','2026','published','7d98a7c9dce42a11e9081bb079c98aa184879c231b148124cea78be95b81c9ea','4c2495f0e7b1c914e41812c227db286c','loa/LoA-007_LOA_SIBERNETIK_UPT-UNISAP_V_2026.pdf','2026-05-18 22:52:15',NULL,NULL,'2026-05-18 22:52:15','2026-05-18 22:53:11'),(23,1,29,'008/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/V/2026','',NULL,'PENGUATAN LITERASI DASAR MELALUI PROGRAM PIJAR KATA PENDEKATAN PEMBELAJARAN MENYENANGKAN DI SEKOLAH DASAR','[{\"name\":\"Ketua: Janatul Aini\"},{\"name\":\"Anggota 1: Ersa Winny Prakusya\"},{\"name\":\"Anggota 2: Juniati\"},{\"name\":\"Anggota 3: M. Irvan Jayadi Assiddik\"},{\"name\":\"Anggota 4: Syafruddin Muhdar\"}]','janatulaini71@gmail.com','[\"Universitas Muhamadiyah Mataram\"]','2','1','2026','published','2683c583bc041ae961cecea988b1dc3e8d904c58f8e74f3c3812a3025f8db9b6','d7ecbfb75ca20e328e8f081d88c4f272','loa/LoA-008_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_V_2026.pdf','2026-05-20 22:28:24',NULL,NULL,'2026-05-20 22:28:24','2026-06-04 03:57:15'),(24,2,30,'007/LOA/ABDIUNISAP/UPT-UNISAP/V/2026','',NULL,'IMPLEMENTASI POSYANDU INTEGRASI (POINTEG) BERBASIS ANDROID DALAM UPAYA PENCEGAHAN STUNTING DAN STROKE DI PEKON JOGYAKARTA SELATAN KECAMATAN GADING REJO  KABUPATEN PRINGSEWU','[{\"name\":\"Ketua: Sri Indra Trigunarso\"},{\"name\":\"Anggota 1: Martini Fairus\"},{\"name\":\"Anggota 2: Bertalina\"},{\"name\":\"Anggota 3: Zainal Muslim\"}]','trigunarsosriindra@gmail.com','[\"Politeknik Kesehatan Kemenkes Tanjungkarang\"]','4','1','2026','published','ccd4335e61591e09c9d875231b489671ea22c9bdcf843b9d607c85d00b06b3e6','fb11298c04cce750a980e689b9ba41bd',NULL,'2026-05-21 15:29:18',NULL,NULL,'2026-05-21 15:29:18','2026-06-11 21:00:37'),(25,3,31,'008/LOA/SIBERNETIK/UPT-UNISAP/V/2026','',NULL,'PENGARUH PENGGUNAAN MEDIA PEMBELAJARAN DIGITAL TERHADAP MOTIVASI BELAJAR SISWA','[{\"name\":\"Ketua: Indri Fitri Hartanti\"},{\"name\":\"Anggota 1: Siti Enik Mukhoiyaroh Bambang\"}]','fitrihartantiindri@gmail.com','[\"Universitas Jambi\"]','4','1','2026','published','b21c8fffb9569e07e166165b6c6290e3f019e1025640082760eafd55724b98df','0885d9440760502b18a6599eab368951','loa/LoA-008_LOA_SIBERNETIK_UPT-UNISAP_V_2026.pdf','2026-05-30 00:40:48',NULL,NULL,'2026-05-30 00:40:48','2026-06-02 02:19:17'),(26,3,33,'009/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PENGUATAN LITERASI DASAR MELALUI PERMAINAN EDUKATIF MISTERI DADU BERWARNA DI SEKOLAH DASAR','[{\"name\":\"Ketua: Eka Kurniati\"},{\"name\":\"Anggota 1: Herwinda Ayu Selviana\"},{\"name\":\"Anggota 2: Larasti Sagita\"},{\"name\":\"Anggota 3: Indah Putri Yayu\"},{\"name\":\"Anggota 4: Maratun Sholihah\"},{\"name\":\"Anggota 5: Ferdiasnyah\"},{\"name\":\"Anggota 6: Syafrudin Muhdar\"}]','ekhakurniati2@gmail.com','[\"Universitas Muhammadiyah Mataram\"]','4','1','2026','published','5597e30854886d983fd65b329bce57dfb0ff516668e1d86bec7bd0aab35109a8','1a4cb84a6aac0cf0cb4f6cc2a19cb732','loa/LoA-009_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-02 01:38:31',NULL,NULL,'2026-06-02 01:38:31','2026-06-02 02:17:54'),(27,3,32,'010/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PENGARUH PUZZLE PANCASILA TERHADAP HASIL BELAJAR SISWA MATERI SIMBOL DAN SILA PANCASILA KELAS 2 SDN BANYUAJUH 5','[{\"name\":\"Anggota 1: Widyaningrum Dwi Oktaviyanti\"},{\"name\":\"Anggota 2: Nanda Kirana Abdullah\"},{\"name\":\"Anggota 3: Pratita Fitrotus Sabilillah\"},{\"name\":\"Anggota 4: Ika Dian Rahmawati\"}]','widyaningrum7908@gmail.com','[\"Universitas Trunodjoyo Madura\"]','4','1','2026','published','d92c2a3fc060c03f85999d09ca0cf1e1fa2ccd4d80e6b644e274e6f68b69ef0f','2c11ce1e861a5fe94ddc9e9bf4484b37','loa/LoA-010_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-03 03:41:15',NULL,NULL,'2026-06-03 03:41:15','2026-06-03 03:41:42'),(28,1,34,'009/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/VI/2026','',NULL,'PENDAMPINGAN PENCATATAN KEUANGAN SEDERHANA DAN PEMAHAMAN PAJAK TERHADAP UMKM WARUNG TENDA DI JALAN WATES YOGYAKARTA ','[{\"name\":\"Anggota 1: Yulia Via Nur Afifah\"},{\"name\":\"Anggota 2: Ika Wulandari\"}]','fifahvia14@gmail.com','[\"Universitas Mercu Buana Yogyakarta\"]','2','2','','published','969518aee769234737708e46074980f1a48821a70b60a04b2039efd6f2b282c8','c6949c83e69a19cd885b4575d5d58206','loa/LoA-009_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_VI_2026.pdf','2026-06-03 16:46:14',NULL,NULL,'2026-06-03 16:46:14','2026-06-03 16:46:48'),(29,2,35,'008/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'Pendidikan Hukum Sebagai Perisai Remaja Dari Tindak Pidana Kekerasan Pada SMA Negeri 1 Kepulauan Aru','[{\"name\":\"Ketua: Maher Syalal Lawalata\"},{\"name\":\"Anggota 1: Clara Kesaulya\"},{\"name\":\"Anggota 2: Johan Pieter Elia Rumangun\"},{\"name\":\"Anggota 3: Dita Ayudia Pratiwi\"},{\"name\":\"Anggota 4: Nugrah Gables Manery\"},{\"name\":\"Anggota 5: Rocky Steevy Mantaiborbir\"},{\"name\":\"Anggota 6: Muhamad Akbar Yanlua\"}]','mahersyalallawalata@gmail.com','[\"Universitas Pattimura\"]','4','1','2026','published','88cbc3dfc16920cf7c6562479dd51a376cf47b4abd5655f840c8eaf24d07c4b7','d1cfdb59bfb4b0cd095292638b56ee64',NULL,'2026-06-04 03:28:00',NULL,NULL,'2026-06-04 03:28:00','2026-06-11 21:00:37'),(30,3,36,'011/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Pengaruh Permainan Bola Beracun Terhadap Koordinasi Mata dan Tangan Siswa Kelas IV Pada Pelajaran PJOK SDN CICARIU','[{\"name\":\"Ketua: Reksa Ashona\"},{\"name\":\"Anggota 1: Yopa Taufik Saleh\"},{\"name\":\"Anggota 2: Rahmat Permana\"}]','astunashona@gmail.com','[\"Universitas Muhammdiyah Tasikmalaya\"]','4','1','2026','published','81177a4885ad50afc5675252cb275fb3302257da8550cc74f5dad9f09de2374b','3cbc3ae95f7b7a0bf3f3a8782c05ce1e','loa/LoA-011_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-05 21:31:26',NULL,NULL,'2026-06-05 21:31:26','2026-06-05 21:32:05'),(31,3,38,'012/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PENGEMBANGAN MEDIA PEMBELAJARAN FLIPBOOK DIGITAL BERBASIS NYANYIAN ANAK PADA MATERI KERAGAMAN BUDAYA DI JAWA TIMUR KELAS V SDN KENITEN 1','[{\"name\":\"Ketua: Devina Damayanti\"},{\"name\":\"Anggota 1: Wahyudi\"},{\"name\":\"Anggota 2: Sutrisno Sahari\"}]','devinadamayanti981@gmail.com','[\"Universitas Nusantara PGRI Kediri\"]','4','1','2026','published','60294808d09c3dce10472d725b262635be4c2e6c3db8ed38e278496b22f7d40d','6d26f9b63bffb77a1dd473f10e1b5181','loa/LoA-012_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-06 03:34:14',NULL,NULL,'2026-06-06 03:34:14','2026-06-06 03:36:33'),(32,2,37,'009/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'PENERAPAN PROGRAM “PANGANDARAN BLUE & CLEAN” MELALUI KEGIATAN PLOGGING DAN KONTEN DIGITAL UNTUK MENINGKATKAN KESADARAN LINGKUNGAN DI KAWASAN WISATA PANGANDARAN ','[{\"name\":\"Ketua: Susilawati\"},{\"name\":\"Anggota 1: Syahrul Ramdani\"},{\"name\":\"Anggota 2: Salma Azhar F\"},{\"name\":\"Anggota 3: Nur Hayati\"},{\"name\":\"Anggota 4: Putri Aisyah\"},{\"name\":\"Anggota 5: Alifa Azzahra\"},{\"name\":\"Anggota 6: Lisvi\"},{\"name\":\"Anggota 7: Herni Yuliantika\"},{\"name\":\"Anggota 8: Siti Nuraisyah\"},{\"name\":\"Anggota 9: Afifah Nadiatul M\"},{\"name\":\"Anggota 10: Devina Dewi Aryanto\"}]','ramdanisyahrul010@gmail.com','[\"Universitas Teknologi Digital\"]','4','1','2026','published','8fda2000bb8119c8b893802ebcdec790bcf2de1257ae4ff9d28fd30b1f3f57a5','cce640df8b565924fa4d2f290a1befec',NULL,'2026-06-06 03:34:44',NULL,NULL,'2026-06-06 03:34:44','2026-06-11 21:00:37'),(33,3,39,'013/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PENERAPAN PEMBELAJARAN BERBASIS DIGITAL STORYTELLING MENGGUNAKAN STORYJUMPER TERHADAP PENINGKATAN KETERAMPILAN LITERASI SISWA SD','[{\"name\":\"Ketua: Wahyu Indri Astuti\"},{\"name\":\"Anggota 1: Rafa Nurul Ghozama\"},{\"name\":\"Anggota 2: Ika Dian Rahmawati\"}]','rafanurulghozama@gmail.com','[\"Universitas Trunodjoyo Madura\"]','4','1','2026','published','8c3f5fb0c90d47b1a3e9c3d573172d52ad04a6a30f2bbd14fe2997189003e048','8a3d328a9b1de1d8ba5d6ad9ad534415','loa/LoA-013_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-08 21:05:20',NULL,NULL,'2026-06-08 21:05:20','2026-06-08 21:06:23'),(34,2,42,'010/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'PELATIHAN MANAJEMEN WAKTU UNTUK MENINGKATKAN PEMAHAMAN DAN KETERAMPILAN PENGELOLAAN WAKTU PADA SISWA SMP IT YAYASAN HAJJAH FAUZIAH BINJAI','[{\"name\":\"Anggota 1: Cici Resi Desoli Saragih\"},{\"name\":\"Anggota 2: Shafwa Davina\"},{\"name\":\"Anggota 3: Prity Sandiya\"},{\"name\":\"Anggota 4: Ade Fania Ramadhani\"},{\"name\":\"Anggota 5: Dwinta Aurelia\"},{\"name\":\"Anggota 6: Anisa Hargita Nupa\"},{\"name\":\"Anggota 7: Fadlan Mulia Alfharizi Siregar\"},{\"name\":\"Anggota 8: Zahra Nabila\"},{\"name\":\"Anggota 9: Eka Danta Jaya Ginting\"}]','shafwadavina126@gmail.com','[\"Universitas Sumatera Utara\"]','4','1','2026','published','d6232bf11f268c8a32f637206c70fb500d6f460934ec3ab4ea279bb05fd70818','313c414d5d84beb10541a2b82adb8bc1','loa/LoA-010_LOA_ABDIUNISAP_UPT-UNISAP_VI_2026.pdf','2026-06-09 00:17:04',NULL,NULL,'2026-06-09 00:17:04','2026-06-12 16:24:50'),(35,3,41,'014/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Pengaruh Latihan Lompat Pipa terhadap Kemampuan *Block* Siswa Ekstrakurikuler Bola Voli MTs Negeri 04 Brebes','[{\"name\":\"Ketua: Ilya Adit Triana\"}]','uliasaja@gmail.com','[\"Universitas muhammadiyah kuningan\"]','4','1','2026','published','3539584e4e5a16fa13a0d58029ed1f58a91786d4ff0c2754288675f676487c4b','3fb673d2b97912e300165fc9b9b87f22','loa/LoA-014_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-09 15:39:53',NULL,NULL,'2026-06-09 15:39:53','2026-06-09 15:40:57'),(36,2,44,'011/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'WORKSHOP PEMBELAJARAN BERBASIS ARTIFICIAL INTELLIGENCE DALAM KETERAMPILAN BERBAHASA  DI ERA 5.0','[{\"name\":\"Ketua: Donny Adiatmana Ginting\"},{\"name\":\"Anggota 1: Donal Fernando Lubis\"},{\"name\":\"Anggota 2: Resti Amalia\"}]','donny-adiatama@gmail.com','[\"Universitas Bangka Belitung\"]','4','1','','published','9fa9298146f24b400e0706f9cf5500b08928a7c8ae8cbf0c108eaff505f3ef55','bcfa21b5ae3d067f878577192692f931',NULL,'2026-06-11 14:07:04',NULL,NULL,'2026-06-11 14:07:04','2026-06-11 21:00:37'),(38,1,46,'010/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/VI/2026','',NULL,'Pelatihan Pembuatan Masker Kain untuk Meningkatkan Kesadaran Kesehatan dan Keterampilan Produktif Masyarakat di Tengaran','[{\"name\":\"Ketua: Rahayu Lestari\"},{\"name\":\"Anggota 1: Mei Restiana\"}]','rahayulest1603@gmail.com','[\"Universitas Terbuka\"]','2','2','','published','c50d1086d1a3a1e235dc6f47f7f0f1ab446efa6bbd6676b0b82770eccacce2ca','06a217c047544aa550693a307ccc7796','loa/LoA-010_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_VI_2026.pdf','2026-06-13 01:25:13',NULL,NULL,'2026-06-13 01:25:13','2026-06-13 01:26:06'),(39,2,45,'012/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'Pendampingan Pengelolaan Sarana dan Prasarana Pasar Rakyat Kota Malang bagi UPT Pasar','[{\"name\":\"Anggota 1: Lale Agustia Olivia Rosadi\"},{\"name\":\"Anggota 2: Rossi Ananda Arifin\"},{\"name\":\"Anggota 3: Ida Soraya\"},{\"name\":\"Anggota 4: Sinollah\"}]','oliviarosadi0@gmail.com','[\"Universitas Islam Raden Rahmat\"]','4','1','2026','published','80258f98314f579d6990791ba302ae9f9891a00b637b45a30a6592131984e607','9b2ef4afff899d18835198fc6837f4cc','loa/LoA-012_LOA_ABDIUNISAP_UPT-UNISAP_VI_2026.pdf','2026-06-13 01:26:54',NULL,NULL,'2026-06-13 01:26:54','2026-06-26 19:38:01'),(40,1,48,'011/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/VI/2026','',NULL,'Memperkuat Daya Saing UMKM Sate dan UMKM Sempol melalui Pencatatan Keuangan Mandiri ','[{\"name\":\"Ketua: Vivia Anggita\"},{\"name\":\"Anggota 1: Martinus Budiantara\"}]','anggitavivia@gmail.com','[\"Universitas Mercu Buana Yogyakarta\"]','2','2','2026','published','c870699b837e56fc0e1679c242fffea5ecb5f9fa12b93ed4e897f4d02aef1c60','fe2811f8348c7111026a20bd9ed3cc1c','loa/LoA-011_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_VI_2026.pdf','2026-06-18 22:41:19',NULL,NULL,'2026-06-18 22:41:19','2026-06-18 22:41:47'),(41,3,49,'015/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Penerapan Mindful Parenting dalam Mengatasi Tantrum pada Anak Usia Dini: Studi Literatur','[{\"name\":\"Ketua: Eisya Maheera\"},{\"name\":\"Anggota 1: Endah Tejaningsih\"}]','eisyamaheera@gmail.com','[\"UIN Raden Mas Said\"]','4','1','','published','68ccedd0ebc7b1aff3586d12a677b5b03ee1cb04e82237a47e4de021565d7d9e','0192ed3bd2d38aa0ef5676cd3bf642c9','loa/LoA-015_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-18 22:42:24',NULL,NULL,'2026-06-18 22:42:24','2026-06-18 22:42:37'),(42,3,52,'016/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Pengembangan Media Diorama Berbasis Kontekstual Pada Pembelajaran IPAS di Kelas V SD Negeri 1 Langsa','[{\"name\":\"Ketua: Maulida\"},{\"name\":\"Anggota 1: Tengku Muhammad Sahudra\"},{\"name\":\"Anggota 2: Mufti Riyani\"}]','mmaulidaa05@gmail.com','[\"Program Studi Pendidikan Guru Sekolah Dasar Fakultas Keguruan dan Ilmu Pendidikan Universitas Samudra\"]','4','1','2026','published','bbda99ba025bd0354a232b4f97b458cde08c5e93c74a7d945810389aec017667','8f13dfd2b5adffd69385785ee6ff5f6f','loa/LoA-016_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-22 21:35:04',NULL,NULL,'2026-06-22 21:35:04','2026-06-22 21:35:45'),(43,3,54,'017/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'ANALISIS ETIKA KEPROFESIAN GURU PAUD DALAM MENGHADAPI TANTANGAN MORAL PADA PEMBELAJARAN DIERA DIGITAL DI RA MUSLIMAT NU SUMURJOMBLANGBOGO','[{\"name\":\"Ketua: Ufiya Adrlina Zahiro\"},{\"name\":\"Anggota 1: Suci Sukma Dewi\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','ufiyaardlina16@gmail.com','[\"Universitas Islam Negeri K.H. Abdurrahman Wahid Pekalongan\"]','4','1','2026','published','d175c5841c5094f9c0ba6e899dd027d2c660776285f10ebe5b2de1fe8dbd6b59','bd93c191bf33aa9ddf02ba04b6cc5d2b','loa/LoA-017_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-23 19:03:31',NULL,NULL,'2026-06-23 19:03:31','2026-06-23 19:03:42'),(44,3,50,'018/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Pentingnya Etika Kejujuran dan Komunikasi Santun pada Anak Usia Dini di Era Digital','[{\"name\":\"Ketua: Nilatul Asna\"},{\"name\":\"Anggota 1: Fadia Hasanah\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','nilaasna208@gmail.com','[\"Universitas Islam Negeri K.H.Abdurrahman Wahid Pekalongan\"]','4','1','','published','ec990ba17c3e5c8fd55751eca1bcd088e75b9bbb0b233f6787dab5addfcbb24f','39334332c342b6e06bffc1bd88996e31','loa/LoA-018_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-24 02:29:06',NULL,NULL,'2026-06-24 02:29:06','2026-06-24 02:29:19'),(45,3,51,'019/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'Eksposur Visual Tanpa Izin Di Tiktok: Analisis Kritis Privasi Anak Usia Dini ','[{\"name\":\"Ketua: Alya Nabila Nailatul Izza\"},{\"name\":\"Anggota 1: Tasya Aulia Zahra\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','alynbilanaizza@gmail.com','[\"Universitas Islam Negeri K.H Abdurrahman Wahid Pekalongan Jawa Tengah\"]','4','1','2026','published','a1b08e58c92ccdaa760009743acbb31eb4eb93e5892b4180bf14946d63278a49','622107bc44a08733f078ad85d1dcee81','loa/LoA-019_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-24 02:30:13',NULL,NULL,'2026-06-24 02:30:13','2026-06-24 02:30:55'),(46,5,53,'001/LOA/LEKSIKON/UPT-UNISAP/VI/2026','',NULL,'Religiusitas dalam Puisi Negeri Daging Karya A. Musthofa Bisri: Kajian Hermeneutika Paul Ricoeur ','[{\"name\":\"Ketua: Aurellia Sarah Gunawan\"},{\"name\":\"Anggota 1: Najma Kamila Safithri\"},{\"name\":\"Anggota 2: Amanda Maharani Asmar\"},{\"name\":\"Anggota 3: Mefta Maudia\"}]','atiqoh.fitriyah94@gmail.com','[\"UIN Syarif Hidayatullah Jakarta\"]','4','1','2026','published','a49bc14707ab71a59b29999621389b1c19899cc02fd90b0dcda1730a5ba7f108','632e55c38da0419bb6fb5c8f44b038e6','loa/LoA-001_LOA_LEKSIKON_UPT-UNISAP_VI_2026.pdf','2026-06-24 15:46:34',NULL,NULL,'2026-06-24 15:46:34','2026-06-24 15:48:01'),(47,3,56,'020/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'EduGrow Sebagai Alat Asesmen Perkembangan Anak Usia 4-6 Tahun Pada Lembaga Paud Berbasis Website ','[{\"name\":\"Ketua: Rimaya Safru Wiyusi\"},{\"name\":\"Anggota 1: I Wayan Sutama\"}]','safruwiyusi@gmail.com','[\"Universitas Negeri Malang\"]','4','1','2026','published','1515f60fb138a54576630775898bcd55ee14a5d6ce8500e93971af619c057160','fc504b956bd5a155a51457dd26a25d5a','loa/LoA-020_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-25 18:11:17',NULL,NULL,'2026-06-25 18:11:17','2026-06-25 18:11:40'),(48,2,57,'013/LOA/ABDIUNISAP/UPT-UNISAP/VI/2026','',NULL,'EDUKASI TEKNIK NONFARMAKOLOGI UNTUK MENGATASI KETIDAKNYAMANAN PADA PENDERITA HIPERTENSI','[{\"name\":\"Ketua: Andi Saifah\"},{\"name\":\"Anggota 1: Suwarty Nursahara Usman Putra\"},{\"name\":\"Anggota 2: Dewi Hartatik\"}]','andi.saifah.untad@gmail.com','[\"Ketua: Universitas Tadulako\",\"Anggota 1: STIKes Bala Keselamatan Palu\",\"Anggota 2: Puskesmas Marawola Sigi\"]','4','1','2026','published','3d6790fad2bb6a0bc09e946b7348b34c8fc33a52bb059a908250c3409351bb12','f306e42cca26c3df2e131ad637892d43','loa/LoA-013_LOA_ABDIUNISAP_UPT-UNISAP_VI_2026.pdf','2026-06-26 03:06:07',NULL,NULL,'2026-06-26 03:06:07','2026-06-26 03:06:41'),(49,3,61,'021/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PERSEPSI GURU TERHADAP PEMBERIAN INSENTIF DAN TUNJANGAN DALAM MENINGKATKAN KINERJA GURU DI SEKOLAH SWASTA KECAMATAN CIPUTAT, KOTA TANGERANG SELATAN','[{\"name\":\"Ketua: Nurrabiatul dan Sri Utaminingsih\"}]','nunamanah@gmail.com','[\"Program Pascasarjana Universitas Pamulang  Magister Manajemen Pendidikan\"]','4','2','','published','e8887d8d4304ae5c0917f94ad7b071964199c8c902fd4a5503c4d760d18d47b0','8cc944fabdda9fbd57ba7075806d381e','loa/LoA-021_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-27 16:14:52',NULL,NULL,'2026-06-27 16:14:52','2026-06-27 16:15:46'),(50,1,59,'012/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/VI/2026','https://ejurnal-unisap.ac.id',NULL,'Pendampingan Pelayanan Administrasi Dalam Meningkatkan Kualitas Layanan Bagi Guru dan Tenaga Kependidikan di UPT DISDIKPORA Tempuran','[{\"name\":\"Ketua: Tia Salsabila Lubna Fahima\"},{\"name\":\"Anggota 1: Milna Wafirah\"}]','salsabilatia11@gmail.com','[\"Institut Agama Islam Syubbanul Wathon Magelang, Indonesia\"]','2','2','2026','published','ad9a1ff53d14f37aa33215f072f1d2b46a318078e9ee18e88cdcf9f38eb3bdb8','9e7fcc80b2dc0536afa8ae1e18383003','loa/LoA-012_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_VI_2026.pdf','2026-06-27 16:30:21',NULL,NULL,'2026-06-27 16:30:21','2026-06-27 16:31:21'),(51,1,62,'013/LOA/ABDI-NUSANTARA/MEDIA-NUSANTARA/VI/2026','',NULL,'Pelatihan Desain Grafis CorelDraw Untuk Membuat Poster di SMK Ibrahimy 1 Sukorejo Situbondo ','[{\"name\":\"Ketua: Zaehol Fatah\"},{\"name\":\"Anggota 1: M Hanif Fachri Zubair\"}]','fachrizubair01@gmail.com','[\"Universitas Ibrahimy\"]','2','2','2026','published','4c547af862b7793671786998beb5e6eae04a977b0b7114528ade0de2702058b5','946fbe5a109ec7cf18b6ebe8d05675f6','loa/LoA-013_LOA_ABDI-NUSANTARA_MEDIA-NUSANTARA_VI_2026.pdf','2026-06-27 17:39:29',NULL,NULL,'2026-06-27 17:39:29','2026-06-27 18:18:23'),(52,3,63,'022/LOA/SIBERNETIK/UPT-UNISAP/VI/2026','',NULL,'PENGARUH PEMBELAJARAN DIAGRAM BATANG EDUCATION (DBE) NUMERASI TERHADAP KEMAMPUAN BERPIKIR KRITIS MURID DI SEKOLAH DASAR','[{\"name\":\"Ketua: Syamsul Hidayah\"},{\"name\":\"Anggota 1: Joko Soebagyo\"},{\"name\":\"Anggota 2: Nila Fitria\"}]','syamsulhidayah1718@gmail.com','[\"Sekolah Pascasarjana, Universitas Muhammadiyah Prof. DR. Hamka\"]','4','2','2026','published','be69a3eb74f642403d02bfb7c9466ec943902115dd6fb0d420bff8d2cbd64a0c','866951ccf21dd5458367aa92e438e113','loa/LoA-022_LOA_SIBERNETIK_UPT-UNISAP_VI_2026.pdf','2026-06-29 22:11:54',NULL,NULL,'2026-06-29 22:11:54','2026-06-29 22:13:54');
/*!40000 ALTER TABLE `loa_letters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loa_notifications`
--

DROP TABLE IF EXISTS `loa_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loa_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loa_letter_id` bigint(20) unsigned NOT NULL,
  `status` varchar(60) NOT NULL DEFAULT 'menunggu',
  `sent_to_email` varchar(191) DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loa_letter_id` (`loa_letter_id`),
  CONSTRAINT `loa_notifications_loa_letter_id_foreign` FOREIGN KEY (`loa_letter_id`) REFERENCES `loa_letters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_notifications`
--

LOCK TABLES `loa_notifications` WRITE;
/*!40000 ALTER TABLE `loa_notifications` DISABLE KEYS */;
INSERT INTO `loa_notifications` VALUES (1,1,'menunggu',NULL,NULL,'2026-02-21 15:07:34','2026-02-21 15:07:36'),(2,2,'menunggu',NULL,NULL,'2026-02-21 15:07:58','2026-02-21 15:08:00'),(3,3,'menunggu',NULL,NULL,'2026-02-22 06:42:13','2026-02-22 06:42:15'),(4,4,'menunggu',NULL,NULL,'2026-02-22 11:03:01','2026-02-22 11:03:04'),(5,5,'menunggu',NULL,NULL,'2026-02-24 03:40:45','2026-02-24 03:40:48'),(6,6,'menunggu',NULL,NULL,'2026-02-25 10:42:14','2026-02-25 10:42:16'),(8,8,'menunggu',NULL,NULL,'2026-03-13 11:27:14','2026-03-13 11:27:17'),(9,9,'menunggu',NULL,NULL,'2026-04-07 09:07:47','2026-04-07 09:07:49'),(10,10,'menunggu',NULL,NULL,'2026-04-07 09:21:38','2026-04-07 09:21:40'),(11,11,'menunggu',NULL,NULL,'2026-04-08 08:16:52','2026-04-08 08:16:54'),(12,12,'menunggu',NULL,NULL,'2026-04-08 08:18:08','2026-04-08 08:18:10'),(13,13,'notifikasi terkirim','rikiabdulhak14@gmail.com','2026-04-25 21:34:29','2026-04-09 08:37:27','2026-04-25 21:34:29'),(14,14,'notifikasi terkirim','fatimahfath1954@gmail.com','2026-04-23 09:12:32','2026-04-20 12:46:48','2026-04-23 09:12:32'),(15,15,'notifikasi terkirim','silvesterjenahut@gmail.com','2026-04-21 15:27:50','2026-04-21 15:02:46','2026-04-21 15:27:50'),(16,16,'notifikasi terkirim','wafatsabita4@gmail.com','2026-04-22 20:11:57','2026-04-22 19:40:31','2026-04-22 20:11:57'),(17,17,'notifikasi terkirim','muhammadkibar528@gmail.com','2026-04-23 09:27:09','2026-04-23 09:26:11','2026-04-23 09:27:09'),(18,18,'notifikasi terkirim','alvianialvin53@gmail.com','2026-05-07 13:45:59','2026-05-07 13:45:25','2026-05-07 13:45:59'),(20,20,'notifikasi terkirim','m.rizkyabdillah10@gmail.com','2026-05-13 17:25:00','2026-05-13 17:24:09','2026-05-13 17:25:00'),(21,21,'notifikasi terkirim','nedyasalaputa05@gmail.com','2026-05-17 19:34:35','2026-05-17 19:28:52','2026-05-17 19:34:35'),(22,22,'notifikasi terkirim','244110402077@mhs.uinsaizu.ac.id','2026-05-18 22:53:12','2026-05-18 22:52:15','2026-05-18 22:53:12'),(23,23,'notifikasi terkirim','janatulaini71@gmail.com','2026-05-20 22:46:30','2026-05-20 22:28:24','2026-05-20 22:46:30'),(24,24,'menunggu',NULL,NULL,'2026-05-21 15:29:18','2026-05-21 15:29:18'),(25,25,'notifikasi terkirim','fitrihartantiindri@gmail.com','2026-06-02 02:19:18','2026-05-30 00:40:48','2026-06-02 02:19:18'),(26,26,'notifikasi terkirim','ekhakurniati2@gmail.com','2026-06-02 02:17:55','2026-06-02 01:38:31','2026-06-02 02:17:55'),(27,27,'notifikasi terkirim','widyaningrum7908@gmail.com','2026-06-03 03:41:42','2026-06-03 03:41:15','2026-06-03 03:41:42'),(28,28,'notifikasi terkirim','fifahvia14@gmail.com','2026-06-03 16:46:48','2026-06-03 16:46:14','2026-06-03 16:46:48'),(29,29,'notifikasi terkirim','mahersyalallawalata@gmail.com','2026-06-04 03:28:24','2026-06-04 03:28:00','2026-06-04 03:28:24'),(30,30,'notifikasi terkirim','astunashona@gmail.com','2026-06-05 21:32:06','2026-06-05 21:31:26','2026-06-05 21:32:06'),(31,31,'notifikasi terkirim','devinadamayanti981@gmail.com','2026-06-06 03:36:33','2026-06-06 03:34:14','2026-06-06 03:36:33'),(32,32,'notifikasi terkirim','ramdanisyahrul010@gmail.com','2026-06-06 03:37:38','2026-06-06 03:34:44','2026-06-06 03:37:38'),(33,33,'notifikasi terkirim','rafanurulghozama@gmail.com','2026-06-08 21:06:24','2026-06-08 21:05:20','2026-06-08 21:06:24'),(34,34,'notifikasi terkirim','shafwadavina126@gmail.com','2026-06-09 15:40:52','2026-06-09 00:17:04','2026-06-09 15:40:52'),(35,35,'notifikasi terkirim','uliasaja@gmail.com','2026-06-09 15:40:57','2026-06-09 15:39:53','2026-06-09 15:40:57'),(36,36,'menunggu',NULL,NULL,'2026-06-11 14:07:04','2026-06-11 14:07:04'),(38,38,'notifikasi terkirim','rahayulest1603@gmail.com','2026-06-13 01:26:07','2026-06-13 01:25:13','2026-06-13 01:26:07'),(39,39,'notifikasi terkirim','oliviarosadi0@gmail.com','2026-06-13 01:27:05','2026-06-13 01:26:54','2026-06-13 01:27:05'),(40,40,'notifikasi terkirim','anggitavivia@gmail.com','2026-06-18 22:41:47','2026-06-18 22:41:19','2026-06-18 22:41:47'),(41,41,'notifikasi terkirim','eisyamaheera@gmail.com','2026-06-18 22:42:37','2026-06-18 22:42:24','2026-06-18 22:42:37'),(42,42,'notifikasi terkirim','mmaulidaa05@gmail.com','2026-06-22 21:35:45','2026-06-22 21:35:04','2026-06-22 21:35:45'),(43,43,'notifikasi terkirim','ufiyaardlina16@gmail.com','2026-06-23 19:03:42','2026-06-23 19:03:31','2026-06-23 19:03:42'),(44,44,'notifikasi terkirim','nilaasna208@gmail.com','2026-06-24 02:29:20','2026-06-24 02:29:06','2026-06-24 02:29:20'),(45,45,'notifikasi terkirim','alynbilanaizza@gmail.com','2026-06-24 02:30:55','2026-06-24 02:30:13','2026-06-24 02:30:55'),(46,46,'notifikasi terkirim','atiqoh.fitriyah94@gmail.com','2026-06-24 15:46:46','2026-06-24 15:46:34','2026-06-24 15:46:46'),(47,47,'notifikasi terkirim','safruwiyusi@gmail.com','2026-06-25 18:11:40','2026-06-25 18:11:17','2026-06-25 18:11:40'),(48,48,'notifikasi terkirim','andi.saifah.untad@gmail.com','2026-06-26 03:06:42','2026-06-26 03:06:07','2026-06-26 03:06:42'),(49,49,'notifikasi terkirim','nunamanah@gmail.com','2026-06-27 16:15:46','2026-06-27 16:14:52','2026-06-27 16:15:46'),(50,50,'notifikasi terkirim','salsabilatia11@gmail.com','2026-06-27 16:31:21','2026-06-27 16:30:21','2026-06-27 16:31:21'),(51,51,'notifikasi terkirim','fachrizubair01@gmail.com','2026-06-27 18:18:24','2026-06-27 17:39:29','2026-06-27 18:18:24'),(52,52,'notifikasi terkirim','syamsulhidayah1718@gmail.com','2026-06-29 22:13:55','2026-06-29 22:11:54','2026-06-29 22:13:55');
/*!40000 ALTER TABLE `loa_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loa_requests`
--

DROP TABLE IF EXISTS `loa_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loa_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_id` bigint(20) unsigned NOT NULL,
  `request_code` varchar(80) NOT NULL,
  `article_url` varchar(255) NOT NULL,
  `article_id_external` varchar(100) DEFAULT NULL,
  `title` text NOT NULL,
  `authors_json` longtext NOT NULL,
  `corresponding_email` varchar(191) NOT NULL,
  `whatsapp_number` varchar(30) DEFAULT NULL,
  `affiliations_json` longtext DEFAULT NULL,
  `volume` varchar(20) DEFAULT NULL,
  `issue_number` varchar(20) DEFAULT NULL,
  `published_year` varchar(4) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `notes_admin` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_code` (`request_code`),
  KEY `loa_requests_journal_id_foreign` (`journal_id`),
  CONSTRAINT `loa_requests_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_requests`
--

LOCK TABLES `loa_requests` WRITE;
/*!40000 ALTER TABLE `loa_requests` DISABLE KEYS */;
INSERT INTO `loa_requests` VALUES (1,1,'PLPI-00005','https://ejurnal.edumedia.or.id/abdinusantara',NULL,'Kuliah Kerja Nyata Merajut untuk Meningkatkan Kreativitas Ibu Rumah Tangga di Cipayung','[{\"name\":\"Mirza Savita\"},{\"name\":\"Sri Lestari Handayani\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','2201035004@uhamka.ac.id',NULL,'[\"Universitas Muhammadiyah Prof. Dr. Hamka\"]','2','1','2026','approved',NULL,NULL,'2026-02-21 15:07:34','2026-02-21 14:35:06','2026-02-21 15:07:34'),(2,1,'PLPI-00004','',NULL,'MENJADI ORANG TUA YANG DI RINDUKAN ANAK MELALUI PROGRAM KKN : PENYULUHAN PSIKOLOG KELUARGA BAHAGIA DESA CIPEUCANG KAB.BOGOR','[{\"name\":\"Rr Ulfah Sholihah\"},{\"name\":\"Aisha Agustina\"},{\"name\":\"Dina Septiana\"},{\"name\":\"Nadya Lutfiah Azizah\"},{\"name\":\"Ade Falah\"},{\"name\":\"Paramita Emi Astuti\"}]','rrulfahsholihah93@gmail.com',NULL,'[\"Manajemen Pendidikan Islam\",\"Pendidikan Guru Madrasah Ibtidaiyah\",\"STIT Fatahillah Cileungsi Kab.Bogor\"]','2','01','2026','approved',NULL,NULL,'2026-02-21 15:07:58','2026-02-21 14:13:27','2026-02-21 15:07:58'),(3,1,'PLPI-00006','',NULL,'Workshop dan Training Implementasi Integrasi Odoo dengan Khanza untuk Sistem Payroll Jasa Medis Dokter di Rumah Sakit','[{\"name\":\"Hasan\"}]','hasansulistyo96@gmail.com',NULL,'[\"Sekolah Tinggi Teknologi Informatika Sony Sugema\"]','2','1','2026','approved',NULL,NULL,'2026-02-22 06:42:13','2026-02-22 06:41:09','2026-02-22 06:42:13'),(4,1,'PLPI-00007','',NULL,'Deep Learning Manifestation for TEYL at PPA IO-0133 Krammer Hilina\'a Kota Gunungsitoli','[{\"name\":\"Yasminar Amaerita Telaumbanua\"},{\"name\":\"Meniati Zebua\"},{\"name\":\"Dari Hati Gulo\"},{\"name\":\"Pujawati Waruwu\"},{\"name\":\"Yaredi Waruwu\"},{\"name\":\"Hasna Zebua\"},{\"name\":\"Zun Kelvin Eferistus Albers Halawa\"}]','meizebua323@gmail.com',NULL,'[\"Universitas Nias\"]','2','1','2026','approved',NULL,NULL,'2026-02-22 11:03:01','2026-02-22 09:56:04','2026-02-22 11:03:01'),(5,1,'PLPI-00008','https://ejurnal.edumedia.or.id/abdinusantara',NULL,'MENDAUR ULANG BAHAN BEKAS SEPERTI KARDUS DAN TUTUP BOTOL MENJADI KALENDER BERSAMA ANAK USIA DINI','[{\"name\":\"Savna Ratu Abidah\"},{\"name\":\"Merina\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','savnaratu03@gmail.com',NULL,'[\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Sejarah, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Biologi, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\",\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka\"]','2','1','2026','approved',NULL,NULL,'2026-02-24 03:40:45','2026-02-23 15:48:52','2026-02-24 03:40:45'),(6,1,'PLPI-00009','',NULL,'Eksplorasi Warna Primer Dalam Kegiatan Finger Painting Untuk Mengembangkan Kreativitas Anak Di Cipayung Jakarta Timur','[{\"name\":\"Vyona Angreini Agus\"},{\"name\":\"Merina\"},{\"name\":\"Gufron Amirullah\"},{\"name\":\"Khusniyati Masykuroh\"}]','2201035026@uhamka.ac.id',NULL,'[\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu\",\"Pendidikan, Universitas Muhammadiyah Prof. Dr. Hamka.\",\"Pendidikan Sejarah,\",\"Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr.\",\"Hamka.\",\"Pendidikan Biologi, Fakultas Keguruan dan Ilmu Pendidikan, Universitas\",\"Muhammadiyah Prof. Dr. Hamka.\",\"Pendidikan Guru Pendidikan Anak Usia Dini, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Prof. Dr.\",\"Hamka.\"]','2','1','2026','approved',NULL,NULL,'2026-02-25 10:42:14','2026-02-25 10:40:34','2026-02-25 10:42:14'),(8,3,'PLPI-00011','',NULL,'PENGARUH MEDIA MANIPULATIF BLOCK TERHADAP KEMAMPUAN REPRESENTASI MATEMATIS SISWA KELAS II SD PADA MATERI PENGURANGAN','[{\"name\":\"Ratih Shafira Wati1\"},{\"name\":\"Meiliana Nurfitriani\"},{\"name\":\"Dr. N Leni Sri Mulyani\"}]','ratihshafirawati@gmail.com',NULL,'[\"Universitas Muhammadiyah Tasikmalaya\",\"Universitas Muhammadiyah Tasikmalaya\",\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','approved',NULL,NULL,'2026-03-13 11:27:14','2026-03-10 23:05:00','2026-03-13 11:27:14'),(9,3,'PLPI-00014','',NULL,'Persepsi Generasi Z Laki-laki Terhadap Minat Menjadi Guru TK','[{\"name\":\"Priska Akwila Buana\"},{\"name\":\"Ahmad Samawi\"}]','priska.akwila.2201536@students.um.ac.id',NULL,'[\"Universitas Negeri Malang\",\"Universitas Negeri Malang\"]','4','1','2026','approved',NULL,NULL,'2026-04-07 09:07:47','2026-04-07 08:15:11','2026-04-07 09:07:47'),(10,3,'PLPI-00015','',NULL,'HUBUNGAN TINGKAT KEBUGARAN JASMANI DENGAN HASIL BELAJAR PJOK SISWA KELAS V SDN SUKARAJA','[{\"name\":\"Nurifti Tahusarriroh\"},{\"name\":\"Rahmat Permana\"},{\"name\":\"Yopa Taufik Saleh\"}]','nurifti.t@gmail.com',NULL,'[\"Pendidikan Guru Sekolah Dasar, Fakultas Keguruan dan Ilmu Pendidikan, Universitas Muhammadiyah Tasikmalaya, Tasikmalaya, Indonesia\"]','4','1','2026','approved',NULL,NULL,'2026-04-07 09:21:38','2026-04-07 09:19:38','2026-04-07 09:21:38'),(11,2,'PLPI-00013','',NULL,'Upaya Pencegahan Masalah Gigi dan Mulut melalui Edukasi dan Pemeriksaan Gigi pada Anak Usia Dini','[{\"name\":\"Desi Andriyani\"},{\"name\":\"Lies Elina P\"}]','desiandriyani2212@gmail.com',NULL,'[\"Poltekkes Kemenkes Tanjung Karang\"]','4','1','2026','approved',NULL,NULL,'2026-04-08 08:16:52','2026-04-06 07:37:05','2026-04-08 08:16:52'),(12,2,'PLPI-00012','',NULL,'Edukasi Kesehatan Gigi dan Mulut terkait Dampak Merokok pada Anak Binaan LPKA Kelas II Bandar Lampung','[{\"name\":\"Lies Elina\"},{\"name\":\"Desi Andriyani\"},{\"name\":\"Erni Gultom\"}]','desiandriyani2212@gmail.com',NULL,'[\"Poltekkes Kemenkes Tanjung Karang\"]','4','1','2026','approved',NULL,NULL,'2026-04-08 08:18:08','2026-04-06 07:33:44','2026-04-08 08:18:08'),(13,3,'PLPI-00016','',NULL,'Pengaruh Model Pembelajaran Kooperatif Tipe STAD Berbantuan Media Educaplay Terhadap Hasil Belajar IPAS Pada Siswa Kelas V SDN Cicariu','[{\"name\":\"Riki Abdul Hak\"},{\"name\":\"Yopa Taufik Saleh M.Pd.\"},{\"name\":\"Dr. N Leni Sri Mulyani M.Pd.\"}]','rikiabdulhak14@gmail.com',NULL,'[\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','approved',NULL,NULL,'2026-04-09 08:37:27','2026-04-09 06:49:22','2026-04-09 08:37:27'),(14,2,'PLPI-00017','',NULL,'PENGENALAN LAPANGAN PERSEKOLAHAN: PENGABDIAN KEPADA MASYARAKAT DALAM BIDANG PENGELOLAAN DI UPA. PERPUSTAKAAN INSTITUT AGAMA ISLAM HIDAYATULLAH BATAM','[{\"name\":\"Fatimah\"},{\"name\":\"Nurpaika\"},{\"name\":\"Halima Molakana\"},{\"name\":\"Sri Ayu Ulumando\"},{\"name\":\"Hadijah\"}]','fatimahfath1954@gmail.com',NULL,'[\"Institut Agama Islam Hidayatullah Batam\"]','4','1','2026','approved',NULL,NULL,'2026-04-20 12:46:48','2026-04-20 12:09:05','2026-04-20 12:46:48'),(15,2,'PLPI-00018','https://ejurnal-unisap.ac.id/index.php/abdiunisap/article/view/468','468','Panduan Deployment PLPI ke Hosting Production','[{\"name\":\"Ketua: Konradus\"}]','silvesterjenahut@gmail.com',NULL,'[\"Universitas San Pedro\"]','4','2','2026','approved',NULL,NULL,'2026-04-21 15:02:46','2026-04-21 15:02:37','2026-04-21 15:02:46'),(16,3,'PLPI-00019','',NULL,'Pengaruh Permainan Ca’throw Terhadap Ketepatan Melempar Pada Pelajaran PJOK Siswa Kelas V SDI An-Nahl','[{\"name\":\"Ketua: Wafa Tsabita Rahmani\"},{\"name\":\"Anggota 1: Yopa Taufik Saleh\"},{\"name\":\"Anggota 2: Rahmat Permana\"}]','wafatsabita4@gmail.com',NULL,'[\"Universitas Muhammadiyah Tasikmalaya\"]','4','1','2026','approved',NULL,NULL,'2026-04-22 19:40:31','2026-04-22 10:29:54','2026-04-22 19:40:31'),(17,2,'PLPI-00020','',NULL,'LAPORAN HASIL KEGIATAN PENGENALAN LAPANGAN PERSEKOLAHAN (PLP) SDII 01 LUQMAN AL-HAKIM 01 HIDAYATULLAH BATAM BATAM','[{\"name\":\"Ketua: Muji sebagai Dosen pembimbing\"},{\"name\":\"Anggota 1: Saharuddin\"},{\"name\":\"Anggota 2: Muhammad Kibar\"},{\"name\":\"Anggota 3: Syahrul Basirun\"}]','muhammadkibar528@gmail.com',NULL,'[\"Institut Agama Islam Hidayatullah Batam\"]','4','1','2026','approved',NULL,NULL,'2026-04-23 09:26:11','2026-04-23 09:23:41','2026-04-23 09:26:11'),(24,3,'PLPI-00027','',NULL,'Pengaruh Pembelajaran Learning Station Method Terhadap KebugaranJasmani Siswa Kelas V SDN 1 Sindanggalih','[{\"name\":\"Ketua: Alvin Alviani\"},{\"name\":\"Anggota 1: Rahmat Permana\"},{\"name\":\"Anggota 2: Yopa Taufik Saleh\"}]','alvianialvin53@gmail.com','085860532847','[\"Universitas Muhamadiyah Tasikmalaya\"]','7','3','2016','approved',NULL,NULL,'2026-05-07 13:45:25','2026-05-07 13:37:43','2026-05-07 13:45:25'),(26,2,'PLPI-00029','',NULL,'Digitalisasi UMKM: Penguatan Identitas Usaha dan Sistem Pembayaran Pada Naong Tenda Gebang Bangkalan','[{\"name\":\"Ketua: Moh. Rizky Abdillah\"},{\"name\":\"Anggota 1: Sudarmiatin\"},{\"name\":\"Anggota 2: Yuli Soesetio\"}]','m.rizkyabdillah10@gmail.com','6285748764956','[\"Universitas Negeri Malang\"]','4','1','2026','approved',NULL,NULL,'2026-05-13 17:24:09','2026-05-13 17:21:54','2026-05-13 17:24:09'),(27,1,'PLPI-00030','https://ejurnal.edumedia.or.id/abdinusantara/article/view/68','68','Pendampingan Kelompok Kolaboratif dalam Penguatan Kemampuan Pemecahan Masalah Matematika Siswa SMP','[{\"name\":\"Ketua: Nedya Salaputa\"},{\"name\":\"Anggota 1: Nurul Azizah\"}]','nedyasalaputa05@gmail.com','082343054663','[\"Universitas Muhammadiyah Makassar\"]','2','2','2026','approved',NULL,NULL,'2026-05-17 19:28:52','2026-05-17 19:28:35','2026-05-17 19:28:52'),(28,3,'PLPI-00031','https://ejurnal-unisap.ac.id/index.php/sibernetik/index',NULL,'Relasi Pendidikan Kejuruan, Etos Kerja, dan Nilai Keagamaan terhadap Aspirasi Karier Siswa di SMK Pesantren Al Kausar','[{\"name\":\"Ketua: Haelah Kajalakee\"},{\"name\":\"Anggota 1: Dr. Muh. Hanif\"}]','244110402077@mhs.uinsaizu.ac.id','+66979736229','[\"Universitas Islam Negeri Profesor Kiai Haji Saifuddin Zuhri Purwokerto\"]','4','2','2026','approved',NULL,NULL,'2026-05-18 22:52:15','2026-05-18 22:37:13','2026-05-18 22:52:15'),(29,1,'PLPI-00032','',NULL,'PENGUATAN LITERASI DASAR MELALUI PROGRAM PIJAR KATA PENDEKATAN PEMBELAJARAN MENYENANGKAN DI SEKOLAH DASAR','[{\"name\":\"Ketua: janatul aini\"},{\"name\":\"Anggota 1: Juniati, Ersa Winny Prakusya, M.Irvan Jayadi Assidk, Safruddin Muhdar\"}]','janatulaini71@gmail.com','081353854743','[\"Universitas Muhamadiyah Mataram\"]','2','11','','approved',NULL,NULL,'2026-05-20 22:28:24','2026-05-20 22:05:43','2026-05-20 22:28:24'),(30,2,'PLPI-00033','',NULL,'IMPLEMENTASI POSYANDU INTEGRASI (POINTEG) BERBASIS ANDROID DALAM UPAYA PENCEGAHAN STUNTING DAN STROKE DI PEKON JOGYAKARTA SELATAN KECAMATAN GADING REJO  KABUPATEN PRINGSEWU','[{\"name\":\"Ketua: Sri Indra Trigunarso\"},{\"name\":\"Anggota 1: Martini Fairus\"},{\"name\":\"Anggota 2: Bertalina\"},{\"name\":\"Anggota 3: Zainal Muslim\"}]','trigunarsosriindra@gmail.com','6282163428050','[\"Politeknik Kesehatan Kemenkes Tanjungkarang\"]','4','1','2026','approved',NULL,NULL,'2026-05-21 15:29:18','2026-05-21 15:29:04','2026-05-21 15:29:18'),(31,3,'PLPI-00034','',NULL,'PENGARUH PENGGUNAAN MEDIA PEMBELAJARAN DIGITAL TERHADAP MOTIVASI BELAJAR SISWA','[{\"name\":\"Ketua: Indri Fitri Hartanti\"}]','fitrihartantiindri@gmail.com','081296869014','[\"Universitas Jambi\"]','4','1','2026','approved',NULL,NULL,'2026-05-30 00:40:48','2026-05-24 16:47:14','2026-05-30 00:40:48'),(32,3,'PLPI-00035','',NULL,'PENGARUH PUZZLE PANCASILA TERHADAP HASIL BELAJAR SISWA MATERI SIMBOL DAN SILA PANCASILA KELAS 2 SDN BANYUAJUH 5','[{\"name\":\"Anggota 1: Widyaningrum Dwi Oktaviyanti\"},{\"name\":\"Anggota 2: Nanda Kirana Abdullah\"},{\"name\":\"Anggota 3: Pratita Fitrotus Sabilillah\"},{\"name\":\"Anggota 4: Ika Dian Rahmawati\"}]','widyaningrum7908@gmail.com','0895379422012','[\"Universitas Trunodjoyo Madura\"]','4','1','2026','approved',NULL,NULL,'2026-06-03 03:41:15','2026-05-26 21:46:12','2026-06-03 03:41:15'),(33,6,'PLPI-00036','',NULL,'PENGUATAN LITERASI DASAR MELALUI PERMAINAN EDUKATIF MISTERI DADU BERWARNA DI SEKOLAH DASAR','[{\"name\":\"Anggota 1: Mahasiswa\"},{\"name\":\"Anggota 2: mahasiswa\"},{\"name\":\"Anggota 3: mahasiswa\"}]','ekhakurniati2@gmail.com','082340625334','[\"Universitas Muhammadiyah Mataram\"]','','','','approved',NULL,NULL,'2026-06-02 01:38:31','2026-05-29 18:34:10','2026-06-02 01:38:31'),(34,1,'PLPI-00037','',NULL,'PENDAMPINGAN PENCATATAN KEUANGAN SEDERHANA DAN PEMAHAMAN PAJAK TERHADAP UMKM WARUNG TENDA DI JALAN WATES YOGYAKARTA ','[{\"name\":\"Anggota 1: Yulia Via Nur Afifah\"},{\"name\":\"Anggota 2: Ika Wulandari\"}]','fifahvia14@gmail.com','+6285725250953','[\"Universitas Mercu Buana Yogyakarta\"]','2','2','','approved',NULL,NULL,'2026-06-03 16:46:14','2026-06-03 16:46:04','2026-06-03 16:46:14'),(35,2,'PLPI-00038','',NULL,'Pendidikan Hukum Sebagai Perisai Remaja Dari Tindak Pidana Kekerasan Pada SMA Negeri 1 Kepulauan Aru','[{\"name\":\"Ketua: Maher Syalal Lawalata\"},{\"name\":\"Anggota 1: Clara Kesaulya\"},{\"name\":\"Anggota 2: Johan Pieter Elia Rumangun\"},{\"name\":\"Anggota 3: Dita Ayudia Pratiwi\"},{\"name\":\"Anggota 4: Nugrah Gables Manery\"},{\"name\":\"Anggota 5: Rocky Steevy Mantaiborbir\"},{\"name\":\"Anggota 6: Muhamad Akbar Yanlua\"}]','mahersyalallawalata@gmail.com','+6281343272460','[\"Universitas Pattimura\"]','4','1','2026','approved',NULL,NULL,'2026-06-04 03:28:00','2026-06-04 03:27:47','2026-06-04 03:28:00'),(36,3,'PLPI-00039','',NULL,'Pengaruh Permainan Bola Beracun Terhadap Koordinasi Mata dan Tangan Siswa Kelas IV Pada Pelajaran PJOK SDN CICARIU','[{\"name\":\"Ketua: Reksa Ashona\"},{\"name\":\"Anggota 1: Yopa Taufik Saleh\"},{\"name\":\"Anggota 2: Rahmat Permana\"}]','astunashona@gmail.com','081395498988','[\"Universitas Muhammdiyah Tasikmalaya\"]','4','1','2026','approved',NULL,NULL,'2026-06-05 21:31:26','2026-06-04 13:01:46','2026-06-05 21:31:26'),(37,2,'PLPI-00040','',NULL,'PENERAPAN PROGRAM “PANGANDARAN BLUE & CLEAN” MELALUI KEGIATAN PLOGGING DAN KONTEN DIGITAL UNTUK MENINGKATKAN KESADARAN LINGKUNGAN DI KAWASAN WISATA PANGANDARAN ','[{\"name\":\"Ketua: Susilawati\"},{\"name\":\"Anggota 1: Syahrul Ramdani\"},{\"name\":\"Anggota 2: Salma Azhar F\"},{\"name\":\"Anggota 3: Nur Hayati\"},{\"name\":\"Anggota 4: Putri Aisyah\"},{\"name\":\"Anggota 5: Alifa Azzahra\"},{\"name\":\"Anggota 6: Lisvi\"},{\"name\":\"Anggota 7: Herni Yuliantika\"},{\"name\":\"Anggota 8: Siti Nuraisyah\"},{\"name\":\"Anggota 9: Afifah Nadiatul M\"},{\"name\":\"Anggota 10: Devina Dewi Aryanto\"}]','ramdanisyahrul010@gmail.com','085872224261','[\"Universitas Teknologi Digital\"]','4','1','2026','approved',NULL,NULL,'2026-06-06 03:34:44','2026-06-05 14:42:04','2026-06-06 03:34:44'),(38,3,'PLPI-00041','',NULL,'PENGEMBANGAN MEDIA PEMBELAJARAN FLIPBOOK DIGITAL BERBASIS NYANYIAN ANAK PADA MATERI KERAGAMAN BUDAYA DI JAWA TIMUR KELAS V SDN KENITEN 1','[{\"name\":\"Ketua: Devina Damayanti\"},{\"name\":\"Anggota 1: Wahyudi\"},{\"name\":\"Anggota 2: Sutrisno Sahari\"}]','devinadamayanti981@gmail.com','085733678904','[\"Universitas Nusantara PGRI Kediri\"]','4','1','2026','approved',NULL,NULL,'2026-06-06 03:34:14','2026-06-05 20:08:49','2026-06-06 03:34:14'),(39,3,'PLPI-00042','',NULL,'PENERAPAN PEMBELAJARAN BERBASIS DIGITAL STORYTELLING MENGGUNAKAN STORYJUMPER TERHADAP PENINGKATAN KETERAMPILAN LITERASI SISWA SD','[{\"name\":\"Ketua: Wahyu Indri Astuti\"},{\"name\":\"Anggota 1: Rafa Nurul Ghozama\"},{\"name\":\"Anggota 2: Ika Dian Rahmawati\"}]','rafanurulghozama@gmail.com','081358900309','[\"Universitas Trunodjoyo Madura\"]','4','1','2026','approved',NULL,NULL,'2026-06-08 21:05:20','2026-06-08 16:56:40','2026-06-08 21:05:20'),(40,3,'PLPI-00043','',NULL,'Pengaruh latihan','[{\"name\":\"Ketua: Ilya Adit Triana\"}]','uliasaja23@gmail.com','085641054525','[\"Universitas muhammadiyah kuningan\"]','3','2','2026','rejected',NULL,'Ditolak oleh admin.',NULL,'2026-06-08 19:43:19','2026-06-09 15:50:04'),(41,3,'PLPI-00044','',NULL,'pengaruh latihan lompat pipa terhadap kemampuan block siswa ekstrakurikuler bola voli mts negeri 04 brebes','[{\"name\":\"Ketua: Ilya Adit Triana\"}]','uliasaja@gmail.com','088983137045','[\"Universitas muhammadiyah kuningan\"]','3','2','2026','approved',NULL,NULL,'2026-06-09 15:39:53','2026-06-08 21:08:11','2026-06-09 15:39:53'),(42,2,'PLPI-00045','',NULL,'EFEKTIVITAS PELATIHAN MANAJEMEN WAKTU TERHADAP PEMAHAMAN DAN KETERAMPILAN SISWA SMP IT YAYASAN HAJJAH FAUZIAH BINJAI','[{\"name\":\"Anggota 1: Cici Resi Desoli Saragih\"},{\"name\":\"Anggota 2: Shafwa Davina\"},{\"name\":\"Anggota 3: Prity Sandiya\"},{\"name\":\"Anggota 4: Ade Fania Ramadhani\"},{\"name\":\"Anggota 5: Dwinta Aurelia\"},{\"name\":\"Anggota 6: Anisa Hargita Nupa\"},{\"name\":\"Anggota 7: Fadlan Mulia Alfharizi Siregar\"},{\"name\":\"Anggota 8: Zahra Nabila\"},{\"name\":\"Anggota 9: Eka Danta Jaya Ginting\"}]','shafwadavina126@gmail.com','082362879283','[\"Universitas Sumatera Utara\"]','4','1','2026','approved',NULL,NULL,'2026-06-09 00:17:04','2026-06-08 21:29:27','2026-06-09 00:17:04'),(43,2,'PLPI-00046','',NULL,'EDUKASI PENDIDIKAN AGAMA TENTANG FIKIH MUAMALAH:  TRANSFORMASI INTEGRITAS JUAL BELI ISLAM PADA ERMODERN BAGI SISWA SMA NEGERI 2 BUKITTINGGI','[{\"name\":\"Ketua: Ashabul Fadhli\"},{\"name\":\"Anggota 1: Usman\"},{\"name\":\"Anggota 2: Devi Syukri Azhari\"}]','devisyukrimpd@gmail.com','081266751233','[\"Universitas Putra Indonesia Yptk Padang\"]','','','','pending',NULL,NULL,NULL,'2026-06-10 17:58:20','2026-06-10 17:58:20'),(44,2,'PLPI-00047','',NULL,'WORKSHOP PEMBELAJARAN BERBASIS ARTIFICIAL INTELLIGENCE DALAM KETERAMPILAN BERBAHASA  DI ERA 5.0','[{\"name\":\"Ketua: Donny Adiatmana Ginting\"},{\"name\":\"Anggota 1: Donal Fernando Lubis\"},{\"name\":\"Anggota 2: Resti Amalia\"}]','donny-adiatama@gmail.com','+6282274227469','[\"Universitas Bangka Belitung\"]','4','1','','approved',NULL,NULL,'2026-06-11 14:07:04','2026-06-11 14:04:34','2026-06-11 14:07:04'),(45,2,'PLPI-00048','',NULL,'PENDAMPINGAN PENGELOLAAN SARANA DAN PRASARANA PASAR RAKYAT KOTA MALANG OLEH UPT PASAR','[{\"name\":\"Anggota 1: Lale Agustia Olivia Rosadi\"},{\"name\":\"Anggota 2: Rossi Ananda Arifin\"},{\"name\":\"Anggota 3: Ida Soraya\"},{\"name\":\"Anggota 4: Sinollah\"}]','oliviarosadi0@gmail.com','0881026076418','[\"Universitas Islam Raden Rahmat\"]','4','1','2026','approved',NULL,NULL,'2026-06-13 01:26:54','2026-06-12 11:01:46','2026-06-13 01:26:54'),(46,1,'PLPI-00049','',NULL,'Pelatihan Pembuatan Masker Kain untuk Meningkatkan Kesadaran Kesehatan dan Keterampilan Produktif Masyarakat di Tengaran','[{\"name\":\"Ketua: Rahayu Lestari\"},{\"name\":\"Anggota 1: Mei Restiana\"}]','rahayulest1603@gmail.com','082138343954','[\"Universitas Terbuka\"]','2','2','','approved',NULL,NULL,'2026-06-13 01:25:13','2026-06-12 21:10:48','2026-06-13 01:25:13'),(48,1,'PLPI-00050','',NULL,'Memperkuat Daya Saing UMKM Sate dan UMKM Sempol melalui Pencatatan Keuangan Mandiri ','[{\"name\":\"Ketua: Vivia Anggita\"},{\"name\":\"Anggota 1: Martinus Budiantara\"}]','anggitavivia@gmail.com','085895859975','[\"Universitas Mercu Buana Yogyakarta\"]','2','2','2026','approved',NULL,NULL,'2026-06-18 22:41:19','2026-06-17 14:48:52','2026-06-18 22:41:19'),(49,3,'PLPI-00051','',NULL,'Penerapan Mindful Parenting dalam Mengatasi Tantrum pada Anak Usia Dini: Studi Literatur','[{\"name\":\"Ketua: Eisya Maheera\"},{\"name\":\"Anggota 1: Endah Tejaningsih\"}]','eisyamaheera@gmail.com','085640040275','[\"UIN Raden Mas Said\"]','4','1','','approved',NULL,NULL,'2026-06-18 22:42:24','2026-06-17 21:51:15','2026-06-18 22:42:24'),(50,3,'PLPI-00052','',NULL,'Pentingnya Etika Kejujuran dan Komunikasi Santun pada Anak Usia Dini di Era Digital','[{\"name\":\"Ketua: Nilatul Asna\"},{\"name\":\"Anggota 1: Fadia Hasanah\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','nilaasna208@gmail.com','0895324887546','[\"Universitas Islam Negeri K.H.Abdurrahman Wahid Pekalongan\"]','4','1','','approved',NULL,NULL,'2026-06-24 02:29:06','2026-06-19 14:49:30','2026-06-24 02:29:06'),(51,3,'PLPI-00053','',NULL,'Eksposur Visual Tanpa Izin Di Tiktok: Analisis Kritis Privasi Anak Usia Dini ','[{\"name\":\"Ketua: Alya Nabila Nailatul Izza\"},{\"name\":\"Anggota 1: Tasya Aulia Zahra\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','alynbilanaizza@gmail.com','085137501894','[\"Universitas Islam Negeri K.H Abdurrahman Wahid Pekalongan Jawa Tengah\"]','4','1','2026','approved',NULL,NULL,'2026-06-24 02:30:13','2026-06-19 19:47:03','2026-06-24 02:30:13'),(52,6,'PLPI-00054','',NULL,'Pengembangan Media Diorama Berbasis Kontekstual Pada Pembelajaran IPAS di Kelas V SD Negeri 1 Langsa','[{\"name\":\"Ketua: Maulida\"},{\"name\":\"Anggota 1: Tengku Muhammad Sahudra\"},{\"name\":\"Anggota 2: Mufti Riyani\"}]','mmaulidaa05@gmail.com','081265345652','[\"Program Studi Pendidikan Guru Sekolah Dasar Fakultas Keguruan dan Ilmu Pendidikan Universitas Samudra\"]','4','1','','approved',NULL,NULL,'2026-06-22 21:35:04','2026-06-21 01:47:22','2026-06-22 21:35:04'),(53,3,'PLPI-00055','',NULL,'Religiusitas dalam Puisi Negeri Daging Karya A. Musthofa Bisri: Kajian Hermeneutika Paul Ricoeur ','[{\"name\":\"Ketua: Aurellia Sarah Gunawan\"},{\"name\":\"Anggota 1: Najma Kamila Safithri\"},{\"name\":\"Anggota 2: Amanda Maharani Asmar\"},{\"name\":\"Anggota 3: Mefta Maudia\"}]','atiqoh.fitriyah94@gmail.com','085183251217','[\"UIN Syarif Hidayatullah Jakarta\"]','4','1','2026','approved',NULL,NULL,'2026-06-24 15:46:34','2026-06-23 15:28:37','2026-06-24 15:46:34'),(54,3,'PLPI-00056','',NULL,'ANALISIS ETIKA KEPROFESIAN GURU PAUD DALAM MENGHADAPI TANTANGAN MORAL PADA PEMBELAJARAN DIERA DIGITAL DI RA MUSLIMAT NU SUMURJOMBLANGBOGO','[{\"name\":\"Ketua: Ufiya Adrlina Zahiro\"},{\"name\":\"Anggota 1: Suci Sukma Dewi\"},{\"name\":\"Anggota 2: Nur Khasanah\"}]','ufiyaardlina16@gmail.com','0895704341069','[\"Universitas Islam Negeri K.H. Abdurrahman Wahid Pekalongan\"]','4','1','2026','approved',NULL,NULL,'2026-06-23 19:03:31','2026-06-23 19:03:23','2026-06-23 19:03:31'),(55,3,'PLPI-00057','',NULL,'EduGrow Sebagai Alat Asesmen Perkembangan Anak Usia 4-6 tahun Pada Lembaga PAUD Berbasis Website','[{\"name\":\"Ketua: Rimaya Safru Wiyusi\"},{\"name\":\"Anggota 1: I Wayan Sutama\"}]','safruwiyusi@gmail.com','085749885457','[\"Universitas Negeri Malang\"]','4','1','2026','rejected',NULL,'Ditolak oleh admin.',NULL,'2026-06-25 15:59:19','2026-06-26 01:29:58'),(56,3,'PLPI-00058','',NULL,'EduGrow Sebagai Alat Asesmen Perkembangan Anak Usia 4-6 Tahun Pada Lembaga Paud Berbasis Website ','[{\"name\":\"Ketua: Rimaya Safru Wiyusi\"},{\"name\":\"Anggota 1: I Wayan Sutama\"}]','safruwiyusi@gmail.com','085749885457','[\"Universitas Negeri Malang\"]','4','1','2026','approved',NULL,NULL,'2026-06-25 18:11:17','2026-06-25 16:37:08','2026-06-25 18:11:17'),(57,2,'PLPI-00059','',NULL,'EDUKASI TEKNIK NONFARMAKOLOGI UNTUK MENGATASI KETIDAKNYAMANAN PADA PENDERITA HIPERTENSI','[{\"name\":\"Ketua: Andi Saifah\"},{\"name\":\"Anggota 1: Suwarty Nursahara Usman Putra\"},{\"name\":\"Anggota 2: Dewi Hartatik\"}]','andi.saifah.untad@gmail.com','082261997470','[\"Ketua: Universitas Tadulako\",\"Anggota 1: STIKes Bala Keselamatan Palu\",\"Anggota 2: Puskesmas Marawola Sigi\"]','4','1','2026','approved',NULL,NULL,'2026-06-26 03:06:07','2026-06-26 03:05:57','2026-06-26 03:06:07'),(58,3,'PLPI-00060','https://drive.google.com/file/d/1hyd4IYUxkVB06GoD7i31PZkWEhSb41uU/view?usp=drivesdk',NULL,'MEDIA \"CAKRA MATH\" UNTUK MENSTIMULASI KEMAMPUAN LOGIKA MATEMATIKA ANAK USIA 5-6 TAHUN','[{\"name\":\"Anggota 1: Drs. Tomas Iriyanto, S.Pd., M.Pd.\"},{\"name\":\"Ketua: Aisyah Maulidya\"}]','aisyahnirana@gmail.com','+62 888-0418-9564','[\"Universitas Negeri Malang\"]','4','1','2026','pending',NULL,NULL,NULL,'2026-06-26 12:37:25','2026-06-26 12:37:25'),(59,2,'PLPI-00061','https://ejurnal-unisap.ac.id',NULL,'Pendampingan Pelayanan Administrasi Dalam Meningkatkan Kualitas Layanan Bagi Guru dan Tenaga Kependidikan di UPT DISDIKPORA Tempuran','[{\"name\":\"Ketua: Tia Salsabila Lubna Fahima, Milna Wafirah\"}]','salsabilatia11@gmail.com','087717712657','[\"Institut Agama Islam Syubbanul Wathon Magelang, Indonesia\"]','2','2','2026','approved',NULL,NULL,'2026-06-27 16:30:21','2026-06-27 12:27:56','2026-06-27 16:30:21'),(61,3,'PLPI-00063','',NULL,'PERSEPSI GURU TERHADAP PEMBERIAN INSENTIF DAN TUNJANGAN DALAM MENINGKATKAN KINERJA GURU DI SEKOLAH SWASTA KECAMATAN CIPUTAT, KOTA TANGERANG SELATAN','[{\"name\":\"Ketua: Nurrabiatul dan Sri Utaminingsih\"}]','nunamanah@gmail.com','081315983194','[\"Program Pascasarjana Universitas Pamulang  Magister Manajemen Pendidikan\"]','4','2','','approved',NULL,NULL,'2026-06-27 16:14:52','2026-06-27 16:05:16','2026-06-27 16:14:52'),(62,1,'PLPI-00064','',NULL,'Pelatihan Desain Grafis CorelDraw Untuk Membuat Poster di SMK Ibrahimy 1 Sukorejo Situbondo ','[{\"name\":\"Ketua: Zaehol Fatah\"},{\"name\":\"Anggota 1: M Hanif Fachri Zubair\"}]','fachrizubair01@gmail.com','082135204467','[\"Universitas Ibrahimy\"]','2','2','2026','approved',NULL,NULL,'2026-06-27 17:39:29','2026-06-27 17:35:14','2026-06-27 17:39:29'),(63,3,'PLPI-00065','',NULL,'PENGARUH PEMBELAJARAN DIAGRAM BATANG EDUCATION (DBE) NUMERASI TERHADAP KEMAMPUAN BERPIKIR KRITIS MURID DI SEKOLAH DASAR','[{\"name\":\"Ketua: Syamsul Hidayah\"},{\"name\":\"Anggota 1: Joko Soebagyo\"},{\"name\":\"Anggota 2: Nila Fitria\"}]','syamsulhidayah1718@gmail.com','085776493717','[\"Sekolah Pascasarjana, Universitas Muhammadiyah Prof. DR. Hamka\"]','4','2','2026','approved',NULL,NULL,'2026-06-29 22:11:54','2026-06-29 22:09:26','2026-06-29 22:11:54'),(64,3,'PLPI-00066','',NULL,'PENERAPAN MODEL PEMBELAJARAN PROBLEM  BASED LEARNING UNTUK MENINGKATKAN KEMAMPUAN BERPIKIR KREATIF  SISWA KELAS V SD','[{\"name\":\"Ketua: Safarna Syifaul Maulida\"},{\"name\":\"Anggota 1: Ahmad Nasriadi\"},{\"name\":\"Anggota 2: Helminsyah\"}]','syifaulsafarna@gmail.com','082271367003','[\"Universitas Bina Bangsa Getsempena\"]','4','2','2026','pending',NULL,NULL,NULL,'2026-07-01 09:43:12','2026-07-01 09:43:12'),(65,3,'PLPI-00067','https://ejurnal-unisap.ac.id/index.php/sibernetik/article/view/704','704','Pengembangan Bahan Ajar Pada Mata Pelajaran Fikih Materi Ijtihad di MAN 1 Kota Cilegon','[{\"name\":\"Ketua: Nanda Agustina\"},{\"name\":\"Anggota 1: Saefudin Zuhri\"},{\"name\":\"Anggota 2: Hasbullah\"}]','nanadaagustina456@gmail.com','081324775730','[\"Universitas Islam Negeri Sultan Maulana Hasanuddin Banten\"]','4','1','','pending',NULL,NULL,NULL,'2026-07-01 12:31:07','2026-07-01 12:31:07'),(66,3,'PLPI-00068','',NULL,'TANTANGAN DAN STRATEGI GURU SEKOLAH REGULER DALAM MEWUJUDKAN RUANG KELAS INKLUSI YANG EFEKTIF','[{\"name\":\"Ketua: Hibbil wali\"},{\"name\":\"Anggota 1: M. Fahri azizul millah\"},{\"name\":\"Anggota 2: M. Mahbubi\"}]','hibbilw@gmail.com','085232472078','[\"Universitas Nurul Jadid\"]','4','2','','pending',NULL,NULL,NULL,'2026-07-01 20:03:24','2026-07-01 20:03:24');
/*!40000 ALTER TABLE `loa_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-07-01-000000','App\\Database\\Migrations\\CreateUsersTable','default','App',1782836116,1),(2,'2026-07-01-001000','App\\Database\\Migrations\\CreateJournalManagementTables','default','App',1782836560,2),(3,'2026-07-01-002000','App\\Database\\Migrations\\CreateAppSettingsTable','default','App',1782837142,3),(4,'2026-07-01-003000','App\\Database\\Migrations\\CreateWhatsappManagementTables','default','App',1782837681,4),(5,'2026-07-01-004000','App\\Database\\Migrations\\CreateEditorReviewerApplicationsTable','default','App',1782838963,5),(6,'2026-07-01-005000','App\\Database\\Migrations\\CreateInvoiceJurnalTable','default','App',1782839489,6),(7,'2026-07-01-006000','App\\Database\\Migrations\\CreateLoaManagementTables','default','App',1782884246,7),(8,'2026-07-01-006500','App\\Database\\Migrations\\AddFullJournalIdentityFields','default','App',1782884449,8),(9,'2026-07-01-007000','App\\Database\\Migrations\\CreateLoaNotificationsTable','default','App',1782886463,9),(10,'2026-07-01-008000','App\\Database\\Migrations\\CreateEducationalArticleTables','default','App',1782892749,10),(11,'2026-07-01-008100','App\\Database\\Migrations\\SeedPublicEducationalArticles','default','App',1782894245,11),(12,'2026-07-01-008000','App\\Database\\Migrations\\ExtendMessageManagementTables','default','App',1782918447,12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publishers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publishers`
--

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
INSERT INTO `publishers` VALUES (1,'UPT UNISAP','UPT Publikasi dan Penerbitan Universitas San Pedro','ejournalunisap@gmail.com','082213331314','Jalan Ir. Soekarno Nomor 06, Kelurahan Fontein, Kecamatan Kota Raja Kota Kupang - Provinsi Nusa Tenggara Timur, 85112','publishers/1776756656_ffab9cfbbc92e43f7f2d.png','2026-04-21 09:55:41','2026-04-22 20:13:30'),(2,'MEDIA-NUSANTARA','PT Media Edukasi Nusantara','ejurnaledumedia@gmail.com','082213331314','Jl. Fetor Foenay RT 036 / RW 014 Kelurahan Oepura, Kecamatan Maulafa, Kota Kupang - Provinsi Nusa Tenggara Timur, 851142','publishers/1776859994_64e648bb0a48347d7d27.png','2026-04-21 09:56:47','2026-04-22 20:13:14'),(4,'LEIBNIZ','Program Studi Matematika Universitas San Pedro','osnimanpaulinamaure@gmail.com','085742653219','Jalan Ir. Soekarno Nomor 06, Kelurahan Fontein, Kecamatan Kota Raja Kota Kupang - Provinsi Nusa Tenggara Timur, 85112','publishers/1782396037_0b5e011ad71ef6064a1e.png','2026-06-25 22:00:37','2026-06-25 22:00:37');
/*!40000 ALTER TABLE `publishers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(40) NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'superadmin','Super Admin','ejournalunisap@gmail.com','$2y$10$WGA9toDjc2POGXmyc0RDo.5OzSZw/v/XICLwYA7QckDddIgcAa9be','superadmin',1,'2026-04-21 09:53:05','2026-06-08 18:28:13'),(2,'osnimanmaure','Osniman Paulina Maure, S.Pd., M.Pd.','osnimanpaulinamaure@gmail.com','$2y$10$V33PuSOA.nxsV.0IWb4Geehzb6h2Ujcn88nx5Mi1xZdMyG4Hdhhwe','admin_jurnal',1,'2026-06-25 22:02:56','2026-06-25 22:02:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_messages`
--

DROP TABLE IF EXISTS `whatsapp_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recipient_name` varchar(191) DEFAULT NULL,
  `phone_number` varchar(40) NOT NULL,
  `message` text NOT NULL,
  `template_id` bigint(20) unsigned DEFAULT NULL,
  `wa_url` text NOT NULL,
  `sent_by` varchar(191) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_messages_template_id_foreign` (`template_id`),
  CONSTRAINT `whatsapp_messages_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `whatsapp_templates` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_messages`
--

LOCK TABLES `whatsapp_messages` WRITE;
/*!40000 ALTER TABLE `whatsapp_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `whatsapp_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `whatsapp_templates`
--

DROP TABLE IF EXISTS `whatsapp_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `whatsapp_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `code` varchar(80) NOT NULL,
  `type` varchar(20) DEFAULT 'whatsapp',
  `subject` varchar(191) DEFAULT NULL,
  `message` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_templates`
--

LOCK TABLES `whatsapp_templates` WRITE;
/*!40000 ALTER TABLE `whatsapp_templates` DISABLE KEYS */;
INSERT INTO `whatsapp_templates` VALUES (1,'Konfirmasi Naskah Telah Terbit','konfirmasi-naskah-telah-terbit','whatsapp',NULL,'Assalamualaikum, Salam sejahtera bagi kita semuanya, Shalom, Om Swastiastu, Namo Buddhaya, dan Salam Kebajikan. \r\n\r\nBapak/Ibu yang kami hormati, naskah Bapak/Ibu yang berjudul: \r\n\r\n*{judul}*\r\n\r\nTelah diterbitkan pada Edisi *Volume 4, Nomor 1, Juni 2026.* Berikut ini Link Artikelnya Bapak/Ibu:\r\n{link artikel}\r\n\r\nTerima kasih atas kepercayaan Bapak/Ibu karena telah memilih jurnal kami. Sampai jumpa di edisi selanjutnya. \r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-26 01:12:00','2026-06-26 01:12:00'),(2,'Konfirmasi Naskah Telah Diterima','konfirmasi-naskah-telah-diterima','whatsapp',NULL,'Assalamu’alaikum warahmatullahi wabarakatuh, Shalom, Om Swastyastu, Namo Buddhaya, Salam Kebajikan, dan Salam Sejahtera.\r\n\r\nYth. Bapak/Ibu Penulis,\r\nDengan hormat, kami informasikan bahwa kami telah menerima hasil revisi naskah artikel Bapak/Ibu yang berjudul:\r\n\r\n*{judul}*\r\n\r\nBerdasarkan hasil evaluasi tim redaksi, maka naskah artikel Bapak/Ibu dinyatakan *Diterima untuk Diterbitkan.*\r\n\r\nStatus naskah saat ini sudah masuk tahap *copyediting* dan dipersiapkan untuk dijadwalkan terbit pada edisi:\r\n*Volume 04, Nomor 01, Juni 2026*\r\n\r\nSehubungan dengan itu, silakan melakukan pembayaran biaya publikasi sebesar *Rp. 200.000 melalui QRIS resmi kami*.\r\n\r\nKami ucapkan terima kasih atas kerja sama dan komitmen Bapak/Ibu dalam menjaga kualitas publikasi ilmiah di jurnal ini.\r\n\r\nHormat kami,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-26 19:51:38','2026-06-29 14:10:37'),(3,'Konfirmasi Naskah Telah Terbit Tapi Belum Payment','konfirmasi-naskah-telah-terbit-tapi-belum-payment','whatsapp',NULL,'Assalamualaikum, Salam sejahtera bagi kita semuanya, Shalom, Om Swastiastu, Namo Buddhaya, dan Salam Kebajikan. \r\n\r\nBapak/Ibu yang kami hormati, naskah Bapak/Ibu yang berjudul: \r\n\r\n*{judul}*\r\n\r\nTelah diterbitkan pada Edisi *Volume 4, Nomor 1, Juni 2026.* Berikut ini Link Artikelnya Bapak/Ibu:\r\n{link artikel}\r\n\r\nSelanjutnya, dimohon kepada Bapak/Ibu untuk melakukan pembayaran biaya publikasi sebesar *Rp200.000* melalui Qris yang kami lampirkan. \r\n\r\nTerima kasih atas kepercayaan Bapak/Ibu karena telah memilih jurnal kami. Sampai jumpa di edisi selanjutnya. \r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-27 02:48:09','2026-06-27 02:48:09'),(4,'Konfirmasi Pembuatan Akun dan Submit Artikel','konfirmasi-pembuatan-akun-dan-submit-artikel','whatsapp',NULL,'Assalamualaikum warahmatullahi wabarakatuh, Salam sejahtera bagi kita semuanya, Shalom, Om Swastiastu, Namo Buddhaya, dan Salam Kebajikan.\r\n\r\nBapak/Ibu yang kami hormati, kami informasikan bahwa naskah Bapak/Ibu yang berjudul:\r\n\r\n{judul}*\r\n\r\nTelah kami bantu submit ke jurnal {jurnal}.\r\n\r\nBerikut akun OJS/Jurnal Bapak/Ibu:\r\n\r\n*Username:*\r\n[isi Manual Saat kirim Pesan]\r\n\r\n*Password:*\r\n[isi Manual Saat kirim Pesan]\r\n\r\n*Link Login OJS/Jurnal:*\r\n{link_jurnal}\r\n\r\nSilakan Bapak/Ibu dapat melakukan login melalui tautan tersebut untuk memantau proses naskah pada sistem OJS.\r\nTerima kasih atas kepercayaan Bapak/Ibu karena telah memilih jurnal kami. Semoga proses publikasi naskah ini dapat berjalan dengan lancar.\r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-27 18:36:06','2026-06-28 02:55:10'),(5,'Konfirmasi Belum Unggah Revisi','konfirmasi-belum-unggah-revisi','whatsapp',NULL,'Assalamualaikum, Salam sejahtera bagi kita semuanya, Shalom, Om Swastiastu, Namo Buddhaya, dan Salam Kebajikan.\r\n\r\nBapak/Ibu yang kami hormati, sehubungan dengan proses penerbitan naskah Bapak/Ibu yang berjudul:\r\n\r\n*{judul}*\r\n\r\nKami ingin mengonfirmasi bahwa hingga saat ini naskah revisi Bapak/Ibu belum terunggah pada sistem OJS jurnal kami.\r\n\r\nApakah Bapak/Ibu mengalami kendala dalam proses revisi atau pengunggahan naskah? Jika terdapat kendala teknis maupun hal lain yang perlu dikonsultasikan, silakan menghubungi kami agar Tim Editor dapat membantu memberikan arahan dan solusi yang diperlukan.\r\n\r\nKami sangat mengharapkan konfirmasi dari Bapak/Ibu agar proses editorial naskah dapat segera dilanjutkan sesuai tahapan yang berlaku, mengingat jadwal penerbitan Edisi Juni 2026 saat ini sedang dalam proses penyelesaian.\r\n\r\nTerima kasih atas perhatian dan kerja sama Bapak/Ibu.\r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-28 02:54:34','2026-06-28 02:54:34'),(6,'Konfirmasi Terbit Tanpa Review','konfirmasi-terbit-tanpa-review','whatsapp',NULL,'Assalamualaikum warahmatullahi wabarakatuh. Salam sejahtera bagi kita semua, Shalom, Om Swastiastu, Namo Buddhaya, dan Salam Kebajikan.\r\n\r\nBapak/Ibu yang kami hormati, kami ingin mengonfirmasi terkait naskah artikel Bapak/Ibu yang telah dikirimkan ke jurnal kami {jurnal} dengan judul:\r\n\r\n*{judul}*\r\n\r\nApakah naskah tersebut berkenan untuk dilanjutkan ke proses penerbitan pada *Edisi Juni 2026*?\r\n\r\nApabila Bapak/Ibu berkenan, mohon konfirmasi melalui pesan ini agar tim editor kami dapat segera menindaklanjuti dan membantu proses penerbitannya pada edisi tersebut.\r\n\r\nTerima kasih atas perhatian dan kepercayaan Bapak/Ibu kepada jurnal kami.\r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-29 03:14:40','2026-06-29 03:17:59'),(7,'Konfirmasi Pengalihan Naskah Ke Jurnal Lain','konfirmasi-pengalihan-naskah-ke-jurnal-lain','whatsapp',NULL,'Assalamu’alaikum warahmatullahi wabarakatuh, Shalom, Om Swastyastu, Namo Buddhaya, Salam Kebajikan, dan Salam Sejahtera.\r\n\r\nYth. Bapak/Ibu Penulis,\r\nTerima kasih telah mempercayakan naskah artikel Bapak/Ibu kepada jurnal kami. \r\nSehubungan dengan naskah yang berjudul:\r\n\r\n*{judul}*\r\n\r\nKami informasikan bahwa proses penerbitan untuk {jurnal} Volume 4, Nomor 1, Juni 2026 telah ditutup.\r\n\r\nSebagai alternatif, kami menawarkan penerbitan pada jurnal kami yang lain, yaitu Abdi Nusantara: Jurnal Pengabdian kepada Masyarakat, yang saat ini masih membuka penerimaan naskah untuk Edisi Mei dan Edisi Agustus 2026.\r\n\r\nApabila Bapak/Ibu berkenan, kami siap membantu proses pengalihan naskah, sehingga Bapak/Ibu tidak perlu melakukan pengajuan dari awal.\r\n\r\nSilakan konfirmasi apabila Bapak/Ibu berminat, agar tim editor kami dapat segera menindaklanjutinya.\r\n\r\nTerima kasih atas perhatian dan kepercayaan Bapak/Ibu.\r\n\r\nSalam,\r\nTim Editor\r\n{jurnal}\r\n{link_jurnal}',1,'2026-06-29 14:09:25','2026-06-29 14:09:25'),(8,'Email Notifikasi LoA Terbit','email_loa_terbit','email','Notifikasi Letter of Acceptance (LoA) - {judul_artikel}','Yth. Bapak/Ibu {nama_penerima}\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul: {judul_artikel}\nJurnal: {nama_jurnal}\n\nHormat kami,\nTim Editor\n{nama_jurnal}',1,'2026-07-02 02:31:52','2026-07-02 02:31:52');
/*!40000 ALTER TABLE `whatsapp_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'plpi_public'
--

--
-- Dumping routines for database 'plpi_public'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02  2:47:08
