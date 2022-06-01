-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2022 at 12:20 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aidealab_standarddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2021_12_29_110227_create_products_table', 1),
(6, '2022_01_01_061308_add_blocked_to_users_table', 1),
(7, '2022_02_04_095145_create_roles_table', 1),
(8, '2022_02_12_125653_create_customers_table', 1),
(9, '2022_02_23_104102_create_locations_table', 1),
(10, '2022_02_24_112604_create_branches_table', 1),
(11, '2022_02_24_113141_create_facilities_table', 1),
(12, '2022_02_24_152419_create_buildings_table', 1),
(13, '2022_02_24_181400_create_floors_table', 1),
(14, '2022_02_24_181444_create_lab_departments_table', 1),
(15, '2022_02_26_122016_create_vendors_table', 1),
(16, '2022_03_03_140921_create_categories_table', 1),
(17, '2022_03_03_150508_create_devices_table', 1),
(18, '2022_03_08_172532_create_device_locations_table', 1),
(19, '2022_03_14_092822_create_sensor_categories_table', 1),
(20, '2022_03_18_142700_create_sensor_units_table', 1),
(21, '2022_03_19_151341_create_sensors_table', 1),
(22, '2022_03_21_161349_create_user_logs_table', 1),
(23, '2022_04_11_114631_alter_table_add_three_columns_to_users_table', 1),
(24, '2022_04_11_150906_create_aqmi_json_data_table', 1),
(25, '2022_04_21_145528_create_aqi_chart_config_values_table', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
