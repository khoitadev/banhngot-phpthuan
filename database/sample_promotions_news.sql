-- Dữ liệu mẫu để test khuyến mãi và tin tức
-- Chạy file này SAU KHI đã chạy upgrade_promotions_news.sql
-- Thêm khuyến mãi mẫu
INSERT INTO `promotions` (
    `Title`,
    `Description`,
    `Content`,
    `DiscountType`,
    `DiscountValue`,
    `MinPurchase`,
    `StartDate`,
    `EndDate`,
    `IsActive`,
    `IsFeatured`,
    `SortOrder`
  )
VALUES (
    'Giảm giá 20% cho tất cả sản phẩm',
    'Chương trình khuyến mãi đặc biệt giảm 20% cho tất cả sản phẩm trong cửa hàng',
    'Chương trình khuyến mãi lớn nhất trong năm! Giảm ngay 20% cho tất cả sản phẩm bánh ngọt, bánh kem, bánh mì... Áp dụng cho mọi đơn hàng từ 100.000 VNĐ trở lên.\n\nThời gian áp dụng: Từ ngày 01/12/2024 đến 31/12/2024\n\nKhông áp dụng kèm các chương trình khuyến mãi khác.',
    'percentage',
    20.00,
    100000.00,
    NOW(),
    DATE_ADD(NOW(), INTERVAL 30 DAY),
    1,
    1,
    1
  ),
  (
    'Giảm 50.000 VNĐ cho đơn hàng từ 300.000 VNĐ',
    'Mua đơn hàng từ 300.000 VNĐ được giảm ngay 50.000 VNĐ',
    'Chương trình khuyến mãi đặc biệt dành cho khách hàng mua số lượng lớn. Khi mua đơn hàng từ 300.000 VNĐ trở lên, bạn sẽ được giảm ngay 50.000 VNĐ.\n\nÁp dụng cho tất cả sản phẩm trong cửa hàng.\n\nThời gian: Từ ngày 15/12/2024 đến 15/01/2025',
    'fixed',
    50000.00,
    300000.00,
    DATE_ADD(NOW(), INTERVAL 5 DAY),
    DATE_ADD(NOW(), INTERVAL 45 DAY),
    1,
    1,
    2
  ),
  (
    'Mua 2 tặng 1 - Bánh kem',
    'Mua 2 bánh kem bất kỳ được tặng 1 bánh kem nhỏ',
    'Chương trình đặc biệt cho bánh kem! Khi mua 2 bánh kem bất kỳ, bạn sẽ được tặng 1 bánh kem nhỏ (giá trị dưới 100.000 VNĐ).\n\nÁp dụng cho tất cả loại bánh kem trong cửa hàng.\n\nThời gian: Từ ngày 20/12/2024 đến 20/01/2025',
    'buy_x_get_y',
    NULL,
    0,
    DATE_ADD(NOW(), INTERVAL 10 DAY),
    DATE_ADD(NOW(), INTERVAL 50 DAY),
    1,
    0,
    3
  );
-- Thêm tin tức mẫu
INSERT INTO `news` (
    `Title`,
    `Summary`,
    `Content`,
    `Category`,
    `Author`,
    `IsPublished`,
    `IsFeatured`,
    `PublishedAt`,
    `SortOrder`
  )
VALUES (
    'Công thức làm bánh kem tại nhà đơn giản',
    'Hướng dẫn chi tiết cách làm bánh kem thơm ngon tại nhà với những nguyên liệu dễ tìm',
    'Bánh kem là món tráng miệng yêu thích của nhiều người. Với công thức đơn giản này, bạn có thể tự tay làm những chiếc bánh kem thơm ngon ngay tại nhà.\n\n**Nguyên liệu cần chuẩn bị:**\n- Bột mì: 200g\n- Đường: 150g\n- Trứng gà: 3 quả\n- Bơ: 100g\n- Kem tươi: 200ml\n- Vanilla: 1 thìa cà phê\n\n**Các bước thực hiện:**\n1. Đánh bơ và đường cho đến khi bông xốp\n2. Thêm trứng vào từng quả một, đánh đều\n3. Rây bột mì và trộn đều\n4. Nướng ở nhiệt độ 180°C trong 25-30 phút\n5. Để nguội và phủ kem lên trên\n\nChúc bạn thành công với công thức này!',
    'Công thức',
    'Admin',
    1,
    1,
    NOW(),
    1
  ),
  (
    'Lợi ích sức khỏe của bánh mì nguyên cám',
    'Bánh mì nguyên cám không chỉ ngon mà còn rất tốt cho sức khỏe. Tìm hiểu những lợi ích tuyệt vời của loại bánh này.',
    'Bánh mì nguyên cám là lựa chọn tốt cho sức khỏe hơn so với bánh mì trắng thông thường. Dưới đây là những lợi ích chính:\n\n**1. Giàu chất xơ**\nBánh mì nguyên cám chứa nhiều chất xơ giúp hỗ trợ tiêu hóa và giảm nguy cơ mắc các bệnh về đường ruột.\n\n**2. Giàu vitamin và khoáng chất**\nChứa nhiều vitamin B, sắt, magie và các khoáng chất cần thiết cho cơ thể.\n\n**3. Giúp kiểm soát đường huyết**\nChỉ số đường huyết thấp hơn so với bánh mì trắng, giúp kiểm soát lượng đường trong máu tốt hơn.\n\n**4. Hỗ trợ giảm cân**\nChất xơ giúp bạn cảm thấy no lâu hơn, giảm cảm giác thèm ăn.\n\nHãy thêm bánh mì nguyên cám vào chế độ ăn uống hàng ngày của bạn!',
    'Sức khỏe',
    'Admin',
    1,
    1,
    DATE_SUB(NOW(), INTERVAL 2 DAY),
    2
  ),
  (
    'Cửa hàng mở rộng - Thêm nhiều sản phẩm mới',
    'Cửa hàng bánh ngọt của chúng tôi vừa mở rộng với nhiều sản phẩm mới và hấp dẫn',
    'Chúng tôi rất vui mừng thông báo về việc mở rộng cửa hàng với nhiều sản phẩm mới và hấp dẫn.\n\n**Những sản phẩm mới:**\n- Bánh kem theo yêu cầu\n- Bánh mì đặc biệt\n- Bánh quy handmade\n- Bánh ngọt theo mùa\n\n**Giờ mở cửa mới:**\n- Thứ 2 - Thứ 6: 7:00 - 20:00\n- Thứ 7 - Chủ nhật: 8:00 - 21:00\n\nChúng tôi luôn cố gắng mang đến những sản phẩm chất lượng nhất cho khách hàng. Hãy đến và trải nghiệm!',
    'Tin tức',
    'Admin',
    1,
    0,
    DATE_SUB(NOW(), INTERVAL 5 DAY),
    3
  ),
  (
    'Mẹo bảo quản bánh ngọt tươi lâu',
    'Những mẹo nhỏ giúp bạn bảo quản bánh ngọt được tươi lâu hơn',
    'Bảo quản bánh ngọt đúng cách sẽ giúp bánh giữ được độ tươi ngon lâu hơn. Dưới đây là một số mẹo hữu ích:\n\n**1. Bảo quản trong tủ lạnh**\nHầu hết các loại bánh ngọt nên được bảo quản trong tủ lạnh ở nhiệt độ 2-4°C.\n\n**2. Sử dụng hộp kín**\nĐựng bánh trong hộp kín để tránh bị khô và mất mùi vị.\n\n**3. Tránh ánh sáng trực tiếp**\nBánh nên được để ở nơi tối, tránh ánh sáng mặt trời trực tiếp.\n\n**4. Không để chung với thực phẩm có mùi**\nBánh dễ hấp thụ mùi, nên tránh để chung với các thực phẩm có mùi mạnh.\n\n**5. Sử dụng trong vòng 3-5 ngày**\nTốt nhất nên sử dụng bánh trong vòng 3-5 ngày để đảm bảo chất lượng.',
    'Mẹo vặt',
    'Admin',
    1,
    0,
    DATE_SUB(NOW(), INTERVAL 7 DAY),
    4
  );