<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/review_helper.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT a.ProductId, a.Name, a.Image, c.CategoryName, b.BrandName, a.BuyPrice,a.SellPrice, 
                     a.CountView, a.Status, a.Description, a.Quantity, a.AverageRating, a.TotalReviews
                FROM `products` a, category c, brands b 
                WHERE a.CategoriId = c.CategoryId and a.BrandId = b.BrandId and a.ProductId = '$id'";

    $data = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($data);

    // Get reviews - with error handling
    $reviewsResult = false;
    $reviewsQuery = "SELECT r.*, c.Fullname, c.Email 
                     FROM reviews r
                     JOIN customers c ON r.CustomerId = c.CustomerId
                     WHERE r.ProductId = $id AND r.IsApproved = 1
                     ORDER BY r.CreatedAt DESC
                     LIMIT 10";
    $reviewsResult = @mysqli_query($conn, $reviewsQuery);
    if (!$reviewsResult) {
        $reviewsResult = false; // Set to false if query fails
    }

    // Get rating distribution - with error handling
    $ratingDistribution = [];
    try {
        $ratingDistribution = @getRatingDistribution($conn, $id);
        if (!$ratingDistribution) {
            $ratingDistribution = [];
        }
    } catch (Exception $e) {
        $ratingDistribution = [];
    }

    // Check if user can review - with error handling
    $canReview = ['can_review' => false, 'order_id' => null, 'message' => ''];
    if (isset($_SESSION['user'])) {
        try {
            $canReview = @canCustomerReview($conn, $_SESSION['user']['CustomerId'], $id);
            if (!$canReview) {
                $canReview = ['can_review' => false, 'order_id' => null, 'message' => ''];
            }
        } catch (Exception $e) {
            $canReview = ['can_review' => false, 'order_id' => null, 'message' => ''];
        }
    }

    //Hiển thị sản phẩm tương tự
    $query1 = "SELECT *from products where status = 1 and is_accept = 1 ORDER BY RAND()";
    $data1 = mysqli_query($conn, $query1);


    if (isset($_GET['id'])) {

        $id = $_GET['id'];
        $query2 = "UPDATE products set CountView = CountView+1 where ProductId = '$id'";
        $data2 = mysqli_query($conn, $query2);
    }
}

?>

<!-- Breadcrumb Begin -->
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Chi tiết sản phẩm</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="./shop.html">Cửa hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->


<section class="product-details spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product__details__img">
                    <div class="product__details__big__img">
                        <img class="big_img" src="..//admin//uploads//<?php echo $product['Image'] ?>" alt="">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <form action="cart.php" method="GET">
                    <div class="product__details__text">
                        <h4><?php echo $product['Name'] ?></h4>

                        <!-- Rating Display -->
                        <?php if ($product['AverageRating'] > 0) : ?>
                            <div class="product-rating mb-3">
                                <?php echo getStarRating($product['AverageRating'], true); ?>
                                <span class="rating-count">(<?php echo $product['TotalReviews']; ?> đánh giá)</span>
                            </div>
                        <?php else : ?>
                            <div class="product-rating mb-3">
                                <span class="no-rating">Chưa có đánh giá</span>
                            </div>
                        <?php endif; ?>

                        <h5>Giá: <?php echo number_format($product['SellPrice'], 0, ',', '.') . ' VND' ?></h5>
                        <p><?php echo $product['Description'] ?></p>
                        <ul>
                            <li>Số lượng: <span><?php echo $product['Quantity'] ?></span></li>
                            <li>Loại: <span><?php echo $product['CategoryName'] ?></span></li>
                            <li>Thương hiệu: <span><?php echo $product['BrandName'] ?></span></li>
                        </ul>
                        <div class="product__details__option">
                            <div class="quantity">
                                <div>
                                    <input class="pro-qty" type="number" value="1" name="quantity" min="1" max="<?php echo $product['Quantity']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $product['ProductId'] ?>">

                                </div>
                            </div>
                            <p>
                                <button style="color: white; " type="submit" class="btn primary-btn">Thêm vào giỏ hàng</button>
                            </p>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>

<section class="related-products spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <h2>Sản phẩm gợi ý cho bạn</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="related__products__slider owl-carousel">

                <?php foreach ($data1 as $key => $value) : ?>
                    <div class="col-lg-3">
                        <div class="product__item">
                            <div class="product__item__pic set-bg">
                                <a href="product_detail.php?id=<?php echo $value['ProductId'] ?> ">
                                    <img src="..//admin//uploads//<?php echo $value['Image'] ?>" alt="Chi tiết sản phẩm">
                                </a>
                                <div class="product__label">
                                    <!-- <span>Cupcake</span> -->
                                </div>
                            </div>
                            <div class="product__item__text">
                                <h6> <a href="product_detail.php?id=<?php echo $value['ProductId'] ?>"><?php echo $value['Name'] ?></a></h6>
                                <h5>Giá <?php echo $value['SellPrice'] . ' VND' ?></h5>
                                <div>
                                    <button class="btn primary-btn mt-4">
                                        <a style="color: white" href="product_detail.php?id=<?php echo $value['ProductId'] ?> ">Chi tiết sản phẩm</a>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="product-reviews spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title">
                    <h2>Đánh giá sản phẩm</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Rating Summary -->
            <div class="col-lg-4">
                <div class="rating-summary-card">
                    <div class="rating-overview text-center mb-4">
                        <?php if ($product['AverageRating'] > 0) : ?>
                            <div class="overall-rating">
                                <h1 class="rating-number"><?php echo number_format($product['AverageRating'], 1); ?></h1>
                                <?php echo getStarRating($product['AverageRating']); ?>
                                <p class="rating-count-text">Dựa trên <?php echo $product['TotalReviews']; ?> đánh giá</p>
                            </div>
                        <?php else : ?>
                            <div class="overall-rating">
                                <p>Chưa có đánh giá nào</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($product['TotalReviews'] > 0 && !empty($ratingDistribution)) : ?>
                        <div class="rating-breakdown">
                            <?php
                            $total = 0;
                            if (!empty($ratingDistribution)) {
                                $total = array_sum(array_column($ratingDistribution, 'count'));
                            }
                            for ($i = 5; $i >= 1; $i--) :
                                $dist = isset($ratingDistribution[$i]) ? $ratingDistribution[$i] : ['count' => 0, 'percentage' => 0];
                            ?>
                                <div class="rating-bar-item">
                                    <div class="rating-label">
                                        <span><?php echo $i; ?> sao</span>
                                    </div>
                                    <div class="rating-bar">
                                        <div class="rating-bar-fill" style="width: <?php echo isset($dist['percentage']) ? $dist['percentage'] : 0; ?>%"></div>
                                    </div>
                                    <div class="rating-count">
                                        <span><?php echo isset($dist['count']) ? $dist['count'] : 0; ?></span>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($canReview['can_review']) : ?>
                        <div class="write-review-btn mt-4">
                            <a href="review_form.php?product_id=<?php echo $id; ?>&order_id=<?php echo $canReview['order_id']; ?>"
                                class="btn btn-primary btn-block">
                                <i class="fa fa-star"></i> Viết đánh giá
                            </a>
                        </div>
                    <?php elseif (isset($_SESSION['user'])) : ?>
                        <div class="write-review-btn mt-4">
                            <p class="text-muted text-center"><?php echo $canReview['message']; ?></p>
                        </div>
                    <?php else : ?>
                        <div class="write-review-btn mt-4">
                            <a href="login.php" class="btn btn-secondary btn-block">
                                Đăng nhập để đánh giá
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="col-lg-8">
                <?php if ($reviewsResult && mysqli_num_rows($reviewsResult) > 0) : ?>
                    <div class="reviews-list">
                        <?php while ($review = mysqli_fetch_assoc($reviewsResult)) :
                            // Get review images - with error handling
                            $reviewImages = [];
                            $imagesQuery = "SELECT ImagePath FROM review_images WHERE ReviewId = " . (int)$review['ReviewId'];
                            $imagesResult = @mysqli_query($conn, $imagesQuery);
                            if ($imagesResult) {
                                while ($img = mysqli_fetch_assoc($imagesResult)) {
                                    $reviewImages[] = $img['ImagePath'];
                                }
                            }

                            // Kiểm tra xem user đã bấm "Hữu ích" chưa
                            $isHelpful = false;
                            if (isset($_SESSION['user']['CustomerId'])) {
                                $customerId = $_SESSION['user']['CustomerId'];
                                $checkHelpfulQuery = "SELECT HelpfulId FROM review_helpful 
                                                      WHERE ReviewId = " . (int)$review['ReviewId'] . " 
                                                      AND CustomerId = $customerId";
                                $checkHelpfulResult = @mysqli_query($conn, $checkHelpfulQuery);
                                if ($checkHelpfulResult && mysqli_num_rows($checkHelpfulResult) > 0) {
                                    $isHelpful = true;
                                }
                            }
                        ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="review-author">
                                        <div class="author-avatar">
                                            <?php echo strtoupper(substr($review['Fullname'], 0, 1)); ?>
                                        </div>
                                        <div class="author-info">
                                            <h6><?php echo htmlspecialchars($review['Fullname']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('d/m/Y', strtotime($review['CreatedAt'])); ?>
                                                <?php if ($review['IsVerified']) : ?>
                                                    <span class="verified-badge">
                                                        <i class="fa fa-check-circle"></i> Đã mua hàng
                                                    </span>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <?php echo getStarRating($review['Rating']); ?>
                                    </div>
                                </div>

                                <?php if (!empty($review['Title'])) : ?>
                                    <h6 class="review-title"><?php echo htmlspecialchars($review['Title']); ?></h6>
                                <?php endif; ?>

                                <p class="review-comment"><?php echo nl2br(htmlspecialchars($review['Comment'])); ?></p>

                                <?php if (!empty($reviewImages)) : ?>
                                    <div class="review-images">
                                        <?php foreach ($reviewImages as $img) : ?>
                                            <a href="../admin/uploads/<?php echo htmlspecialchars($img); ?>"
                                                data-lightbox="review-<?php echo $review['ReviewId']; ?>">
                                                <img src="../admin/uploads/<?php echo htmlspecialchars($img); ?>"
                                                    alt="Review image" class="review-thumbnail">
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="review-footer">
                                    <?php if (isset($_SESSION['user'])) : ?>
                                        <button class="btn-helpful <?php echo $isHelpful ? 'active' : ''; ?>"
                                            data-review-id="<?php echo $review['ReviewId']; ?>"
                                            data-helpful-count="<?php echo $review['HelpfulCount']; ?>">
                                            <i class="fa fa-thumbs-up"></i>
                                            <span class="helpful-text"><?php echo $isHelpful ? 'Đã đánh dấu' : 'Hữu ích'; ?></span>
                                            (<span class="helpful-count"><?php echo $review['HelpfulCount']; ?></span>)
                                        </button>
                                    <?php else : ?>
                                        <button class="btn-helpful" disabled title="Đăng nhập để đánh dấu hữu ích">
                                            <i class="fa fa-thumbs-up"></i> Hữu ích (<?php echo $review['HelpfulCount']; ?>)
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <?php if ($product['TotalReviews'] > 10) : ?>
                        <div class="text-center mt-4">
                            <a href="product_reviews.php?id=<?php echo $id; ?>" class="btn btn-outline-primary">
                                Xem tất cả <?php echo $product['TotalReviews']; ?> đánh giá
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="no-reviews text-center">
                        <i class="fa fa-comment-o fa-3x text-muted mb-3"></i>
                        <p>Chưa có đánh giá nào cho sản phẩm này</p>
                        <?php if ($canReview['can_review']) : ?>
                            <a href="review_form.php?product_id=<?php echo $id; ?>&order_id=<?php echo $canReview['order_id']; ?>"
                                class="btn btn-primary">
                                <i class="fa fa-star"></i> Viết đánh giá đầu tiên
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    .product-rating {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .star-rating {
        display: inline-flex;
        gap: 2px;
    }

    .star-rating .fa-star {
        color: #ffc107;
    }

    .star-rating .fa-star-o {
        color: #ddd;
    }

    .star-rating .fa-star-half-o {
        color: #ffc107;
    }

    .rating-number {
        display: inline-block;
        margin-left: 5px;
        font-weight: 600;
    }

    .rating-count {
        color: #666;
        font-size: 14px;
    }

    .rating-summary-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 25px;
        margin-bottom: 20px;
    }

    .overall-rating {
        padding: 20px 0;
    }

    .overall-rating .rating-number {
        font-size: 48px;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    .rating-breakdown {
        margin-top: 20px;
    }

    .rating-bar-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .rating-label {
        width: 60px;
        font-size: 14px;
    }

    .rating-bar {
        flex: 1;
        height: 8px;
        background: #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
    }

    .rating-bar-fill {
        height: 100%;
        background: #ffc107;
        transition: width 0.3s;
    }

    .rating-count {
        text-align: right;
        font-size: 14px;
        color: #666;
    }

    .review-item {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-bottom: 20px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .review-author {
        display: flex;
        gap: 15px;
    }

    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #f08632;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 20px;
    }

    .author-info h6 {
        margin-bottom: 5px;
    }

    .verified-badge {
        color: #28a745;
        margin-left: 5px;
    }

    .review-title {
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .review-comment {
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .review-images {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .review-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
        border: 2px solid #e0e0e0;
        transition: all 0.3s;
    }

    .review-thumbnail:hover {
        border-color: #f08632;
        transform: scale(1.05);
    }

    .review-footer {
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .btn-helpful {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
        transition: all 0.3s;
        font-size: 14px;
    }

    .btn-helpful:hover:not(:disabled) {
        background: #f0f0f0;
        color: #f08632;
    }

    .btn-helpful.active {
        color: #f08632;
        font-weight: 600;
    }

    .btn-helpful.active i {
        color: #f08632;
    }

    .btn-helpful:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-helpful.loading {
        opacity: 0.6;
        cursor: wait;
    }

    .no-reviews {
        padding: 60px 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý click nút "Hữu ích"
        const helpfulButtons = document.querySelectorAll('.btn-helpful:not(:disabled)');

        helpfulButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                const countElement = this.querySelector('.helpful-count');
                const currentCount = countElement ? parseInt(countElement.textContent) || 0 : 0;
                const isActive = this.classList.contains('active');

                // Không cho phép click nhiều lần khi đang xử lý
                if (this.classList.contains('loading')) {
                    return;
                }

                // Hiển thị trạng thái loading
                this.classList.add('loading');
                const originalHTML = this.innerHTML;

                // Gọi AJAX
                const formData = new FormData();
                formData.append('review_id', reviewId);

                fetch('mark_review_helpful.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.classList.remove('loading');

                        if (data.success) {
                            // Cập nhật số lượng
                            const countElement = this.querySelector('.helpful-count');
                            if (countElement) {
                                countElement.textContent = data.helpful_count;
                            }

                            // Cập nhật trạng thái
                            const textElement = this.querySelector('.helpful-text');
                            if (textElement) {
                                textElement.textContent = data.is_helpful ? 'Đã đánh dấu' : 'Hữu ích';
                            }

                            // Toggle class active
                            if (data.is_helpful) {
                                this.classList.add('active');
                            } else {
                                this.classList.remove('active');
                            }

                            // Hiển thị thông báo (tùy chọn)
                            // console.log(data.message);
                        } else {
                            // Hiển thị lỗi
                            alert(data.message || 'Có lỗi xảy ra');
                            this.innerHTML = originalHTML;
                        }
                    })
                    .catch(error => {
                        this.classList.remove('loading');
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi kết nối server');
                        this.innerHTML = originalHTML;
                    });
            });
        });
    });
</script>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>