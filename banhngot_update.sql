-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251118.dfcf3dd949
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 23, 2025 at 02:07 PM
-- Server version: 8.0.42
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banhngot`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Id` int NOT NULL,
  `Username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_vietnamese_ci NOT NULL,
  `Role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Id`, `Username`, `Email`, `Password`, `Role`) VALUES
(1, 'admin', 'admin@gmail.com', '202cb962ac59075b964b07152d234b70', 1);

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `BannerId` int NOT NULL,
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề banner',
  `Subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Phụ đề',
  `ButtonText` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Text nút',
  `ButtonLink` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link nút',
  `Image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ảnh banner',
  `SortOrder` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự hiển thị',
  `IsActive` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Đang hoạt động',
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`BannerId`, `Title`, `Subtitle`, `ButtonText`, `ButtonLink`, `Image`, `SortOrder`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Làm cho cuộc sống của bạn ngọt ngào hơn', NULL, 'Bánh của chúng tôi', 'list_product.php', 'img/hero/anh1.jpg', 1, 1, '2025-11-23 17:26:18', NULL),
(2, 'Hãy khám phá những bí mật nho nhỏ cùng Cake Shop nhé!', NULL, 'Khám phá ngay', 'list_product.php', 'img/hero/anh2.jpg', 2, 1, '2025-11-23 17:26:18', '2025-11-23 20:05:14'),
(3, 'Bánh ngọt tươi mỗi ngày - Chất lượng đảm bảo', NULL, 'Xem khuyến mãi', 'promotions.php', 'img/hero/hero-11.jpg', 3, 1, '2025-11-23 17:26:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `BrandId` int NOT NULL,
  `BrandName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Status` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`BrandId`, `BrandName`, `Image`, `Status`) VALUES
(2, 'Bánh kem Tous Les Jours', '23fa8e56c2.cart-2.jpg', 0),
(88, 'Tous Les Jours (Hàn Quốc)', '5efabeb186.th1.JPG', 1),
(96, 'Givral (Sài Gòn)', 'df4bea3d22.th4.JPG', 1),
(97, 'Paris Baguette (Pháp)', '0fb89bd7a5.th2.JPG', 1),
(101, 'Chez Moi (Pháp)', 'fe588555c8.th3.JPG', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `CategoryId` int NOT NULL,
  `CategoryName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Icon` varchar(255) COLLATE utf8mb3_vietnamese_ci DEFAULT NULL,
  `Description` text COLLATE utf8mb3_vietnamese_ci,
  `status` int NOT NULL DEFAULT '1',
  `SortOrder` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`CategoryId`, `CategoryName`, `Icon`, `Description`, `status`, `SortOrder`) VALUES
(2, 'Bánh ngọt', NULL, NULL, 1, 1),
(3, 'Bánh kem', NULL, NULL, 1, 2),
(4, 'Bánh mì', NULL, NULL, 1, 3),
(15, 'Bánh quy', NULL, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `ContactId` int NOT NULL,
  `UserName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Message` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`ContactId`, `UserName`, `Email`, `Message`) VALUES
(5, 'anh', 'anh@gmail.com', 'haaaaa'),
(6, 'thanh', 'anh@gmail.com', 'bánh ngon'),
(7, 'thanh', 'anh@gmail.com', 'bánh ngon');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `CustomerId` int NOT NULL,
  `Fullname` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `PhoneNumber` char(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '1',
  `Email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Date_Login` datetime NOT NULL,
  `Date_Logout` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CustomerId`, `Fullname`, `Image`, `PhoneNumber`, `Address`, `Status`, `Email`, `Password`, `Date_Login`, `Date_Logout`) VALUES
(7, 'Anh', '', '', '', 1, 'anh@gmail.com', '202cb962ac59075b964b07152d234b70', '2025-11-23 16:44:10', '2023-12-22 13:25:29'),
(8, 'thanhle', '', '', '', 1, 'lethithaothanh2001@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-01-30 14:15:04', '2024-01-30 15:23:04'),
(9, 'thanhle', '', '', '', 0, 'admin@gmail.com', '202cb962ac59075b964b07152d234b70', '2023-11-27 20:47:21', '2023-11-27 21:04:36'),
(10, 'hoa', '', '', '', 1, 'hoa@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-01-30 19:45:00', '2023-12-14 20:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `NewsId` int NOT NULL,
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề tin tức',
  `Slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL friendly',
  `Summary` text COLLATE utf8mb4_unicode_ci COMMENT 'Tóm tắt',
  `Content` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Nội dung',
  `Image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện',
  `Category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Danh mục tin tức',
  `Author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tác giả',
  `IsPublished` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã xuất bản',
  `IsFeatured` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nổi bật',
  `ViewCount` int NOT NULL DEFAULT '0' COMMENT 'Lượt xem',
  `SortOrder` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự hiển thị',
  `PublishedAt` datetime DEFAULT NULL COMMENT 'Ngày xuất bản',
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`NewsId`, `Title`, `Slug`, `Summary`, `Content`, `Image`, `Category`, `Author`, `IsPublished`, `IsFeatured`, `ViewCount`, `SortOrder`, `PublishedAt`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Công thức làm bánh kem tại nhà đơn giản', NULL, 'Hướng dẫn chi tiết cách làm bánh kem thơm ngon tại nhà với những nguyên liệu dễ tìm', 'Bánh kem là món tráng miệng yêu thích của nhiều người. Với công thức đơn giản này, bạn có thể tự tay làm những chiếc bánh kem thơm ngon ngay tại nhà.\n\n**Nguyên liệu cần chuẩn bị:**\n- Bột mì: 200g\n- Đường: 150g\n- Trứng gà: 3 quả\n- Bơ: 100g\n- Kem tươi: 200ml\n- Vanilla: 1 thìa cà phê\n\n**Các bước thực hiện:**\n1. Đánh bơ và đường cho đến khi bông xốp\n2. Thêm trứng vào từng quả một, đánh đều\n3. Rây bột mì và trộn đều\n4. Nướng ở nhiệt độ 180°C trong 25-30 phút\n5. Để nguội và phủ kem lên trên\n\nChúc bạn thành công với công thức này!', NULL, 'Công thức', 'Admin', 1, 1, 0, 1, '2025-11-23 17:16:41', '2025-11-23 17:16:41', NULL),
(2, 'Lợi ích sức khỏe của bánh mì nguyên cám', NULL, 'Bánh mì nguyên cám không chỉ ngon mà còn rất tốt cho sức khỏe. Tìm hiểu những lợi ích tuyệt vời của loại bánh này.', 'Bánh mì nguyên cám là lựa chọn tốt cho sức khỏe hơn so với bánh mì trắng thông thường. Dưới đây là những lợi ích chính:\n\n**1. Giàu chất xơ**\nBánh mì nguyên cám chứa nhiều chất xơ giúp hỗ trợ tiêu hóa và giảm nguy cơ mắc các bệnh về đường ruột.\n\n**2. Giàu vitamin và khoáng chất**\nChứa nhiều vitamin B, sắt, magie và các khoáng chất cần thiết cho cơ thể.\n\n**3. Giúp kiểm soát đường huyết**\nChỉ số đường huyết thấp hơn so với bánh mì trắng, giúp kiểm soát lượng đường trong máu tốt hơn.\n\n**4. Hỗ trợ giảm cân**\nChất xơ giúp bạn cảm thấy no lâu hơn, giảm cảm giác thèm ăn.\n\nHãy thêm bánh mì nguyên cám vào chế độ ăn uống hàng ngày của bạn!', NULL, 'Sức khỏe', 'Admin', 1, 1, 0, 2, '2025-11-21 17:16:41', '2025-11-23 17:16:41', NULL),
(3, 'Cửa hàng mở rộng - Thêm nhiều sản phẩm mới', NULL, 'Cửa hàng bánh ngọt của chúng tôi vừa mở rộng với nhiều sản phẩm mới và hấp dẫn', 'Chúng tôi rất vui mừng thông báo về việc mở rộng cửa hàng với nhiều sản phẩm mới và hấp dẫn.\n\n**Những sản phẩm mới:**\n- Bánh kem theo yêu cầu\n- Bánh mì đặc biệt\n- Bánh quy handmade\n- Bánh ngọt theo mùa\n\n**Giờ mở cửa mới:**\n- Thứ 2 - Thứ 6: 7:00 - 20:00\n- Thứ 7 - Chủ nhật: 8:00 - 21:00\n\nChúng tôi luôn cố gắng mang đến những sản phẩm chất lượng nhất cho khách hàng. Hãy đến và trải nghiệm!', NULL, 'Tin tức', 'Admin', 1, 0, 0, 3, '2025-11-18 17:16:41', '2025-11-23 17:16:41', NULL),
(4, 'Mẹo bảo quản bánh ngọt tươi lâu', NULL, 'Những mẹo nhỏ giúp bạn bảo quản bánh ngọt được tươi lâu hơn', 'Bảo quản bánh ngọt đúng cách sẽ giúp bánh giữ được độ tươi ngon lâu hơn. Dưới đây là một số mẹo hữu ích:\n\n**1. Bảo quản trong tủ lạnh**\nHầu hết các loại bánh ngọt nên được bảo quản trong tủ lạnh ở nhiệt độ 2-4°C.\n\n**2. Sử dụng hộp kín**\nĐựng bánh trong hộp kín để tránh bị khô và mất mùi vị.\n\n**3. Tránh ánh sáng trực tiếp**\nBánh nên được để ở nơi tối, tránh ánh sáng mặt trời trực tiếp.\n\n**4. Không để chung với thực phẩm có mùi**\nBánh dễ hấp thụ mùi, nên tránh để chung với các thực phẩm có mùi mạnh.\n\n**5. Sử dụng trong vòng 3-5 ngày**\nTốt nhất nên sử dụng bánh trong vòng 3-5 ngày để đảm bảo chất lượng.', NULL, 'Mẹo vặt', 'Admin', 1, 0, 0, 4, '2025-11-16 17:16:41', '2025-11-23 17:16:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oders`
--

CREATE TABLE `oders` (
  `OderId` int NOT NULL,
  `CustomerId` int NOT NULL,
  `Note` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `order_date` datetime NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `number_phone` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `status_name` varchar(50) COLLATE utf8mb3_vietnamese_ci DEFAULT NULL,
  `status_updated_at` datetime DEFAULT NULL,
  `shipping_date` datetime DEFAULT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL,
  `cancelled_reason` text COLLATE utf8mb3_vietnamese_ci,
  `total_price` int NOT NULL DEFAULT '0',
  `CouponCode` varchar(50) COLLATE utf8mb3_vietnamese_ci DEFAULT NULL COMMENT 'Mã giảm giá đã sử dụng',
  `DiscountAmount` decimal(10,2) DEFAULT '0.00' COMMENT 'Số tiền đã giảm',
  `FinalTotal` decimal(10,2) DEFAULT NULL COMMENT 'Tổng tiền cuối cùng sau khi giảm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `oders`
--

INSERT INTO `oders` (`OderId`, `CustomerId`, `Note`, `order_date`, `address`, `number_phone`, `status`, `status_name`, `status_updated_at`, `shipping_date`, `delivered_date`, `cancelled_date`, `cancelled_reason`, `total_price`, `CouponCode`, `DiscountAmount`, `FinalTotal`) VALUES
(115, 8, ' Giao nhanh nha!', '2023-12-14 20:27:53', '18, Hà Huy Tập', '0987452717', 1, NULL, NULL, NULL, NULL, NULL, NULL, 325000, NULL, 0.00, NULL),
(116, 10, ' ', '2023-12-14 20:28:57', 'vinh', '0987452557', 2, NULL, NULL, NULL, NULL, NULL, NULL, 50000, NULL, 0.00, NULL),
(117, 8, ' ', '2023-12-14 20:32:05', 'vinh', '0987452557', 0, NULL, NULL, NULL, NULL, NULL, NULL, 340000, NULL, 0.00, NULL),
(118, 8, ' ', '2023-12-14 20:32:38', 'vinh', '0987452717', 0, NULL, NULL, NULL, NULL, NULL, NULL, 45000, NULL, 0.00, NULL),
(119, 7, ' giao nhanh ', '2023-12-15 13:30:23', 'vinh', '0987452719', 0, NULL, NULL, NULL, NULL, NULL, NULL, 105000, NULL, 0.00, NULL),
(120, 10, ' ', '2023-12-15 13:31:45', '18, Hà Huy Tập', '0987452717', 0, NULL, NULL, NULL, NULL, NULL, NULL, 35000, NULL, 0.00, NULL),
(122, 8, ' ', '2023-12-22 13:27:50', '18, Hà Huy Tập', '0987452719', 1, NULL, NULL, NULL, NULL, NULL, NULL, 655000, NULL, 0.00, NULL),
(123, 10, ' ', '2024-01-30 19:45:12', 'vinh', '0987452718', 0, NULL, NULL, NULL, NULL, NULL, NULL, 60000, NULL, 0.00, NULL),
(124, 7, ' rt', '2025-11-19 21:24:17', 'étrd', 'rtr', 0, NULL, NULL, NULL, NULL, NULL, NULL, 30000, NULL, 0.00, NULL),
(125, 7, ' vcxhvcertrdt', '2025-11-23 16:45:14', 'abc', '21432445435', 0, NULL, NULL, NULL, NULL, NULL, NULL, 245000, NULL, 0.00, NULL),
(126, 7, ' 45654656', '2025-11-23 16:47:05', 'fgytfgy', '546546', 3, 'Đã nhận hàng', '2025-11-23 16:55:36', NULL, '2025-11-23 16:55:36', NULL, NULL, 245000, NULL, 0.00, NULL),
(127, 7, ' fdgfdg', '2025-11-23 18:36:43', 'fgfdg', 'dfgfdg', 0, NULL, NULL, NULL, NULL, NULL, NULL, 245000, NULL, 0.00, NULL),
(128, 7, ' fdgfdg', '2025-11-23 18:38:04', 'fgfdg', 'dfgfdg', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.00, NULL),
(129, 7, ' fdgfdg', '2025-11-23 18:38:08', 'fgfdg', 'dfgfdg', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.00, NULL),
(130, 7, ' try', '2025-11-23 18:44:14', 'reyrety', 'rtytry', 0, NULL, NULL, NULL, NULL, NULL, NULL, 245000, NULL, 0.00, NULL),
(131, 7, ' xcg', '2025-11-23 18:49:30', 'dsfdsf', '34543543', 0, NULL, NULL, NULL, NULL, NULL, NULL, 245000, NULL, 0.00, NULL),
(132, 7, ' xcg', '2025-11-23 18:52:31', 'dsfdsf', '34543543', 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.00, NULL),
(133, 7, ' dsf', '2025-11-23 18:53:03', 'dsfdsfg', 'sdfdsf', 0, NULL, NULL, NULL, NULL, NULL, NULL, 50000, NULL, 0.00, NULL),
(134, 7, ' rtytry', '2025-11-23 18:57:06', '5rtytry', 'trytry', 0, NULL, NULL, NULL, NULL, NULL, NULL, 600000, NULL, 0.00, NULL),
(135, 7, ' rty', '2025-11-23 18:57:36', 'rtytry', 'rtytry', 0, NULL, NULL, NULL, NULL, NULL, NULL, 25000, NULL, 0.00, NULL),
(136, 7, ' dfg', '2025-11-23 18:58:34', 'dfgfdg', 'fdgfdg', -1, NULL, NULL, NULL, NULL, '2025-11-23 18:59:13', 'tôi không cần nữa', 50000, NULL, 0.00, NULL),
(137, 7, ' fghgf', '2025-11-23 19:31:15', 'gfhfgh', 'fghgfh', 0, NULL, NULL, NULL, NULL, NULL, NULL, 365000, '', 0.00, 365000.00),
(138, 7, ' dstgfdg', '2025-11-23 20:14:27', 'xuân hồng, nghi xuân, hà tĩnh', '0848685657', 0, NULL, NULL, NULL, NULL, NULL, NULL, 500000, 'SAVE50K', 50000.00, 450000.00),
(139, 7, ' fdg', '2025-11-23 20:58:14', 'xcbvf', 'fdgfdgdf', 0, NULL, NULL, NULL, NULL, NULL, NULL, 300000, 'ALL20', 60000.00, 240000.00),
(140, 7, ' fdghgfh', '2025-11-23 21:02:42', 'xuân hồng, nghi xuân, hà tĩnh', '0848685657', 0, NULL, NULL, NULL, NULL, NULL, NULL, 150000, 'ALL20', 30000.00, 120000.00);

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `Order_Detail_Id` int NOT NULL,
  `Status` int NOT NULL DEFAULT '1',
  `Price` float NOT NULL DEFAULT '0',
  `Quantity` int NOT NULL DEFAULT '0',
  `ProductId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `orderdetails`
--

INSERT INTO `orderdetails` (`Order_Detail_Id`, `Status`, `Price`, `Quantity`, `ProductId`) VALUES
(115, 1, 150000, 1, 25),
(115, 1, 45000, 1, 15),
(115, 1, 130000, 1, 17),
(116, 1, 50000, 1, 20),
(117, 1, 20000, 1, 32),
(117, 1, 20000, 1, 16),
(117, 1, 300000, 1, 23),
(118, 1, 45000, 1, 27),
(119, 1, 35000, 3, 33),
(120, 1, 35000, 1, 33),
(121, 1, 150000, 1, 25),
(122, 1, 150000, 1, 25),
(122, 1, 45000, 1, 27),
(122, 1, 300000, 1, 23),
(122, 1, 20000, 1, 32),
(122, 1, 50000, 1, 20),
(122, 1, 45000, 2, 22),
(123, 1, 30000, 2, 19),
(124, 1, 30000, 1, 19),
(125, 1, 150000, 1, 25),
(125, 1, 50000, 1, 20),
(125, 1, 45000, 1, 15),
(126, 1, 150000, 1, 25),
(126, 1, 50000, 1, 20),
(126, 1, 45000, 1, 15),
(127, 1, 150000, 1, 25),
(127, 1, 50000, 1, 20),
(127, 1, 45000, 1, 15),
(130, 1, 45000, 1, 15),
(130, 1, 200000, 1, 14),
(131, 1, 45000, 1, 15),
(131, 1, 200000, 1, 14),
(133, 1, 50000, 1, 20),
(134, 1, 300000, 2, 23),
(135, 1, 25000, 1, 18),
(136, 1, 50000, 1, 31),
(137, 1, 130000, 1, 17),
(137, 1, 35000, 1, 33),
(137, 1, 200000, 1, 14),
(138, 1, 200000, 1, 14),
(138, 1, 300000, 1, 23),
(139, 1, 300000, 1, 23),
(140, 1, 150000, 1, 25);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `HistoryId` int NOT NULL,
  `OderId` int NOT NULL,
  `OldStatus` int NOT NULL,
  `NewStatus` int NOT NULL,
  `StatusName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ChangedBy` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin hoặc System',
  `Note` text COLLATE utf8mb4_unicode_ci,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`HistoryId`, `OderId`, `OldStatus`, `NewStatus`, `StatusName`, `ChangedBy`, `Note`, `CreatedAt`) VALUES
(1, 126, 0, 0, 'Chờ xử lý', 'Admin', 'Cập nhật bởi admin', '2025-11-23 16:48:14'),
(2, 126, 0, 0, 'Chờ xử lý', 'Admin', 'Cập nhật bởi admin', '2025-11-23 16:48:17'),
(3, 126, 0, 1, 'Đang chuẩn bị hàng', 'Admin', 'Cập nhật bởi admin', '2025-11-23 16:48:23'),
(4, 126, 1, 3, 'Đã nhận hàng', 'Admin', 'Cập nhật bởi admin', '2025-11-23 16:55:36'),
(5, 136, 0, -1, 'Đã hủy', 'Customer', 'tôi không cần nữa', '2025-11-23 18:59:13');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductId` int NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Quantity` int NOT NULL,
  `Description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `BuyPrice` float NOT NULL,
  `SellPrice` float NOT NULL,
  `Status` tinyint(1) NOT NULL DEFAULT '1',
  `CountView` int NOT NULL DEFAULT '0',
  `AverageRating` decimal(3,2) DEFAULT NULL,
  `TotalReviews` int NOT NULL DEFAULT '0',
  `CategoriId` int NOT NULL,
  `BrandId` int NOT NULL,
  `is_accept` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductId`, `Name`, `Image`, `Quantity`, `Description`, `BuyPrice`, `SellPrice`, `Status`, `CountView`, `AverageRating`, `TotalReviews`, `CategoriId`, `BrandId`, `is_accept`) VALUES
(6, 'Bánh socola sữa', 'f57d9c2d03.product-2.jpg', 100, ' Bánh socola sữa là một loại bánh ngọt được làm từ bột mì, đường, trứng, bơ, sữa và socola sữa. Bánh có màu nâu sẫm đặc trưng của socola, vị ngọt ngào của đường và bơ, và vị béo ngậy của sữa. Bánh socola sữa có thể được ăn kèm với trà, cà phê hoặc sữa. ', 30000, 60000, 1, 31, NULL, 0, 2, 88, 0),
(11, 'banhs kem ngon', '543148f7b1.product-1.jpg', 100, 'done', 100, 121, 1, 22, NULL, 0, 3, 88, 0),
(14, 'Bánh kem trà xanh', 'a1f7728fc5.tra.jpg', 1500, ' Bánh kem trà xanh là một loại bánh kem được làm từ cốt bánh trà xanh và kem trà xanh. Bánh có màu xanh nhạt, vị ngọt thanh của trà xanh, béo ngậy của kem tươi.', 100000, 200000, 1, 39, NULL, 0, 3, 96, 1),
(15, 'Bánh mì kem bơ', '3a7179a973.b2.jpg', 1000, 'Bánh mì kem bơ là một loại bánh mì được làm từ bột mì, bơ, đường, trứng, sữa và men nở. Bánh có phần vỏ ngoài giòn rụm, phần ruột mềm xốp, và nhân kem bơ béo ngậy, thơm ngon.', 20000, 45000, 1, 29, NULL, 0, 4, 97, 1),
(16, 'Bánh mì tươi', 'd59be13bec.08514a288e.1.png', 1000, 'Bánh mì tươi là một loại bánh mì được làm từ bột mì, nước, men, đường và muối. Bánh có đặc điểm là mềm, tơi, xốp, có hương vị khá giống bánh bông lan nhưng chưa đạt đến độ mềm như bánh bông lan.  Bánh mì tươi có màu vàng nhạt, vỏ bánh mỏng, ruột bánh mềm mịn, có độ đàn hồi cao. Khi ăn, bánh có vị ngọt nhẹ, thơm mùi sữa và men.', 10000, 20000, 1, 10, NULL, 0, 4, 96, 1),
(17, 'Bánh mousse vani', '83d69d140b.vani.jpg', 1500, ' Bánh mousse vani là một loại bánh ngọt được làm từ cốt bánh bông lan và mousse vani. Bánh có màu trắng kem, vị ngọt ngào, béo ngậy của vani.', 70000, 130000, 1, 27, NULL, 0, 3, 88, 1),
(18, 'Bánh mì trứng', '910bc4b266.08514a288e.1.png', 1000, 'Bánh mì trứng là một loại bánh mì được làm từ bột mì, trứng gà, đường, sữa tươi và các loại gia vị khác. Bánh có hương vị thơm ngon, béo ngậy của trứng gà, hòa quyện với vị ngọt của đường và sữa tươi. Bánh thường được ăn sáng hoặc làm món ăn nhẹ.', 10000, 25000, 1, 17, NULL, 0, 4, 96, 1),
(19, 'Bánh socola dâu', '1679847831_product-6.jpg', 1000, ' Bánh socola dâu là một loại bánh kem được làm từ cốt bánh socola và kem tươi, có thêm các loại dâu tây tươi. Bánh có vị ngọt ngào, béo ngậy của socola, chua nhẹ của dâu tây, và thơm ngon của cốt bánh bông lan.', 15000, 30000, 1, 5, NULL, 0, 2, 88, 1),
(20, 'Bánh dâu', '7bc8f94747.product-9.jpg', 1000, ' Bánh dâu là một loại bánh ngọt được làm từ cốt bánh bông lan và kem tươi, có thêm các loại dâu tây tươi. Bánh có vị ngọt ngào của dâu tây, béo ngậy của kem tươi, và thơm ngon của cốt bánh bông lan. ', 25000, 50000, 1, 23, NULL, 0, 2, 88, 1),
(21, 'Bánh kem xoài', '98ffedd1b0.5d6dced255.cat.png', 1000, ' Bánh kem xoài là một loại bánh kem được làm từ cốt bánh mềm mịn, kem tươi béo ngậy và xoài tươi thơm ngon. Bánh có màu vàng tươi bắt mắt, hương vị ngọt ngào, thanh mát, là món tráng miệng được nhiều người yêu thích.', 100000, 150000, 1, 0, NULL, 0, 3, 88, 1),
(22, 'Bánh socola hạt', '1679848089_product-10.jpg', 1000, 'Bánh socola hạt là một loại bánh ngọt được làm từ cốt bánh socola và kem socola, có thêm các loại hạt như hạt điều, hạt hạnh nhân, hạt óc chó,... Bánh có vị ngọt ngào, béo ngậy của socola, và vị thơm bùi của các loại hạt. ', 20000, 45000, 1, 6, NULL, 0, 2, 88, 1),
(23, 'Bánh socola trái tim', '5bd398cbc8.tim.jpg', 100, 'h socola trái tim là một loại bánh kem được làm từ cốt bánh socola và kem socola. Bánh có hình dạng trái tim, màu sắc nâu đen đặc trưng của socola, và vị ngọt ngào, béo ngậy của socola.', 150000, 300000, 1, 16, NULL, 0, 3, 88, 1),
(24, 'Bánh kem dâu ', '0ec11a82b9.1678766983_product-1.jpg', 100, ' Bánh kem dâu là một loại bánh kem được làm từ cốt bánh bông lan và kem tươi, có thêm các loại dâu tây tươi. Bánh có vị ngọt ngào của dâu tây, béo ngậy của kem tươi, và thơm ngon của cốt bánh bông lan. ', 120000, 250000, 1, 7, NULL, 0, 3, 88, 1),
(25, 'Bánh kem đào', '832a23d67a.dao.jpg', 1000, 'Bánh kem đào là một loại bánh kem được làm với cốt bánh mềm, ngọt ngào và kem tươi béo ngậy. Đào là một loại trái cây có vị ngọt thanh, chua nhẹ, rất phù hợp để kết hợp với bánh kem.', 100000, 150000, 1, 61, 5.00, 1, 3, 88, 1),
(27, 'Bánh ngọt matcha', '1700913738_product-5.jpg', 200, 'Bánh ngọt matcha là một loại bánh ngọt được làm từ bột mì, trứng, đường, sữa, bơ và bột trà xanh. Bánh có hương vị thơm ngon, béo ngậy của bột trà xanh, kết hợp với vị ngọt thanh của đường và sữa, tạo nên một món ăn hấp dẫn.', 20000, 45000, 1, 4, NULL, 0, 2, 101, 1),
(28, 'Bánh mì ngũ cốc', '1701088560_bmngucoc.png', 1500, 'Bánh mì ngũ cốc được làm từ nhiều loại ngũ cốc khác nhau, chẳng hạn như lúa mì, lúa mạch, lúa mạch đen và yến mạch. Nó có kết cấu dai và có hương vị đậm đà.', 20000, 45000, 1, 8, NULL, 0, 4, 88, 1),
(29, 'Bánh mì Pita', '1701088940_bita.png', 150, 'Bánh mì Pita  có hàm lượng vitamin cao, giàu chất dinh dưỡng và chất xơ mà lại chứa rất ít calo nên thường được sử dụng làm thực phẩm ăn kiêng, kiểm soát cân nặng. Bánh mì Pita  cũng có chỉ số GI thấp nên không làm tăng chỉ số đường huyết, có lợi cho sức khỏe người sử dụng.', 10000, 20000, 1, 5, NULL, 0, 4, 96, 1),
(30, 'Bánh mì sandwich', '1701089287_sw.png', 1000, 'Bánh mì sandwich là một loại bánh mì kẹp nhân được làm từ hai lát bánh mì kẹp nhân bên trong. Nó là một món ăn phổ biến trên toàn thế giới và có thể được làm với nhiều loại nhân khác nhau, bao gồm thịt, phô mai, rau, trứng và nhiều loại khác.', 5000, 15000, 1, 2, NULL, 0, 4, 97, 1),
(31, 'Bánh mousse dâu tây ', '96812e82fe.product-big-4.jpg', 200, 'Bánh mousse dâu tây là loại bánh có lớp vỏ giòn, bên trong có nhân phô mai chua ngọt, bên trên có lớp phủ dâu tây. Bánh thường được trang trí thêm kem tươi hoặc dâu tây tươi.', 25000, 50000, 1, 15, NULL, 0, 2, 101, 1),
(32, 'Bánh quy dừa', '1701090793_dua.jpg', 1000, 'Bánh quy dừa là một loại bánh quy phổ biến ở Việt Nam, có vị béo bùi của dừa, thơm ngon và dễ ăn. Bánh quy dừa có thể được làm với nhiều hình dạng khác nhau, như hình tròn, hình vuông, hình chữ nhật,...', 10000, 20000, 1, 5, NULL, 0, 15, 96, 1),
(33, 'Bánh quy bơ', '1701090988_bo.jpg', 4000, 'Bánh quy bơ là một loại bánh quy phổ biến trên thế giới, có vị ngọt ngào, béo ngậy của bơ, thơm ngon và dễ ăn. Bánh quy bơ có thể được làm với nhiều hình dạng khác nhau, như hình tròn, hình vuông, hình chữ nhật,...', 25000, 35000, 1, 10, NULL, 0, 15, 101, 1);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `PromotionId` int NOT NULL,
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu đề khuyến mãi',
  `Slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL friendly',
  `Description` text COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả ngắn',
  `Content` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Nội dung chi tiết',
  `Image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ảnh đại diện',
  `DiscountType` enum('percentage','fixed','buy_x_get_y') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage' COMMENT 'Loại giảm giá',
  `DiscountValue` decimal(10,2) DEFAULT NULL COMMENT 'Giá trị giảm giá',
  `MinPurchase` decimal(10,2) DEFAULT '0.00' COMMENT 'Giá trị đơn hàng tối thiểu',
  `MaxDiscount` decimal(10,2) DEFAULT NULL COMMENT 'Giảm giá tối đa',
  `Code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giảm giá (nếu có)',
  `UsageLimit` int DEFAULT NULL COMMENT 'Số lần sử dụng tối đa',
  `UsedCount` int NOT NULL DEFAULT '0' COMMENT 'Số lần đã sử dụng',
  `UserLimit` int DEFAULT NULL COMMENT 'Số lần mỗi user được sử dụng',
  `StartDate` datetime NOT NULL COMMENT 'Ngày bắt đầu',
  `EndDate` datetime NOT NULL COMMENT 'Ngày kết thúc',
  `IsActive` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Đang hoạt động',
  `IsFeatured` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Nổi bật',
  `ViewCount` int NOT NULL DEFAULT '0' COMMENT 'Lượt xem',
  `SortOrder` int NOT NULL DEFAULT '0' COMMENT 'Thứ tự hiển thị',
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`PromotionId`, `Title`, `Slug`, `Description`, `Content`, `Image`, `DiscountType`, `DiscountValue`, `MinPurchase`, `MaxDiscount`, `Code`, `UsageLimit`, `UsedCount`, `UserLimit`, `StartDate`, `EndDate`, `IsActive`, `IsFeatured`, `ViewCount`, `SortOrder`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Giảm giá 20% cho tất cả sản phẩm', NULL, 'Chương trình khuyến mãi đặc biệt giảm 20% cho tất cả sản phẩm trong cửa hàng', 'Chương trình khuyến mãi lớn nhất trong năm! Giảm ngay 20% cho tất cả sản phẩm bánh ngọt, bánh kem, bánh mì... Áp dụng cho mọi đơn hàng từ 100.000 VNĐ trở lên.\n\nThời gian áp dụng: Từ ngày 01/12/2024 đến 31/12/2024\n\nKhông áp dụng kèm các chương trình khuyến mãi khác.', NULL, 'percentage', 20.00, 100000.00, NULL, 'ALL20', NULL, 2, NULL, '2025-11-23 17:16:41', '2025-12-23 17:16:41', 1, 1, 7, 1, '2025-11-23 17:16:41', '2025-11-23 21:02:42'),
(2, 'Giảm 50.000 VNĐ cho đơn hàng từ 300.000 VNĐ', NULL, 'Mua đơn hàng từ 300.000 VNĐ được giảm ngay 50.000 VNĐ', 'Chương trình khuyến mãi đặc biệt dành cho khách hàng mua số lượng lớn. Khi mua đơn hàng từ 300.000 VNĐ trở lên, bạn sẽ được giảm ngay 50.000 VNĐ.\n\nÁp dụng cho tất cả sản phẩm trong cửa hàng.\n\nThời gian: Từ ngày 15/12/2024 đến 15/01/2025', NULL, 'fixed', 50000.00, 300000.00, NULL, 'DEAL50', NULL, 0, NULL, '2025-11-28 17:16:41', '2026-01-07 17:16:41', 1, 1, 0, 2, '2025-11-23 17:16:41', '2025-11-23 19:30:42'),
(4, 'Chào mừng khách hàng mới', NULL, 'Giảm 10% cho đơn hàng đầu tiên, tối đa 50,000 VND', NULL, NULL, 'percentage', 10.00, 100000.00, 50000.00, 'WELCOME10', 100, 0, 1, '2025-11-23 19:16:53', '2026-11-23 19:16:53', 1, 0, 0, 0, '2025-11-23 19:16:53', NULL),
(5, 'Tiết kiệm 50k', NULL, 'Giảm 50,000 VND cho đơn hàng từ 300,000 VND', NULL, NULL, 'fixed', 50000.00, 300000.00, NULL, 'SAVE50K', 200, 1, NULL, '2025-11-23 19:16:53', '2026-05-23 19:16:53', 1, 0, 0, 0, '2025-11-23 19:16:53', '2025-11-23 20:14:27'),
(6, 'Khuyến mãi mùa hè', NULL, 'Giảm 20% tối đa 100,000 VND cho đơn hàng từ 200,000 VND', NULL, NULL, 'percentage', 20.00, 200000.00, 100000.00, 'SUMMER20', 500, 0, 2, '2025-11-23 19:16:53', '2026-02-23 19:16:53', 1, 0, 0, 0, '2025-11-23 19:16:53', NULL),
(7, 'Chào năm mới', NULL, 'Giảm 15% cho đơn hàng từ 150,000 VND, không giới hạn', NULL, NULL, 'percentage', 15.00, 150000.00, NULL, 'NEWYEAR15', NULL, 0, NULL, '2025-11-23 19:16:53', '2026-01-23 19:16:53', 1, 0, 0, 0, '2025-11-23 19:16:53', NULL),
(8, 'VIP 100k', NULL, 'Giảm 100,000 VND cho đơn hàng từ 500,000 VND', NULL, NULL, 'fixed', 100000.00, 500000.00, NULL, 'VIP100K', 50, 0, 1, '2025-11-23 19:16:53', '2026-11-23 19:16:53', 1, 0, 0, 0, '2025-11-23 19:16:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promotion_products`
--

CREATE TABLE `promotion_products` (
  `PromotionProductId` int NOT NULL,
  `PromotionId` int NOT NULL,
  `ProductId` int NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewId` int NOT NULL,
  `ProductId` int NOT NULL,
  `CustomerId` int NOT NULL,
  `OrderId` int DEFAULT NULL COMMENT 'ID đơn hàng (để xác minh đã mua)',
  `Rating` tinyint(1) NOT NULL DEFAULT '5' COMMENT 'Điểm đánh giá từ 1-5',
  `Title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tiêu đề đánh giá',
  `Comment` text COLLATE utf8mb4_unicode_ci COMMENT 'Nội dung đánh giá',
  `Images` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array của các ảnh đính kèm',
  `IsVerified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã xác minh mua hàng',
  `IsApproved` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Đã được admin duyệt',
  `HelpfulCount` int NOT NULL DEFAULT '0' COMMENT 'Số lượt hữu ích',
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewId`, `ProductId`, `CustomerId`, `OrderId`, `Rating`, `Title`, `Comment`, `Images`, `IsVerified`, `IsApproved`, `HelpfulCount`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 25, 7, 126, 5, 'Sản phẩm chất lượng tuyệt vời', 'Sản phẩm tốt vượt mức kỳ vọng', '[\"reviews/review_1_1763892216_0.jpeg\"]', 1, 1, 0, '2025-11-23 17:03:36', '2025-11-23 17:03:36');

--
-- Triggers `reviews`
--
DELIMITER $$
CREATE TRIGGER `update_product_rating_after_delete` AFTER DELETE ON `reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET AverageRating = (
        SELECT COALESCE(AVG(Rating), 0) 
        FROM reviews 
        WHERE ProductId = OLD.ProductId AND IsApproved = 1
    ),
    TotalReviews = (
        SELECT COUNT(*) 
        FROM reviews 
        WHERE ProductId = OLD.ProductId AND IsApproved = 1
    )
    WHERE ProductId = OLD.ProductId;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_product_rating_after_insert` AFTER INSERT ON `reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET AverageRating = (
        SELECT AVG(Rating) 
        FROM reviews 
        WHERE ProductId = NEW.ProductId AND IsApproved = 1
    ),
    TotalReviews = (
        SELECT COUNT(*) 
        FROM reviews 
        WHERE ProductId = NEW.ProductId AND IsApproved = 1
    )
    WHERE ProductId = NEW.ProductId;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_product_rating_after_update` AFTER UPDATE ON `reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET AverageRating = (
        SELECT AVG(Rating) 
        FROM reviews 
        WHERE ProductId = NEW.ProductId AND IsApproved = 1
    ),
    TotalReviews = (
        SELECT COUNT(*) 
        FROM reviews 
        WHERE ProductId = NEW.ProductId AND IsApproved = 1
    )
    WHERE ProductId = NEW.ProductId;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `review_helpful`
--

CREATE TABLE `review_helpful` (
  `HelpfulId` int NOT NULL,
  `ReviewId` int NOT NULL,
  `CustomerId` int NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_images`
--

CREATE TABLE `review_images` (
  `ImageId` int NOT NULL,
  `ReviewId` int NOT NULL,
  `ImagePath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_images`
--

INSERT INTO `review_images` (`ImageId`, `ReviewId`, `ImagePath`, `CreatedAt`) VALUES
(1, 1, 'reviews/review_1_1763892216_0.jpeg', '2025-11-23 17:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `RoleId` int NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `Description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_vietnamese_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`RoleId`, `Name`, `Description`) VALUES
(1, 'Admin', 'Control everything'),
(2, 'SubAdmin', 'Control less than Admin\r\n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `Role` (`Role`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`BannerId`),
  ADD KEY `idx_active` (`IsActive`),
  ADD KEY `idx_sort` (`SortOrder`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`BrandId`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryId`),
  ADD KEY `idx_status_sort` (`status`,`SortOrder`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`ContactId`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`CustomerId`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`NewsId`),
  ADD KEY `idx_published` (`IsPublished`),
  ADD KEY `idx_featured` (`IsFeatured`),
  ADD KEY `idx_category` (`Category`),
  ADD KEY `idx_slug` (`Slug`),
  ADD KEY `idx_published_at` (`PublishedAt`);

--
-- Indexes for table `oders`
--
ALTER TABLE `oders`
  ADD PRIMARY KEY (`OderId`),
  ADD KEY `FK_Customer_Id` (`CustomerId`),
  ADD KEY `idx_coupon_code` (`CouponCode`);

--
-- Indexes for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD KEY `ProductId` (`ProductId`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`HistoryId`),
  ADD KEY `idx_order_id` (`OderId`),
  ADD KEY `idx_created_at` (`CreatedAt`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductId`),
  ADD KEY `FK_Brand_Id` (`BrandId`),
  ADD KEY `FK_Categori_Id` (`CategoriId`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`PromotionId`),
  ADD KEY `idx_active` (`IsActive`),
  ADD KEY `idx_featured` (`IsFeatured`),
  ADD KEY `idx_dates` (`StartDate`,`EndDate`),
  ADD KEY `idx_slug` (`Slug`),
  ADD KEY `idx_code` (`Code`);

--
-- Indexes for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD PRIMARY KEY (`PromotionProductId`),
  ADD UNIQUE KEY `unique_promotion_product` (`PromotionId`,`ProductId`),
  ADD KEY `idx_promotion_id` (`PromotionId`),
  ADD KEY `idx_product_id` (`ProductId`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewId`),
  ADD KEY `idx_product_id` (`ProductId`),
  ADD KEY `idx_customer_id` (`CustomerId`),
  ADD KEY `idx_order_id` (`OrderId`),
  ADD KEY `idx_approved` (`IsApproved`),
  ADD KEY `idx_rating` (`Rating`),
  ADD KEY `idx_created_at` (`CreatedAt`);

--
-- Indexes for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD PRIMARY KEY (`HelpfulId`),
  ADD UNIQUE KEY `unique_review_customer` (`ReviewId`,`CustomerId`),
  ADD KEY `idx_review_id` (`ReviewId`),
  ADD KEY `idx_customer_id` (`CustomerId`);

--
-- Indexes for table `review_images`
--
ALTER TABLE `review_images`
  ADD PRIMARY KEY (`ImageId`),
  ADD KEY `idx_review_id` (`ReviewId`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`RoleId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `BannerId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `BrandId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `CategoryId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `ContactId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `CustomerId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `NewsId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `oders`
--
ALTER TABLE `oders`
  MODIFY `OderId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `HistoryId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `PromotionId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `promotion_products`
--
ALTER TABLE `promotion_products`
  MODIFY `PromotionProductId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_helpful`
--
ALTER TABLE `review_helpful`
  MODIFY `HelpfulId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_images`
--
ALTER TABLE `review_images`
  MODIFY `ImageId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`Role`) REFERENCES `roles` (`RoleId`);

--
-- Constraints for table `oders`
--
ALTER TABLE `oders`
  ADD CONSTRAINT `FK_Customer_Id` FOREIGN KEY (`CustomerId`) REFERENCES `customers` (`CustomerId`);

--
-- Constraints for table `orderdetails`
--
ALTER TABLE `orderdetails`
  ADD CONSTRAINT `FK_Product_Id_01` FOREIGN KEY (`ProductId`) REFERENCES `products` (`ProductId`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_Brand_Id` FOREIGN KEY (`BrandId`) REFERENCES `brands` (`BrandId`),
  ADD CONSTRAINT `FK_Categori_Id` FOREIGN KEY (`CategoriId`) REFERENCES `category` (`CategoryId`);

--
-- Constraints for table `promotion_products`
--
ALTER TABLE `promotion_products`
  ADD CONSTRAINT `promotion_products_ibfk_1` FOREIGN KEY (`PromotionId`) REFERENCES `promotions` (`PromotionId`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotion_products_ibfk_2` FOREIGN KEY (`ProductId`) REFERENCES `products` (`ProductId`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`ProductId`) REFERENCES `products` (`ProductId`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`CustomerId`) REFERENCES `customers` (`CustomerId`) ON DELETE CASCADE;

--
-- Constraints for table `review_helpful`
--
ALTER TABLE `review_helpful`
  ADD CONSTRAINT `review_helpful_ibfk_1` FOREIGN KEY (`ReviewId`) REFERENCES `reviews` (`ReviewId`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_helpful_ibfk_2` FOREIGN KEY (`CustomerId`) REFERENCES `customers` (`CustomerId`) ON DELETE CASCADE;

--
-- Constraints for table `review_images`
--
ALTER TABLE `review_images`
  ADD CONSTRAINT `review_images_ibfk_1` FOREIGN KEY (`ReviewId`) REFERENCES `reviews` (`ReviewId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
