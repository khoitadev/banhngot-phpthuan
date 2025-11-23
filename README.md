Link: www/banhngot

File: banhngot

DB: banhngot

TK User: anh@gmail.com
MK: 123

Link admin: Dưới cùng trang -> admin
TK Admin: admin@gmail.com
MK: 123

## Chatbot AI setup

### Cách hoạt động:

Chatbot hoạt động theo 3 tầng thông minh:

1. **Tầng 1 - Câu trả lời có sẵn**: Trả lời ngay các câu hỏi thường gặp (giờ mở cửa, địa chỉ, giao hàng...)
2. **Tầng 2 - Truy vấn Database**: Tự động lấy thông tin từ database cho các câu hỏi như:
   - "bánh nào bán chạy nhất" → Lấy từ bảng orderdetails
   - "bánh đắt nhất" / "bánh rẻ nhất" → Lấy từ bảng products
   - "còn hàng không" → Kiểm tra số lượng sản phẩm
3. **Tầng 3 - AI Fallback**: Nếu không tìm thấy, sẽ dùng OpenAI API để trả lời thông minh (nếu có API key)

### Cài đặt AI (Tùy chọn):

1. Lấy API key từ https://platform.openai.com/api-keys
2. Chọn 1 trong 2 cách cấu hình:
   - **Biến môi trường** (khuyến khích cho môi trường production):
     ```
     setx OPENAI_API_KEY "sk-..." /M
     ```
     Sau đó khởi động lại Apache/Laragon
   - **Ghi trực tiếp vào file** `config/chatbot.php`:
     ```php
     define('CHATBOT_OPENAI_API_KEY', 'sk-...');
     ```
     (Dùng khi chỉ test local, tuyệt đối không commit key thật lên repo)
3. Bảo đảm PHP có bật extension `curl` (thường đã có sẵn trong Laragon)

### Lưu ý:

- Chatbot vẫn hoạt động tốt **KHÔNG CẦN** API key - nó sẽ dùng database và câu trả lời có sẵn
- AI chỉ được dùng khi không tìm thấy câu trả lời ở 2 tầng đầu
- Các lỗi AI sẽ được ghi vào error log của PHP
