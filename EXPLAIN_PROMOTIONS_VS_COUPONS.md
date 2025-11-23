# Giải thích: Promotions vs Coupons

## Hiện tại có 2 bảng:

### 1. **Bảng `promotions`** (Khuyến mãi - Marketing Campaigns)
**Mục đích**: Quản lý các chương trình khuyến mãi, tin tức khuyến mãi

**Chức năng**:
- Hiển thị thông tin marketing (Title, Description, Content, Image)
- Quản lý thời gian khuyến mãi (StartDate, EndDate)
- Hiển thị trên website (IsFeatured, ViewCount, SortOrder)
- Có thể liên kết với mã giảm giá qua `CouponCode`

**Ví dụ**: 
- "Khuyến mãi mùa hè 2024 - Giảm 20%"
- "Chương trình tri ân khách hàng"
- Có ảnh, mô tả dài, nội dung HTML

### 2. **Bảng `coupons`** (Mã giảm giá - Discount Codes)
**Mục đích**: Quản lý mã giảm giá thực tế, có thể áp dụng khi checkout

**Chức năng**:
- Lưu mã giảm giá (Code: WELCOME10, SAVE50K, ...)
- Validation logic (MinPurchase, MaxDiscount, UsageLimit)
- Theo dõi sử dụng (UsedCount, UserLimit)
- Tracking lịch sử sử dụng (bảng coupon_usage)

**Ví dụ**:
- Code: `WELCOME10` - Giảm 10%, tối đa 50k
- Code: `SAVE50K` - Giảm 50,000 VND
- User nhập mã này vào checkout để được giảm giá

## Vấn đề trùng lặp:

Cả 2 bảng đều có:
- `DiscountType`, `DiscountValue`
- `MinPurchase`, `MaxDiscount`
- `StartDate`, `EndDate`
- `IsActive`

## Giải pháp:

### Option 1: Giữ nguyên 2 bảng (Khuyến nghị)
**Ưu điểm**:
- Tách biệt rõ ràng: Marketing vs Logic
- Linh hoạt: 1 promotion có thể có nhiều mã giảm giá
- Dễ mở rộng: Có thể thêm nhiều tính năng cho từng bảng

**Nhược điểm**:
- Phức tạp hơn
- Có trùng lặp dữ liệu

**Cách dùng**:
- Tạo promotion trên trang "Khuyến mãi" (có ảnh, mô tả)
- Tạo mã giảm giá trong bảng `coupons`
- Liên kết: `UPDATE promotions SET CouponCode = 'WELCOME10' WHERE PromotionId = 1`

### Option 2: Gộp thành 1 bảng (Đơn giản hóa)
**Ưu điểm**:
- Đơn giản, dễ quản lý
- Không trùng lặp dữ liệu
- Dễ hiểu cho người mới

**Nhược điểm**:
- Mất tính linh hoạt
- Khó mở rộng sau này

**Cách làm**:
- Chạy script `database/simplify_promotions_coupons.sql`
- Xóa bảng `coupons`, chỉ dùng `promotions`
- Thêm cột `Code` vào `promotions`

### Option 3: Chỉ dùng `coupons` (Nếu không cần marketing)
**Ưu điểm**:
- Rất đơn giản
- Tập trung vào mã giảm giá

**Nhược điểm**:
- Không có trang khuyến mãi đẹp
- Không có nội dung marketing phong phú

## Khuyến nghị:

**Nếu bạn muốn có trang khuyến mãi đẹp với ảnh, mô tả**: Giữ nguyên 2 bảng (Option 1)

**Nếu bạn chỉ cần mã giảm giá đơn giản**: Gộp thành 1 bảng (Option 2)

Bạn muốn chọn option nào?

