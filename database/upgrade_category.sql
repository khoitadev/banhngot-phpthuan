-- SQL để cải thiện bảng category cho tính năng phân loại sản phẩm
-- Chạy file này trong phpMyAdmin hoặc MySQL
-- Thêm cột Icon cho category (tùy chọn)
ALTER TABLE `category`
ADD COLUMN `Icon` VARCHAR(255) NULL DEFAULT NULL
AFTER `CategoryName`,
  ADD COLUMN `Description` TEXT NULL DEFAULT NULL
AFTER `Icon`,
  ADD COLUMN `SortOrder` INT(11) NOT NULL DEFAULT 0
AFTER `status`;
-- Cập nhật SortOrder cho các category hiện có
UPDATE `category`
SET `SortOrder` = 1
WHERE `CategoryId` = 2;
-- Bánh ngọt
UPDATE `category`
SET `SortOrder` = 2
WHERE `CategoryId` = 3;
-- Bánh kem
UPDATE `category`
SET `SortOrder` = 3
WHERE `CategoryId` = 4;
-- Bánh mì
UPDATE `category`
SET `SortOrder` = 4
WHERE `CategoryId` = 15;
-- Bánh quy
-- Tạo index để tối ưu truy vấn
ALTER TABLE `category`
ADD INDEX `idx_status_sort` (`status`, `SortOrder`);