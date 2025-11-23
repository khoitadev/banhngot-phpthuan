<?php
/**
 * Helper functions for product reviews
 */

/**
 * Get star rating HTML
 * @param float $rating Rating value (0-5)
 * @param bool $showNumber Show rating number
 * @return string HTML stars
 */
function getStarRating($rating, $showNumber = false) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    $html = '<div class="star-rating">';
    
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fa fa-star"></i>';
    }
    
    // Half star
    if ($halfStar) {
        $html .= '<i class="fa fa-star-half-o"></i>';
    }
    
    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="fa fa-star-o"></i>';
    }
    
    if ($showNumber) {
        $html .= '<span class="rating-number">' . number_format($rating, 1) . '</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get rating distribution
 * @param mysqli $conn Database connection
 * @param int $productId Product ID
 * @return array Distribution array
 */
function getRatingDistribution($conn, $productId) {
    $query = "SELECT Rating, COUNT(*) as count 
              FROM reviews 
              WHERE ProductId = $productId AND IsApproved = 1 
              GROUP BY Rating 
              ORDER BY Rating DESC";
    $result = mysqli_query($conn, $query);
    
    $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $total = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $distribution[$row['Rating']] = (int)$row['count'];
        $total += (int)$row['count'];
    }
    
    // Calculate percentages
    foreach ($distribution as $rating => $count) {
        $distribution[$rating] = [
            'count' => $count,
            'percentage' => $total > 0 ? round(($count / $total) * 100) : 0
        ];
    }
    
    return $distribution;
}

/**
 * Check if customer can review product
 * @param mysqli $conn Database connection
 * @param int $customerId Customer ID
 * @param int $productId Product ID
 * @return array ['can_review' => bool, 'order_id' => int|null, 'message' => string]
 */
function canCustomerReview($conn, $customerId, $productId) {
    // Check if already reviewed
    $checkQuery = "SELECT ReviewId FROM reviews 
                   WHERE CustomerId = $customerId AND ProductId = $productId";
    $checkResult = @mysqli_query($conn, $checkQuery);
    
    if ($checkResult && mysqli_num_rows($checkResult) > 0) {
        return [
            'can_review' => false,
            'order_id' => null,
            'message' => 'Bạn đã đánh giá sản phẩm này rồi'
        ];
    }
    
    // Check if customer has purchased this product
    // Note: Using Order_Detail_Id instead of OderId for orderdetails table
    $orderQuery = "SELECT DISTINCT o.OderId 
                   FROM oders o
                   JOIN orderdetails od ON o.OderId = od.Order_Detail_Id
                   WHERE o.CustomerId = $customerId 
                   AND od.ProductId = $productId 
                   AND o.status >= 3
                   ORDER BY o.order_date DESC
                   LIMIT 1";
    $orderResult = @mysqli_query($conn, $orderQuery);
    
    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
        $order = mysqli_fetch_assoc($orderResult);
        return [
            'can_review' => true,
            'order_id' => $order['OderId'],
            'message' => 'Bạn có thể đánh giá sản phẩm này'
        ];
    }
    
    return [
        'can_review' => false,
        'order_id' => null,
        'message' => 'Bạn cần mua sản phẩm này trước khi đánh giá'
    ];
}

