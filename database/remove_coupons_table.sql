-- SQL để xóa bảng coupons và coupon_usage (sau khi đã gộp vào promotions)
-- CHỈ CHẠY SAU KHI ĐÃ CHẠY simplify_promotions_coupons.sql
-- VÀ ĐÃ XÁC NHẬN DỮ LIỆU ĐÃ ĐƯỢC CHUYỂN SANG promotions
-- Bước 1: Xóa bảng coupon_usage (nếu có)
DROP TABLE IF EXISTS `coupon_usage`;
-- Bước 2: Xóa bảng coupons (nếu có)
DROP TABLE IF EXISTS `coupons`;
-- Sau khi chạy script này, hệ thống sẽ chỉ sử dụng bảng promotions