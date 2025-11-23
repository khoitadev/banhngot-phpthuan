-- SQL để tạo bảng banners cho quản lý slide trang chủ
-- Chạy file này trong phpMyAdmin hoặc MySQL (tùy chọn - để quản lý từ admin sau)
CREATE TABLE IF NOT EXISTS `banners` (
  `BannerId` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề banner',
  `Subtitle` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Phụ đề',
  `ButtonText` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Text nút',
  `ButtonLink` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Link nút',
  `Image` VARCHAR(255) NOT NULL COMMENT 'Ảnh banner',
  `SortOrder` INT(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `IsActive` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Đang hoạt động',
  `CreatedAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`BannerId`),
  INDEX `idx_active` (`IsActive`),
  INDEX `idx_sort` (`SortOrder`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Thêm dữ liệu mẫu
INSERT INTO `banners` (
    `Title`,
    `Subtitle`,
    `ButtonText`,
    `ButtonLink`,
    `Image`,
    `SortOrder`,
    `IsActive`
  )
VALUES (
    'Làm cho cuộc sống của bạn ngọt ngào hơn',
    NULL,
    'Bánh của chúng tôi',
    'list_product.php',
    'img/hero/anh1.jpg',
    1,
    1
  ),
  (
    'Hãy khám phá những bí mật nho nhỏ cùng Cake Shop nhé!',
    NULL,
    'Khám phá ngay',
    'list_product.php',
    'img/hero/anh2.jpg',
    2,
    1
  ),
  (
    'Bánh ngọt tươi mỗi ngày - Chất lượng đảm bảo',
    NULL,
    'Xem khuyến mãi',
    'promotions.php',
    'img/hero/hero-11.jpg',
    3,
    1
  );