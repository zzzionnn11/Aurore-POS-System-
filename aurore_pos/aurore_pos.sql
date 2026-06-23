-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 05:58 PM
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
-- Database: `aurore_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `price`, `stock`, `image_url`) VALUES
(1, 'Classic T-Shirt', 29.99, 12, 'https://media.istockphoto.com/id/482948743/photo/blank-white-t-shirt-front-with-clipping-path.jpg?s=612x612&w=0&k=20&c=cJG_B0mOIG42FKtC_rqIeZCClYOj7UCFNNs9WTkYEEE='),
(2, 'Trendy Denim Jeans', 79.99, 8, 'https://cdn-images.farfetch-contents.com/20/41/89/55/20418955_51295248_1000.jpg'),
(3, 'Vintage Hoodie', 59.99, 12, 'https://medievalmnl.com/cdn/shop/files/ACIDWASHEDB.png?v=1742482089&width=1946'),
(4, 'Sneakers', 99.99, 3, 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_2000,h_2000/global/395019/01/sv01/fnd/PHL/fmt/png/Court-Classic-Lux-Sneakers'),
(5, 'Starboy Leather Jacket', 149.99, 5, 'https://cdn-images.farfetch-contents.com/17/81/19/24/17811924_37690679_600.jpg'),
(6, 'Running Shoes', 89.99, 100, 'https://i5.walmartimages.com/seo/Damyuan-Running-Shoes-Men-Fashion-Sneakers-Slip-on-Casual-Walking-Shoes-Sport-Athletic-Shoes-Lightweight-Breathable-Comfortable_4114141f-7d26-4dd7-933d-babc24080395.516ad145e1a1d8d82a801ac48231950d.jpeg'),
(7, 'LA Dodgers Baseball Cap', 19.99, 24, 'https://neweracap.ph/cdn/shop/files/02257962-GRABMART-2_1000x.jpg?v=1718281111'),
(8, 'Sunglasses', 49.99, 10, 'https://cdn-images.farfetch-contents.com/22/72/65/52/22726552_52691585_600.jpg'),
(10, 'Watch', 299.99, 0, 'https://www.seikowatches.com/us-en/-/media/Images/Product--Image/All/Seiko/2023/03/02/11/46/SWR083P1/SWR083P1.png'),
(11, 'Scarf', 24.99, 18, 'https://www.stormtechusa.com/cdn/shop/products/SCX-1-04000000-FRONT.jpg?v=1755642768&width=1200'),
(12, 'Beanie', 14.99, 20, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRyK5OfqHKemVn_AQoBhqXh2gbwoqwOz7FfQg&s'),
(14, 'Silver Rings', 89.99, 15, 'https://i5.walmartimages.com/seo/Silvora-925-Sterling-Silver-Rings-for-Women-Men-5mm-Eternity-Wedding-Engagement-Ring-Polished-Stackable-Band-Ring-Jewelry-Gift-Size-10_35c5e99f-944c-4471-959d-b1e7e699797d.71640d79c66db170117b86739e4a29b7.jpeg?odnHeight=768&odnWidth=768&odnBg=FFFFFF');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `transaction_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_amount`, `payment_amount`, `change_amount`, `transaction_date`) VALUES
(1, 1, 579.94, 600.00, 20.06, '2026-04-14 00:49:20'),
(2, 1, 29.99, 100.00, 70.01, '2026-04-19 15:57:37'),
(3, 1, 59.98, 100.00, 40.02, '2026-04-19 16:10:31'),
(4, 1, 19.99, 19.99, 0.00, '2026-04-20 23:53:57');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_time` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `price_at_time`) VALUES
(1, 1, 6, 2, 89.99),
(2, 1, 4, 4, 99.99),
(3, 2, 1, 1, 29.99),
(4, 3, 1, 2, 29.99),
(5, 4, 7, 1, 19.99);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','cashier') DEFAULT 'cashier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'admin'),
(2, 'cashier', 'cashier123', 'Cashier User', 'cashier');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`),
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
