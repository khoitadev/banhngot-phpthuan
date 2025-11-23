-- SQL để tạo bảng coupons (mã giảm giá)
-- Chạy file này trong phpMyAdmin hoặc MySQL
-- LƯU Ý: Script này sẽ XÓA và TẠO LẠI bảng coupons
-- Xóa bảng cũ nếu đã tồn tại (CẨN THẬN: Sẽ mất dữ liệu cũ)
DROP TABLE IF EXISTS `coupon_usage`;
DROP TABLE IF EXISTS `coupons`;
-- Tạo bảng coupons
CREATE TABLE `coupons` (
  `CouponId` INT(11) NOT NULL AUTO_INCREMENT,
  `Code` VARCHAR(50) NOT NULL COMMENT 'Mã giảm giá',
  `Name` VARCHAR(255) NOT NULL COMMENT 'Tên mã giảm giá',
  `Description` TEXT NULL DEFAULT NULL COMMENT 'Mô tả',
  `DiscountType` ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage' COMMENT 'Loại giảm giá: phần trăm hoặc số tiền cố định',
  `DiscountValue` DECIMAL(10, 2) NOT NULL COMMENT 'Giá trị giảm giá',
  `MinPurchase` DECIMAL(10, 2) NULL DEFAULT 0 COMMENT 'Giá trị đơn hàng tối thiểu để áp dụng',
  `MaxDiscount` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT 'Giảm giá tối đa (cho loại percentage)',
  `UsageLimit` INT(11) NULL DEFAULT NULL COMMENT 'Số lần sử dụng tối đa (NULL = không giới hạn)',
  `UsedCount` INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lần đã sử dụng',
  `UserLimit` INT(11) NULL DEFAULT NULL COMMENT 'Số lần mỗi user được sử dụng (NULL = không giới hạn)',
  `StartDate` DATETIME NOT NULL COMMENT 'Ngày bắt đầu',
  `EndDate` DATETIME NOT NULL COMMENT 'Ngày kết thúc',
  `IsActive` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Đang hoạt động',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`CouponId`),
  UNIQUE KEY `unique_code` (`Code`),
  INDEX `idx_code` (`Code`),
  INDEX `idx_active` (`IsActive`),
  INDEX `idx_dates` (`StartDate`, `EndDate`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tạo bảng coupon_usage để theo dõi việc sử dụng mã giảm giá
CREATE TABLE `coupon_usage` (
  `UsageId` INT(11) NOT NULL AUTO_INCREMENT,
  `CouponId` INT(11) NOT NULL,
  `OderId` INT(11) NOT NULL,
  `CustomerId` INT(11) NULL DEFAULT NULL COMMENT 'ID khách hàng sử dụng',
  `DiscountAmount` DECIMAL(10, 2) NOT NULL COMMENT 'Số tiền đã giảm',
  `OrderTotal` DECIMAL(10, 2) NOT NULL COMMENT 'Tổng tiền đơn hàng trước khi giảm',
  `FinalTotal` DECIMAL(10, 2) NOT NULL COMMENT 'Tổng tiền sau khi giảm',
  `UsedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UsageId`),
  INDEX `idx_coupon_id` (`CouponId`),
  INDEX `idx_order_id` (`OderId`),
  INDEX `idx_customer_id` (`CustomerId`),
  FOREIGN KEY (`CouponId`) REFERENCES `coupons`(`CouponId`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Thêm cột coupon_code và discount_amount vào bảng oders
-- LƯU Ý: Nếu cột đã tồn tại, sẽ báo lỗi - bạn có thể bỏ qua lỗi đó hoặc comment các dòng này
-- Kiểm tra và thêm cột CouponCode vào oders
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'oders'
      AND COLUMN_NAME = 'CouponCode'
  );
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `oders` ADD COLUMN `CouponCode` VARCHAR(50) NULL DEFAULT NULL COMMENT ''Mã giảm giá đã sử dụng'' AFTER `total_price`',
    'SELECT ''Column CouponCode already exists'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Kiểm tra và thêm cột DiscountAmount vào oders
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'oders'
      AND COLUMN_NAME = 'DiscountAmount'
  );
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `oders` ADD COLUMN `DiscountAmount` DECIMAL(10, 2) NULL DEFAULT 0 COMMENT ''Số tiền đã giảm'' AFTER `CouponCode`',
    'SELECT ''Column DiscountAmount already exists'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Kiểm tra và thêm cột FinalTotal vào oders
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'oders'
      AND COLUMN_NAME = 'FinalTotal'
  );
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `oders` ADD COLUMN `FinalTotal` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT ''Tổng tiền cuối cùng sau khi giảm'' AFTER `DiscountAmount`',
    'SELECT ''Column FinalTotal already exists'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Kiểm tra và tạo index cho CouponCode trong oders
SET @idx_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'oders'
      AND INDEX_NAME = 'idx_coupon_code'
  );
SET @sql = IF(
    @idx_exists = 0,
    'ALTER TABLE `oders` ADD INDEX `idx_coupon_code` (`CouponCode`)',
    'SELECT ''Index idx_coupon_code already exists'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Kiểm tra và thêm cột CouponCode vào promotions
SET @col_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'promotions'
      AND COLUMN_NAME = 'CouponCode'
  );
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `promotions` ADD COLUMN `CouponCode` VARCHAR(50) NULL DEFAULT NULL COMMENT ''Mã giảm giá liên kết'' AFTER `MaxDiscount`',
    'SELECT ''Column CouponCode already exists in promotions'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Kiểm tra và tạo index cho CouponCode trong promotions
SET @idx_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'promotions'
      AND INDEX_NAME = 'idx_promotion_coupon'
  );
SET @sql = IF(
    @idx_exists = 0,
    'ALTER TABLE `promotions` ADD INDEX `idx_promotion_coupon` (`CouponCode`)',
    'SELECT ''Index idx_promotion_coupon already exists'' AS info'
  );
PREPARE stmt
FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
-- Thêm dữ liệu mẫu
INSERT INTO `coupons` (
    `Code`,
    `Name`,
    `Description`,
    `DiscountType`,
    `DiscountValue`,
    `MinPurchase`,
    `MaxDiscount`,
    `UsageLimit`,
    `UserLimit`,
    `StartDate`,
    `EndDate`,
    `IsActive`
  )
VALUES (
    'WELCOME10',
    'Chào mừng khách hàng mới',
    'Giảm 10% cho đơn hàng đầu tiên, tối đa 50,000 VND',
    'percentage',
    10.00,
    100000,
    50000,
    100,
    1,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 1 YEAR),
    1
  ),
  (
    'SAVE50K',
    'Tiết kiệm 50k',
    'Giảm 50,000 VND cho đơn hàng từ 300,000 VND',
    'fixed',
    50000.00,
    300000,
    NULL,
    200,
    NULL,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 6 MONTH),
    1
  ),
  (
    'SUMMER20',
    'Khuyến mãi mùa hè',
    'Giảm 20% tối đa 100,000 VND cho đơn hàng từ 200,000 VND',
    'percentage',
    20.00,
    200000,
    100000,
    500,
    2,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 3 MONTH),
    1
  ),
  (
    'NEWYEAR15',
    'Chào năm mới',
    'Giảm 15% cho đơn hàng từ 150,000 VND, không giới hạn',
    'percentage',
    15.00,
    150000,
    NULL,
    NULL,
    NULL,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 2 MONTH),
    1
  ),
  (
    'VIP100K',
    'VIP 100k',
    'Giảm 100,000 VND cho đơn hàng từ 500,000 VND',
    'fixed',
    100000.00,
    500000,
    NULL,
    50,
    1,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 1 YEAR),
    1
  );