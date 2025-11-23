<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/coupon_helper.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/cart.php");

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'discount' => 0, 'final_total' => 0];

// Handle remove coupon
if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    $response['success'] = true;
    $response['message'] = 'Đã xóa mã giảm giá';
    echo json_encode($response);
    exit;
}

if (!isset($_POST['coupon_code'])) {
    $response['message'] = 'Vui lòng nhập mã giảm giá';
    echo json_encode($response);
    exit;
}

$couponCode = strtoupper(trim($_POST['coupon_code']));
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$customerId = isset($_SESSION['user']) ? $_SESSION['user']['CustomerId'] : null;

// Calculate cart total
$class = new Cart();
$cartTotal = $class->total_price($cart);

// Validate coupon
$coupon = validateCoupon($couponCode, $cartTotal, $customerId);

if ($coupon === false) {
    $response['message'] = 'Mã giảm giá không hợp lệ hoặc đã hết hạn';
} elseif (isset($coupon['error'])) {
    $response['message'] = $coupon['error'];
} else {
    // Calculate discount
    $discountInfo = calculateCouponDiscount($coupon, $cartTotal);
    
    $response['success'] = true;
    $response['message'] = 'Áp dụng mã giảm giá thành công!';
    $response['discount'] = floatval($discountInfo['discount']);
    $response['final_total'] = floatval($discountInfo['final_total']);
    $response['coupon_name'] = isset($coupon['Name']) ? $coupon['Name'] : (isset($coupon['Title']) ? $coupon['Title'] : '');
    $response['discount_type'] = isset($coupon['DiscountType']) ? $coupon['DiscountType'] : '';
    $response['discount_value'] = isset($coupon['DiscountValue']) ? floatval($coupon['DiscountValue']) : 0;
    $response['cart_total'] = floatval($cartTotal);
    
    // Use PromotionId from promotions table
    $couponId = $coupon['PromotionId'];
    $response['coupon_id'] = $couponId;
    
    // Store coupon in session for later use
    $_SESSION['applied_coupon'] = [
        'code' => $couponCode,
        'id' => $couponId,
        'discount' => $discountInfo['discount'],
        'final_total' => $discountInfo['final_total']
    ];
}

echo json_encode($response);

