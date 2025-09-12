-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2025 at 01:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `movie_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `show_id` int(11) NOT NULL,
  `seats` varchar(255) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `customer_name`, `customer_phone`, `show_id`, `seats`, `status`, `booking_date`) VALUES
(6, 1, 'Maleesha Nethmini', '0725871463', 19, '40,50', 'confirmed', '2025-09-11 04:28:24'),
(7, 2, 'Vihini Wimansa', '0751428596', 19, '1,2', 'cancelled', '2025-09-11 04:51:36'),
(8, 3, 'Nethu Perera', '0712635742', 19, '8,9', 'confirmed', '2025-09-11 07:11:17'),
(10, NULL, 'Thivona Onadi', '0775896341', 19, 'S3,S4', 'confirmed', '2025-09-11 09:18:30'),
(11, 3, 'Imandi Nimthara', '0728536974', 19, '1,11', 'confirmed', '2025-09-11 09:22:26'),
(12, 3, 'Imandi Nimthara', '0728536974', 19, '2,3', 'cancelled', '2025-09-11 09:23:29'),
(13, 4, 'Thivona Onadi', '0728536974', 19, '51,52', 'pending', '2025-09-11 09:29:17'),
(14, NULL, 'Dewni Prashansa', '0728569742', 19, 'S10,S20,S54,S56', 'confirmed', '2025-09-11 09:37:21'),
(15, 5, 'Nimasha Nimnadi', '0771524369', 19, '4,5', 'pending', '2025-09-11 09:45:00'),
(16, 5, 'Nimani Prathiba', '0112457896', 19, '31', 'confirmed', '2025-09-11 09:52:03'),
(17, 4, 'Maleesha Nethmini', '0728536974', 19, '35', 'pending', '2025-09-12 05:46:50'),
(18, 3, 'Tharushi Nimasha', '0728536974', 19, '36,37,47,56', 'pending', '2025-09-12 06:45:47');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbacks`
--

INSERT INTO `feedbacks` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Maleesha Nethmini', 'maasha1111jay@gmail.com', 'Wow! This is a very user friendly web application. Easy to find new movie details and also can book tickets easily. Thank You', '2025-09-11 11:16:23');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `trailer_link` varchar(255) DEFAULT NULL,
  `status` enum('now_showing','coming_soon') DEFAULT 'now_showing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ticket_price` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `poster`, `trailer_link`, `status`, `created_at`, `ticket_price`) VALUES
(2, 'Ratatouille', 'New movie ', '../assets/posters/Ratatouille_Movie_1757265850.jfif', 'https://youtu.be/PeFGdSrFTUw?si=k62ZowooJ7GrV2_u', 'now_showing', '2025-09-07 17:24:10', NULL),
(20, 'Ground Zero', 'This movie is inspired by the real-life Operation Ghazi Baba in Kashmir, delivers a tense and engaging military thriller.', '../assets/posters/Ground_Zero_1757382295.jfif', 'https://youtu.be/oAdc62oGzW8?si=q6T2bF3mVmah0mPp', 'now_showing', '2025-09-09 01:44:55', NULL),
(22, 'ddda', '     ghjnnnnnnnnnkknjiudlkddid106452575241', '../assets/posters/ddd_1757467338.jfif', 'https://youtu.be/oAdc62oGzW8?si=q6T2bF3mVmah0mPp', 'now_showing', '2025-09-10 01:22:18', 'LKR 750'),
(23, 'The Map That Leads to You', 'Heather\'s European adventure takes a turn when she meets Jack-sparking an unexpected emotional journey neither of them saw coming.\r\n\r\nRomance Movie\r\n\r\n1h 36m\r\n', '../assets/posters/The_Map_That_Leads_to_You_1757655834.jpg', 'https://youtu.be/FK9BQm471c0?si=XG3eiiuQW3256iDp', 'now_showing', '2025-09-11 11:57:03', 'LKR 800'),
(24, 'The Roses', 'Life seems easy for picture-perfect couple Theo and Ivy: successful careers, a loving marriage, great kids. However, a tinderbox of fierce competition and hidden resentments soon emerge when Theo\'s career nosedives and Ivy\'s own ambitions take off.\r\n\r\nDark comedy · 1h 45m\r\n', '../assets/posters/The_Roses_1757655729.jpg', 'https://youtu.be/xfygPZvQN8o?si=9LzSzyisUZqxW7wb', 'coming_soon', '2025-09-11 12:05:33', '900'),
(25, 'BTS 2019 WORLD TOUR \'LOVE YOURSELF', 'A breathtaking expression of true love and the meaning of existence beneath a star-filled sky? <BTS 2019 WORLD TOUR \'LOVE YOURSELF: SPEAK YOURSELF\' \r\n\r\n1h 48m', '../assets/posters/BTS_2019_WORLD_TOUR__LOVE_YOURSELF_1757655615.jpg', 'https://youtu.be/la_Or-PuP7Q?si=zS0XCyGoLXKCSqMj', 'coming_soon', '2025-09-11 12:14:17', '1200'),
(28, 'Miraculous', 'Bestowed with magical powers of creation, Ladybug must unite with her opposite, Cat Noir, to save Paris as a villain unleashes chaos into the city.\r\n\r\n\r\n2023 · Children · 1h 45m', '../assets/posters/Miraculous_1757655488.jpg', 'https://youtu.be/Eo73DkyXywE?si=3-j4Jdn8IgkfphfZ', 'coming_soon', '2025-09-12 05:38:08', '600');

-- --------------------------------------------------------

--
-- Table structure for table `shows`
--

CREATE TABLE `shows` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `showdate` date NOT NULL,
  `showtime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shows`
--

INSERT INTO `shows` (`id`, `movie_id`, `theater_id`, `showdate`, `showtime`) VALUES
(19, 2, 3, '2025-09-12', '18:30:00'),
(21, 22, 3, '2025-12-06', '18:30:00'),
(38, 25, 1, '2025-11-11', '18:30:00'),
(39, 24, 1, '2025-11-11', '14:30:00'),
(40, 23, 3, '2025-12-09', '18:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `capacity`) VALUES
(1, 'Main Hall', 100),
(2, 'VIP Hall', 50),
(3, '3D Theater', 80);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@mondycinema.com', '0192023a7bbd73250516f069df18b500', 'admin', '2025-09-07 15:38:25'),
(2, 'Vihini wimansa', 'rinay70689@gmail.com', 'a229eb46060693ec66c655e17f324c00', 'customer', '2025-09-09 13:32:42'),
(3, 'Maasha Nethmini', 'maasha1111jay@gmail.com', 'ce960b689a15d9748451da2f36da9713', 'customer', '2025-09-09 13:44:36'),
(4, 'Thinaya Thasheli', 'thivona159ona@gmail.com', '9f4a358addaf03d99aee3b98b6a01eb6', 'customer', '2025-09-10 13:16:40'),
(5, 'Bihanga Manudini ', 'bihangaj111@gmail.com', '83051a36818a08407c1ff1bf4b3355a1', 'customer', '2025-09-11 09:26:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test` (`show_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shows`
--
ALTER TABLE `shows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `shows`
--
ALTER TABLE `shows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `test` FOREIGN KEY (`show_id`) REFERENCES `shows` (`id`);

--
-- Constraints for table `shows`
--
ALTER TABLE `shows`
  ADD CONSTRAINT `shows_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `shows_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
