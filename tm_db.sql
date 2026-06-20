-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2026 at 12:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-alphabuilders@example.com|127.0.0.1', 'i:1;', 1781770144),
('laravel-cache-alphabuilders@example.com|127.0.0.1:timer', 'i:1781770144;', 1781770144);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_04_042438_add_company_fields_to_users_table', 1),
(5, '2026_04_04_144204_create_tenders_table', 1),
(7, '2026_05_17_000002_add_pending_documents_to_users_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcon_reviews`
--

CREATE TABLE `subcon_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tender_id` bigint(20) UNSIGNED NOT NULL,
  `subcon_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcon_reviews`
--

INSERT INTO `subcon_reviews` (`id`, `tender_id`, `subcon_id`, `admin_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 1, 5, 'Clean Work', '2026-06-06 08:48:31', '2026-06-06 08:48:31'),
(2, 2, 2, 1, 5, 'Work is completed, well structured', '2026-06-06 08:51:13', '2026-06-06 09:11:06'),
(3, 3, 3, 1, 3, 'Work is not well managed, and not completed', '2026-06-06 09:16:21', '2026-06-06 09:16:46');

-- --------------------------------------------------------

--
-- Table structure for table `tenders`
--

CREATE TABLE `tenders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `selected_subcon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `tender_ref_number` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `required_grade` varchar(255) DEFAULT NULL,
  `required_services` text DEFAULT NULL,
  `years_experience_required` int(11) NOT NULL DEFAULT 0,
  `estimated_budget` decimal(15,2) DEFAULT NULL,
  `site_location` varchar(255) DEFAULT NULL,
  `site_visit_date` datetime DEFAULT NULL,
  `deadline` date NOT NULL,
  `priority_level` varchar(255) NOT NULL DEFAULT 'medium',
  `status` varchar(255) NOT NULL DEFAULT 'open',
  `work_status` varchar(255) NOT NULL DEFAULT 'not_started',
  `progress_percent` int(11) NOT NULL DEFAULT 0,
  `report_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_path`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tenders`
--

INSERT INTO `tenders` (`id`, `selected_subcon_id`, `title`, `tender_ref_number`, `description`, `required_grade`, `required_services`, `years_experience_required`, `estimated_budget`, `site_location`, `site_visit_date`, `deadline`, `priority_level`, `status`, `work_status`, `progress_percent`, `report_path`, `created_at`, `updated_at`) VALUES
(1, 5, 'Rainwater Harvesting & Solar Microgrid Installation', 'GRN-2026-008C', 'Integration of commercial rainwater storage and rooftop solar arrays on new civic buildings.', 'G5,G6', 'Renewable Energy Infrastructure, Plumbing and Reticulation', 6, 2800000.00, 'Cyberjaya, Selangor', '2026-06-21 10:30:00', '2026-06-30', 'normal', 'open', 'assigned', 100, '{\"files\":{\"site_photos\":[{\"path\":\"tenders\\/1\\/7751X6OIb8pGGecUBbtnRIwWx4jtEPjtQB6zcLtE.jpg\",\"status\":\"approved\",\"feedback\":null,\"description\":\"contoh gambar\",\"uploaded_at\":\"2026-05-20 15:18:49\",\"reviewed_at\":\"2026-05-20 15:31:13\"}],\"financial_docs\":[{\"path\":\"tenders\\/1\\/0V6xTCo6HgugdRxlTnPn90gCwNqcgbxOn6TzKzDS.pdf\",\"status\":\"approved\",\"feedback\":null,\"description\":\"contoh report\",\"uploaded_at\":\"2026-05-20 15:19:03\",\"reviewed_at\":\"2026-05-20 15:31:16\"}],\"invoices\":[{\"path\":\"tenders\\/1\\/HKYEpyj1xvoosFoep2LS8Pe5tx19q5r5OlnQ1PbA.pdf\",\"status\":\"approved\",\"feedback\":null,\"description\":\"report invoice\",\"uploaded_at\":\"2026-05-20 15:19:22\",\"reviewed_at\":\"2026-06-06 16:37:43\"},{\"path\":\"tenders\\/1\\/yX1MPfA6KThJPDYadPLKueF6aLUZgdYWrnVjS3y3.pdf\",\"status\":\"approved\",\"feedback\":null,\"description\":null,\"uploaded_at\":\"2026-05-25 14:52:04\",\"reviewed_at\":\"2026-06-06 16:37:47\"}]}}', '2026-05-10 06:24:59', '2026-06-06 08:37:47'),
(2, 2, 'Stormwater Drainage Widening and Retention Basin Construction', 'FLD-2026-021E', 'Deepening of the main stormwater culvert, riverbank stabilization, and construction of automated concrete gates.', 'G6,G7', 'Earthworks, Drainage and Water Reticulation', 10, 9800000.00, 'Shah Alam, Selangor', '2026-08-23 10:00:00', '2026-08-31', 'high', 'open', 'assigned', 0, NULL, '2026-05-13 06:16:50', '2026-06-06 08:50:39'),
(3, 3, 'Proposed Construction of Automated Distribution Center', 'ADC-2026-004B', 'Complete structural execution and automated high-bay warehouse integration for a multi-tenant hub.', 'G6,G7', 'Civil & Structural Works, Mechanical & Electrical Systems', 10, 4500000.00, 'Klang Valley, Selangor', '2026-07-15 10:15:00', '2026-06-30', 'normal', 'open', 'assigned', 0, NULL, '2026-06-06 09:15:41', '2026-06-06 09:16:15'),
(4, NULL, 'Infrastructure Fibre Optic Backbone Upgrade', 'FIB-2026-012A', 'Trenching and installation of high-speed fibre infrastructure along the coastal highway.', 'G5,G6', 'Telecommunication Engineering, Low-Voltage Cabling', 7, 5400000.00, 'Iskandar Puteri, Johor', '2026-07-10 11:30:00', '2026-06-30', 'normal', 'open', 'not_started', 0, NULL, '2026-06-06 09:19:02', '2026-06-06 09:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'subcon',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `pic_name` varchar(255) DEFAULT NULL,
  `pic_phone` varchar(255) DEFAULT NULL,
  `office_phone` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `cidb_reg_number` varchar(255) DEFAULT NULL,
  `ssm_number` varchar(255) DEFAULT NULL,
  `company_level` varchar(255) DEFAULT NULL,
  `year_established` year(4) DEFAULT NULL,
  `cidb_grades` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cidb_grades`)),
  `services_provided` text DEFAULT NULL,
  `pending_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pending_documents`)),
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `company_name`, `company_address`, `pic_name`, `pic_phone`, `office_phone`, `company_email`, `cidb_reg_number`, `ssm_number`, `company_level`, `year_established`, `cidb_grades`, `services_provided`, `pending_documents`, `status`) VALUES
(1, 'ADMIN AITO', 'admindept@aito.com', 'admin', NULL, '$2y$12$EEQJe7bITFv90Qv/36uyjuKFgQhSNkfxOcPQuoLdHneOH.TosHGBW', NULL, '2026-05-10 06:12:29', '2026-05-10 06:12:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(2, 'Ahmad bin Abdullah', 'ahmad@buildfast.com.my', 'subcon', NULL, '$2y$12$tR/FLFZA5cS9IZK1fm1sU.bPe1FS56ftT1wWuzcGyUMxIM5hd/0Ki', NULL, '2026-05-10 06:13:10', '2026-05-13 06:27:32', 'BuildFast Construction Sdn Bhd', 'No. 12, Jalan Industri 3/1, Taman Perindustrian, 47100 Puchong, Selangor', 'Ahmad bin Abdullah', '+60123456789', '+60377281000', 'admin@buildfast.com.my', 'C1234567890', '202001012345 (1223456-A)', 'Bumiputera', '2018', '[\"G6\",\"G7\"]', 'Earthworks, Deep Foundation, Structural Concrete Works', NULL, 'active'),
(3, 'Ravindran a/l Arumugam', 'ravi@visionaryresources.com.my', 'subcon', NULL, '$2y$12$uphuyFoDoi6l7AsetiowTeWlszVUxHt42Mq7xExrWoFvsDTfY.olS', NULL, '2026-05-10 06:13:30', '2026-06-18 00:08:26', 'Visionary Resources Sdn Bhd', '15, Lebuh Downing, 10300 George Town, Pulau Pinang', 'Ravindran a/l Arumugam', '+60123456789', '+60132145678', 'contact@visionaryresources.com.my', 'C4443332221', '201201021244 (1011234-T)', 'Non-Bumiputera', '2012', '[\"G7\"]', '[\"Civil Works\",\"Carpentry\"]', NULL, 'active'),
(4, 'Siti Aishah binti Razak', 'siti.aishah@merantimaju.com', 'subcon', NULL, '$2y$12$n1QlwLZPBiOx7AOuNIB5euKUgswgBQIGmmWpf.45EOQNT8R.yGvve', NULL, '2026-05-10 06:13:49', '2026-06-18 00:31:12', 'Meranti Maju Enterprise', 'No 45, Jalan Setia 4/2, Bandar Setia Alam, 40170 Shah Alam, Selangor', 'Siti Aishah binti Razak', '+60172233445', '+60355109988', 'operation@merantimaju.com', 'C1112223334', '202203123456 (1455666-K)', 'Bumiputera', '2020', '[\"G3\"]', '[\"General Contracting\",\"Civil Works\"]', '[{\"path\":\"pending-account-documents\\/4\\/iYLBVHc9fTUTyBHybMwrwW9NWXPjVNItbIGXNF31.pdf\",\"original_name\":\"CV Haiyan hazrin (WorldLine).pdf\",\"type\":\"bank\",\"status\":\"pending\",\"uploaded_at\":\"2026-06-10 03:02:34\"},{\"path\":\"pending-account-documents\\/4\\/pr2n7T5oiI7YkjgvpxDiC0HOq6kMrI0D1vQHuX3b.pdf\",\"original_name\":\"CV Haiyan hazrin (WorldLine).pdf\",\"type\":\"bank\",\"status\":\"pending\",\"uploaded_at\":\"2026-06-10 03:02:35\"},{\"path\":\"pending-account-documents\\/4\\/azw39o6W9HOJKivXLUJc4Iqe3FBx8UGaWB94bCgT.pdf\",\"original_name\":\"CV Haiyan hazrin (WD).pdf\",\"type\":\"bank\",\"status\":\"pending\",\"uploaded_at\":\"2026-06-10 03:03:10\"}]', 'pending'),
(5, 'Tan Wei Kiat', 'wei.kiat@setiateguh.com', 'subcon', NULL, '$2y$12$P5ntwqsfW5leQfVQTSVsGuuo0lJkNMWrq2AcLwx.kRB/WNgzB/SSq', NULL, '2026-05-10 06:14:13', '2026-06-18 00:11:28', 'Setia Teguh Engineering', 'Suite 8-2, Plaza Mentari, Jalan Kuning, 80250 Johor Bahru, Johor', 'Tan Wei Kiat', '+60198765432', '+6073335555', 'info@setiateguh.com', 'C9876543210', '201703032145 (1345678-X)', 'Non-Bumiputera', '2015', '[\"G5\"]', '[\"Electrical\",\"Mechanical\"]', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `subcon_reviews`
--
ALTER TABLE `subcon_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcon_reviews_tender_id_foreign` (`tender_id`),
  ADD KEY `subcon_reviews_subcon_id_foreign` (`subcon_id`);

--
-- Indexes for table `tenders`
--
ALTER TABLE `tenders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenders_tender_ref_number_unique` (`tender_ref_number`),
  ADD KEY `tenders_selected_subcon_id_foreign` (`selected_subcon_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subcon_reviews`
--
ALTER TABLE `subcon_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tenders`
--
ALTER TABLE `tenders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subcon_reviews`
--
ALTER TABLE `subcon_reviews`
  ADD CONSTRAINT `subcon_reviews_subcon_id_foreign` FOREIGN KEY (`subcon_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subcon_reviews_tender_id_foreign` FOREIGN KEY (`tender_id`) REFERENCES `tenders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tenders`
--
ALTER TABLE `tenders`
  ADD CONSTRAINT `tenders_selected_subcon_id_foreign` FOREIGN KEY (`selected_subcon_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
