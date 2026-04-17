-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 17, 2026 at 05:38 AM
-- Server version: 10.11.16-MariaDB-cll-lve
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rushpayl_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `full_name`, `phone`, `address`, `city`, `state`, `pincode`, `created_at`) VALUES
(2, 2, 'shubham', '9524671668', 'bara bigha', 'bihar', 'bihar', '803113', '2026-03-24 04:39:22'),
(3, 2, 'vikash', '93463498758', 'patna', 'pat', 'bihar', '803113', '2026-03-24 04:48:03'),
(4, 3, 'shubham', '9524671668', 'mumbai', 'mumbai', 'mumbai', '20031', '2026-03-24 13:42:30'),
(5, 2, 'vishal kumar', '6205474244', 'bara bigha lakhan dukhan', 'Bihar sharif', 'Bihar', '803113', '2026-03-24 15:36:59'),
(6, 6, 'sahil kumar', '93463498758', 'Bara Bigha', 'Bihar sharif', 'Bihar', '803113', '2026-03-24 18:12:22'),
(7, 7, 'Shubham singh', '8877072479', 'Bara Bigha', 'Patna', 'Bihar', '803115', '2026-03-25 05:30:50'),
(8, 16, 'shubham', '9524671668', 'bara', 'bihar', 'bihar', '803113', '2026-03-27 06:33:25'),
(9, 17, 'Shubham kumar', '08877072479', 'Patna\r\nPatna', 'Patna', 'Bihar', '800013', '2026-03-27 07:07:41'),
(10, 34, 'Rohit Samrat', '8252246846', 'shivpuri ramchandrapur bihar sharif , nalanda', 'Bihar sharif', 'Bihar', '803101', '2026-04-09 16:23:24'),
(14, 36, 'Rohit ', '8252246846', 'shivpuri ramhandrapur bihar sharif , nalanda', 'Bihar sharif', 'Bihar', '803101', '2026-04-11 08:05:01'),
(15, 37, 'siyaram', '8252246846', 'shivpuri ramchandrapur bihar sharif , nalanda', 'Bihar sharif', 'Bihar', '803101', '2026-04-11 08:35:15'),
(16, 38, 'Rohit Samrat', '8252246846', 'shivpuri ramchandrapur bihar sharif , nalanda', 'Bihar sharif', 'Bihar', '803101', '2026-04-11 11:19:20'),
(17, 39, 'Shubham Kumar', '08877072479', 'Bihar Sharif', 'Patna', 'Bihar', '803131', '2026-04-12 05:40:11'),
(18, 40, 'samrat', '8252246846', 'patna', 'patna', 'Bihar', '800024', '2026-04-13 06:21:26'),
(19, 41, 'samrat', '8252246846', 'patna', 'patna', 'Bihar', '800024', '2026-04-13 10:15:04');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `size`) VALUES
(39, 4, 7, 13, '2026-03-24 16:25:50', NULL),
(40, 4, 8, 9, '2026-03-24 16:31:06', NULL),
(47, 5, 6, 1, '2026-03-26 06:03:43', NULL),
(48, 15, 6, 1, '2026-03-27 06:06:39', NULL),
(49, 15, 7, 1, '2026-03-27 06:07:52', NULL),
(94, 39, 41, 1, '2026-04-15 16:55:10', 'M'),
(95, 39, 14, 1, '2026-04-15 16:55:19', 'L'),
(96, 39, 19, 1, '2026-04-15 16:55:27', 'L'),
(97, 39, 12, 1, '2026-04-15 16:55:36', 'M');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Processing','Confirmed','Shipped','Delivered','Cancelled','Paid') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `created_at`, `payment_method`, `address_id`, `transaction_id`) VALUES
(29, 6, 300.00, '', '2026-03-24 18:12:25', 'COD', 6, '...'),
(30, 6, 999.00, '', '2026-03-24 18:13:55', 'COD', 6, '...'),
(31, 7, 300.00, '', '2026-03-25 05:31:01', 'COD', 7, '...'),
(32, 7, 6900.00, 'Processing', '2026-03-25 05:33:46', 'COD', 7, '...'),
(33, 7, 499.00, '', '2026-03-25 05:45:19', 'COD', 7, '...'),
(34, 7, 999.00, 'Processing', '2026-03-25 05:52:00', 'COD', 7, '...'),
(35, 16, 399.00, '', '2026-03-27 06:33:28', 'COD', 8, '...'),
(36, 16, 699.00, '', '2026-03-27 06:35:26', 'COD', 8, '...'),
(37, 17, 399.00, '', '2026-03-27 07:07:44', 'COD', 9, '...'),
(38, 17, 699.00, 'Delivered', '2026-03-27 07:11:58', 'COD', 9, '...'),
(54, 36, 899.00, 'Pending', '2026-04-11 06:48:59', 'COD', 11, NULL),
(55, 36, 899.00, 'Pending', '2026-04-11 07:04:10', NULL, 11, NULL),
(56, 36, 899.00, 'Pending', '2026-04-11 07:04:14', NULL, 11, NULL),
(57, 36, 899.00, 'Pending', '2026-04-11 07:04:32', NULL, 11, NULL),
(58, 36, 899.00, 'Pending', '2026-04-11 07:16:36', NULL, 1, NULL),
(59, 36, 899.00, 'Pending', '2026-04-11 07:16:44', NULL, 11, NULL),
(60, 36, 899.00, 'Shipped', '2026-04-11 07:20:32', NULL, 11, NULL),
(61, 36, 899.00, 'Pending', '2026-04-11 07:50:36', 'COD', 12, NULL),
(62, 36, 699.00, 'Pending', '2026-04-11 07:59:20', 'COD', 12, NULL),
(63, 36, 999.00, 'Pending', '2026-04-11 08:00:42', 'COD', 12, NULL),
(64, 36, 399.00, 'Pending', '2026-04-11 08:09:44', NULL, 14, NULL),
(65, 36, 699.00, 'Pending', '2026-04-11 08:15:19', 'COD', 14, NULL),
(66, 36, 699.00, 'Pending', '2026-04-11 08:16:17', 'COD', 14, NULL),
(67, 37, 1.00, 'Processing', '2026-04-11 08:35:20', 'Online', 15, 'pay_Sc7btfpF7zKSp8'),
(69, 39, 1.00, 'Delivered', '2026-04-12 05:40:15', 'Online', 17, 'pay_ScT9lbmBOzolLe'),
(70, 39, 2.00, 'Processing', '2026-04-13 06:16:10', 'Online', 17, 'pay_ScsIumQHrGRHTn'),
(72, 41, 399.00, 'Pending', '2026-04-13 10:15:13', NULL, 19, NULL),
(73, 41, 1.00, 'Processing', '2026-04-13 10:16:55', 'Online', 19, 'pay_ScwPGbZuNQGPtF'),
(74, 39, 3396.00, 'Pending', '2026-04-15 17:15:20', NULL, 17, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `size` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `size`) VALUES
(16, 34, 19, 1, 999.00, NULL),
(18, 36, 20, 1, 699.00, NULL),
(19, 37, 27, 1, 399.00, NULL),
(20, 38, 20, 1, 699.00, NULL),
(36, 54, 41, 1, 899.00, NULL),
(37, 55, 41, 1, 899.00, 'M'),
(38, 56, 41, 1, 899.00, 'M'),
(39, 57, 41, 1, 899.00, 'M'),
(40, 58, 41, 1, 899.00, 'M'),
(41, 59, 41, 1, 899.00, 'M'),
(42, 60, 41, 1, 899.00, 'M'),
(43, 61, 41, 1, 899.00, 'M'),
(44, 62, 20, 1, 699.00, 'M'),
(45, 63, 19, 1, 999.00, 'XXL'),
(46, 64, 27, 1, 399.00, NULL),
(47, 65, 20, 1, 699.00, NULL),
(48, 66, 12, 1, 699.00, NULL),
(49, 67, 42, 1, 1.00, NULL),
(51, 69, 42, 1, 1.00, NULL),
(52, 70, 42, 1, 1.00, NULL),
(53, 70, 42, 1, 1.00, NULL),
(55, 72, 27, 1, 399.00, NULL),
(56, 73, 42, 1, 1.00, NULL),
(57, 74, 41, 1, 899.00, NULL),
(58, 74, 14, 1, 799.00, NULL),
(59, 74, 19, 1, 999.00, NULL),
(60, 74, 12, 1, 699.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(255) DEFAULT NULL,
  `size_s` int(11) DEFAULT 1,
  `size_m` int(11) DEFAULT 1,
  `size_l` int(11) DEFAULT 1,
  `size_xl` int(11) DEFAULT 1,
  `size_xxl` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image`, `created_at`, `category`, `size_s`, `size_m`, `size_l`, `size_xl`, `size_xxl`) VALUES
(6, 'KAJARU Pack of 2 Men Self Design Zip Neck Polyester Black, Pink T-Shirt', 459.00, 'Mens 100% poly blend printed stylish polo neck t-shirt. Perfect and suitable for all seasons. Unique collection to your wardrobe. HalfSleeve t-shirt with a pair of tracks can afford the wearer a very classic look. Pure Polyesters Blend makes the fabric extra soft. Trendy and latest design with classic colors and premium fabric makes the t-shirt fashionable and comfortable. Comfortable for gym and all sports. Color may slightly vary depending on your screen brightness. Verify the size chart for size references', 'xxl-polo-8016-kajaru-original-imahezmsxtnya3yn.webp', '2026-03-24 15:28:49', NULL, 1, 1, 1, 1, 1),
(7, 'TAZO Men Solid Polo Neck Cotton Blend Maroon T-Shirt', 1.00, 'Made from high-quality cotton fabric, these polo neck t-shirts offer breathability and softness against the skin, ensuring all-day comfort. The classic polo neck design adds a touch of sophistication to your look, making it versatile for various occasions.', 'm-ts-407-wine-tazo-original-imahhbbh7ahhxyq9.webp', '2026-03-24 15:30:30', NULL, 1, 1, 1, 1, 1),
(8, 'TripBroz Men Solid Round Neck Cotton Blend Maroon T-Shirt', 799.00, 'General\r\nBrand\r\nTripBroz\r\nTripBroz\r\nType\r\nRound Neck\r\nRound Neck\r\nSleeve\r\nHalf Sleeve\r\nHalf Sleeve\r\nFit\r\nOversized\r\nOversized\r\nFabric\r\nCotton Blend\r\nCotton Blend\r\nPack of\r\n1\r\n1\r\nStyle Code\r\nMn-Over-Plain-Maroon_M\r\nMn-Over-Plain-Maroon_M\r\nNeck Type\r\nRound Neck\r\nRound Neck\r\nIdeal For\r\nMen\r\nMen\r\nSize\r\nM\r\nM\r\nPattern\r\nSolid\r\nSolid\r\nSuitable For\r\nWestern Wear\r\nWestern Wear\r\nReversible\r\nNo\r\nNo\r\nFabric Care\r\nMachine wash as per tag\r\nMachine wash as per tag\r\nNet Quantity\r\n1\r\n1\r\nColor\r\nMaroon\r\nMaroon\r\nBrand Color\r\nMaroon\r\nMaroon\r\nDetail Placement\r\nNone\r\nNone\r\nOccasion\r\nCasual\r\nCasual\r\nPrint Coverage\r\nNone\r\nNone\r\nPattern/Print Type\r\nSolid\r\nSolid\r\nSleeve Details\r\nNo Details\r\nNo Details\r\nSport Type\r\nNA\r\nNA\r\nSurface Styling\r\nApplique\r\nApplique\r\nTee Length\r\nLong\r\nLong\r\nTrend\r\nSolids\r\nSolids\r\nElevate your activewear wardrobe with this stylish and breathable mesh Crop T-shirt. Crafted from lightweight and stretchable fabric, it offers superior ventilation and moisture-wicking properties to keep you cool and comfortable all day long. Whether youï¿½re hitting the gym, going for a run, or lounging casually, this Crop T-shirt provides the perfect balance of function and fashion. The mesh design not only enhances airflow but also adds a sporty, edgy touch to your look.', 'm-mn-over-plain-maroon-m-tripbroz-original-imaherf8rgypsp5t.webp', '2026-03-24 15:32:53', NULL, 1, 1, 1, 1, 1),
(9, 'TRIPR Pack of 4 Men Solid Round Neck Cotton Blend Multicolor T-Shirt', 999.00, 'General\r\nBrand\r\nTRIPR\r\nTRIPR\r\nType\r\nRound Neck\r\nRound Neck\r\nSleeve\r\nHalf Sleeve\r\nHalf Sleeve\r\nFit\r\nRegular\r\nRegular\r\nFabric\r\nCotton Blend\r\nCotton Blend\r\nPack of\r\n4\r\n4\r\nStyle Code\r\nTMR-BL-GY-CHBLRNPLAIND164\r\nTMR-BL-GY-CHBLRNPLAIND164\r\nNeck Type\r\nRound Neck\r\nRound Neck\r\nIdeal For\r\nMen\r\nMen\r\nSize\r\nS\r\nS\r\nPattern\r\nSolid\r\nSolid\r\nSuitable For\r\nWestern Wear\r\nWestern Wear\r\nReversible\r\nNo\r\nNo\r\nFabric Care\r\nRegular Machine Wash\r\nRegular Machine Wash\r\nNet Quantity\r\n4\r\n4\r\nColor\r\nMulticolor\r\nMulticolor\r\nBrand Color\r\nMulticolor009\r\nMulticolor009\r\nOccasion\r\nCasual\r\nCasual\r\nSwitch up your everyday wardrobe with this trending t-shirt from TRIPR. Ideal to wear for all seasons, this versatile t-shirt features a Plain Round Neck and Half sleeves. Style it with a pair of straight-fit denims and white shoes to complete your outfit. Regular fit. Comfortable essential with Solid pattern', 'xxl-tmr-bl-gy-chblrnplaind164-tripr-original-imahgy2nmyz7bjsh.webp', '2026-03-24 15:35:19', NULL, 1, 1, 1, 1, 1),
(12, 'Watch for man', 699.00, 'Fashion Watch', '1-sl-01-shivark-men-original-imahggzhdrmjdcpg.webp', '2026-03-24 15:46:22', NULL, 1, 1, 1, 1, 1),
(13, 'Simple Watch for men', 799.00, 'Elevate your style with this elegant stainless-steel wristwatch, designed for those who appreciate timeless sophistication. The watch features a brushed silver-tone finish, a minimalist dial with linear indices, and a refined integrated bracelet.', '1-sk-pg-4078-wyt-brwn-basic-with-day-and-date-display-provogue-original-imahhgtkuafbe3qy.webp', '2026-03-24 15:47:17', NULL, 1, 1, 1, 1, 1),
(14, 'PROVOGUE Trending Premium Quality Functioning', 799.00, 'PROVOGUE Trending Premium Quality Functioning for Boys Analog Watch  - For Men SK-PG-4078-WYT-BRWN Basic with Day and Date Display', '1-lcs-4312-lois-caron-men-original-imahkrc2pjzfghrh.webp', '2026-03-24 15:49:48', NULL, 1, 1, 1, 1, 1),
(19, 'PRJ IN STYLE Men Loose Fit Mid Rise Light Blue Jeans', 999.00, 'Step into comfort and timeless street style with these Men’s Light Blue Baggy Denim Jeans. Crafted from durable, high-quality denim, these jeans offer a relaxed, loose fit that sits comfortably at the waist and flows down with a wide leg for that effortlessly laid-back look.', '36-prj-icebgy-d1-dm-prj-in-style-original-imahbvgyeanmkncx.webp', '2026-03-24 16:08:09', NULL, 1, 1, 1, 1, 1),
(20, 'Jeans Foe men', 699.00, 'Step into comfort and timeless street style with these Men’s Light Blue Baggy Denim Jeans. Crafted from durable, high-quality denim, these jeans offer a relaxed, loose fit that sits comfortably at the waist and flows down with a wide leg for that effortlessly laid-back look', '34-sm-a6-shimtec-original-imahg2gsfsnzcfvp.webp', '2026-03-24 16:36:29', NULL, 1, 1, 1, 1, 1),
(27, 'Casual Shirt', 399.00, 'This shirt for men is one of the top selling product from premium quality casual shirts collection exclusively manufactured by VeBNoR brand. You can use this mens shirt on jeans as well as it is appropriate as formal office wear. Acurate stitching by skilled workers gives this shirt for men branded an authentic classic look. You will love to wear shirt for men cotton for multi purpose use as men shirt , formal shirts for men , shirt for boys , casual shirts for men , formal shirt', 's-st2-vebnor-original-imahhy7edatnmfvx.webp', '2026-03-27 06:33:56', NULL, 1, 1, 1, 1, 1),
(41, 'CAMPUS HURRICANE Running Shoes For Men (Navy , 6)', 899.00, 'Shoes\' Upper- The breathable mesh upper of these shoes for men ensures comfortable feet with optimal air circulation. Their versatile design seamlessly complements both formal and casual outfits, be it for work or college.', '-original-imahgbryaghpuquq.webp', '2026-04-10 16:41:08', NULL, 1, 1, 1, 1, 1),
(42, 'Blue Shirt ', 1.00, 'Blue Shirt in Casual Demin Shirt', 'NVFSRE4092NAVYBLUE_1.webp', '2026-04-11 08:31:09', NULL, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 10,
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `stock`, `is_active`) VALUES
(1, 28, 'S', 'red', 10, 1),
(2, 29, 's m l xl', 'red blue green yellow', 10, 1),
(3, 30, 'S M L XL XXL', 'RED BLUE', 10, 1),
(4, 31, 'S M L XL XXL', 'RED BLUE', 10, 1),
(5, 32, 'S M L XL XXL', 'RED BLUE', 10, 1),
(6, 33, 'S M L XL XXL', 'RED BLUE', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `review`, `created_at`) VALUES
(1, 15, 28, 5, 'khbhj', '2026-03-29 06:03:51'),
(2, 34, 28, 5, 'mast item hai ', '2026-04-09 16:22:31'),
(3, 34, 28, 5, 'mast item hai ', '2026-04-09 16:22:37'),
(4, 39, 42, 5, 'Thanks you', '2026-04-12 05:43:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `otp_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `otp`, `is_verified`, `otp_expire`) VALUES
(1, 'Admin', 'admin@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'admin', '2026-03-23 14:49:39', NULL, 0, NULL),
(4, 'Rohit', 'rohit@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'user', '2026-03-24 16:20:44', NULL, 0, NULL),
(5, 'shubham', 'shu@gmail.com', '3d2172418ce305c7d16d4b05597c6a59', 'user', '2026-03-24 17:32:07', NULL, 0, NULL),
(6, 'sahil kumar', 'sahil@gmail.com', '030b3b699d37e3972c182e9449221472', 'user', '2026-03-24 18:11:27', NULL, 0, NULL),
(7, 'Shubham Singh', 'Shubhamkumar88@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-03-25 04:59:49', NULL, 0, NULL),
(8, 'karan singh', 'karansingh@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-03-25 05:16:05', NULL, 0, NULL),
(15, 'Shubham Singh', 'shubhamkumar@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'admin', '2026-03-27 06:06:20', '269161', 0, '2026-04-07 09:31:43'),
(16, 'demo', 'demo12@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', '2026-03-27 06:27:15', NULL, 0, NULL),
(17, 'shubam', 'shubham123@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-03-27 07:06:48', NULL, 0, NULL),
(18, 'Joker', 'joker@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'admin', '2026-04-08 15:00:52', NULL, 0, NULL),
(19, 'abhi', 'abhishek6205474@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-08 15:28:08', '290902', 0, '2026-04-08 15:33:08'),
(20, 'rakesh', 'rakesh@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-08 15:28:52', '519160', 0, '2026-04-08 15:33:52'),
(23, 'Aniket', 'aniketkumarcrpf@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', '2026-04-08 16:50:43', '899768', 0, '2026-04-08 16:55:43'),
(24, 'Don', 'faithearning8877@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'user', '2026-04-09 03:41:10', NULL, 0, '2026-04-10 17:24:23'),
(25, 'Shubham', 'faithearner@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-09 05:12:14', '430937', 0, '2026-04-09 05:17:14'),
(31, 'Shubham', 'dook@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'user', '2026-04-09 05:49:40', '755743', 0, '2026-04-09 05:54:40'),
(32, 'Vishal', 'vishalsingh88crpf@gmail.com', 'fff1ad9118aa1256e3128a00f2ea795a', 'user', '2026-04-09 09:21:33', '427916', 0, '2026-04-09 09:28:00'),
(33, 'vishal', 'vishalsinghcrpf88@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'user', '2026-04-09 09:23:50', NULL, 1, '2026-04-09 09:30:32'),
(35, 'Aryan Kumar', 'aryank5942@gmail.com', '165669f10483da5f34d1b4ccc25bf308', 'user', '2026-04-11 02:59:39', NULL, 1, NULL),
(36, 'Shubham Kumar', 'shubhamsinghtricks@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-11 06:46:37', NULL, 1, NULL),
(37, 'Siyaram', 'siyaramkumar103@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-11 08:34:41', NULL, 1, NULL),
(39, 'Shubham', 'shubhamkumar8877crpf@gmail.com', '845ec33fbebe65bab57be12ceebdd0e6', 'user', '2026-04-12 05:39:23', NULL, 1, NULL),
(41, 'samrat', 'rohitsamrat901@gmail.com', 'fd6d29a15199c56ed72e0f04417e1571', 'user', '2026-04-13 10:13:43', NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 34, 20, '2026-04-10 16:31:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
