<?php
ob_start();
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user = $_SESSION['user'];
$customerId = $user['CustomerId'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$title = isset($_POST['title']) ? mysqli_real_escape_string($conn, trim($_POST['title'])) : '';
$comment = isset($_POST['comment']) ? mysqli_real_escape_string($conn, trim($_POST['comment'])) : '';

// Validation
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn điểm đánh giá']);
    exit;
}

if (empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung đánh giá']);
    exit;
}

// Check if already reviewed
$checkQuery = "SELECT ReviewId FROM reviews 
               WHERE CustomerId = $customerId AND ProductId = $productId";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi']);
    exit;
}

// Verify order (optional but recommended)
$isVerified = 0;
if ($orderId > 0) {
    $verifyQuery = "SELECT o.OderId 
                     FROM oders o
                     JOIN orderdetails od ON o.OderId = od.Order_Detail_Id
                     WHERE o.CustomerId = $customerId 
                     AND od.ProductId = $productId 
                     AND o.OderId = $orderId
                     AND o.status >= 3";
    $verifyResult = mysqli_query($conn, $verifyQuery);
    $isVerified = mysqli_num_rows($verifyResult) > 0 ? 1 : 0;
}

// Insert review
$insertQuery = "INSERT INTO reviews (ProductId, CustomerId, OrderId, Rating, Title, Comment, IsVerified, IsApproved) 
                VALUES ($productId, $customerId, " . ($orderId > 0 ? $orderId : 'NULL') . ", $rating, 
                        '$title', '$comment', $isVerified, 1)";

if (mysqli_query($conn, $insertQuery)) {
    $reviewId = mysqli_insert_id($conn);
    
    // Handle image uploads
    $uploadedImages = [];
    if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/admin/uploads/reviews/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $imagePaths = [];
        foreach ($_FILES['review_images']['name'] as $key => $filename) {
            if ($_FILES['review_images']['error'][$key] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['review_images']['tmp_name'][$key];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $newFilename = 'review_' . $reviewId . '_' . time() . '_' . $key . '.' . $extension;
                $targetPath = $uploadDir . $newFilename;
                
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $imagePath = 'reviews/' . $newFilename;
                    $imagePaths[] = $imagePath;
                    
                    // Insert into review_images table
                    $imgQuery = "INSERT INTO review_images (ReviewId, ImagePath) VALUES ($reviewId, '$imagePath')";
                    mysqli_query($conn, $imgQuery);
                }
            }
        }
        
        if (!empty($imagePaths)) {
            $imagesJson = json_encode($imagePaths);
            $updateQuery = "UPDATE reviews SET Images = '$imagesJson' WHERE ReviewId = $reviewId";
            mysqli_query($conn, $updateQuery);
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được gửi.',
        'review_id' => $reviewId
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra. Vui lòng thử lại.']);
}

