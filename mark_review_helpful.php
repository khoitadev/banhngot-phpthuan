<?php
/**
 * Endpoint để xử lý đánh dấu review là "Hữu ích"
 * Method: POST
 * Parameters: review_id
 */

session_start();
header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['CustomerId'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Vui lòng đăng nhập để đánh dấu review hữu ích'
    ]);
    exit;
}

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

// Lấy dữ liệu
$reviewId = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
$customerId = isset($_SESSION['user']['CustomerId']) ? (int)$_SESSION['user']['CustomerId'] : 0;

if ($customerId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Thông tin khách hàng không hợp lệ'
    ]);
    exit;
}

if ($reviewId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Review ID không hợp lệ'
    ]);
    exit;
}

// Kết nối database
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

// Kiểm tra review có tồn tại không
$checkReviewQuery = "SELECT ReviewId FROM reviews WHERE ReviewId = $reviewId AND IsApproved = 1";
$checkReviewResult = mysqli_query($conn, $checkReviewQuery);

if (!$checkReviewResult || mysqli_num_rows($checkReviewResult) == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Review không tồn tại hoặc chưa được duyệt'
    ]);
    exit;
}

// Kiểm tra xem đã bấm "Hữu ích" chưa
$checkHelpfulQuery = "SELECT HelpfulId FROM review_helpful 
                      WHERE ReviewId = $reviewId AND CustomerId = $customerId";
$checkHelpfulResult = mysqli_query($conn, $checkHelpfulQuery);

if (mysqli_num_rows($checkHelpfulResult) > 0) {
    // Đã bấm rồi, xóa bỏ (unlike)
    $deleteQuery = "DELETE FROM review_helpful 
                    WHERE ReviewId = $reviewId AND CustomerId = $customerId";
    mysqli_query($conn, $deleteQuery);
    
    // Giảm HelpfulCount
    $updateCountQuery = "UPDATE reviews 
                        SET HelpfulCount = GREATEST(0, HelpfulCount - 1) 
                        WHERE ReviewId = $reviewId";
    mysqli_query($conn, $updateCountQuery);
    
    // Lấy HelpfulCount mới
    $getCountQuery = "SELECT HelpfulCount FROM reviews WHERE ReviewId = $reviewId";
    $getCountResult = mysqli_query($conn, $getCountQuery);
    $countData = mysqli_fetch_assoc($getCountResult);
    $newCount = (int)$countData['HelpfulCount'];
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã bỏ đánh dấu hữu ích',
        'helpful_count' => $newCount,
        'is_helpful' => false
    ]);
} else {
    // Chưa bấm, thêm mới (like)
    $insertQuery = "INSERT INTO review_helpful (ReviewId, CustomerId) 
                    VALUES ($reviewId, $customerId)";
    
    if (mysqli_query($conn, $insertQuery)) {
        // Tăng HelpfulCount
        $updateCountQuery = "UPDATE reviews 
                            SET HelpfulCount = HelpfulCount + 1 
                            WHERE ReviewId = $reviewId";
        mysqli_query($conn, $updateCountQuery);
        
        // Lấy HelpfulCount mới
        $getCountQuery = "SELECT HelpfulCount FROM reviews WHERE ReviewId = $reviewId";
        $getCountResult = mysqli_query($conn, $getCountQuery);
        $countData = mysqli_fetch_assoc($getCountResult);
        $newCount = (int)$countData['HelpfulCount'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã đánh dấu hữu ích',
            'helpful_count' => $newCount,
            'is_helpful' => true
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . mysqli_error($conn)
        ]);
    }
}

mysqli_close($conn);
?>

