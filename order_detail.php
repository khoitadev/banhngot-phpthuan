<?php
ob_start();
error_reporting(E_ERROR | E_PARSE);

include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/order_status.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$id_user = $user['CustomerId'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($order_id == 0) {
    header("Location: history_order.php");
    exit;
}

// Kiểm tra đơn hàng thuộc về user này
$orderQuery = "SELECT o.*, c.Fullname, c.Email 
               FROM oders o
               JOIN customers c ON o.CustomerId = c.CustomerId
               WHERE o.OderId = $order_id AND o.CustomerId = $id_user";
$orderResult = mysqli_query($conn, $orderQuery);

if (mysqli_num_rows($orderResult) == 0) {
    header("Location: history_order.php");
    exit;
}

$order = mysqli_fetch_assoc($orderResult);
$statusInfo = getOrderStatus($order['status']);
$timeline = getOrderStatusTimeline($order['status']);

// Xử lý hủy đơn hàng
if ($action == 'cancel' && isset($_POST['cancel_reason'])) {
    if ($order['status'] == 0 || $order['status'] == 1) {
        $cancelReason = mysqli_real_escape_string($conn, $_POST['cancel_reason']);
        $updateQuery = "UPDATE oders SET status = -1, cancelled_reason = '$cancelReason', cancelled_date = NOW() 
                       WHERE OderId = $order_id";
        if (mysqli_query($conn, $updateQuery)) {
            // Lưu lịch sử
            $historyQuery = "INSERT INTO order_status_history (OderId, OldStatus, NewStatus, StatusName, ChangedBy, Note) 
                            VALUES ($order_id, {$order['status']}, -1, 'Đã hủy', 'Customer', '$cancelReason')";
            mysqli_query($conn, $historyQuery);
            
            header("Location: order_detail.php?id=$order_id");
            exit;
        }
    }
}

// Lấy chi tiết sản phẩm
$detailQuery = "SELECT od.*, p.Name, p.Image, p.ProductId
                FROM orderdetails od
                JOIN products p ON od.ProductId = p.ProductId
                WHERE od.Order_Detail_Id = $order_id";
$detailsResult = mysqli_query($conn, $detailQuery);

// Lấy lịch sử thay đổi trạng thái
$historyQuery = "SELECT * FROM order_status_history 
                 WHERE OderId = $order_id 
                 ORDER BY CreatedAt DESC";
$historyResult = mysqli_query($conn, $historyQuery);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Chi tiết đơn hàng #<?php echo $order_id; ?></h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="./history_order.php">Lịch sử đơn hàng</a>
                    <span>Chi tiết đơn hàng</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Order Header -->
                <div class="order-detail-card mb-4">
                    <div class="order-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4>Đơn hàng #<?php echo $order_id; ?></h4>
                                <p class="text-muted mb-0">
                                    <i class="fa fa-calendar"></i> 
                                    Đặt ngày: <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?>
                                </p>
                            </div>
                            <div class="col-md-3 text-center">
                                <?php echo getOrderStatusBadge($order['status']); ?>
                            </div>
                            <div class="col-md-3 text-right">
                                <?php 
                                $displayTotal = isset($order['FinalTotal']) ? $order['FinalTotal'] : $order['total_price'];
                                $discountAmount = isset($order['DiscountAmount']) ? floatval($order['DiscountAmount']) : 0;
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
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Timeline -->
                    <div class="order-timeline">
                        <h6 class="mb-3">Tiến trình đơn hàng:</h6>
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
                </div>
                
                <!-- Order Items -->
                <div class="order-detail-card mb-4">
                    <h5 class="mb-3">Sản phẩm trong đơn hàng</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                while ($item = mysqli_fetch_assoc($detailsResult)) : 
                                    $itemTotal = $item['Price'] * $item['Quantity'];
                                    $total += $itemTotal;
                                ?>
                                    <tr>
                                        <td>
                                            <img src="../admin/uploads/<?php echo htmlspecialchars($item['Image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['Name']); ?>"
                                                 class="img-thumbnail" style="width: 80px;">
                                        </td>
                                        <td>
                                            <a href="product_detail.php?id=<?php echo $item['ProductId']; ?>">
                                                <?php echo htmlspecialchars($item['Name']); ?>
                                            </a>
                                        </td>
                                        <td class="text-center"><?php echo $item['Quantity']; ?></td>
                                        <td class="text-right"><?php echo number_format($item['Price'], 0, ',', '.'); ?> VNĐ</td>
                                        <td class="text-right"><strong><?php echo number_format($itemTotal, 0, ',', '.'); ?> VNĐ</strong></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="table-info">
                                    <td colspan="4" class="text-right"><strong>Tổng tiền:</strong></td>
                                    <td class="text-right"><strong><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</strong></td>
                                </tr>
                                <?php 
                                $orderDiscount = isset($order['DiscountAmount']) ? floatval($order['DiscountAmount']) : 0;
                                $orderFinalTotal = isset($order['FinalTotal']) ? floatval($order['FinalTotal']) : $total;
                                if ($orderDiscount > 0) : 
                                ?>
                                <tr class="text-success">
                                    <td colspan="4" class="text-right">
                                        <strong>Giảm giá 
                                        <?php if (!empty($order['CouponCode'])) : ?>
                                            (<?php echo htmlspecialchars($order['CouponCode']); ?>)
                                        <?php endif; ?>:
                                        </strong>
                                    </td>
                                    <td class="text-right"><strong>-<?php echo number_format($orderDiscount, 0, ',', '.'); ?> VNĐ</strong></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="bg-light">
                                    <td colspan="4" class="text-right"><strong>Thành tiền:</strong></td>
                                    <td class="text-right"><strong><?php echo number_format($orderFinalTotal, 0, ',', '.'); ?> VNĐ</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Order Information -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="order-detail-card">
                            <h5 class="mb-3">Thông tin giao hàng</h5>
                            <p><strong>Người nhận:</strong><br><?php echo htmlspecialchars($order['Fullname']); ?></p>
                            <p><strong>Địa chỉ:</strong><br><?php echo htmlspecialchars($order['address']); ?></p>
                            <p><strong>Số điện thoại:</strong><br><?php echo htmlspecialchars($order['number_phone']); ?></p>
                            <?php if (!empty($order['note'])) : ?>
                                <p><strong>Ghi chú:</strong><br><?php echo htmlspecialchars($order['note']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="order-detail-card">
                            <h5 class="mb-3">Lịch sử thay đổi</h5>
                            <?php if (mysqli_num_rows($historyResult) > 0) : ?>
                                <div class="status-history">
                                    <?php while ($history = mysqli_fetch_assoc($historyResult)) : ?>
                                        <div class="history-item">
                                            <div class="history-status">
                                                <strong><?php echo htmlspecialchars($history['StatusName']); ?></strong>
                                            </div>
                                            <div class="history-meta">
                                                <small>
                                                    <?php echo date('d/m/Y H:i', strtotime($history['CreatedAt'])); ?>
                                                    <?php if ($history['ChangedBy']) : ?>
                                                        - <?php echo htmlspecialchars($history['ChangedBy']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <?php if (!empty($history['Note'])) : ?>
                                                <div class="history-note">
                                                    <small><?php echo htmlspecialchars($history['Note']); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else : ?>
                                <p class="text-muted">Chưa có lịch sử thay đổi</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="order-detail-card">
                    <div class="order-actions">
                        <a href="history_order.php" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Quay lại
                        </a>
                        <?php if ($order['status'] == 3 || $order['status'] == 4) : 
                            // Get first product that can be reviewed
                            $reviewProductQuery = "SELECT od.ProductId 
                                                  FROM orderdetails od
                                                  LEFT JOIN reviews r ON od.ProductId = r.ProductId AND r.CustomerId = $id_user AND r.OrderId = $order_id
                                                  WHERE od.Order_Detail_Id = $order_id AND r.ReviewId IS NULL
                                                  LIMIT 1";
                            $reviewProductResult = mysqli_query($conn, $reviewProductQuery);
                            if (mysqli_num_rows($reviewProductResult) > 0) {
                                $reviewProduct = mysqli_fetch_assoc($reviewProductResult);
                        ?>
                            <a href="review_form.php?product_id=<?php echo $reviewProduct['ProductId']; ?>&order_id=<?php echo $order_id; ?>" class="btn btn-success">
                                <i class="fa fa-star"></i> Đánh giá sản phẩm
                            </a>
                        <?php 
                            } else {
                        ?>
                            <span class="btn btn-secondary disabled">
                                <i class="fa fa-check"></i> Đã đánh giá tất cả sản phẩm
                            </span>
                        <?php 
                            }
                        endif; 
                        ?>
                        <?php if ($order['status'] == 0 || $order['status'] == 1) : ?>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#cancelModal">
                                <i class="fa fa-times"></i> Hủy đơn hàng
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Cancel Order Modal -->
<?php if ($order['status'] == 0 || $order['status'] == 1) : ?>
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy đơn hàng</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="?id=<?php echo $order_id; ?>&action=cancel">
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn hủy đơn hàng này?</p>
                    <div class="form-group">
                        <label>Lý do hủy đơn hàng:</label>
                        <textarea name="cancel_reason" class="form-control" rows="3" required 
                                  placeholder="Vui lòng nhập lý do hủy đơn hàng..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.order-detail-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 25px;
}

.order-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.order-timeline {
    margin: 20px 0;
    padding: 20px 0;
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

.timeline-step.completed .timeline-label,
.timeline-step.current .timeline-label {
    font-weight: 600;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.status-history {
    max-height: 300px;
    overflow-y: auto;
}

.history-item {
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 10px;
}

.history-item:last-child {
    border-bottom: none;
}

.history-status {
    color: #007bff;
    font-weight: 600;
}

.history-meta {
    color: #666;
    margin-top: 5px;
}

.history-note {
    margin-top: 5px;
    padding: 5px;
    background: #f8f9fa;
    border-radius: 3px;
}

.order-actions {
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.order-actions .btn {
    margin-right: 10px;
}
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>

