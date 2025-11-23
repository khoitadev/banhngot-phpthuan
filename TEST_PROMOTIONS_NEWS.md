# Hướng dẫn Test Phần Khuyến mãi và Tin tức

## Bước 1: Chạy SQL để tạo bảng và dữ liệu mẫu

1. Mở phpMyAdmin hoặc MySQL client
2. Chọn database của bạn (thường là `banhngot`)
3. Chạy các file SQL theo thứ tự:
   - **Bước 1**: Chạy `database/upgrade_promotions_news.sql` (tạo bảng)
   - **Bước 2**: Chạy `database/sample_promotions_news.sql` (thêm dữ liệu mẫu)

## Bước 2: Tạo thư mục upload

Tạo các thư mục sau (nếu chưa có):

```
admin/uploads/promotions/
admin/uploads/news/
```

**Cách tạo trên Windows:**

- Mở File Explorer
- Điều hướng đến: `D:\laragon\www\banhngot\admin\uploads\`
- Tạo 2 thư mục: `promotions` và `news`

**Hoặc dùng lệnh:**

```bash
mkdir -p admin/uploads/promotions
mkdir -p admin/uploads/news
```

## Bước 3: Test Frontend (Trang người dùng)

### 3.1. Test trang Khuyến mãi

1. Truy cập: `http://banhngot.test/promotions.php`
2. Kiểm tra:
   - ✅ Hiển thị danh sách khuyến mãi
   - ✅ Hiển thị badge giảm giá
   - ✅ Hiển thị thời gian còn lại
   - ✅ Pagination hoạt động

### 3.2. Test trang Chi tiết Khuyến mãi

1. Click vào một khuyến mãi
2. Truy cập: `http://banhngot.test/promotion_detail.php?id=1`
3. Kiểm tra:
   - ✅ Hiển thị đầy đủ thông tin khuyến mãi
   - ✅ Hiển thị banner giảm giá
   - ✅ Hiển thị thời gian áp dụng
   - ✅ Nút "Mua ngay" hoạt động

### 3.3. Test trang Tin tức

1. Truy cập: `http://banhngot.test/news.php`
2. Kiểm tra:
   - ✅ Hiển thị danh sách tin tức
   - ✅ Filter theo category hoạt động
   - ✅ Pagination hoạt động

### 3.4. Test trang Chi tiết Tin tức

1. Click vào một tin tức
2. Truy cập: `http://banhngot.test/news_detail.php?id=1`
3. Kiểm tra:
   - ✅ Hiển thị đầy đủ nội dung tin tức
   - ✅ Hiển thị tin tức liên quan
   - ✅ Thông tin tác giả, ngày đăng

### 3.5. Test trên Trang chủ

1. Truy cập: `http://banhngot.test/index.php`
2. Kiểm tra:
   - ✅ Section "Khuyến mãi" hiển thị 3 khuyến mãi nổi bật
   - ✅ Section "Tin tức" hiển thị 3 tin tức nổi bật
   - ✅ Link "Xem tất cả" hoạt động

## Bước 4: Test Admin (Trang quản lý)

### 4.1. Đăng nhập Admin

1. Truy cập: `http://banhngot.test/admin/html/`
2. Đăng nhập với tài khoản admin

### 4.2. Test Quản lý Khuyến mãi

#### 4.2.1. Xem danh sách

1. Vào menu "Khuyến mãi" → "Danh sách khuyến mãi"
2. Hoặc truy cập: `http://banhngot.test/admin/html/promotion_list.php`
3. Kiểm tra:
   - ✅ Hiển thị danh sách khuyến mãi
   - ✅ Hiển thị trạng thái (Đang hoạt động, Sắp diễn ra, Đã kết thúc)
   - ✅ Badge "Nổi bật" cho khuyến mãi featured
   - ✅ Pagination hoạt động

#### 4.2.2. Thêm khuyến mãi mới

1. Click nút "Thêm khuyến mãi"
2. Hoặc truy cập: `http://banhngot.test/admin/html/promotion_add.php`
3. Điền form:
   - Tiêu đề: "Test Khuyến mãi"
   - Mô tả: "Mô tả test"
   - Nội dung: "Nội dung chi tiết"
   - Loại giảm giá: Chọn "Phần trăm"
   - Giá trị: 15
   - Đơn hàng tối thiểu: 50000
   - Ngày bắt đầu: Chọn ngày hôm nay
   - Ngày kết thúc: Chọn ngày sau 7 ngày
   - Check "Đang hoạt động"
   - Check "Nổi bật"
4. Upload ảnh (tùy chọn)
5. Click "Lưu"
6. Kiểm tra:
   - ✅ Lưu thành công và quay về danh sách
   - ✅ Khuyến mãi mới xuất hiện trong danh sách

#### 4.2.3. Sửa khuyến mãi

1. Click icon "Sửa" (màu xanh) bên cạnh một khuyến mãi
2. Sửa thông tin
3. Click "Lưu"
4. Kiểm tra:
   - ✅ Cập nhật thành công

#### 4.2.4. Xóa khuyến mãi

1. Click icon "Xóa" (màu đỏ) bên cạnh một khuyến mãi
2. Xác nhận xóa
3. Kiểm tra:
   - ✅ Xóa thành công và biến mất khỏi danh sách

### 4.3. Test Quản lý Tin tức

#### 4.3.1. Xem danh sách

1. Vào menu "Tin tức" → "Danh sách tin tức"
2. Hoặc truy cập: `http://banhngot.test/admin/html/news_list.php`
3. Kiểm tra:
   - ✅ Hiển thị danh sách tin tức
   - ✅ Hiển thị trạng thái (Đã xuất bản / Bản nháp)
   - ✅ Badge "Nổi bật" cho tin featured
   - ✅ Pagination hoạt động

#### 4.3.2. Thêm tin tức mới

1. Click nút "Thêm tin tức"
2. Hoặc truy cập: `http://banhngot.test/admin/html/news_add.php`
3. Điền form:
   - Tiêu đề: "Test Tin tức"
   - Tóm tắt: "Tóm tắt tin tức test"
   - Nội dung: "Nội dung chi tiết của tin tức..."
   - Danh mục: "Tin tức"
   - Tác giả: "Admin"
   - Check "Đã xuất bản"
   - Check "Nổi bật"
4. Upload ảnh (tùy chọn)
5. Click "Lưu"
6. Kiểm tra:
   - ✅ Lưu thành công và quay về danh sách
   - ✅ Tin tức mới xuất hiện trong danh sách
   - ✅ Slug được tạo tự động từ tiêu đề

#### 4.3.3. Sửa tin tức

1. Click icon "Sửa" bên cạnh một tin tức
2. Sửa thông tin
3. Click "Lưu"
4. Kiểm tra:
   - ✅ Cập nhật thành công

#### 4.3.4. Xóa tin tức

1. Click icon "Xóa" bên cạnh một tin tức
2. Xác nhận xóa
3. Kiểm tra:
   - ✅ Xóa thành công

## Bước 5: Test các trường hợp đặc biệt

### 5.1. Test Filter

- **Tin tức**: Click vào các category filter, kiểm tra danh sách được lọc đúng

### 5.2. Test Trạng thái

- **Khuyến mãi**:
  - Tạo khuyến mãi với ngày bắt đầu trong tương lai → Hiển thị "Sắp diễn ra"
  - Tạo khuyến mãi với ngày kết thúc trong quá khứ → Hiển thị "Đã kết thúc"
  - Tạo khuyến mãi đang trong thời gian áp dụng → Hiển thị "Đang hoạt động"

### 5.3. Test Upload ảnh

- Upload ảnh khi thêm mới → Kiểm tra ảnh hiển thị
- Upload ảnh mới khi sửa → Kiểm tra ảnh cũ bị thay thế
- Không upload ảnh → Kiểm tra không bị lỗi

### 5.4. Test Pagination

- Tạo nhiều khuyến mãi/tin tức (>10) → Kiểm tra pagination hoạt động

## Checklist Test

### Frontend

- [ ] Trang danh sách khuyến mãi hiển thị đúng
- [ ] Trang chi tiết khuyến mãi hiển thị đúng
- [ ] Trang danh sách tin tức hiển thị đúng
- [ ] Trang chi tiết tin tức hiển thị đúng
- [ ] Filter category tin tức hoạt động
- [ ] Trang chủ hiển thị khuyến mãi và tin tức nổi bật
- [ ] Pagination hoạt động

### Admin

- [ ] Danh sách khuyến mãi hiển thị đúng
- [ ] Thêm khuyến mãi thành công
- [ ] Sửa khuyến mãi thành công
- [ ] Xóa khuyến mãi thành công
- [ ] Upload ảnh khuyến mãi hoạt động
- [ ] Danh sách tin tức hiển thị đúng
- [ ] Thêm tin tức thành công
- [ ] Sửa tin tức thành công
- [ ] Xóa tin tức thành công
- [ ] Upload ảnh tin tức hoạt động
- [ ] Slug tự động tạo từ tiêu đề

## Lưu ý

1. **Nếu không thấy dữ liệu mẫu**: Kiểm tra lại đã chạy `sample_promotions_news.sql` chưa
2. **Nếu upload ảnh bị lỗi**: Kiểm tra quyền ghi của thư mục `admin/uploads/`
3. **Nếu trang bị lỗi 404**: Kiểm tra đường dẫn file có đúng không
4. **Nếu không hiển thị trên trang chủ**: Kiểm tra khuyến mãi/tin tức có `IsFeatured = 1` và `IsActive = 1` (hoặc `IsPublished = 1`)

## Troubleshooting

### Lỗi: "Table 'promotions' doesn't exist"

→ Chạy lại file `upgrade_promotions_news.sql`

### Lỗi: "Cannot upload image"

→ Kiểm tra quyền thư mục `admin/uploads/promotions/` và `admin/uploads/news/`

### Lỗi: "Slug is null"

→ Slug sẽ tự động tạo khi lưu, không cần nhập thủ công

### Không hiển thị trên trang chủ

→ Kiểm tra:

- Khuyến mãi: `IsActive = 1`, `IsFeatured = 1`, và trong thời gian áp dụng
- Tin tức: `IsPublished = 1`, `IsFeatured = 1`
