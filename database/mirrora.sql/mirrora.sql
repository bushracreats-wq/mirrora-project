-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2026 at 12:17 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mirrora`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'Unread',
  `is_visible` bit(1) DEFAULT b'1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`, `is_visible`) VALUES
(1, 'Bushra Creates', 'bushracreats@gmail.com', NULL, 'asslamu alaikum ilike your dress ', '2026-07-27 10:27:03', 'approved', b'1'),
(2, 'hira', 'hira@gmail.com', NULL, 'i love your dresses ', '2026-07-28 08:49:59', 'approved', b'1'),
(3, 'sadaf ', 'sadaf@gmail.com', NULL, 'i love this brand and the clothes are very comfortable and the fabric is very good i really definatly recommended this clothes', '2026-07-29 13:02:28', 'approved', b'1'),
(4, 'Bushra Creates', 'bushracreats@gmail.com', NULL, 'assalamu alaikum', '2026-08-04 08:39:29', 'approved', b'1'),
(5, 'Bushra Creates', 'bushracreats@gmail.com', NULL, 'like\r\n', '2026-08-07 16:13:13', 'Unread', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_products` text,
  `product_color` varchar(50) DEFAULT NULL,
  `product_size` varchar(50) DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `payment_method` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `email`, `phone`, `address`, `city`, `total_amount`, `order_products`, `product_color`, `product_size`, `order_date`, `status`, `payment_method`) VALUES
(12, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 84050.00, 'men\'s collection (Qty: 2, Price: 37500) | Smocked Sundress 1 (Qty: 1, Price: 5200) | Playful Picks (Qty: 1, Price: 3850)', NULL, NULL, '2026-07-27 20:54:15', 'Pending', 'COD'),
(13, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 8600.00, 'Everyday Walking Sneaker (Qty: 1, Price: 3900) | Pro-Stride Running Shoe (Qty: 1, Price: 4700)', NULL, NULL, '2026-07-27 22:41:09', 'Pending', 'Bank Transfer'),
(14, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 41350.00, 'men\'s collection (Qty: 1, Price: 37500) | Playful Picks (Qty: 1, Price: 3850)', NULL, NULL, '2026-07-28 13:09:41', 'Pending', 'Bank Transfer'),
(15, 'kiran ', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 37500.00, 'men\'s collection (Qty: 1, Price: 37500)', NULL, NULL, '2026-07-28 13:15:29', 'Pending', 'COD'),
(16, 'sadaf', 'bushracreats@gmail.com', '03214567899', 'karimabad', 'karachi', 41350.00, 'men\'s collection (Qty: 1, Price: 37500) | Playful Picks (Qty: 1, Price: 3850)', NULL, NULL, '2026-07-29 17:54:25', 'Processing', 'COD'),
(17, 'jannat', 'jannat@gamil.com', '0300-7875636', 'garden south', 'lahore', 10400.00, 'Smocked Sundress 1 (Qty: 2, Price: 5200)', NULL, NULL, '2026-07-29 18:12:10', 'Delivered', 'Credit/Debit Card'),
(18, 'usman ', 'usmna@gmail.com', '0321-364784736', 'grden', 'lahore', 37500.00, 'men\'s collection (Qty: 1, Price: 37500)', NULL, NULL, '2026-07-31 19:24:32', 'Pending', 'EasyPaisa / JazzCash'),
(19, 'muhammad ali ', 'ali@gmail.com', '0345897668', 'up', 'karachi', 37500.00, 'men\'s collection (Qty: 1, Price: 37500, Color: N/A, Size: N/A)', 'N/A', 'N/A', '2026-07-31 19:28:43', 'Pending', 'COD'),
(20, 'sajid', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 37500.00, 'men\'s collection (Qty: 1, Price: 37500, Color: green, Size: large)', 'green', 'large', '2026-07-31 19:32:23', 'Cancelled', 'COD'),
(21, 'bisma', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 7200.00, 'Silken Breeze (Qty: 1, Price: 7200, Color: N/A, Size: N/A)', 'N/A', 'N/A', '2026-07-31 19:38:37', 'Delivered', 'Bank Transfer'),
(22, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 26000.00, 'Classic Cotton Kurta (Qty: 2, Price: 8000, Color: N/A, Size: N/A) | Classic White Kurta (Qty: 1, Price: 10000, Color: N/A, Size: N/A)', 'N/A, N/A', 'N/A, N/A', '2026-08-01 11:21:14', 'Processing', 'COD'),
(23, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 13500.00, 'Matel Jewelry  (Qty: 1, Price: 3500, Color: N/A, Size: N/A) | Casual Linen Shirt (Qty: 1, Price: 10000, Color: N/A, Size: N/A)', 'N/A, N/A', 'N/A, N/A', '2026-08-01 19:21:00', 'Pending', 'COD'),
(24, 'uzma', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 44100.00, 'Modern Ethnic (Qty: 1, Price: 3000, Color: N/A, Size: N/A) | men\'s collection (Qty: 1, Price: 37500, Color: green, Size: large) | Radiant Custom Gold Name Chain (Qty: 1, Price: 3600, Color: N/A, Size: N/A)', 'N/A, green, N/A', 'N/A, large, N/A', '2026-08-04 14:55:47', 'Cancelled', 'COD'),
(25, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 47500.00, 'Classic White Kurta (Qty: 1, Price: 10000, Color: N/A, Size: N/A) | men\'s collection (Qty: 1, Price: 37500, Color: balck, Size: samll)', 'N/A, balck', 'N/A, samll', '2026-08-05 14:15:48', 'Pending', 'COD'),
(26, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 10000.00, 'Casual Linen Shirt (Qty: 1, Price: 10000, Color: N/A, Size: N/A)', 'N/A', 'N/A', '2026-08-06 13:46:49', 'Pending', 'COD'),
(27, 'Bushra Creates', 'bushracreats@gmail.com', '03214567899', 'sana homes', 'karachi', 5000.00, 'Luxe Diamond-Cut Script Name Pendant (Qty: 1, Price: 5000, Color: Red, Size: Adjustable)', 'Red', 'Adjustable', '2026-08-06 20:58:09', 'Processing', 'COD');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `description` text,
  `size` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `images` text,
  `image` varchar(255) NOT NULL,
  `discount_percent` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `description`, `size`, `color`, `images`, `image`, `discount_percent`) VALUES
(1, 'men\'s collection', 'Men', 50000, 'Exclusive men collection featuring versatile and stylish everyday wear.', 'Small, Medium, Large, XL', 'Assorted, Black, Navy', '1784390842_men1.jpg', '', 25),
(2, 'Classic White Kurta', 'Men', 10000, 'Elegant and comfortable classic white kurta, perfect for casual and festive occasions.', 'Small, Medium, Large, XL', 'White', '1784391048_men2.jpg', '', 0),
(7, 'Casual Linen Shirt', 'Men', 10000, 'Breathable and lightweight casual linen shirt designed for everyday summer wear.', 'Medium, Large, XL', 'Blue, Beige, Grey', '1784391585_extra.jfif', '', 0),
(6, 'Premium Cotton Shalwar', 'Men', 15000, 'Soft premium cotton shalwar offering maximum comfort and durability.', 'Standard, Free Size', 'White, Black, Off-White', '1784391491_men3.jpg', '', 0),
(18, 'Tapered Chino Pants', 'Men', 12000, 'Modern slim-fit tapered chino pants suitable for smart-casual styling.', '30, 32, 34, 36', 'Navy Blue, Khaki, Black', '1784392467_men16.jpg', '', 0),
(8, 'Traditional Prince Coat', 'Men', 18000, 'Exquisite traditional prince coat featuring fine tailoring for weddings and formal events.', 'Medium, Large, XL', 'Maroon, Black, Navy', '1784391659_men4.jpg', '', 0),
(9, 'Designer Waist Coat', 'Men', 18000, 'Stylish designer waist coat to elevate your traditional and formal outfits.', 'Small, Medium, Large', 'Brown, Grey, Black', '1784391699_men5.jpg', '', 0),
(11, 'Stylish Denim Jacket', 'Men', 18000, 'Rugged and trendy denim jacket designed for a cool, casual street style look.', 'Medium, Large, XL', 'Blue, Light Blue, Black', '1784391961_men6.jpg', '', 0),
(12, 'Royal Velvet Sherwani', 'Men', 18000, 'Luxurious royal velvet sherwani crafted with intricate embroidery for special occasions.', 'Medium, Large, XL', 'Deep Maroon, Navy Blue, Black', '1784391988_men7.jpg', '', 0),
(13, 'Smart Casual Blazer', 'Men', 18000, 'Contemporary smart casual blazer tailored for versatile styling and professional looks.', '38, 40, 42, 44', 'Grey, Navy, Charcoal', '1784392156_men8.jpg', '', 0),
(14, 'Embroidered Party Kurta', 'Men', 20000, 'Stunning embroidered party kurta designed with rich detailing for festive gatherings.', 'Medium, Large, XL', 'Cream, Gold, Black', '1784392195_men9.jpg', '', 0),
(15, 'Office Wear Suit', 'Men', 25000, 'Classic professional office wear suit tailored for clean corporate styling.', '38, 40, 42, 44', 'Charcoal Grey, Navy Blue', '1784392298_men12.jpg', '', 0),
(16, 'Festive Silk Shalwar', 'Men', 25000, 'Rich festive silk shalwar kameez set offering a grand look and comfortable drape.', 'Medium, Large, XL', 'Champagne Gold, Royal Blue', '1784392359_men13.jpg', '', 0),
(19, 'Classic Nehru Jacket', 'Men', 12000, 'Timeless classic Nehru jacket to layer effortlessly over kurtas and shirts.', 'Medium, Large, XL', 'Beige, Black, Maroon', '1784392549_men17.jpg', '', 0),
(20, 'Luxury Eid Collection', 'Men', 12000, 'Exclusive luxury Eid collection featuring premium fabrics and refined tailoring.', 'Medium, Large, XL', 'Teal, White, Emerald', '1784392583_men10.jpg', '', 0),
(21, 'Professional Tuxedo Suit', 'Men', 12000, 'Immaculately tailored professional tuxedo suit for high-end formal events and galas.', '38, 40, 42, 44', 'Jet Black, Midnight Blue', '1784392625_men18.jpg', '', 0),
(22, 'Comfortable Track Suit', 'Men', 14000, 'Flexible and comfortable athletic track suit built for gym sessions and running.', 'Small, Medium, Large, XL', 'Black, Charcoal, Grey', '1784392676_men21.jpg', '', 0),
(23, 'Breathable Khaddar Suit', 'Men', 14000, 'Warm and breathable winter khaddar suit designed for traditional seasonal comfort.', 'Medium, Large, XL', 'Rust, Navy, Dark Brown', '1784392697_men23.jpg', '', 0),
(24, 'Elegant Formal Trousers', 'Men', 14000, 'Sharp and clean-cut elegant formal trousers tailored for business attire.', '30, 32, 34, 36', 'Black, Navy, Grey', '1784392732_men24.jpg', '', 0),
(25, 'Trendy Kurta Pajama', 'Men', 14000, 'Trendy and comfortable kurta pajama set combining modern fit with traditional style.', 'Medium, Large, XL', 'White, Cream, Light Grey', '1784392768_men20.jpg', '', 50),
(31, 'Embroidered Lawn Sui', 'Women', 12000, 'Beautiful embroidered lawn suit featuring intricate threadwork and matching digital print dupatta.', 'Small, Medium, Large', 'Multi-color, Pastel', '1784445104_wom2.jpg', '', 0),
(68, 'Dainty Dot', 'Women', 12000, 'Charming Dainty Dot patterned casual shirt tailored for comfortable daily styling.', 'Small, Medium, Large, XL', 'Black, White', '1784448131_wom16.jpg', '', 0),
(69, 'Modern Ethnic', 'Women', 3000, 'Modern ethnic wear outfit designed with contemporary cuts and traditional prints.', 'Small, Medium, Large', 'Teal, Rust, Mustard', '1784448159_wom17.jpg', '', 0),
(30, 'Elegant Chiffon Maxi', 'Women', 12000, 'Elegant flowing chiffon maxi dress perfect for formal dinners and evening events.', 'Small, Medium, Large', 'Royal Blue, Emerald Green', '1784445067_wom1.jpg', '', 0),
(70, 'Silken Breeze', 'Women', 12000, 'Silken breeze lightweight summer outfit offering a soft and luxurious feel.', 'Small, Medium, Large', 'Sky Blue, Mint Green', '1784448192_wom18.jpg', '', 40),
(60, 'Abstract Fusion', 'Women', 20000, 'Abstract fusion printed chic tunic top with contemporary styling.', 'Small, Medium, Large', 'Multi-color Abstract', '1784447164_wom10.jpg', '', 0),
(58, 'Tropical Vibe', 'Women', 34000, 'Tropical vibe summer wear designed with vibrant and fresh prints.', 'Small, Medium, Large, XL', 'Yellow, Green, Peach', '1784446675_wom9.jpg', '', 25),
(67, 'Golden Lace', 'Women', 23000, 'Golden lace embellished formal shirt paired with delicate detailing.', 'Small, Medium, Large', 'Gold, Beige, Off-White', '1784448007_wom15.jpg', '', 40),
(66, 'Classic Check', 'Women', 30000, 'Classic check patterned casual shirt for an effortlessly stylish look.', 'Small, Medium, Large', 'Black/White Check, Red Check', '1784447969_wom14.jpg', '', 25),
(65, 'Soft Serenity', 'Women', 12000, 'Soft serenity soothing cotton outfit crafted for ultimate comfort.', 'Small, Medium, Large, XL', 'Lavender, Baby Pink', '1784447941_wom13.jpg', '', 45),
(64, 'Urban Edge', 'Women', 23000, 'Urban edge contemporary streetwear top designed for modern styling.', 'Small, Medium, Large', 'Charcoal Grey, Jet Black', '1784447905_wom12.jpg', '', 30),
(63, 'Midnight Velvet', 'Women', 4000, 'Midnight velvet luxurious formal suit with rich deep tones and fine texture.', 'Small, Medium, Large', 'Deep Black, Midnight Blue, Maroon', '1784447832_wom4.jpg', '', 0),
(62, 'Sun-Drenched Prints', 'Women', 4999, 'Sun-drenched prints summer collection featuring bright and cheerful hues.', 'Small, Medium, Large', 'Yellow, Orange, White', '1784447606_wom11.jpg', '', 0),
(61, 'Satin Smooth', 'Women', 13000, 'Satin smooth premium fabric top offering an ultra-soft glossy finish.', 'Small, Medium, Large', 'Champagne, Silver, Rose Gold', '1784447448_wom6.jpg', '', 30),
(50, 'Floral Bloom', 'Women', 2300, 'Floral bloom vibrant printed lawn shirt adorned with blossom motifs.', 'Small, Medium, Large, XL', 'Pink, Multi Floral', '1784446185_wom5.jpg', '', 0),
(71, 'Chic Border', 'Women', 30000, 'Chic border detailed elegant shirt featuring stylish daman motifs.', 'Small, Medium, Large', 'White, Cream, Navy', '1784448213_wom19.jpg', '', 25),
(72, 'Bold Contrast', 'Women', 5000, 'Bold contrast striking color-blocked modern outfit for a standout look.', 'Small, Medium, Large', 'Black/Red, Blue/White', '1784448236_wom20.jpg', '', 0),
(73, 'Geometric Essence', 'Women', 6000, 'Geometric essence artistic pattern printed contemporary dress.', 'Small, Medium, Large', 'Geo Multi, Monochrome', '1784448294_wom22.jpg', '', 0),
(80, 'Tiny Treasures', 'Kids', 5000, 'Charming little festive dress made with delicate, non-scratchy fabric lining to keep little ones comfortable all day long while looking exceptionally adorable.', '1-2Y, 2-3Y, 4-5Y', 'Pastel Pink, Mint Green', '1784630925_kid3.jpg', '', 0),
(75, 'Minimalist Lines', 'Women', 40000, 'Minimalist lines subtle and clean linear printed casual wear shirt.', 'Small, Medium, Large', 'White/Black Stripe', '1784448349_wom23.jpg', '', 0),
(76, 'Embroidered Elegance', 'Women', 70000, 'Embroidered elegance premium formal stitched outfit with exquisite handwork.', 'Small, Medium, Large', 'Maroon, Bottle Green, Gold', '1784448372_wom24.jpg', '', 0),
(81, 'The Kiddie Korner', 'Kids', 6000, 'Playful and stylish casual set featuring soft elastic waistbands and breathable textiles tailored specifically for active play and cheerful family gatherings.', '4-5Y, 6-7Y, 8-9Y', 'Royal Blue, Orange', '1784630976_kid4.jpg', '', 0),
(78, 'Classic Cotton Kurta', 'Kids', 8000, 'Soft breathable cotton kurta designed for kids daily comfort, featuring traditional embroidery accents and gentle fabric lining for all-day ease.', '3-4Y, 5-6Y, 7-8Y', 'White, Sky Blue', '1784450651_kid1.jpg', '', 0),
(79, 'kids fun', 'Kids', 12333, 'Fun and vibrant everyday wear outfit crafted from skin-friendly fabric, ensuring absolute freedom of movement for active kids during playtime or casual outings.', '2-3Y, 4-5Y, 6-7Y', 'Bright Yellow, Red', '1784452935_kid2.jpg', '', 0),
(82, 'Playful Picks', 'Kids', 7000, 'Trendy, lightweight outfit crafted with durability in mind, perfect for outdoor birthday parties, celebrations, and festive daytime events.', '3-4Y, 5-6Y, 7-8Y', 'Coral, Peach', '1784631025_kid5.jpg', '', 45),
(83, 'Bnag On Best', 'Shoes', 5500, 'Brag On Best stylish casual and sports footwear designed for ultimate comfort.', '40, 41, 42, 43, 44', 'Black, White, Grey', '1784632992_sho1.jpg', '', 0),
(84, 'Out Class Collection', 'Shoes', 6000, 'Out Class Collection premium footwear featuring durable sole and modern aesthetics.', '40, 41, 42, 43, 44, 45', 'Brown, Black, Tan', '1784633029_sho2.jpg', '', 0),
(85, 'Star Pair Set', 'Jewelry', 3000, 'Star Pair Set elegant matching jewelry pieces designed for a subtle sparkle.', 'Adjustable', 'Gold, Silver', '1784633074_jew1.jpg', '', 0),
(86, 'Matel Jewelry ', 'Jewelry', 3500, 'Matel Jewelry chic and trendy contemporary metallic ornament set.', 'Standard', 'Gold, Silver, Rose Gold', '1784633126_jew2.jpg', '', 0),
(87, 'Pastel Whisper', 'Kids', 6999, 'Graceful pastel-shaded outfit featuring fine detailing and soft inner lining, designed to give a dreamy traditional ethnic look for young ones.', '5-6Y, 7-8Y, 9-10Y', 'Lavender, Mint', '1784731076_5599.jpg', '', 25),
(88, 'Maxi Dress 1', 'Kids', 7000, 'Elegant floor-length maxi dress for young princesses, crafted from flowing fabric with subtle shimmer details and comfortable inner stitching.', '4-5Y, 6-7Y, 8-9Y, 10-11Y', 'Ruby Red, Maroon', '1784732957_4630.jpg', '', 0),
(89, 'Formal Gown 1', 'Kids', 7000, 'Exquisite formal gown designed for weddings and grand special occasions, featuring layered tulle and a completely skin-safe cotton inner lining.', '5-6Y, 7-8Y, 9-10Y', 'Champagne, Rose Gold', '1784733094_8719.jpg', '', 0),
(90, 'Tiered Frock 1', 'Kids', 3000, 'Beautiful multi-tiered frock that adds a bouncy, joyful flair, made from soft, high-quality fabric designed for endless playtime fun and elegance.', '2-3Y, 4-5Y, 6-7Y', 'Sunshine Yellow, Turquoise', '1784733472_6556.jpg', '', 0),
(91, 'Smocked Dress 1', 'Kids', 9000, 'Classic smocked bodice dress with charming hand-embroidered floral motifs, offering a timeless, elegant, and exceptionally comfortable fit for kids.', '3-4Y, 5-6Y, 7-8Y', 'Dusty Pink, Sky Blue', '1784733509_9375.jpg', '', 0),
(93, 'A-Line Dress 1', 'Kids', 7000, 'Simple yet chic A-line dress tailored for casual elegance, ensuring maximum breathability and ease during warm weather and outdoor activities.', '4-5Y, 6-7Y, 8-9Y', 'Coral Red, Mustard', '1784733607_1304.jpg', '', 0),
(94, 'Poplin Dress 1', 'Kids', 5500, 'Crisp cotton-poplin dress featuring a smart structure, button-down front, and cute bow accents for a smart-casual look suitable for formal lunches.', '3-4Y, 5-6Y, 7-8Y', 'Navy Blue, White', '1784733631_9561.jpg', '', 25),
(95, 'Bubble Dress 1', 'Kids', 6000, 'Adorable bubble-hem dress designed with playful volume and ultra-soft fabric to keep kids cozy, fashionable, and cheerful throughout the day.', '2-3Y, 4-5Y, 6-7Y', 'Bubblegum Pink, Lilac', '1784733665_3817.jpg', '', 0),
(96, 'Shirtdress 1', 'Kids', 8000, 'Versatile and comfortable shirtdress style outfit, ideal for school events, family outings, and casual weekend hangouts with a smart tailored finish.', '5-6Y, 7-8Y, 9-10Y', 'Olive Green, Khaki', '1784733688_2023.jpg', '', 0),
(97, 'Smocked Sundress 1', 'Kids', 8000, 'Breezy summer sundress featuring elastic smocking for a flexible fit and lively playful prints that kids love to wear during sunny days.', '3-4Y, 5-6Y, 7-8Y', 'Lemon Yellow, Peach', '1784733719_7927.jpg', '', 35),
(98, 'Pleated Dress 1', 'Kids', 6000, 'Sophisticated pleated design dress crafted with premium lightweight fabric that flows gracefully with every single step your child takes.', '6-7Y, 8-9Y, 10-11Y', 'Emerald Green, Burgundy', '1784733756_1451.jpg', '', 0),
(99, 'Wrap Dress 1', 'Kids', 7800, 'Modern wrap-style dress with secure inner ties and a comfortable loose fit, perfect for fashionable young trendsetters looking for unique style.', '4-5Y, 6-7Y, 8-9Y', 'Teal, Coral', '1784733794_6267.jpg', '', 0),
(101, 'happy kids 1', 'Kids', 9000, 'Cheerful and colorful celebratory outfit designed with vibrant hues and skin-safe dyes to brighten up any festive occasion or family celebration.', '2-3Y, 4-5Y, 6-7Y, 8-9Y', 'Multi-Color, Bright Red', '1784733973_7518.jpg', '', 0),
(144, 'Pro-Stride Running Shoe', 'Shoes', 4700, 'Pro Stride Running Shoe engineered with breathable mesh and cushioned grip for athletes.', '40, 41, 42, 43, 44, 45', 'Black, Grey, Neon Blue', 'sho17.jpg', '', 40),
(104, 'Luxe Diamond-Cut Script Name Pendant', 'Jewelry', 5000, 'Luxe Diamond-Cut Script Name Pendant featuring custom typography and sparkling finish.', '18 inch chain', 'Gold, Silver', 'jew4.jpg', '', 25),
(105, 'Radiant Custom Gold Name Chain', 'Jewelry', 6000, 'Radiant Custom Gold Name Chain elegant personalized jewelry crafted in rich gold tone.', 'Adjustable', 'Gold', 'jew5.jpg', '', 0),
(106, 'Sleek Modern Cursive Nameplate', 'Jewelry', 7000, 'Sleek Modern Cursive Nameplate minimalist name necklace with clean lettering.', 'Standard', 'Silver, Gold', 'jew6.jpg', '', 0),
(107, 'Sophisticated Monogram Name Necklace', 'Jewelry', 8000, 'Sophisticated Monogram Name Necklace stylish interwoven initials pendant.', 'Adjustable Chain', 'Gold, Rose Gold, Silver', 'jew7.jpg', '', 45),
(108, 'Dainty Interlocking Letter Pendant', 'Jewelry', 12000, 'Dainty Interlocking Letter Pendant delicate double initial charm necklace.', 'Standard', 'Gold, Silver', 'jew8.jpg', '', 0),
(109, 'Signature Style Nameplate Jewelry', 'Jewelry', 2000, 'Signature Style Nameplate Jewelry premium custom-designed name necklace.', 'Adjustable', 'Gold, Silver', 'jew9.jpg', '', 15),
(110, ' Classic Name Necklace', 'Jewelry', 2300, 'Classic Name Necklace timeless personalized accessory for daily wear.', 'Standard (18 inch)', 'Gold, Silver', 'jew10.jpg', '', 0),
(111, ' Necklace', 'Jewelry', 6000, 'Necklace elegant statement piece suitable for formal and casual outfits.', 'Adjustable', 'Gold, Silver, Multi', 'jew11.jpg', '', 0),
(112, 'Elegant Floating Letter Name Pendant', 'Jewelry', 77000, 'Elegant Floating Letter Name Pendant minimalist single-initial charm necklace.', 'Standard', 'Gold, Silver', 'jew12.jpg', '', 0),
(113, 'Polished Edge Custom Name Necklace', 'Jewelry', 1200, 'Polished Edge Custom Name Necklace premium nameplate with smooth finished borders.', 'Adjustable', 'Gold, Rose Gold', 'jew13.jpg', '', 25),
(114, 'Minimalist Bar Style Nameplate', 'Jewelry', 89000, 'Minimalist Bar Style Nameplate sleek horizontal bar engraved name necklace.', 'Standard', 'Silver, Gold, Black', 'jew14.jpg', '', 0),
(122, 'Athletic Running Trainer', 'Shoes', 5000, 'Athletic Running Trainer built for high-performance track and gym workouts.', '40, 41, 42, 43, 44', 'White/Black, Red/Black', 'sho3.jpg', '', 0),
(116, 'Radiant Custom Gold Name Chain', 'Jewelry', 6000, 'Radiant Custom Gold Name Chain bright polished personalized name jewelry.', 'Adjustable', 'Gold', 'jew16.jpg', '', 40),
(117, 'Sleek Modern  Nameplate', 'Jewelry', 7000, 'Sleek Modern Nameplate contemporary thin font custom name necklace.', 'Standard', 'Silver, Gold', 'jew17.jpg', '', 25),
(118, 'Sophisticated Monogram ', 'Jewelry', 8000, 'Sophisticated Monogram intricate multi-letter artistic monogram pendant.', 'Adjustable Chain', 'Gold, Silver', 'jew18.jpg', '', 23),
(119, ' Interlocking Letter Pendant', 'Jewelry', 12000, 'Interlocking Letter Pendant dual connected initial charm necklace.', 'Standard', 'Gold, Rose Gold, Silver', 'jew19.jpg', '', 0),
(120, 'Nameplate Jewelry', 'Jewelry', 2000, 'Nameplate Jewelry classic personalized name accessory with clean design.', 'Adjustable', 'Gold, Silver', 'jew20.jpg', '', 40),
(123, 'Lightweight Jogging Shoe', 'Shoes', 4200, 'Lightweight Jogging Shoe offering flexible movement and shock absorption.', '40, 41, 42, 43, 44', 'Blue, Grey, Black', 'sho4.jpg', '', 0),
(124, 'Comfort Fit Sport Shoe', 'Shoes', 4600, 'Comfort Fit Sport Shoe designed with ergonomic arch support for all-day wear.', '40, 41, 42, 43, 44, 45', 'Black, Charcoal, White', 'sho5.jpg', '', 0),
(125, 'Dynamic Training Sneaker', 'Shoes', 5200, 'Dynamic Training Sneaker crafted for versatile cross-training and agility.', '40, 41, 42, 43, 44', 'Olive, Black, Grey', 'sho6.jpg', '', 0),
(127, 'Active Endurance Shoe', 'Shoes', 5500, 'Active Endurance Shoe built to withstand long-distance running and rigorous training.', '40, 41, 42, 43, 44, 45', 'Navy, Black, Orange', 'sho8.jpg', '', 0),
(128, 'Everyday Walking Sneaker', 'Shoes', 3900, 'Everyday Walking Sneaker featuring soft memory foam for relaxed daily strolling.', '40, 41, 42, 43, 44', 'Grey, White, Beige', 'sho9.jpg', '', 0),
(129, 'Performance Sport Footwear', 'Shoes', 5300, 'Performance Sport Footwear engineered with advanced breathable flyknit upper.', '40, 41, 42, 43, 44, 45', 'Black, Red, White', 'sho10.jpg', '', 0),
(130, 'Flex-Sole Running Shoe', 'Shoes', 4700, 'Flex-Sole Running Shoe offering maximum flexibility and natural stride motion.', '40, 41, 42, 43, 44', 'Blue, Black, Grey', 'sho11.jpg', '', 0),
(131, 'Speedster Marathon Sneaker', 'Shoes', 6000, 'Speedster Marathon Sneaker ultra-lightweight design built for speed enthusiasts.', '40, 41, 42, 43, 44, 45', 'Neon Green, Black, White', 'sho12.jpg', '', 0),
(132, 'Air-Cushion Sport Shoe', 'Shoes', 5100, 'Air-Cushion Sport Shoe featuring responsive heel air units for smooth landings.', '40, 41, 42, 43, 44', 'White, Black, Silver', 'sho13.jpg', '', 0),
(133, 'Terrain Trekking Runner', 'Shoes', 5800, 'Terrain Trekking Runner durable outdoor shoes designed for rough and uneven paths.', '41, 42, 43, 44, 45', 'Tan, Brown, Olive', 'sho14.jpg', '', 0),
(134, 'Prime Sport Trainer', 'Shoes', 4600, 'Prime Sport Trainer premium fitness shoes combining style with high durability.', '40, 41, 42, 43, 44', 'Black, Charcoal', 'sho15.jpg', '', 25),
(135, 'Ultra-Boost Running Shoe', 'Shoes', 6200, 'Ultra-Boost Running Shoe equipped with high-energy return cushioning sole.', '40, 41, 42, 43, 44, 45', 'Triple Black, Clean White, Grey', 'sho16.jpg', '', 40),
(136, 'Pro-Stride Running Shoe', 'Shoes', 4700, 'Pro Stride Running Shoe professional-grade footwear for maximum track performance.', '40, 41, 42, 43, 44', 'Blue, Black', 'sho17.jpg', '', 0),
(137, 'Elite Athletic Trainer', 'Shoes', 5400, 'Elite Athletic Trainer top-tier sports shoes for dedicated athletes and trainers.', '40, 41, 42, 43, 44, 45', 'Red, Black, White', 'sho18.jpg', '', 0),
(138, 'Sprint Master Sneaker', 'Shoes', 4900, 'Sprint Master Sneaker aerodynamic design optimized for short-distance running.', '40, 41, 42, 43, 44', 'Yellow, Black, Grey', 'sho19.jpg', '', 45),
(139, 'Velocity Sport Footwear', 'Shoes', 5600, 'Velocity Sport Footwear high-speed agility training shoes with secure lockdown lacing.', '40, 41, 42, 43, 44', 'Navy Blue, White', 'sho20.jpg', '', 35),
(140, 'Endurance Mesh Runner', 'Shoes', 4300, 'Endurance Mesh Runner high-airflow running shoes to keep feet cool during workouts.', '40, 41, 42, 43, 44, 45', 'Grey, Black, Blue', 'sho21.jpg', '', 24),
(141, 'Motion Control Sneaker', 'Shoes', 5100, 'Motion Control Sneaker designed to provide stability and correct overpronation.', '40, 41, 42, 43, 44', 'Black, Charcoal', 'sho22.jpg', '', 25),
(142, 'Dynamic Fit Jogger', 'Shoes', 4800, 'Dynamic Fit Jogger versatile daily jogging shoes offering snug ankle support.', '40, 41, 42, 43, 44', 'White, Grey, Navy', 'sho23.jpg', '', 0),
(143, 'Apex Performance Trainer', 'Shoes', 5900, 'Apex Performance Trainer ultimate sports shoe engineered for peak physical output.', '40, 41, 42, 43, 44, 45', 'Black, Red, Gold', 'sho24.jpg', '', 25);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `review_text` text,
  `rating` int DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_name`, `review_text`, `rating`, `status`, `created_at`) VALUES
(1, 12, 'muskan', 'fabric outclass', 5, 'approved', '2026-08-04 09:01:22'),
(2, 82, 'bushra', 'amazing products i love it quality amazing ', 4, 'approved', '2026-08-04 09:24:31'),
(7, 141, 'jawad', 'comfrtable good in summer', 5, 'approved', '2026-08-04 15:55:26'),
(8, 141, 'jawad', 'comfrtable good in summer', 5, 'approved', '2026-08-04 15:56:18'),
(9, 9, 'muskan', 'good', 4, 'approved', '2026-08-05 07:35:41'),
(10, 1, 'ayaan ', 'like it \r\n', 5, 'approved', '2026-08-07 15:29:38'),
(6, 9, 'alizay', 'mindblowing ', 5, 'approved', '2026-08-04 10:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT '1',
  `product_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 1, 7, '2026-07-27 16:24:42'),
(2, 1, 68, '2026-07-27 16:25:57'),
(3, 1, 11, '2026-07-27 16:28:07'),
(7, 1, 2, '2026-08-05 09:16:05'),
(8, 1, 6, '2026-08-06 08:47:02'),
(9, 1, 104, '2026-08-06 08:47:21'),
(10, 1, 123, '2026-08-06 08:55:40'),
(11, 1, 69, '2026-08-06 09:35:40'),
(12, 1, 20, '2026-08-06 09:37:51'),
(13, 1, 90, '2026-08-06 09:38:44'),
(14, 1, 84, '2026-08-06 09:54:36'),
(16, 1, 106, '2026-08-06 15:56:13'),
(17, 1, 12, '2026-08-10 11:25:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
