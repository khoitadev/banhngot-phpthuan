-- SQL để đơn giản hóa: Gộp promotions và coupons thành 1 bảng
-- LƯU Ý: Script này sẽ XÓA bảng coupons và chỉ dùng promotions
-- Chỉ chạy nếu bạn muốn đơn giản hóa hệ thống
-- Bước 1: Thêm các cột còn thiếu vào bảng promotions
ALTER TABLE `promotions`
ADD COLUMN `Code` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Mã giảm giá (nếu có)'
AFTER `MaxDiscount`,
  ADD COLUMN `UsageLimit` INT(11) NULL DEFAULT NULL COMMENT 'Số lần sử dụng tối đa'
AFTER `Code`,
  ADD COLUMN `UsedCount` INT(11) NOT NULL DEFAULT 0 COMMENT 'Số lần đã sử dụng'
AFTER `UsageLimit`,
  ADD COLUMN `UserLimit` INT(11) NULL DEFAULT NULL COMMENT 'Số lần mỗi user được sử dụng'
AFTER `UsedCount`;
-- Tạo index cho Code
ALTER TABLE `promotions`
ADD INDEX `idx_code` (`Code`);
-- Bước 2: Copy dữ liệu từ coupons sang promotions (nếu có)
-- Chỉ copy những mã chưa có trong promotions
INSERT INTO `promotions` (
    `Title`,
    `Description`,
    `DiscountType`,
    `DiscountValue`,
    `MinPurchase`,
    `MaxDiscount`,
    `Code`,
    `UsageLimit`,
    `UsedCount`,
    `UserLimit`,
    `StartDate`,
    `EndDate`,
    `IsActive`,
    `IsFeatured`,
    `CreatedAt`
  )
SELECT c.`Name` AS `Title`,
  c.`Description`,
  c.`DiscountType`,
  c.`DiscountValue`,
  c.`MinPurchase`,
  c.`MaxDiscount`,
  c.`Code`,
  c.`UsageLimit`,
  c.`UsedCount`,
  c.`UserLimit`,
  c.`StartDate`,
  c.`EndDate`,
  c.`IsActive`,
  0 AS `IsFeatured`,
  c.`CreatedAt`
FROM `coupons` c
  LEFT JOIN `promotions` p ON c.`Code` = p.`CouponCode`
WHERE p.`PromotionId` IS NULL;
-- Bước 3: Cập nhật CouponCode trong promotions thành Code
UPDATE `promotions`
SET `Code` = `CouponCode`
WHERE `CouponCode` IS NOT NULL
  AND `Code` IS NULL;
-- Bước 4: Xóa cột CouponCode (không cần nữa vì đã có Code)
ALTER TABLE `promotions` DROP COLUMN `CouponCode`;
-- Bước 5: Xóa bảng coupons và coupon_usage (SAU KHI ĐÃ BACKUP DỮ LIỆU)
-- UNCOMMENT DÒNG DƯỚI ĐÂY SAU KHI ĐÃ KIỂM TRA MỌI THỨ HOẠT ĐỘNG TỐT
-- DROP TABLE IF EXISTS `coupon_usage`;
-- DROP TABLE IF EXISTS `coupons`;