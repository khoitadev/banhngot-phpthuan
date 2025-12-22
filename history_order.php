<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);

// Kết nối tới database và các file cần thiết
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
include($_SERVER['DOCUMENT_ROOT'] . "/classes/cart.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/order_status.php");

// Kiểm tra người dùng đăng nhập hay chưa
if (isset($_SESSION['user'])) {
    // Lấy thông tin người dùng đăng nhập
    $user = $_SESSION['user'];
    $id_user = $user['CustomerId'];

    // Truy vấn để lấy tất cả các đơn hàng của người dùng (group by order)
    // Include FinalTotal and DiscountAmount if they exist
    $query = "SELECT o.OderId, o.order_date, o.status, o.total_price, 
                     COALESCE(o.FinalTotal, o.total_price) as final_total,
                     COALESCE(o.DiscountAmount, 0) as discount_amount,
                     o.CouponCode, o.address, o.number_phone, o.note,
                     COUNT(od.Order_Detail_Id) as item_count
              FROM oders o
              LEFT JOIN orderdetails od ON o.OderId = od.Order_Detail_Id
              WHERE o.CustomerId = '$id_user'
              GROUP BY o.OderId
              ORDER BY o.order_date DESC";

    // Thực hiện truy vấn
    $ordersResult = mysqli_query($conn, $query);

?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="breadcrumb__text">
                        <h2>Lịch sử đơn hàng</h2>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="breadcrumb__links">
                        <a href="./index.php">Trang chủ</a>
                        <a href="./list_product.php">Cửa hàng</a>
                        <span>Lịch sử đơn hàng</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="shop spad">
        <div class="container">
            <?php if (mysqli_num_rows($ordersResult) > 0) : ?>
                <div class="row">
                    <?php while ($order = mysqli_fetch_assoc($ordersResult)) :
                        $statusInfo = getOrderStatus($order['status']);
                        $timeline = getOrderStatusTimeline($order['status']);

                        // Lấy danh sách sản phẩm đã được đánh giá bởi user này
                        $reviewedProductsQuery = "SELECT ProductId FROM reviews 
                                                  WHERE CustomerId = '$id_user' AND IsApproved = 1";
                        $reviewedProductsResult = mysqli_query($conn, $reviewedProductsQuery);
                        $reviewedProductIds = [];
                        if ($reviewedProductsResult) {
                            while ($row = mysqli_fetch_assoc($reviewedProductsResult)) {
                                $reviewedProductIds[] = $row['ProductId'];
                            }
                        }

                        // Lấy chi tiết sản phẩm trong đơn hàng
                        $detailQuery = "SELECT od.*, p.Name, p.Image 
                                       FROM orderdetails od
                                       JOIN products p ON od.ProductId = p.ProductId
                                       WHERE od.Order_Detail_Id = " . $order['OderId'];
                        $detailsResult = mysqli_query($conn, $detailQuery);
                    ?>
                        <div class="col-lg-12 mb-4">
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h4>Đơn hàng #<?php echo $order['OderId']; ?></h4>
                                            <p class="text-muted mb-0">
                                                <i class="fa fa-calendar"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-center">
                                            <?php echo getOrderStatusBadge($order['status']); ?>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <?php
                                            $displayTotal = isset($order['final_total']) ? $order['final_total'] : $order['total_price'];
                                            $discountAmount = isset($order['discount_amount']) ? floatval($order['discount_amount']) : 0;
                                            ?>
                                            <h5 class="text-primary mb-0">
                                                <?php echo number_format($displayTotal, 0, ',', '.'); ?> VNĐ
                                            </h5>
                                            <?php if ($discountAmount > 0) : ?>
                                                <small class="text-success">
                                                    <i class="fa fa-tag"></i> Đã giảm: <?php echo number_format($discountAmount, 0, ',', '.'); ?> VNĐ
                                                    <?php if (!empty($order['CouponCode'])) : ?>
                                                        (<?php echo htmlspecialchars($order['CouponCode']); ?>)
                                                    <?php endif; ?>
                                                </small><br>
                                            <?php endif; ?>
                                            <small class="text-muted"><?php echo $order['item_count']; ?> sản phẩm</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Timeline -->
                                <div class="order-timeline">
                                    <div class="timeline-steps">
                                        <?php foreach ($timeline as $step) :
                                            $stepClass = $step['current'] ? 'current' : ($step['active'] ? 'completed' : 'pending');
                                        ?>
                                            <div class="timeline-step <?php echo $stepClass; ?>">
                                                <div class="timeline-icon">
                                                    <i class="fa fa-<?php echo $step['icon']; ?>"></i>
                                                </div>
                                                <div class="timeline-label">
                                                    <small><?php echo $step['name']; ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Order Details -->
                                <div class="order-details">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h6 class="mb-3">Sản phẩm trong đơn hàng:</h6>
                                            <div class="order-items">
                                                <?php while ($item = mysqli_fetch_assoc($detailsResult)) :
                                                    $isReviewed = in_array($item['ProductId'], $reviewedProductIds);
                                                ?>
                                                    <div class="order-item">
                                                        <div class="row align-items-center">
                                                            <div class="col-2">
                                                                <img src="../admin/uploads/<?php echo htmlspecialchars($item['Image']); ?>"
                                                                    alt="<?php echo htmlspecialchars($item['Name']); ?>"
                                                                    class="img-thumbnail">
                                                            </div>
                                                            <div class="col-6">
                                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['Name']); ?></h6>
                                                                <small class="text-muted">Số lượng: <?php echo $item['Quantity']; ?></small>
                                                                <?php if ($isReviewed) : ?>
                                                                    <div class="mt-2">
                                                                        <span class="badge bg-success">
                                                                            <i class="fa fa-check-circle"></i> Đã đánh giá
                                                                        </span>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-4 text-right">
                                                                <strong><?php echo number_format($item['Price'] * $item['Quantity'], 0, ',', '.'); ?> VNĐ</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="order-info">
                                                <h6>Thông tin giao hàng:</h6>
                                                <p><strong>Địa chỉ:</strong><br><?php echo htmlspecialchars($order['address']); ?></p>
                                                <p><strong>Số điện thoại:</strong><br><?php echo htmlspecialchars($order['number_phone']); ?></p>
                                                <?php if (!empty($order['note'])) : ?>
                                                    <p><strong>Ghi chú:</strong><br><?php echo htmlspecialchars($order['note']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="order-footer">
                                    <a href="order_detail.php?id=<?php echo $order['OderId']; ?>" class="btn btn-primary">
                                        <i class="fa fa-eye"></i> Xem chi tiết
                                    </a>
                                    <?php if ($order['status'] == 3 || $order['status'] == 4) : ?>
                                        <a href="order_detail.php?id=<?php echo $order['OderId']; ?>&action=review" class="btn btn-success">
                                            <i class="fa fa-star"></i> Đánh giá sản phẩm
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($order['status'] == 0 || $order['status'] == 1) : ?>
                                        <a href="order_detail.php?id=<?php echo $order['OderId']; ?>&action=cancel"
                                            class="btn btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                            <i class="fa fa-times"></i> Hủy đơn hàng
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="alert alert-info text-center">
                    <i class="fa fa-shopping-cart fa-3x mb-3"></i>
                    <h4>Bạn chưa có đơn hàng nào</h4>
                    <p>Hãy bắt đầu mua sắm ngay!</p>
                    <a href="list_product.php" class="btn btn-primary">Mua sắm ngay</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <style>
        .order-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
        }

        .order-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .order-timeline {
            margin: 20px 0;
            padding: 20px 0;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        .timeline-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            padding: 0 20px;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-step {
            position: relative;
            z-index: 1;
            text-align: center;
            flex: 1;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #999;
            transition: all 0.3s;
        }

        .timeline-step.completed .timeline-icon {
            background: #28a745;
            color: #fff;
        }

        .timeline-step.current .timeline-icon {
            background: #007bff;
            color: #fff;
            animation: pulse 2s infinite;
        }

        .timeline-step.pending .timeline-icon {
            background: #e0e0e0;
            color: #999;
        }

        .timeline-label {
            font-size: 12px;
            color: #666;
        }

        .timeline-step.completed .timeline-label {
            color: #28a745;
            font-weight: 600;
        }

        .timeline-step.current .timeline-label {
            color: #007bff;
            font-weight: 600;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .order-items {
            max-height: 300px;
            overflow-y: auto;
        }

        .order-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .order-info p {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .order-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .order-footer .btn {
            margin-right: 10px;
        }

        .badge {
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>

<?php
} else {
    // Nếu người dùng chưa đăng nhập
?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h2>Lịch sử đơn hàng</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="shop spad">
        <div class="container">
            <div class="alert alert-warning text-center">
                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>Vui lòng đăng nhập</h4>
                <p>Bạn cần đăng nhập để xem lịch sử đơn hàng.</p>
                <a href="login.php" class="btn btn-primary">Đăng nhập ngay</a>
            </div>
        </div>
    </section>
<?php
}

// Bao gồm phần footer
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>