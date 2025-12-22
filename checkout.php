<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ob_start();
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/cart.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/coupon_helper.php");
?>

<?php
$user = $_SESSION['user'];
$class = new Cart();
$cart_totals = $class->total_price($cart);

// Handle coupon from URL parameter
// If new coupon code is provided, replace the old one
if (isset($_GET['coupon'])) {
    $couponCode = strtoupper(trim($_GET['coupon']));

    // Check if it's a different coupon code
    $isNewCoupon = true;
    if (isset($_SESSION['applied_coupon'])) {
        $oldCouponCode = strtoupper(trim($_SESSION['applied_coupon']['code']));
        if ($oldCouponCode === $couponCode) {
            $isNewCoupon = false; // Same coupon, don't re-validate
        } else {
            // Different coupon, remove old one
            unset($_SESSION['applied_coupon']);
        }
    }

    // Apply new coupon if it's different
    if ($isNewCoupon) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/coupon_helper.php");
        $customerId = isset($_SESSION['user']) ? $_SESSION['user']['CustomerId'] : null;
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $coupon = validateCoupon($couponCode, $cart_totals, $customerId, $cart);

        if ($coupon !== false && !isset($coupon['error'])) {
            $discountInfo = calculateCouponDiscount($coupon, $cart_totals);

            // Use PromotionId from promotions table
            $couponId = $coupon['PromotionId'];

            $_SESSION['applied_coupon'] = [
                'code' => $couponCode,
                'id' => $couponId,
                'discount' => $discountInfo['discount'],
                'final_total' => $discountInfo['final_total']
            ];
        } else {
            // Invalid coupon, make sure old one is removed
            unset($_SESSION['applied_coupon']);
        }
    }
}

// Handle coupon discount
$discount_amount = 0;
$final_total = $cart_totals;
$applied_coupon = null;

if (isset($_SESSION['applied_coupon'])) {
    $applied_coupon = $_SESSION['applied_coupon'];
    $discount_amount = $applied_coupon['discount'];
    $final_total = $applied_coupon['final_total'];
}

if (isset($_POST['submit'])) {
    $id_user = $user['CustomerId'];
    $address = $_POST['address'];
    $number_phone = $_POST['number_phone'];
    $note = $_POST['note'];
    $email =  $_POST['email'];
    $fullname = $_POST['full_name'];

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $current_date = new DateTime('now');
    $date_order = $current_date->format("Y-m-d H:i:s");

    // Get coupon info if applied
    $coupon_code = null;
    $coupon_id = null;
    $discount_amount_order = 0;
    $final_total_order = $cart_totals;

    if (isset($_SESSION['applied_coupon'])) {
        $coupon_code = $_SESSION['applied_coupon']['code'];
        $coupon_id = $_SESSION['applied_coupon']['id'];
        $discount_amount_order = $_SESSION['applied_coupon']['discount'];
        $final_total_order = $_SESSION['applied_coupon']['final_total'];
    }

    // Use final_total_order for order insertion
    $order_total_to_insert = $final_total_order;

    $sql = "INSERT INTO oders(CustomerId, Note, order_date, address, number_phone, total_price, CouponCode, DiscountAmount, FinalTotal) 
            VALUES ('$id_user','$note','$date_order','$address','$number_phone','$cart_totals','$coupon_code','$discount_amount_order','$order_total_to_insert')";
    $result = mysqli_query($conn, $sql);
    $content = "<table width='500' border='1'>";
    $content .= "<tr><th>#</th><th>Tên sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Thành tiền</th></tr>";
    if ($result) {
        $id_order = mysqli_insert_id($conn);
        $i = 0;
        foreach ($cart as $value) {
            $i++;
            $price = $value['sellprice'];
            $num = $value['quantity'];
            $intro_money = $num * $price;
            $insert_order_detail = "INSERT INTO `orderdetails`(`Order_Detail_Id`,`ProductId`, `Price`, `Quantity`) VALUES ('$id_order','$value[id]', $value[sellprice], '$value[quantity]')";
            mysqli_query($conn, $insert_order_detail);
            $content .= "<tr><td>$i</td><td>" . $value['name'] . "</td><td>" . $value['quantity'] . "</td><td>" . $value['sellprice'] . "</td><td>$intro_money</td></tr>";
        }
        $content .= "<p>Tổng tiền: " . number_format($cart_totals, 0, ',', '.') . " VND</p>";
        if ($discount_amount_order > 0) {
            $content .= "<p>Mã giảm giá: $coupon_code</p>";
            $content .= "<p>Giảm giá: " . number_format($discount_amount_order, 0, ',', '.') . " VND</p>";
            $content .= "<p><strong>Thành tiền: " . number_format($final_total_order, 0, ',', '.') . " VND</strong></p>";
        }
        $content .= "<table>";

        // Record coupon usage if applied
        if ($coupon_id !== null) {
            recordCouponUsage($coupon_id, $id_order, $id_user, $discount_amount_order, $cart_totals, $final_total_order);
        }

        // Clear cart and coupon immediately after successful order - BEFORE redirect
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);

        // Redirect immediately to success page - don't wait for email to avoid hanging
        header("Location: order_success.php");

        // Flush output buffer and close connection to allow redirect
        if (ob_get_level()) {
            ob_end_flush();
        }

        // FastCGI: close connection and continue processing
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Send email in background (after redirect) - don't block user
        // Load PHPMailer classes
        $phpmailerPath = $_SERVER['DOCUMENT_ROOT'] . '/PHPMailer/src/';
        if (file_exists($phpmailerPath . 'Exception.php')) {
            require_once $phpmailerPath . 'Exception.php';
            require_once $phpmailerPath . 'PHPMailer.php';
            require_once $phpmailerPath . 'SMTP.php';
        }

        // Instantiation and passing `true` enables exceptions
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->SMTPSecure = "ssl";
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = '465';
            $mail->Username   = 'cake.sale.php@gmail.com';  // SMTP account username
            $mail->Password   = 'futsgvclaskmvsiw';
            $mail->SMTPKeepAlive = false; // Don't keep connection alive
            $mail->Mailer = "smtp";
            $mail->IsSMTP(); // telling the class to use SMTP  
            $mail->SMTPAuth   = true;                  // enable SMTP authentication  
            $mail->CharSet = 'utf-8';
            $mail->SMTPDebug  = 0;
            $mail->Timeout = 10; // Set timeout to avoid hanging

            //Recipients
            $mail->setFrom('cake@gmail.com', 'cake- shop');
            $mail->addAddress($email, $fullname);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Thông tin chi tiết đơn đặt hàng';
            $mail->Body    = $content;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            // Email failed - log but don't stop order
            error_log("Email sending failed: {$mail->ErrorInfo}");
        }

        exit();
    } else {
        echo '<script language="javascript">';
        echo 'alert("Đặt hàng thất bại!!!")';
        echo '</script>';
    }
}
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Thanh toán</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="./list_product.php">Cửa hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Shop Section Begin -->
<section class="shop spad">
    <?php if (isset($_SESSION['user'])) {  ?>
        <div class="container">
            <div class="shop__option">
                <div class="row">
                    <div class="col-lg-5 col-md-5">
                        <form method="post">
                            <div class="form-group">
                                <label for="full_name">Tên tài khoản</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['Fullname'] ?>" required placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['Email'] ?>" required placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="address">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" name="address" required placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="number_phone">SDT </label>
                                <input type="text" class="form-control" id="number_phone" name="number_phone" required placeholder="">
                            </div>
                            <div class="form-group">
                                <label for="note">Ghi chú</label>
                                <textarea type="text" class="form-control" id="note" name="note" required placeholder="Hàng dễ vỡ xin nhẹ tay !!!"> </textarea>
                            </div>
                            <div class="form-group">
                                <label for="coupon_code">Mã giảm giá (nếu có)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Nhập mã giảm giá" value="<?php echo isset($applied_coupon) ? $applied_coupon['code'] : (isset($_GET['coupon']) ? htmlspecialchars($_GET['coupon']) : ''); ?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" id="apply_coupon_btn">Áp dụng</button>
                                    </div>
                                </div>
                                <small id="coupon_message" class="form-text"></small>
                                <?php if (isset($applied_coupon)): ?>
                                    <div class="alert alert-success mt-2" id="coupon_success">
                                        <strong>✓ Mã giảm giá đã được áp dụng!</strong><br>
                                        <small>Giảm: <?php echo number_format($discount_amount, 0, ',', '.'); ?> VND</small>
                                        <button type="button" class="btn btn-sm btn-link p-0 ml-2" id="remove_coupon_btn">Xóa mã</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success" name="submit">Thanh toán</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-1 col-md-1">
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="table-responsive text-nowrap">
                            <h4 class="mb-4">Thông tin chi tiết sản phẩm</h4>
                            <table class="table" style="text-align: center">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Số lượng</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    <?php
                                    $stt = 1;
                                    foreach ($cart as $key => $value) : ?>
                                        <tr>
                                            <td><?php echo $stt++ ?></td>
                                            <td><?php echo $value['name'] ?></td>
                                            <td><?php echo $value['quantity'] ?></td>
                                            <td><?php echo $value['sellprice'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="4" class="text-right"><strong>Tổng tiền:</strong></td>
                                        <td class="text-center"><strong><?php echo number_format($cart_totals, 0, ',', '.'); ?> VND</strong></td>
                                    </tr>
                                    <?php if ($discount_amount > 0): ?>
                                        <tr class="text-success">
                                            <td colspan="4" class="text-right"><strong>Giảm giá (<?php echo isset($applied_coupon) ? $applied_coupon['code'] : ''; ?>):</strong></td>
                                            <td class="text-center"><strong>-<?php echo number_format($discount_amount, 0, ',', '.'); ?> VND</strong></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="bg-light">
                                        <td colspan="4" class="text-right"><strong>Thành tiền:</strong></td>
                                        <td class="text-center"><strong id="final_total_display"><?php echo number_format($final_total, 0, ',', '.'); ?> VND</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Đăng nhập để mua hàng</strong>
            <a href="login.php?action=checkout">Đăng nhập</a>
        </div>
    <?php } ?>
</section>
<!-- Shop Section End -->

<!-- Map End -->
<script>
    // Wait for jQuery to be loaded (from footer.php)
    (function() {
        function initCouponScript() {
            if (typeof jQuery === 'undefined') {
                setTimeout(initCouponScript, 100);
                return;
            }

            jQuery(document).ready(function($) {
                $('#apply_coupon_btn').click(function() {
                    var couponCode = $('#coupon_code').val().trim().toUpperCase();
                    var $message = $('#coupon_message');
                    var $btn = $(this);

                    if (!couponCode) {
                        $message.removeClass('text-success').addClass('text-danger').text('Vui lòng nhập mã giảm giá');
                        return;
                    }

                    $btn.prop('disabled', true).text('Đang kiểm tra...');
                    $message.text('');

                    $.ajax({
                        url: 'validate_coupon.php',
                        type: 'POST',
                        data: {
                            coupon_code: couponCode
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $message.removeClass('text-danger').addClass('text-success').text(response.message);
                                $('#coupon_success').remove();
                                var successHtml = '<div class="alert alert-success mt-2" id="coupon_success">' +
                                    '<strong>✓ Mã giảm giá đã được áp dụng!</strong><br>' +
                                    '<small>Giảm: ' + formatNumber(response.discount) + ' VND</small> ' +
                                    '<button type="button" class="btn btn-sm btn-link p-0 ml-2" id="remove_coupon_btn">Xóa mã</button>' +
                                    '</div>';
                                $('#coupon_code').closest('.form-group').append(successHtml);

                                // Update totals immediately without reload
                                var originalTotal = <?php echo $cart_totals; ?>;
                                var finalTotal = parseFloat(response.final_total) || 0;
                                var discountAmount = parseFloat(response.discount) || 0;

                                // Update final total display (Thành tiền) - find by ID
                                var $finalTotalDisplay = $('#final_total_display');
                                if ($finalTotalDisplay.length > 0) {
                                    var newTotalText = formatNumber(finalTotal) + ' VND';
                                    $finalTotalDisplay.text(newTotalText);
                                } else {
                                    // Try alternative selector
                                    $('tbody tr.bg-light td:last strong').text(formatNumber(finalTotal) + ' VND');
                                }

                                // Add or update discount row
                                var $discountRow = $('tbody tr.text-success');
                                if ($discountRow.length === 0) {
                                    // Add new discount row before the final total row (bg-light)
                                    var discountRow = '<tr class="text-success">' +
                                        '<td colspan="4" class="text-right"><strong>Giảm giá (' + couponCode + '):</strong></td>' +
                                        '<td class="text-center"><strong>-' + formatNumber(discountAmount) + ' VND</strong></td>' +
                                        '</tr>';
                                    var $finalRow = $('tbody tr.bg-light');
                                    if ($finalRow.length > 0) {
                                        $finalRow.before(discountRow);
                                    } else {
                                        // If bg-light not found, add before last row
                                        $('tbody tr:last').before(discountRow);
                                    }
                                } else {
                                    // Update existing discount row
                                    $discountRow.find('td:first').html('<strong>Giảm giá (' + couponCode + '):</strong>');
                                    $discountRow.find('td:last').html('<strong>-' + formatNumber(discountAmount) + ' VND</strong>');
                                }

                                // Update coupon code input value
                                $('#coupon_code').val(couponCode);

                                // Force update display - try multiple selectors
                                var totalText = formatNumber(finalTotal) + ' VND';

                                // Method 1: By ID
                                $('#final_total_display').text(totalText);

                                // Method 2: By parent row
                                $('tbody tr.bg-light td:last strong').text(totalText);

                                // Method 3: By text content
                                $('tbody tr.bg-light').find('strong').last().text(totalText);
                            } else {
                                $message.removeClass('text-success').addClass('text-danger').text(response.message);
                                // Remove success message if exists
                                $('#coupon_success').remove();
                            }
                            $btn.prop('disabled', false).text('Áp dụng');
                        },
                        error: function() {
                            $message.removeClass('text-success').addClass('text-danger').text('Có lỗi xảy ra, vui lòng thử lại');
                            $btn.prop('disabled', false).text('Áp dụng');
                        }
                    });
                });

                $(document).on('click', '#remove_coupon_btn', function() {
                    $.ajax({
                        url: 'validate_coupon.php',
                        type: 'POST',
                        data: {
                            remove_coupon: true
                        },
                        dataType: 'json',
                        success: function(response) {
                            // Remove coupon success message
                            $('#coupon_success').remove();
                            $('#coupon_message').removeClass('text-success text-danger').text('');

                            // Reset coupon code input
                            $('#coupon_code').val('');

                            // Remove discount row
                            $('tbody tr.text-success').remove();

                            // Reset totals to original
                            var originalTotal = <?php echo $cart_totals; ?>;
                            $('#final_total_display').text(formatNumber(originalTotal) + ' VND');
                        },
                        error: function() {
                            location.reload();
                        }
                    });
                });

                function formatNumber(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            });
        }

        // Start initialization
        initCouponScript();
    })();
</script>
<?php
include($_SERVER["DOCUMENT_ROOT"] . '/inc/footer.php');
?>