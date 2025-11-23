-- SQL để tạo bảng reviews cho tính năng đánh giá sản phẩm
-- Chạy file này trong phpMyAdmin hoặc MySQL
-- Tạo bảng reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `ReviewId` INT(11) NOT NULL AUTO_INCREMENT,
  `ProductId` INT(11) NOT NULL,
  `CustomerId` INT(11) NOT NULL,
  `OrderId` INT(11) NULL DEFAULT NULL COMMENT 'ID đơn hàng (để xác minh đã mua)',
  `Rating` TINYINT(1) NOT NULL DEFAULT 5 COMMENT 'Điểm đánh giá từ 1-5',
  `Title` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Tiêu đề đánh giá',
  `Comment` TEXT NULL DEFAULT NULL COMMENT 'Nội dung đánh giá',
  `Images` TEXT NULL DEFAULT NULL COMMENT 'JSON array của các ảnh đính kèm',
  `IsVerified` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Đã xác minh mua hàng',
  `IsApproved` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Đã được admin duyệt',
  `HelpfulCount` INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lượt hữu ích',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReviewId`),
  INDEX `idx_product_id` (`ProductId`),
  INDEX `idx_customer_id` (`CustomerId`),
  INDEX `idx_order_id` (`OrderId`),
  INDEX `idx_approved` (`IsApproved`),
  INDEX `idx_rating` (`Rating`),
  INDEX `idx_created_at` (`CreatedAt`),
  FOREIGN KEY (`ProductId`) REFERENCES `products`(`ProductId`) ON DELETE CASCADE,
  FOREIGN KEY (`CustomerId`) REFERENCES `customers`(`CustomerId`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tạo bảng review_images để lưu ảnh đánh giá
CREATE TABLE IF NOT EXISTS `review_images` (
  `ImageId` INT(11) NOT NULL AUTO_INCREMENT,
  `ReviewId` INT(11) NOT NULL,
  `ImagePath` VARCHAR(255) NOT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ImageId`),
  INDEX `idx_review_id` (`ReviewId`),
  FOREIGN KEY (`ReviewId`) REFERENCES `reviews`(`ReviewId`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tạo bảng review_helpful để lưu lượt "Hữu ích"
CREATE TABLE IF NOT EXISTS `review_helpful` (
  `HelpfulId` INT(11) NOT NULL AUTO_INCREMENT,
  `ReviewId` INT(11) NOT NULL,
  `CustomerId` INT(11) NOT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HelpfulId`),
  UNIQUE KEY `unique_review_customer` (`ReviewId`, `CustomerId`),
  INDEX `idx_review_id` (`ReviewId`),
  INDEX `idx_customer_id` (`CustomerId`),
  FOREIGN KEY (`ReviewId`) REFERENCES `reviews`(`ReviewId`) ON DELETE CASCADE,
  FOREIGN KEY (`CustomerId`) REFERENCES `customers`(`CustomerId`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Thêm cột AverageRating vào bảng products để lưu điểm trung bình
ALTER TABLE `products`
ADD COLUMN `AverageRating` DECIMAL(3, 2) NULL DEFAULT NULL
AFTER `CountView`,
  ADD COLUMN `TotalReviews` INT(11) NOT NULL DEFAULT 0
AFTER `AverageRating`;
-- Tạo trigger để tự động cập nhật AverageRating khi có review mới
DELIMITER $$ CREATE TRIGGER `update_product_rating_after_insert`
AFTER
INSERT ON `reviews` FOR EACH ROW BEGIN
UPDATE products
SET AverageRating = (
    SELECT AVG(Rating)
    FROM reviews
    WHERE ProductId = NEW.ProductId
      AND IsApproved = 1
  ),
  TotalReviews = (
    SELECT COUNT(*)
    FROM reviews
    WHERE ProductId = NEW.ProductId
      AND IsApproved = 1
  )
WHERE ProductId = NEW.ProductId;
END $$ CREATE TRIGGER `update_product_rating_after_update`
AFTER
UPDATE ON `reviews` FOR EACH ROW BEGIN
UPDATE products
SET AverageRating = (
    SELECT AVG(Rating)
    FROM reviews
    WHERE ProductId = NEW.ProductId
      AND IsApproved = 1
  ),
  TotalReviews = (
    SELECT COUNT(*)
    FROM reviews
    WHERE ProductId = NEW.ProductId
      AND IsApproved = 1
  )
WHERE ProductId = NEW.ProductId;
END $$ CREATE TRIGGER `update_product_rating_after_delete`
AFTER DELETE ON `reviews` FOR EACH ROW BEGIN
UPDATE products
SET AverageRating = (
    SELECT COALESCE(AVG(Rating), 0)
    FROM reviews
    WHERE ProductId = OLD.ProductId
      AND IsApproved = 1
  ),
  TotalReviews = (
    SELECT COUNT(*)
    FROM reviews
    WHERE ProductId = OLD.ProductId
      AND IsApproved = 1
  )
WHERE ProductId = OLD.ProductId;
END $$ DELIMITER;