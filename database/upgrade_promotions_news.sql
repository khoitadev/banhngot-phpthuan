-- SQL để tạo bảng promotions và news
-- Chạy file này trong phpMyAdmin hoặc MySQL
-- Tạo bảng promotions (Khuyến mãi)
CREATE TABLE IF NOT EXISTS `promotions` (
  `PromotionId` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề khuyến mãi',
  `Slug` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL friendly',
  `Description` TEXT NULL DEFAULT NULL COMMENT 'Mô tả ngắn',
  `Content` LONGTEXT NULL DEFAULT NULL COMMENT 'Nội dung chi tiết',
  `Image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ảnh đại diện',
  `DiscountType` ENUM('percentage', 'fixed', 'buy_x_get_y') NOT NULL DEFAULT 'percentage' COMMENT 'Loại giảm giá',
  `DiscountValue` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT 'Giá trị giảm giá',
  `MinPurchase` DECIMAL(10, 2) NULL DEFAULT 0 COMMENT 'Giá trị đơn hàng tối thiểu',
  `MaxDiscount` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT 'Giảm giá tối đa',
  `StartDate` DATETIME NOT NULL COMMENT 'Ngày bắt đầu',
  `EndDate` DATETIME NOT NULL COMMENT 'Ngày kết thúc',
  `IsActive` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Đang hoạt động',
  `IsFeatured` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Nổi bật',
  `ViewCount` INT(11) NOT NULL DEFAULT 0 COMMENT 'Lượt xem',
  `SortOrder` INT(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PromotionId`),
  INDEX `idx_active` (`IsActive`),
  INDEX `idx_featured` (`IsFeatured`),
  INDEX `idx_dates` (`StartDate`, `EndDate`),
  INDEX `idx_slug` (`Slug`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tạo bảng news (Tin tức)
CREATE TABLE IF NOT EXISTS `news` (
  `NewsId` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề tin tức',
  `Slug` VARCHAR(255) NULL DEFAULT NULL COMMENT 'URL friendly',
  `Summary` TEXT NULL DEFAULT NULL COMMENT 'Tóm tắt',
  `Content` LONGTEXT NULL DEFAULT NULL COMMENT 'Nội dung',
  `Image` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Ảnh đại diện',
  `Category` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Danh mục tin tức',
  `Author` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Tác giả',
  `IsPublished` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Đã xuất bản',
  `IsFeatured` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Nổi bật',
  `ViewCount` INT(11) NOT NULL DEFAULT 0 COMMENT 'Lượt xem',
  `SortOrder` INT(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `PublishedAt` DATETIME NULL DEFAULT NULL COMMENT 'Ngày xuất bản',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NewsId`),
  INDEX `idx_published` (`IsPublished`),
  INDEX `idx_featured` (`IsFeatured`),
  INDEX `idx_category` (`Category`),
  INDEX `idx_slug` (`Slug`),
  INDEX `idx_published_at` (`PublishedAt`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tạo bảng promotion_products để liên kết khuyến mãi với sản phẩm
CREATE TABLE IF NOT EXISTS `promotion_products` (
  `PromotionProductId` INT(11) NOT NULL AUTO_INCREMENT,
  `PromotionId` INT(11) NOT NULL,
  `ProductId` INT(11) NOT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PromotionProductId`),
  UNIQUE KEY `unique_promotion_product` (`PromotionId`, `ProductId`),
  INDEX `idx_promotion_id` (`PromotionId`),
  INDEX `idx_product_id` (`ProductId`),
  FOREIGN KEY (`PromotionId`) REFERENCES `promotions`(`PromotionId`) ON DELETE CASCADE,
  FOREIGN KEY (`ProductId`) REFERENCES `products`(`ProductId`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;