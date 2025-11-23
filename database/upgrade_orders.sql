-- SQL để cải thiện bảng orders cho tính năng tình trạng đơn hàng
-- Chạy file này trong phpMyAdmin hoặc MySQL
-- Thêm các cột mới cho bảng oders
ALTER TABLE `oders`
ADD COLUMN `status_name` VARCHAR(50) NULL DEFAULT NULL
AFTER `status`,
  ADD COLUMN `status_updated_at` DATETIME NULL DEFAULT NULL
AFTER `status_name`,
  ADD COLUMN `shipping_date` DATETIME NULL DEFAULT NULL
AFTER `status_updated_at`,
  ADD COLUMN `delivered_date` DATETIME NULL DEFAULT NULL
AFTER `shipping_date`,
  ADD COLUMN `cancelled_date` DATETIME NULL DEFAULT NULL
AFTER `delivered_date`,
  ADD COLUMN `cancelled_reason` TEXT NULL DEFAULT NULL
AFTER `cancelled_date`;
-- Tạo bảng order_status_history để lưu lịch sử thay đổi trạng thái
CREATE TABLE IF NOT EXISTS `order_status_history` (
  `HistoryId` INT(11) NOT NULL AUTO_INCREMENT,
  `OderId` INT(11) NOT NULL,
  `OldStatus` INT(1) NOT NULL,
  `NewStatus` INT(1) NOT NULL,
  `StatusName` VARCHAR(50) NOT NULL,
  `ChangedBy` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Admin hoặc System',
  `Note` TEXT NULL DEFAULT NULL,
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`HistoryId`),
  INDEX `idx_order_id` (`OderId`),
  INDEX `idx_created_at` (`CreatedAt`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Cập nhật status_name cho các đơn hàng hiện có
UPDATE `oders`
SET `status_name` = 'Chờ xử lý'
WHERE `status` = 0;
UPDATE `oders`
SET `status_name` = 'Đang chuẩn bị hàng'
WHERE `status` = 1;
UPDATE `oders`
SET `status_name` = 'Đã giao hàng'
WHERE `status` = 2;
-- Tạo index để tối ưu truy vấn
ALTER TABLE `oders`
ADD INDEX `idx_customer_status` (`CustomerId`, `status`);
ALTER TABLE `oders`
ADD INDEX `idx_order_date` (`order_date`);