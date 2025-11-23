<?php
/**
 * Helper functions for coupon/discount code management
 */

/**
 * Validate and get coupon information
 * @param string $code Coupon code
 * @param float $cartTotal Total cart amount
 * @param int $customerId Customer ID (optional, for user limit check)
 * @return array|false Returns coupon data if valid, false otherwise
 */
function validateCoupon($code, $cartTotal = 0, $customerId = null) {
    global $conn;
    
    if (empty($code)) {
        return false;
    }
    
    $code = strtoupper(trim($code));
    
    // Use promotions table only (after simplify script)
    // Check if Code column exists in promotions
    $checkCodeColumnQuery = "SHOW COLUMNS FROM promotions LIKE 'Code'";
    $checkCodeColumnResult = mysqli_query($conn, $checkCodeColumnQuery);
    $hasCodeColumn = ($checkCodeColumnResult && mysqli_num_rows($checkCodeColumnResult) > 0);
    
    if (!$hasCodeColumn) {
        return false;
    }
    
    // Use promotions table
    // Dates in DB are already in Vietnam timezone, so compare directly with NOW()
    // MySQL NOW() is set to +07:00 in connect.php, so it returns Vietnam time
    $query = "SELECT * FROM promotions 
              WHERE Code = ? AND IsActive = 1 
              AND StartDate <= NOW() 
              AND EndDate >= NOW()";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        return false;
    }
    
    mysqli_stmt_bind_param($stmt, "s", $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $coupon = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Check usage limit
    if (isset($coupon['UsageLimit']) && $coupon['UsageLimit'] !== null && 
        isset($coupon['UsedCount']) && $coupon['UsedCount'] >= $coupon['UsageLimit']) {
        return ['error' => 'Mã giảm giá đã hết lượt sử dụng'];
    }
    
    // Check user limit (only if coupon_usage table exists)
    if ($customerId !== null && isset($coupon['UserLimit']) && $coupon['UserLimit'] !== null) {
        $checkUsageTableQuery = "SHOW TABLES LIKE 'coupon_usage'";
        $checkUsageTableResult = mysqli_query($conn, $checkUsageTableQuery);
        $usageTableExists = ($checkUsageTableResult && mysqli_num_rows($checkUsageTableResult) > 0);
        
        if ($usageTableExists) {
            $usageQuery = "SELECT COUNT(*) as count FROM coupon_usage 
                           WHERE PromotionId = ? AND CustomerId = ?";
            $usageStmt = mysqli_prepare($conn, $usageQuery);
            $couponId = $coupon['PromotionId'];
            mysqli_stmt_bind_param($usageStmt, "ii", $couponId, $customerId);
            mysqli_stmt_execute($usageStmt);
            $usageResult = mysqli_stmt_get_result($usageStmt);
            $usageData = mysqli_fetch_assoc($usageResult);
            mysqli_stmt_close($usageStmt);
            
            if ($usageData['count'] >= $coupon['UserLimit']) {
                return ['error' => 'Bạn đã sử dụng mã này đủ số lần cho phép'];
            }
        }
    }
    
    // Check minimum purchase
    if (isset($coupon['MinPurchase']) && $cartTotal < $coupon['MinPurchase']) {
        $minPurchaseFormatted = number_format($coupon['MinPurchase'], 0, ',', '.');
        return ['error' => "Đơn hàng tối thiểu {$minPurchaseFormatted} VND để sử dụng mã này"];
    }
    
    return $coupon;
}

/**
 * Calculate discount amount from coupon
 * @param array $coupon Coupon data
 * @param float $cartTotal Total cart amount
 * @return array ['discount' => float, 'final_total' => float]
 */
function calculateCouponDiscount($coupon, $cartTotal) {
    $discount = 0;
    
    // Ensure DiscountType and DiscountValue exist
    if (!isset($coupon['DiscountType']) || !isset($coupon['DiscountValue'])) {
        return [
            'discount' => 0,
            'final_total' => $cartTotal
        ];
    }
    
    $discountType = strtolower(trim($coupon['DiscountType']));
    $discountValue = floatval($coupon['DiscountValue']);
    
    if ($discountType == 'percentage') {
        // Percentage discount: e.g., 20% means DiscountValue = 20
        $discount = ($cartTotal * $discountValue) / 100;
        
        // Apply max discount if set
        if (isset($coupon['MaxDiscount']) && $coupon['MaxDiscount'] !== null && $discount > floatval($coupon['MaxDiscount'])) {
            $discount = floatval($coupon['MaxDiscount']);
        }
    } else {
        // Fixed amount discount: e.g., 100000 means 100,000 VND
        $discount = $discountValue;
        
        // Don't discount more than cart total
        if ($discount > $cartTotal) {
            $discount = $cartTotal;
        }
    }
    
    $finalTotal = $cartTotal - $discount;
    
    // Ensure final total is not negative
    if ($finalTotal < 0) {
        $finalTotal = 0;
        $discount = $cartTotal;
    }
    
    return [
        'discount' => round($discount, 2),
        'final_total' => round($finalTotal, 2)
    ];
}

/**
 * Record coupon usage
 * @param int $couponId Coupon ID
 * @param int $orderId Order ID
 * @param int|null $customerId Customer ID
 * @param float $discountAmount Discount amount
 * @param float $orderTotal Order total before discount
 * @param float $finalTotal Final total after discount
 * @return bool
 */
function recordCouponUsage($couponId, $orderId, $customerId, $discountAmount, $orderTotal, $finalTotal) {
    global $conn;
    
    // Check if coupon_usage table exists
    $checkUsageTableQuery = "SHOW TABLES LIKE 'coupon_usage'";
    $checkUsageTableResult = mysqli_query($conn, $checkUsageTableQuery);
    $usageTableExists = ($checkUsageTableResult && mysqli_num_rows($checkUsageTableResult) > 0);
    
    if ($usageTableExists) {
        // Insert usage record (using PromotionId)
        $query = "INSERT INTO coupon_usage (PromotionId, OderId, CustomerId, DiscountAmount, OrderTotal, FinalTotal) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "iiiddd", $couponId, $orderId, $customerId, $discountAmount, $orderTotal, $finalTotal);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $result = true; // Table doesn't exist, skip usage tracking
    }
    
    if ($result) {
        // Update coupon used count in promotions table
        $updateQuery = "UPDATE promotions SET UsedCount = UsedCount + 1 WHERE PromotionId = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "i", $couponId);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);
    }
    
    return $result;
}

