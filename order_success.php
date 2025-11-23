<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt hàng thành công</title>
  <style>
    .success-container {
      min-height: 60vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }

    .success-box {
      background: #fff;
      border-radius: 10px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      width: 100%;
    }

    .success-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: #4CAF50;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 50px;
      color: white;
    }

    .success-title {
      font-size: 28px;
      color: #4CAF50;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .success-message {
      font-size: 16px;
      color: #666;
      margin-bottom: 30px;
      line-height: 1.6;
    }

    .success-actions {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 30px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s;
      display: inline-block;
    }

    .btn-primary {
      background: #4CAF50;
      color: white;
    }

    .btn-primary:hover {
      background: #45a049;
    }

    .btn-secondary {
      background: #f0f0f0;
      color: #333;
    }

    .btn-secondary:hover {
      background: #e0e0e0;
    }

    .countdown {
      margin-top: 20px;
      color: #999;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="success-container">
    <div class="success-box">
      <div class="success-icon">✓</div>
      <h1 class="success-title">Đặt hàng thành công!</h1>
      <p class="success-message">
        Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được tiếp nhận và chúng tôi sẽ xử lý trong thời gian sớm nhất.
      </p>
      <div class="success-actions">
        <a href="index.php" class="btn btn-primary">Về trang chủ</a>
        <a href="history_order.php" class="btn btn-secondary">Xem đơn hàng</a>
      </div>
      <div class="countdown">
        Tự động chuyển về trang chủ sau <span id="countdown">5</span> giây...
      </div>
    </div>
  </div>

  <script>
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');

    const timer = setInterval(function() {
      countdown--;
      countdownElement.textContent = countdown;

      if (countdown <= 0) {
        clearInterval(timer);
        window.location.href = 'index.php';
      }
    }, 1000);
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . "/inc/footer.php"); ?>
</body>

</html>