<?php
// Include database connection first
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: review_list.php");
    exit;
}

// Handle approve/reject/delete BEFORE any output
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'approve':
            $query = "UPDATE reviews SET IsApproved = 1 WHERE ReviewId = $id";
            mysqli_query($conn, $query);
            $message = "Đã duyệt đánh giá thành công";
            break;
        case 'reject':
            // Từ chối = hủy duyệt (chuyển về chờ duyệt)
            $query = "UPDATE reviews SET IsApproved = 0 WHERE ReviewId = $id";
            mysqli_query($conn, $query);
            $message = "Đã hủy duyệt đánh giá";
            break;
        case 'unapprove':
            // Hủy duyệt (chuyển từ đã duyệt về chờ duyệt)
            $query = "UPDATE reviews SET IsApproved = 0 WHERE ReviewId = $id";
            mysqli_query($conn, $query);
            $message = "Đã hủy duyệt đánh giá";
            break;
        case 'delete':
            $query = "DELETE FROM reviews WHERE ReviewId = $id";
            mysqli_query($conn, $query);
            header("Location: review_list.php");
            exit;
    }
}

// Get review details
$query = "SELECT r.*, p.Name as ProductName, p.Image as ProductImage, p.ProductId,
                 c.Fullname, c.Email, c.CustomerId
          FROM reviews r
          JOIN products p ON r.ProductId = p.ProductId
          JOIN customers c ON r.CustomerId = c.CustomerId
          WHERE r.ReviewId = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: review_list.php");
    exit;
}

$review = mysqli_fetch_assoc($result);

// Get review images
$imagesQuery = "SELECT ImagePath FROM review_images WHERE ReviewId = $id";
$imagesResult = mysqli_query($conn, $imagesQuery);
$reviewImages = [];
if ($imagesResult) {
    while ($img = mysqli_fetch_assoc($imagesResult)) {
        $reviewImages[] = $img['ImagePath'];
    }
}

// Include header and navbar AFTER handling actions and getting data
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/review_helper.php");
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold py-3 mb-0">Chi tiết đánh giá #<?php echo $review['ReviewId']; ?></h4>
                <a href="review_list.php" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <?php if (isset($message)) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <!-- Review Content -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Nội dung đánh giá</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Đánh giá:</strong>
                                <div class="mt-2">
                                    <?php echo getStarRating($review['Rating']); ?>
                                    <span class="ms-2"><?php echo $review['Rating']; ?>/5 sao</span>
                                </div>
                            </div>

                            <?php if (!empty($review['Title'])) : ?>
                                <div class="mb-3">
                                    <strong>Tiêu đề:</strong>
                                    <p class="mt-1"><?php echo htmlspecialchars($review['Title']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <strong>Nội dung:</strong>
                                <p class="mt-1"><?php echo nl2br(htmlspecialchars($review['Comment'])); ?></p>
                            </div>

                            <?php if (!empty($reviewImages)) : ?>
                                <div class="mb-3">
                                    <strong>Hình ảnh đính kèm:</strong>
                                    <div class="row mt-2">
                                        <?php foreach ($reviewImages as $img) : ?>
                                            <div class="col-md-4 mb-3">
                                                <a href="../../admin/uploads/<?php echo htmlspecialchars($img); ?>"
                                                    data-lightbox="review-images"
                                                    data-title="Hình ảnh đánh giá">
                                                    <img src="../../admin/uploads/<?php echo htmlspecialchars($img); ?>"
                                                        alt="Review image"
                                                        class="img-thumbnail w-100"
                                                        style="height: 200px; object-fit: cover;">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Product Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="../../admin/uploads/<?php echo htmlspecialchars($review['ProductImage']); ?>"
                                    alt="<?php echo htmlspecialchars($review['ProductName']); ?>"
                                    class="img-thumbnail"
                                    style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                            <h6><?php echo htmlspecialchars($review['ProductName']); ?></h6>
                            <a href="../product_list.php" class="btn btn-sm btn-outline-primary">
                                Xem sản phẩm
                            </a>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Họ tên:</strong><br><?php echo htmlspecialchars($review['Fullname']); ?></p>
                            <p><strong>Email:</strong><br><?php echo htmlspecialchars($review['Email']); ?></p>
                            <?php if ($review['IsVerified']) : ?>
                                <p class="text-success">
                                    <i class="fa fa-check-circle"></i> Đã xác minh mua hàng
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Review Info -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Thông tin đánh giá</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Trạng thái:</strong><br>
                                <?php if ($review['IsApproved']) : ?>
                                    <span class="badge bg-success">Đã duyệt</span>
                                <?php else : ?>
                                    <span class="badge bg-warning">Chờ duyệt</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Ngày tạo:</strong><br><?php echo date('d/m/Y H:i:s', strtotime($review['CreatedAt'])); ?></p>
                            <?php if ($review['UpdatedAt']) : ?>
                                <p><strong>Ngày cập nhật:</strong><br><?php echo date('d/m/Y H:i:s', strtotime($review['UpdatedAt'])); ?></p>
                            <?php endif; ?>
                            <p><strong>Lượt hữu ích:</strong><br><?php echo $review['HelpfulCount']; ?></p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Thao tác</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="d-grid gap-2">
                                <?php if (!$review['IsApproved']) : ?>
                                    <button type="submit" name="action" value="approve" class="btn btn-success">
                                        <i class="fa fa-check"></i> Duyệt đánh giá
                                    </button>
                                <?php else : ?>
                                    <button type="submit" name="action" value="unapprove" class="btn btn-warning"
                                        onclick="return confirm('Hủy duyệt đánh giá này? Đánh giá sẽ chuyển về trạng thái chờ duyệt.')">
                                        <i class="fa fa-undo"></i> Hủy duyệt
                                    </button>
                                <?php endif; ?>
                                <button type="submit" name="action" value="delete" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                    <i class="fa fa-trash"></i> Xóa đánh giá
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .img-thumbnail {
        cursor: pointer;
        transition: transform 0.3s;
    }

    .img-thumbnail:hover {
        transform: scale(1.05);
    }
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
?>