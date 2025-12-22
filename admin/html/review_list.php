<?php
// Handle approve/reject/delete BEFORE any output
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $reviewId = (int)$_GET['id'];

    switch ($action) {
        case 'approve':
            $query = "UPDATE reviews SET IsApproved = 1 WHERE ReviewId = $reviewId";
            mysqli_query($conn, $query);
            break;
        case 'reject':
            // Từ chối = hủy duyệt (chuyển về chờ duyệt)
            $query = "UPDATE reviews SET IsApproved = 0 WHERE ReviewId = $reviewId";
            mysqli_query($conn, $query);
            break;
        case 'unapprove':
            // Hủy duyệt (chuyển từ đã duyệt về chờ duyệt)
            $query = "UPDATE reviews SET IsApproved = 0 WHERE ReviewId = $reviewId";
            mysqli_query($conn, $query);
            break;
        case 'delete':
            $query = "DELETE FROM reviews WHERE ReviewId = $reviewId";
            mysqli_query($conn, $query);
            break;
    }

    header("Location: review_list.php");
    exit;
}

// Include header and navbar AFTER handling actions
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/review_helper.php");

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // all, approved, pending
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build query
$whereConditions = [];
if ($filter == 'approved') {
    $whereConditions[] = "r.IsApproved = 1";
} elseif ($filter == 'pending') {
    $whereConditions[] = "r.IsApproved = 0";
}

if (!empty($search)) {
    $whereConditions[] = "(p.Name LIKE '%$search%' OR c.Fullname LIKE '%$search%' OR r.Comment LIKE '%$search%')";
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count
$countQuery = "SELECT COUNT(*) as total 
               FROM reviews r
               JOIN products p ON r.ProductId = p.ProductId
               JOIN customers c ON r.CustomerId = c.CustomerId
               $whereClause";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Get reviews
$query = "SELECT r.*, p.Name as ProductName, p.Image as ProductImage, c.Fullname, c.Email
          FROM reviews r
          JOIN products p ON r.ProductId = p.ProductId
          JOIN customers c ON r.CustomerId = c.CustomerId
          $whereClause
          ORDER BY r.CreatedAt DESC
          LIMIT $offset, $limit";
$reviewsResult = mysqli_query($conn, $query);
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Quản lý đánh giá</h4>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <select name="filter" class="form-select">
                                <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>Tất cả</option>
                                <option value="approved" <?php echo $filter == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                <option value="pending" <?php echo $filter == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm theo sản phẩm, khách hàng, nội dung..."
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reviews Table -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Khách hàng</th>
                                <th>Đánh giá</th>
                                <th>Nội dung</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($reviewsResult) > 0) : ?>
                                <?php while ($review = mysqli_fetch_assoc($reviewsResult)) : ?>
                                    <tr>
                                        <td><?php echo $review['ReviewId']; ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../../admin/uploads/<?php echo htmlspecialchars($review['ProductImage']); ?>"
                                                    alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; margin-right: 10px;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($review['ProductName']); ?></strong>
                                                    <?php if ($review['IsVerified']) : ?>
                                                        <br><small class="text-success"><i class="fa fa-check-circle"></i> Đã mua hàng</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($review['Fullname']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($review['Email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php echo getStarRating($review['Rating']); ?>
                                            <br><small class="text-muted"><?php echo $review['Rating']; ?>/5</small>
                                        </td>
                                        <td>
                                            <?php if (!empty($review['Title'])) : ?>
                                                <strong><?php echo htmlspecialchars($review['Title']); ?></strong><br>
                                            <?php endif; ?>
                                            <small><?php echo htmlspecialchars(substr($review['Comment'], 0, 100)); ?><?php echo strlen($review['Comment']) > 100 ? '...' : ''; ?></small>
                                        </td>
                                        <td>
                                            <?php if ($review['IsApproved']) : ?>
                                                <span class="badge bg-success">Đã duyệt</span>
                                            <?php else : ?>
                                                <span class="badge bg-warning">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($review['CreatedAt'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="review_detail.php?id=<?php echo $review['ReviewId']; ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if (!$review['IsApproved']) : ?>
                                                    <a href="?action=approve&id=<?php echo $review['ReviewId']; ?>"
                                                        class="btn btn-sm btn-success"
                                                        onclick="return confirm('Duyệt đánh giá này?')"
                                                        title="Duyệt đánh giá">
                                                        <i class="fa fa-check"></i> Duyệt
                                                    </a>
                                                <?php else : ?>
                                                    <a href="?action=unapprove&id=<?php echo $review['ReviewId']; ?>"
                                                        class="btn btn-sm btn-warning"
                                                        onclick="return confirm('Hủy duyệt đánh giá này? Đánh giá sẽ chuyển về trạng thái chờ duyệt.')"
                                                        title="Hủy duyệt">
                                                        <i class="fa fa-undo"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="?action=delete&id=<?php echo $review['ReviewId']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Xóa đánh giá này?')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">Không có đánh giá nào</td>
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

                                // Previous
                                if ($page > 1) {
                                    $queryParams['page'] = $page - 1;
                                    echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($queryParams) . '">Trước</a></li>';
                                }

                                // Pages
                                for ($i = 1; $i <= $totalPages; $i++) {
                                    $queryParams['page'] = $i;
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($queryParams) . '">' . $i . '</a></li>';
                                }

                                // Next
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