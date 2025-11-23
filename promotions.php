<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$itemsPerPage = 9;
$offset = ($page - 1) * $itemsPerPage;

// Check if Code column exists in promotions table (after simplify script)
$checkColumnQuery = "SHOW COLUMNS FROM promotions LIKE 'Code'";
$checkColumnResult = mysqli_query($conn, $checkColumnQuery);
$hasCodeColumn = ($checkColumnResult && mysqli_num_rows($checkColumnResult) > 0);

// Get active promotions
// After simplify script, Code is stored directly in promotions table
$query = "SELECT * FROM promotions 
          WHERE IsActive = 1 
          AND StartDate <= NOW() 
          AND EndDate >= NOW()
          ORDER BY IsFeatured DESC, SortOrder ASC, CreatedAt DESC
          LIMIT $offset, $itemsPerPage";
$promotionsResult = mysqli_query($conn, $query);

// Check for query errors
if (!$promotionsResult) {
    // Log error but don't show to user
    error_log("Promotions query error: " . mysqli_error($conn));
    // Fallback to simple query
    $query = "SELECT * FROM promotions 
              WHERE IsActive = 1 
              AND StartDate <= NOW() 
              AND EndDate >= NOW()
              ORDER BY IsFeatured DESC, SortOrder ASC, CreatedAt DESC
              LIMIT $offset, $itemsPerPage";
    $promotionsResult = mysqli_query($conn, $query);
}

// Get total count
$totalQuery = "SELECT COUNT(*) as total FROM promotions 
               WHERE IsActive = 1 
               AND StartDate <= NOW() 
               AND EndDate >= NOW()";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $itemsPerPage);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Khuyến mãi</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <span>Khuyến mãi</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <?php if (mysqli_num_rows($promotionsResult) > 0) : ?>
            <div class="row">
                <?php while ($promo = mysqli_fetch_assoc($promotionsResult)) : 
                    $daysLeft = floor((strtotime($promo['EndDate']) - time()) / 86400);
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="promotion-card">
                            <?php if ($promo['IsFeatured']) : ?>
                                <span class="featured-badge">Nổi bật</span>
                            <?php endif; ?>
                            
                            <?php if ($promo['Image']) : ?>
                                <div class="promotion-image">
                                    <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>">
                                        <img src="../admin/uploads/promotions/<?php echo htmlspecialchars($promo['Image']); ?>" 
                                             alt="<?php echo htmlspecialchars($promo['Title']); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <div class="promotion-content">
                                <div class="promotion-discount">
                                    <?php if ($promo['DiscountType'] == 'percentage') : ?>
                                        <span class="discount-badge">-<?php echo $promo['DiscountValue']; ?>%</span>
                                    <?php elseif ($promo['DiscountType'] == 'fixed') : ?>
                                        <span class="discount-badge">-<?php echo number_format($promo['DiscountValue'], 0, ',', '.'); ?> VNĐ</span>
                                    <?php endif; ?>
                                </div>
                                
                                <h4>
                                    <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>">
                                        <?php echo htmlspecialchars($promo['Title']); ?>
                                    </a>
                                </h4>
                                
                                <?php if ($promo['Description']) : ?>
                                    <p><?php echo htmlspecialchars(substr($promo['Description'], 0, 100)); ?><?php echo strlen($promo['Description']) > 100 ? '...' : ''; ?></p>
                                <?php endif; ?>
                                
                                <div class="promotion-meta">
                                    <div class="promotion-dates">
                                        <i class="fa fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($promo['StartDate'])); ?> - 
                                        <?php echo date('d/m/Y', strtotime($promo['EndDate'])); ?>
                                    </div>
                                    <?php if ($daysLeft > 0) : ?>
                                        <div class="promotion-countdown">
                                            <i class="fa fa-clock-o"></i> Còn <?php echo $daysLeft; ?> ngày
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php 
                                // After simplify script, Code is stored directly in promotions table
                                $couponCode = !empty($promo['Code']) ? $promo['Code'] : '';
                                if (!empty($couponCode)) : 
                                    $couponInfo = $promo; // All coupon info is now in promotions table
                                ?>
                                    <div class="promotion-coupon-code mb-3">
                                        <div class="coupon-display">
                                            <strong><i class="fa fa-ticket"></i> Mã giảm giá:</strong>
                                            <div class="coupon-code-box">
                                                <span class="coupon-code-text" id="coupon_<?php echo $promo['PromotionId']; ?>"><?php echo htmlspecialchars($couponCode); ?></span>
                                                <button type="button" class="btn-copy-coupon" data-coupon="<?php echo htmlspecialchars($couponCode); ?>" data-toggle="tooltip" title="Sao chép mã">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if ($couponInfo && isset($couponInfo['MinPurchase']) && $couponInfo['MinPurchase'] > 0) : ?>
                                            <div class="coupon-condition">
                                                <small><i class="fa fa-info-circle"></i> Áp dụng cho đơn hàng từ <?php echo number_format($couponInfo['MinPurchase'], 0, ',', '.'); ?> VNĐ</small>
                                            </div>
                                        <?php endif; ?>
                                        <a href="checkout.php?coupon=<?php echo urlencode($couponCode); ?>" class="btn btn-success btn-sm btn-block mt-2">
                                            <i class="fa fa-shopping-cart"></i> Áp dụng mã ngay
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>" class="btn btn-primary btn-sm">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1) : ?>
                <div class="shop__pagination mt-4">
                    <?php
                    $queryParams = $_GET;
                    unset($queryParams['page']);
                    
                    if ($page > 1) {
                        $queryParams['page'] = $page - 1;
                        echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-left"></span></a>';
                    }
                    
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $queryParams['page'] = $i;
                        $activeClass = $i == $page ? 'active' : '';
                        echo '<a href="?' . http_build_query($queryParams) . '" class="' . $activeClass . '">' . $i . '</a>';
                    }
                    
                    if ($page < $totalPages) {
                        $queryParams['page'] = $page + 1;
                        echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-right"></span></a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="alert alert-info text-center">
                <i class="fa fa-gift fa-3x mb-3"></i>
                <h4>Hiện chưa có khuyến mãi nào</h4>
                <p>Vui lòng quay lại sau!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.promotion-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s;
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.promotion-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}

.featured-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #f08632;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 2;
}

.promotion-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
    position: relative;
}

.promotion-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.promotion-card:hover .promotion-image img {
    transform: scale(1.1);
}

.promotion-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.promotion-discount {
    margin-bottom: 15px;
}

.discount-badge {
    display: inline-block;
    background: #dc3545;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    font-size: 18px;
    font-weight: 700;
}

.promotion-content h4 {
    margin-bottom: 10px;
}

.promotion-content h4 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.promotion-content h4 a:hover {
    color: #f08632;
}

.promotion-content p {
    color: #666;
    margin-bottom: 15px;
    flex: 1;
}

.promotion-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
    font-size: 14px;
    color: #666;
}

.promotion-countdown {
    color: #dc3545;
    font-weight: 600;
}

.promotion-coupon-code {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border: 2px dashed #28a745;
}

.coupon-display {
    margin-bottom: 10px;
}

.coupon-code-box {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 8px;
    background: white;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
}

.coupon-code-text {
    flex: 1;
    font-size: 18px;
    font-weight: 700;
    color: #28a745;
    letter-spacing: 2px;
    font-family: 'Courier New', monospace;
}

.coupon-condition {
    margin-top: 8px;
    padding: 8px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 4px;
    color: #155724;
}

.btn-copy-coupon {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-copy-coupon:hover {
    background: #218838;
    transform: scale(1.05);
}
</style>

<script>
$(document).ready(function() {
    // Copy coupon code functionality
    $('.btn-copy-coupon, .btn-copy-coupon-detail').click(function() {
        var couponCode = $(this).data('coupon');
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(couponCode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i>').css('background', '#28a745');
        setTimeout(function() {
            $btn.html(originalHtml).css('background', '');
        }, 2000);
        
        // Show toast notification
        alert('Đã sao chép mã: ' + couponCode);
    });
});
</script>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>

