<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

try {
    include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/helpers/review_helper.php");
} catch (Exception $e) {
    die("Error loading files: " . $e->getMessage());
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$customerId = $user['CustomerId'];
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($productId <= 0) {
    header("Location: index.php");
    exit;
}

// Get product info
$productQuery = "SELECT ProductId, Name, Image FROM products WHERE ProductId = $productId";
$productResult = mysqli_query($conn, $productQuery);
if (mysqli_num_rows($productResult) == 0) {
    header("Location: index.php");
    exit;
}
$product = mysqli_fetch_assoc($productResult);

// Check if can review (with error handling)
$canReview = ['can_review' => true, 'order_id' => $orderId, 'message' => 'Bạn có thể đánh giá sản phẩm này'];

// Check if reviews table exists
$tableCheck = @mysqli_query($conn, "SHOW TABLES LIKE 'reviews'");
if ($tableCheck && mysqli_num_rows($tableCheck) > 0) {
    try {
        $canReview = canCustomerReview($conn, $customerId, $productId);
    } catch (Exception $e) {
        // If error, still allow review but log error
        error_log("Review check error: " . $e->getMessage());
        // Use order_id from URL if provided
        if ($orderId > 0) {
            $canReview = ['can_review' => true, 'order_id' => $orderId, 'message' => 'Bạn có thể đánh giá sản phẩm này'];
        }
    }
} else {
    // Table doesn't exist - show warning but allow form
    $canReview['message'] = 'Hệ thống đánh giá chưa được cài đặt. Vui lòng chạy file SQL upgrade_reviews.sql trước khi đánh giá.';
    // Still allow if order_id is provided
    if ($orderId > 0) {
        $canReview['can_review'] = true;
    }
}

// If cannot review and no order_id, show error
if (!$canReview['can_review'] && $orderId == 0) {
    ?>
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__text">
                        <h2>Đánh giá sản phẩm</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <section class="shop spad">
        <div class="container">
            <div class="alert alert-warning text-center">
                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                <h4><?php echo $canReview['message']; ?></h4>
                <a href="product_detail.php?id=<?php echo $productId; ?>" class="btn btn-primary">Quay lại sản phẩm</a>
            </div>
        </div>
    </section>
    <?php
    include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
    ob_end_flush();
    exit;
}

// Use order_id from canReview if available
if ($canReview['order_id']) {
    $orderId = $canReview['order_id'];
}
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Đánh giá sản phẩm</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="product_detail.php?id=<?php echo $productId; ?>">Chi tiết sản phẩm</a>
                    <span>Đánh giá</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="review-form-card">
                    <div class="review-product-info mb-4">
                        <div class="row align-items-center">
                            <div class="col-3">
                                <img src="../admin/uploads/<?php echo htmlspecialchars($product['Image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['Name']); ?>"
                                     class="img-fluid rounded">
                            </div>
                            <div class="col-9">
                                <h4><?php echo htmlspecialchars($product['Name']); ?></h4>
                                <p class="text-muted mb-0">Mã sản phẩm: #<?php echo $productId; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <form id="reviewForm" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                        
                        <div class="form-group mb-4">
                            <label class="form-label">Đánh giá của bạn <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <input type="hidden" name="rating" id="rating" value="5" required>
                                <div class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <i class="fa fa-star star-icon" data-rating="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-text">Tuyệt vời</span>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="title" class="form-label">Tiêu đề đánh giá</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   placeholder="VD: Sản phẩm rất tốt, đáng mua">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="comment" class="form-label">Nội dung đánh giá <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="comment" name="comment" rows="6" 
                                      placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                            <small class="form-text text-muted">Tối thiểu 10 ký tự</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="review_images" class="form-label">Ảnh đính kèm (tùy chọn)</label>
                            <input type="file" class="form-control" id="review_images" name="review_images[]" 
                                   accept="image/*" multiple>
                            <small class="form-text text-muted">Tối đa 5 ảnh, mỗi ảnh tối đa 2MB</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-paper-plane"></i> Gửi đánh giá
                            </button>
                            <a href="product_detail.php?id=<?php echo $productId; ?>" class="btn btn-secondary btn-lg">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.review-form-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
}

.review-product-info {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 5px;
}

.rating-input {
    display: flex;
    align-items: center;
    gap: 15px;
}

.stars {
    display: flex;
    gap: 5px;
    font-size: 32px;
}

.star-icon {
    color: #ddd;
    cursor: pointer;
    transition: all 0.2s;
}

.star-icon:hover,
.star-icon.active {
    color: #ffc107;
}

.rating-text {
    font-size: 16px;
    color: #666;
    font-weight: 600;
}

#imagePreview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.preview-image {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 5px;
    overflow: hidden;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-image .remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255,0,0,0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    cursor: pointer;
    font-size: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-icon');
    const ratingInput = document.getElementById('rating');
    const ratingText = document.querySelector('.rating-text');
    
    const ratingTexts = {
        1: 'Rất tệ',
        2: 'Tệ',
        3: 'Bình thường',
        4: 'Tốt',
        5: 'Tuyệt vời'
    };
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            ratingInput.value = rating;
            
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                    s.classList.remove('fa-star-o');
                    s.classList.add('fa-star');
                } else {
                    s.classList.remove('active');
                    s.classList.remove('fa-star');
                    s.classList.add('fa-star-o');
                }
            });
            
            ratingText.textContent = ratingTexts[rating];
        });
        
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
        
        star.addEventListener('mouseleave', function() {
            // Reset to current selected rating
            const currentRating = parseInt(ratingInput.value);
            stars.forEach((s, index) => {
                if (index < currentRating) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Initialize stars to show 5 stars (default rating)
    stars.forEach((s, index) => {
        if (index < 5) {
            s.classList.add('active');
            s.classList.remove('fa-star-o');
            s.classList.add('fa-star');
            s.style.color = '#ffc107';
        }
    });
    
    // Image preview
    const imageInput = document.getElementById('review_images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        const files = Array.from(e.target.files).slice(0, 5);
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-image';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage(${index})">×</button>
                    `;
                    imagePreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Form submission
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const comment = formData.get('comment');
        
        if (comment.length < 10) {
            alert('Nội dung đánh giá phải có ít nhất 10 ký tự');
            return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
        
        fetch('submit_review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'product_detail.php?id=<?php echo $productId; ?>';
            } else {
                alert(data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Gửi đánh giá';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Gửi đánh giá';
        });
    });
});

function removeImage(index) {
    const input = document.getElementById('review_images');
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    input.files = dt.files;
    
    // Refresh preview
    const event = new Event('change');
    input.dispatchEvent(event);
}
</script>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
ob_end_flush();
?>

