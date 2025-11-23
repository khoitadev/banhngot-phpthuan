# Hướng dẫn sử dụng mã giảm giá

## Cách 1: Từ trang Khuyến mãi (Dễ nhất)

### Bước 1: Vào trang Khuyến mãi

- Truy cập: `http://banhngot.test/promotions.php`
- Hoặc click menu "Khuyến mãi" trên header

### Bước 2: Xem mã giảm giá

- Mỗi khuyến mãi có mã giảm giá sẽ hiển thị:
  - Mã giảm giá (ví dụ: WELCOME10)
  - Điều kiện sử dụng (ví dụ: Đơn hàng tối thiểu 100,000 VNĐ)
  - Nút "Áp dụng mã ngay"

### Bước 3: Áp dụng mã

- **Cách A**: Click nút "Áp dụng mã ngay" → Tự động chuyển đến trang thanh toán với mã đã được áp dụng
- **Cách B**: Click nút "Sao chép mã" → Copy mã → Vào giỏ hàng → Thanh toán → Dán mã vào ô "Mã giảm giá"

## Cách 2: Từ trang Chi tiết Khuyến mãi

### Bước 1: Click "Xem chi tiết" trên khuyến mãi

- Vào trang chi tiết khuyến mãi

### Bước 2: Xem mã giảm giá trong sidebar

- Mã giảm giá hiển thị rõ ràng bên phải
- Có thông tin đầy đủ:
  - Mã giảm giá
  - % giảm giá hoặc số tiền giảm
  - Điều kiện sử dụng
  - Số lượt còn lại

### Bước 3: Áp dụng mã

- Click "Áp dụng mã ngay" → Tự động chuyển đến checkout với mã đã áp dụng
- Hoặc "Sao chép mã" → Dán vào checkout

## Cách 3: Nhập trực tiếp khi thanh toán

### Bước 1: Thêm sản phẩm vào giỏ hàng

- Chọn sản phẩm → Thêm vào giỏ hàng

### Bước 2: Vào trang Thanh toán

- Click "Thanh toán" hoặc truy cập: `http://banhngot.test/checkout.php`

### Bước 3: Nhập mã giảm giá

- Tìm ô "Mã giảm giá (nếu có)" trong form thanh toán
- Nhập mã giảm giá (ví dụ: WELCOME10)
- Click nút "Áp dụng"

### Bước 4: Xem kết quả

- Nếu mã hợp lệ:
  - Hiển thị thông báo "Áp dụng mã giảm giá thành công!"
  - Tổng tiền sẽ được cập nhật tự động
  - Hiển thị số tiền giảm và tổng tiền sau giảm
- Nếu mã không hợp lệ:
  - Hiển thị lỗi (ví dụ: "Mã giảm giá không hợp lệ", "Đơn hàng tối thiểu 100,000 VNĐ")

### Bước 5: Hoàn tất đơn hàng

- Điền thông tin giao hàng
- Click "Thanh toán"
- Mã giảm giá sẽ được áp dụng tự động

## Các mã giảm giá mẫu (sau khi chạy SQL)

1. **WELCOME10**

   - Giảm 10% (tối đa 50,000 VNĐ)
   - Đơn hàng tối thiểu: 100,000 VNĐ
   - Mỗi user dùng 1 lần

2. **SAVE50K**

   - Giảm 50,000 VNĐ
   - Đơn hàng tối thiểu: 300,000 VNĐ
   - Không giới hạn user

3. **SUMMER20**
   - Giảm 20% (tối đa 100,000 VNĐ)
   - Đơn hàng tối thiểu: 200,000 VNĐ
   - Mỗi user dùng 2 lần

## Lưu ý:

1. **Mã giảm giá phải còn hiệu lực**

   - Kiểm tra ngày bắt đầu và kết thúc

2. **Đơn hàng phải đạt giá trị tối thiểu**

   - Ví dụ: WELCOME10 cần đơn hàng từ 100,000 VNĐ

3. **Số lượt sử dụng**

   - Một số mã có giới hạn số lượt sử dụng
   - Một số mã có giới hạn số lần mỗi user

4. **Mã chỉ áp dụng 1 lần cho mỗi đơn hàng**

   - Không thể dùng nhiều mã cùng lúc

5. **Mã sẽ tự động bị xóa sau khi đặt hàng thành công**
   - Nếu muốn dùng lại, phải nhập lại mã

## Troubleshooting:

### Mã không hoạt động?

1. Kiểm tra mã đã nhập đúng chưa (không phân biệt hoa thường)
2. Kiểm tra đơn hàng có đạt giá trị tối thiểu chưa
3. Kiểm tra mã còn hiệu lực chưa
4. Kiểm tra đã dùng hết lượt chưa

### Không thấy ô nhập mã?

- Đảm bảo đã đăng nhập
- Đảm bảo có sản phẩm trong giỏ hàng
- Refresh lại trang

### Mã bị mất sau khi refresh?

- Mã được lưu trong session
- Nếu đăng xuất hoặc xóa session, mã sẽ mất
- Cần nhập lại mã
