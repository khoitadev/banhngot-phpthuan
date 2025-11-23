<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM promotions WHERE PromotionId = $id");
    header("Location: promotion_list.php");
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total
$totalQuery = "SELECT COUNT(*) as total FROM promotions";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Get promotions
$query = "SELECT * FROM promotions 
          ORDER BY CreatedAt DESC
          LIMIT $offset, $limit";
$promotions = mysqli_query($conn, $query);
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Quản lý khuyến mãi</h4>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Danh sách khuyến mãi</h5>
                    <a href="promotion_add.php" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Thêm khuyến mãi
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Giảm giá</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th>Lượt xem</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($promotions) > 0) : ?>
                                <?php while ($promo = mysqli_fetch_assoc($promotions)) : 
                                    $now = time();
                                    $start = strtotime($promo['StartDate']);
                                    $end = strtotime($promo['EndDate']);
                                    $isActive = $promo['IsActive'] && $now >= $start && $now <= $end;
                                ?>
                                    <tr>
                                        <td><?php echo $promo['PromotionId']; ?></td>
                                        <td>
                                            <?php if ($promo['Image']) : ?>
                                                <img src="../../admin/uploads/promotions/<?php echo htmlspecialchars($promo['Image']); ?>" 
                                                     alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                            <?php else : ?>
                                                <span class="text-muted">Không có ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($promo['Title']); ?></strong>
                                            <?php if ($promo['IsFeatured']) : ?>
                                                <br><span class="badge bg-warning">Nổi bật</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($promo['DiscountType'] == 'percentage') : ?>
                                                <span class="badge bg-danger">-<?php echo $promo['DiscountValue']; ?>%</span>
                                            <?php elseif ($promo['DiscountType'] == 'fixed') : ?>
                                                <span class="badge bg-danger">-<?php echo number_format($promo['DiscountValue'], 0, ',', '.'); ?> VNĐ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small>
                                                <?php echo date('d/m/Y', strtotime($promo['StartDate'])); ?><br>
                                                đến <?php echo date('d/m/Y', strtotime($promo['EndDate'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($isActive) : ?>
                                                <span class="badge bg-success">Đang hoạt động</span>
                                            <?php elseif ($now < $start) : ?>
                                                <span class="badge bg-info">Sắp diễn ra</span>
                                            <?php elseif ($now > $end) : ?>
                                                <span class="badge bg-secondary">Đã kết thúc</span>
                                            <?php else : ?>
                                                <span class="badge bg-warning">Tạm dừng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $promo['ViewCount']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="promotion_add.php?id=<?php echo $promo['PromotionId']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $promo['PromotionId']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Xóa khuyến mãi này?')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">Chưa có khuyến mãi nào</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1) : ?>
                    <div class="card-footer">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php
                                $queryParams = $_GET;
                                unset($queryParams['page']);
                                
                                if ($page > 1) {
                                    $queryParams['page'] = $page - 1;
                                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">Trước</a></li>';
                                }
                                
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $queryParams['page'] = $i;
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                                }
                                
                                if ($page < $totalPages) {
                                    $queryParams['page'] = $page + 1;
                                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">Sau</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
?>

