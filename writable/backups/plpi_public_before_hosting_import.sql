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
INSERT INTO `app_settings` VALUES (1,'uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/app_logo-1782915329_ffd1b154901ac53e5309.png','uploads/app-settings/favicon-1782915329_dbe7ceac17294e8ab2d0.png','Asia/Jakarta','2026-06-30 16:32:22','2026-07-01 14:15:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
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
INSERT INTO `invoice_jurnal` VALUES (1,'INV-JRN-0001/2026','2026-07-01','2026-07-20','Pelatihan Meningkatkan Kualitas Guru Paud dengan Menggunakan Aplikasi Kipin School','Silvester Jenahut','Universitas San Pedro','Abdi Unisap: Jurnal Pengabdian Kepada Masyarakat',250000.00,'Lunas',NULL,'2026-07-01 07:47:40','2026-07-01 07:47:40',NULL);
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
INSERT INTO `journals` VALUES (1,1,'Abdi Unisap: Jurnal Pengabdian Kepada Masyarakat','ABDIUNISAP','abdi-unisap-jurnal-pengabdian-kepada-masyarakat','','2987-9175','2987-9183','https://ejurnal-unisap.ac.id/index.php/abdiunisap','https://ejurnal-unisap.ac.id/index.php/abdiunisap/pernyataankomitmenpenulis','<p></p><div style=\"text-align: justify;\"><div>ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat diterbitkan oleh UPT Publikasi dan Penerbitan Universitas San Pedro. ABDI UNISAP menerima naskah artikel yang merupakan diseminasi dari luaran program kegiatan pengabdian kepada masyarakat (PKM). Adapun kegiatan PKM yang dapat dipublikasikan, yaitu kegiatan PKM yang merupakan bagian dari penerapan hasil penelitian yang dan pengembangan IPTEKS yang sesuai dengan kebutuhan masyarakat untuk memajukan kesejahteraan masyarakat dan mencerdaskan kehidupan bangsa. Kegiatan PkM yang dapat dipublikasikan dapat berupa Layanan kepada masyarakat, Penerapan IPTEKS, Peningkatan kapasitas masyarakat, dan Pemberdayaan masyarakat.</div><div><br></div><div>Kami menyadari bahwa untuk mencapai kualitas dan mutu sebuah jurnal ilmiah, tentunya membutuhkan waktu yang cukup lama. Tidak hanya itu, kerja sama tim redaksi khususnya para Reviewer sangat diperlukan untuk bisa mencapai tujuan yang dimaksud.&nbsp;</div><div><br></div><div>Berkaitan dengan hal di atas, maka kami Tim Redaksi ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat mengundang Bapak/Ibu/Saudara-Saudari sekalian untuk bergabung bersama kami sebagai Editor atau Reviewer&nbsp; di jurnal yang sedang kami kelola saat ini. Harapannya, kehadiran Bapak/Ibu sebagai Editor/ Reviewer dapat memberikan kontribusi yang sangat berarti demi kemajuan Jurnal ini.&nbsp;</div><div><br></div><div>Bagi Bapak/Ibu yang bersedia untuk bergabung bersama kami sebagai&nbsp; Editor/Reviewer, bisa mengisi formulir yang sudah persiapkan.</div><div><br></div><div>Demikian penyampaian kami, atas perhatian dan kerja samanya, kami ucapkan terima kasih.&nbsp;</div><div><br></div><div>Salam hormat kami,&nbsp;</div><div>ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat</div></div><p></p>',NULL,'journals/logos/1782884789_71b4fc01121e196b8453.jpg','Konradus Silvester Jenahut, S.Pd., M.Pd.','Ketua Dewan Redaksi','journals/signatures/1782884789_51d7de85b9beecc0951f.png',120,120,110,100,'2026-02-17 15:28:39','2026-07-01 05:46:29'),(26,1,'SIBERNETIK: Jurnal Pendidikan dan Pembelajaran','SIBERNETIK','sibernetik-jurnal-pendidikan-dan-pembelajaran','','2988-0823','2988-0858','https://ejurnal-unisap.ac.id/index.php/sibernetik','','<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat diterbitkan oleh UPT Publikasi dan Penerbitan Universitas San Pedro. ABDI UNISAP menerima naskah artikel yang merupakan diseminasi dari luaran program kegiatan pengabdian kepada masyarakat (PKM). Adapun kegiatan PKM yang dapat dipublikasikan, yaitu kegiatan PKM yang merupakan bagian dari penerapan hasil penelitian yang dan pengembangan IPTEKS yang sesuai dengan kebutuhan masyarakat untuk memajukan kesejahteraan masyarakat dan mencerdaskan kehidupan bangsa. Kegiatan PkM yang dapat dipublikasikan dapat berupa Layanan kepada masyarakat, Penerapan IPTEKS, Peningkatan kapasitas masyarakat, dan Pemberdayaan masyarakat.</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Kami menyadari bahwa untuk mencapai kualitas dan mutu sebuah jurnal ilmiah, tentunya membutuhkan waktu yang cukup lama. Tidak hanya itu, kerja sama tim redaksi khususnya para Reviewer sangat diperlukan untuk bisa mencapai tujuan yang dimaksud. </span></p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Berkaitan dengan hal di atas, maka kami Tim Redaksi ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat mengundang Bapak/Ibu/Saudara-Saudari sekalian untuk bergabung bersama kami sebagai Editor atau Reviewer  di jurnal yang sedang kami kelola saat ini. Harapannya, kehadiran Bapak/Ibu sebagai Editor/ Reviewer dapat memberikan kontribusi yang sangat berarti demi kemajuan Jurnal ini. </span></p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Bagi Bapak/Ibu yang bersedia untuk bergabung bersama kami sebagai  Editor/Reviewer, bisa mengisi formulir yang sudah persiapkan.</span></p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Demikian penyampaian kami, atas perhatian dan kerja samanya, kami ucapkan terima kasih. </span></p>\r\n<p style=\"text-align: justify;\"> </p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\">Salam hormat kami, </span></p>\r\n<p style=\"text-align: justify;\"><span style=\"font-family: arial, helvetica, sans-serif;\"><a href=\"https://ejurnal-unisap.ac.id/index.php/abdiunisap/rekrutmeneditordanreviewer\" target=\"_blank\" rel=\"noopener\">ABDI UNISAP: Jurnal Pengabdian Kepada Masyarakat</a></span></p>',NULL,'journals/logos/1776529226_2088a9d680f0631b93a5.png','Konradus Silvester Jenahut, S.Pd., M.Pd.','Editor in Chief','journals/signatures/1775655449_ecf06ef0be6d3bf6d9ed.jpg',20,10,85,125,'2026-04-08 20:37:29','2026-07-01 08:23:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_letters`
--

LOCK TABLES `loa_letters` WRITE;
/*!40000 ALTER TABLE `loa_letters` DISABLE KEYS */;
INSERT INTO `loa_letters` VALUES (1,26,2,'001/LOA/SIBERNETIK/UPT-UNISAP/VII/2026','',NULL,'Efektivitas Sosialisasi dan Edukasi Gangguan Akibat Kekurangan Yodium (GAKY) terhadap Peningkatan Pengetahuan Anak Sekolah di SDN 24 Meulaboh Kabupaten Aceh Barat','[{\"name\":\"Ketua: Konradus  Silvester Jenahut\"},{\"name\":\"Anggota 1: Osniman\"}]','silvesterjenahut@gmail.com','[\"Universitas San Pedro\"]','4','1','2026','published','b559fea0d437e3f6925f3785aaefa3a1ef485842f86e38876d93c955466cdb3e','3905043cf580f3e449a6ec26f66829f3','loa/LoA-001_LOA_SIBERNETIK_UPT-UNISAP_VII_2026.pdf','2026-07-01 06:18:05',NULL,NULL,'2026-07-01 06:18:05','2026-07-01 06:18:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_notifications`
--

LOCK TABLES `loa_notifications` WRITE;
/*!40000 ALTER TABLE `loa_notifications` DISABLE KEYS */;
INSERT INTO `loa_notifications` VALUES (1,1,'menunggu',NULL,NULL,'2026-07-01 06:18:08','2026-07-01 06:18:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loa_requests`
--

LOCK TABLES `loa_requests` WRITE;
/*!40000 ALTER TABLE `loa_requests` DISABLE KEYS */;
INSERT INTO `loa_requests` VALUES (2,26,'PLPI-00001','',NULL,'Efektivitas Sosialisasi dan Edukasi Gangguan Akibat Kekurangan Yodium (GAKY) terhadap Peningkatan Pengetahuan Anak Sekolah di SDN 24 Meulaboh Kabupaten Aceh Barat','[{\"name\":\"Ketua: Konradus  Silvester Jenahut\"},{\"name\":\"Anggota 1: Osniman\"}]','silvesterjenahut@gmail.com','08113821126','[\"Universitas San Pedro\"]','4','1','2026','approved',NULL,NULL,'2026-07-01 06:18:05','2026-07-01 06:17:36','2026-07-01 06:18:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publishers`
--

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
INSERT INTO `publishers` VALUES (1,'UPT-UNISAP','UPT Publikasi dan Penerbitan Universitas San Pedro','info@ejurnal-unisap.ac.id','082213331314','Jalan Ir. Soekarno Nomor 06, Kelurahan Fontein, Kecamatan Kota Raja, Kota Kupang, Nusa Tenggara Timur 85112','publishers/1782884743_552041afc81e193fc4f1.png','2026-02-17 15:28:39','2026-07-01 05:45:43'),(2,'MAT-UNISAP','Program Studi Matematika Universitas San Pedro','ejournal.leibniz@gmail.com','','Jalan Ir. Soekarno Nomor 06, Kelurahan Fontein, Kecamatan Kota Raja Kota Kupang - Provinsi Nusa Tenggara Timur, 85112','publishers/1775655336_1ddf48ecb98731a6e2df.png','2026-02-18 12:33:26','2026-04-08 20:35:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'superadmin.plpi@plpi','Superadmin PLPI','superadmin.plpi@plpi','$2y$10$fwmPdAa7bLyVCLNlsfoop.T4RqvLNHxCNpiGkbYmDxrjPSejXQov6','superadmin',1,'2026-06-30 16:15:16','2026-06-30 16:15:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `whatsapp_templates`
--

LOCK TABLES `whatsapp_templates` WRITE;
/*!40000 ALTER TABLE `whatsapp_templates` DISABLE KEYS */;
INSERT INTO `whatsapp_templates` VALUES (1,'Notifikasi LoA Terbit','loa_terbit','whatsapp',NULL,'Yth. Bapak/Ibu Penulis,\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul:\n*{judul_artikel}*\n\nHormat kami,\n*Tim Editor*\n*{nama_jurnal}*',1,'2026-06-30 16:41:21','2026-06-30 16:41:21'),(2,'Pengingat Revisi','pengingat_revisi','whatsapp',NULL,'Yth. Bapak/Ibu Penulis,\n\nKami mengingatkan kembali terkait revisi naskah:\n*{judul_artikel}*\n\nMohon dapat segera melengkapi revisi sesuai catatan editor.\n\nTerima kasih.\n*{nama_jurnal}*',1,'2026-06-30 16:41:21','2026-06-30 16:41:21'),(3,'Email Notifikasi LoA Terbit','email_loa_terbit','email','Notifikasi Letter of Acceptance (LoA) - {judul_artikel}','Yth. Bapak/Ibu {nama_penerima},\n\nLetter of Acceptance (LoA) untuk artikel berikut telah diterbitkan:\n\nJudul: {judul_artikel}\nJurnal: {nama_jurnal}\n\nHormat kami,\nTim Editor\n{nama_jurnal}',1,'2026-07-01 15:07:27','2026-07-01 15:07:27');
/*!40000 ALTER TABLE `whatsapp_templates` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-02  2:29:19
