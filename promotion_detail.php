<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: promotions.php");
    exit;
}

// Get promotion
// After simplify script, Code and all coupon info is stored directly in promotions table
$query = "SELECT * FROM promotions WHERE PromotionId = $id AND IsActive = 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: promotions.php");
    exit;
}

$promo = mysqli_fetch_assoc($result);

// Update view count
mysqli_query($conn, "UPDATE promotions SET ViewCount = ViewCount + 1 WHERE PromotionId = $id");

// Get related products if any
$productsQuery = "SELECT p.*, c.CategoryName, b.BrandName
                  FROM products p
                  JOIN promotion_products pp ON p.ProductId = pp.ProductId
                  LEFT JOIN category c ON p.CategoriId = c.CategoryId
                  LEFT JOIN brands b ON p.BrandId = b.BrandId
                  WHERE pp.PromotionId = $id AND p.status = 1 AND p.is_accept = 1
                  LIMIT 6";
$productsResult = mysqli_query($conn, $productsQuery);

$daysLeft = floor((strtotime($promo['EndDate']) - time()) / 86400);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2><?php echo htmlspecialchars($promo['Title']); ?></h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="./promotions.php">Khuyến mãi</a>
                    <span>Chi tiết</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="promotion-detail-card">
                    <?php if ($promo['Image']) : ?>
                        <div class="promotion-detail-image mb-4">
                            <img src="../admin/uploads/promotions/<?php echo htmlspecialchars($promo['Image']); ?>" 
                                 alt="<?php echo htmlspecialchars($promo['Title']); ?>" class="img-fluid">
                        </div>
                    <?php endif; ?>
                    
                    <div class="promotion-discount-banner mb-4">
                        <?php if ($promo['DiscountType'] == 'percentage') : ?>
                            <h1 class="discount-text">GIẢM <?php echo $promo['DiscountValue']; ?>%</h1>
                        <?php elseif ($promo['DiscountType'] == 'fixed') : ?>
                            <h1 class="discount-text">GIẢM <?php echo number_format($promo['DiscountValue'], 0, ',', '.'); ?> VNĐ</h1>
                        <?php endif; ?>
                        <?php if ($daysLeft > 0) : ?>
                            <p class="countdown-text">Còn <?php echo $daysLeft; ?> ngày</p>
                        <?php endif; ?>
                    </div>
                    
                    <?php 
                    // Display coupon code prominently if exists
                    $couponCode = !empty($promo['Code']) ? $promo['Code'] : '';
                    if (!empty($couponCode)) : 
                    ?>
                        <div class="promotion-coupon-banner mb-4">
                            <div class="coupon-banner-content">
                                <div class="coupon-banner-icon">
                                    <i class="fa fa-ticket fa-3x"></i>
                                </div>
                                <div class="coupon-banner-text">
                                    <h3>Mã giảm giá của bạn</h3>
                                    <div class="coupon-banner-code">
                                        <span class="coupon-code-large"><?php echo htmlspecialchars($couponCode); ?></span>
                                        <button type="button" class="btn-copy-large" data-coupon="<?php echo htmlspecialchars($couponCode); ?>" title="Sao chép mã">
                                            <i class="fa fa-copy"></i> Sao chép
                                        </button>
                                    </div>
                                    <p class="coupon-banner-hint">
                                        <i class="fa fa-info-circle"></i> Nhập mã này khi thanh toán để được giảm giá
                                    </p>
                                    <a href="checkout.php?coupon=<?php echo urlencode($couponCode); ?>" class="btn btn-light btn-lg mt-3">
                                        <i class="fa fa-shopping-cart"></i> Áp dụng mã ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <h2><?php echo htmlspecialchars($promo['Title']); ?></h2>
                    
                    <?php if ($promo['Description']) : ?>
                        <p class="lead"><?php echo nl2br(htmlspecialchars($promo['Description'])); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($promo['Content']) : ?>
                        <div class="promotion-content">
                            <?php echo nl2br(htmlspecialchars($promo['Content'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="promotion-info mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Thời gian:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($promo['StartDate'])); ?> - 
                                <?php echo date('d/m/Y H:i', strtotime($promo['EndDate'])); ?></p>
                            </div>
                            <?php if ($promo['MinPurchase'] > 0) : ?>
                                <div class="col-md-6">
                                    <p><strong>Đơn hàng tối thiểu:</strong><br>
                                    <?php echo number_format($promo['MinPurchase'], 0, ',', '.'); ?> VNĐ</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="promotion-sidebar">
                    <div class="sidebar-card mb-4">
                        <h5>Thông tin khuyến mãi</h5>
                        <ul class="list-unstyled">
                            <li><i class="fa fa-calendar"></i> Bắt đầu: <?php echo date('d/m/Y', strtotime($promo['StartDate'])); ?></li>
                            <li><i class="fa fa-calendar-check-o"></i> Kết thúc: <?php echo date('d/m/Y', strtotime($promo['EndDate'])); ?></li>
                            <?php if ($daysLeft > 0) : ?>
                                <li><i class="fa fa-clock-o"></i> Còn lại: <?php echo $daysLeft; ?> ngày</li>
                            <?php else : ?>
                                <li><i class="fa fa-exclamation-triangle"></i> Đã kết thúc</li>
                            <?php endif; ?>
                            <li><i class="fa fa-eye"></i> Lượt xem: <?php echo $promo['ViewCount']; ?></li>
                        </ul>
                    </div>
                    
                    <?php 
                    // After simplify script, Code is stored directly in promotions table
                    $couponCode = !empty($promo['Code']) ? $promo['Code'] : '';
                    if (!empty($couponCode)) : 
                        $couponInfo = $promo; // All coupon info is now in promotions table
                    ?>
                        <div class="sidebar-card mb-4 coupon-sidebar">
                            <h5><i class="fa fa-ticket"></i> Mã giảm giá</h5>
                            <div class="coupon-code-display">
                                <div class="coupon-code-box-detail">
                                    <span class="coupon-code-text-detail" id="coupon_detail_<?php echo $promo['PromotionId']; ?>"><?php echo htmlspecialchars($couponCode); ?></span>
                                    <button type="button" class="btn-copy-coupon-detail" data-coupon="<?php echo htmlspecialchars($couponCode); ?>" data-toggle="tooltip" title="Sao chép mã">
                                        <i class="fa fa-copy"></i>
                                    </button>
                                </div>
                                
                                <?php if ($couponInfo) : ?>
                                    <div class="coupon-details mt-3">
                                        <?php if ($couponInfo['DiscountType'] == 'percentage') : ?>
                                            <p class="coupon-discount-info">
                                                <strong>Giảm <?php echo $couponInfo['DiscountValue']; ?>%</strong>
                                                <?php if ($couponInfo['MaxDiscount'] > 0) : ?>
                                                    <br><small>Tối đa <?php echo number_format($couponInfo['MaxDiscount'], 0, ',', '.'); ?> VNĐ</small>
                                                <?php endif; ?>
                                            </p>
                                        <?php else : ?>
                                            <p class="coupon-discount-info">
                                                <strong>Giảm <?php echo number_format($couponInfo['DiscountValue'], 0, ',', '.'); ?> VNĐ</strong>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if ($couponInfo['MinPurchase'] > 0) : ?>
                                            <p class="coupon-instruction">
                                                <small><i class="fa fa-info-circle"></i> Áp dụng cho đơn hàng từ <?php echo number_format($couponInfo['MinPurchase'], 0, ',', '.'); ?> VNĐ</small>
                                            </p>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($couponInfo['UsageLimit']) && $couponInfo['UsageLimit'] !== null) : ?>
                                            <p class="coupon-instruction">
                                                <small><i class="fa fa-users"></i> Còn <?php echo max(0, $couponInfo['UsageLimit'] - (isset($couponInfo['UsedCount']) ? $couponInfo['UsedCount'] : 0)); ?> lượt sử dụng</small>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php else : ?>
                                    <p class="coupon-instruction mt-3">
                                        <small><i class="fa fa-info-circle"></i> Nhập mã này khi thanh toán để được giảm giá</small>
                                    </p>
                                <?php endif; ?>
                                
                                <a href="checkout.php?coupon=<?php echo urlencode($couponCode); ?>" class="btn btn-success btn-block mt-3">
                                    <i class="fa fa-shopping-cart"></i> Áp dụng mã ngay
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <a href="list_product.php" class="btn btn-primary btn-block btn-lg">
                        <i class="fa fa-shopping-cart"></i> Mua ngay
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (mysqli_num_rows($productsResult) > 0) : ?>
            <div class="row mt-5">
                <div class="col-lg-12">
                    <h3 class="mb-4">Sản phẩm áp dụng khuyến mãi</h3>
                    <div class="row">
                        <?php while ($product = mysqli_fetch_assoc($productsResult)) : ?>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <div class="product__item">
                                    <div class="product__item__pic set-bg">
                                        <a href="product_detail.php?id=<?php echo $product['ProductId']; ?>">
                                            <img src="../admin/uploads/<?php echo htmlspecialchars($product['Image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['Name']); ?>">
                                        </a>
                                    </div>
                                    <div class="product__item__text">
                                        <h6>
                                            <a href="product_detail.php?id=<?php echo $product['ProductId']; ?>">
                                                <?php echo htmlspecialchars($product['Name']); ?>
                                            </a>
                                        </h6>
                                        <h5><?php echo number_format($product['SellPrice'], 0, ',', '.'); ?> VNĐ</h5>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.promotion-detail-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
}

.promotion-detail-image img {
    width: 100%;
    border-radius: 8px;
}

.promotion-discount-banner {
    background: linear-gradient(135deg, #f08632 0%, #dc3545 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
}

.discount-text {
    font-size: 48px;
    font-weight: 700;
    margin: 0;
}

.countdown-text {
    font-size: 18px;
    margin: 10px 0 0 0;
}

.promotion-content {
    line-height: 1.8;
    color: #666;
}

.promotion-sidebar {
    position: sticky;
    top: 20px;
}

.sidebar-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
}

.sidebar-card ul li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.sidebar-card ul li:last-child {
    border-bottom: none;
}

.sidebar-card ul li i {
    margin-right: 10px;
    color: #f08632;
    width: 20px;
}

.coupon-sidebar {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.coupon-sidebar h5 {
    color: white;
    margin-bottom: 15px;
}

.coupon-code-box-detail {
    display: flex;
    align-items: center;
    gap: 10px;
    background: white;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.coupon-code-text-detail {
    flex: 1;
    font-size: 24px;
    font-weight: 700;
    color: #28a745;
    letter-spacing: 3px;
    font-family: 'Courier New', monospace;
    text-align: center;
}

.btn-copy-coupon-detail {
    background: #28a745;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-copy-coupon-detail:hover {
    background: #218838;
    transform: scale(1.05);
}

.coupon-instruction {
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.promotion-coupon-banner {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 10px;
    padding: 30px;
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.coupon-banner-content {
    display: flex;
    align-items: center;
    gap: 30px;
}

.coupon-banner-icon {
    flex-shrink: 0;
}

.coupon-banner-text {
    flex: 1;
}

.coupon-banner-text h3 {
    color: white;
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: 700;
}

.coupon-banner-code {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
    background: white;
    padding: 15px 20px;
    border-radius: 8px;
}

.coupon-code-large {
    flex: 1;
    font-size: 32px;
    font-weight: 700;
    color: #28a745;
    letter-spacing: 4px;
    font-family: 'Courier New', monospace;
    text-align: center;
}

.btn-copy-large {
    background: #28a745;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    white-space: nowrap;
}

.btn-copy-large:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.coupon-banner-hint {
    color: rgba(255, 255, 255, 0.9);
    margin: 15px 0 0 0;
    font-size: 16px;
}

@media (max-width: 768px) {
    .coupon-banner-content {
        flex-direction: column;
        text-align: center;
    }
    
    .coupon-code-large {
        font-size: 24px;
    }
    
    .coupon-banner-code {
        flex-direction: column;
    }
}

.coupon-details {
    background: rgba(255, 255, 255, 0.1);
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}

.coupon-discount-info {
    color: white;
    font-size: 16px;
    margin-bottom: 10px;
}

.coupon-discount-info strong {
    font-size: 20px;
    display: block;
    margin-bottom: 5px;
}
</style>

<script>
$(document).ready(function() {
    // Copy coupon code functionality
    $('.btn-copy-coupon-detail, .btn-copy-large').click(function() {
        var couponCode = $(this).data('coupon');
        var $temp = $('<input>');
        $('body').append($temp);
        $temp.val(couponCode).select();
        document.execCommand('copy');
        $temp.remove();
        
        // Show feedback
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.html('<i class="fa fa-check"></i> Đã sao chép!').css('background', '#20c997');
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

