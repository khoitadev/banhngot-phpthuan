<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$promo = null;

if ($id > 0) {
    $query = "SELECT * FROM promotions WHERE PromotionId = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $promo = mysqli_fetch_assoc($result);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $discountType = $_POST['discount_type'];
    $discountValue = (float)$_POST['discount_value'];
    $minPurchase = (float)$_POST['min_purchase'];
    $maxDiscount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $sortOrder = (int)$_POST['sort_order'];
    
    // Handle image upload
    $imageName = $promo ? $promo['Image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/admin/uploads/promotions/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = 'promo_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $imageName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Delete old image if exists
            if ($promo && $promo['Image'] && file_exists($uploadDir . $promo['Image'])) {
                unlink($uploadDir . $promo['Image']);
            }
        } else {
            $imageName = $promo ? $promo['Image'] : '';
        }
    }
    
    if ($id > 0) {
        // Update
        $query = "UPDATE promotions SET 
                  Title = '$title',
                  Description = '$description',
                  Content = '$content',
                  DiscountType = '$discountType',
                  DiscountValue = $discountValue,
                  MinPurchase = $minPurchase,
                  MaxDiscount = " . ($maxDiscount ? $maxDiscount : 'NULL') . ",
                  StartDate = '$startDate',
                  EndDate = '$endDate',
                  IsActive = $isActive,
                  IsFeatured = $isFeatured,
                  SortOrder = $sortOrder,
                  Image = '$imageName',
                  UpdatedAt = NOW()
                  WHERE PromotionId = $id";
    } else {
        // Insert
        $query = "INSERT INTO promotions (Title, Description, Content, DiscountType, DiscountValue, MinPurchase, MaxDiscount, StartDate, EndDate, IsActive, IsFeatured, SortOrder, Image) 
                  VALUES ('$title', '$description', '$content', '$discountType', $discountValue, $minPurchase, " . ($maxDiscount ? $maxDiscount : 'NULL') . ", '$startDate', '$endDate', $isActive, $isFeatured, $sortOrder, '$imageName')";
    }
    
    if (mysqli_query($conn, $query)) {
        // Get promotion ID (new or existing)
        $promotionId = $id > 0 ? $id : mysqli_insert_id($conn);
        
        // Handle promotion products
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            // Delete existing products for this promotion
            mysqli_query($conn, "DELETE FROM promotion_products WHERE PromotionId = $promotionId");
            
            // Insert selected products
            $selectedProducts = array_filter($_POST['products'], function($pid) {
                return is_numeric($pid) && $pid > 0;
            });
            
            foreach ($selectedProducts as $productId) {
                $productId = (int)$productId;
                $insertProductQuery = "INSERT INTO promotion_products (PromotionId, ProductId) 
                                      VALUES ($promotionId, $productId)
                                      ON DUPLICATE KEY UPDATE PromotionId = PromotionId";
                mysqli_query($conn, $insertProductQuery);
            }
        } else {
            // If no products selected, delete all existing
            if ($id > 0) {
                mysqli_query($conn, "DELETE FROM promotion_products WHERE PromotionId = $promotionId");
            }
        }
        
        echo '<script>window.location.href = "promotion_list.php";</script>';
        exit;
    } else {
        $error = "Có lỗi xảy ra: " . mysqli_error($conn);
    }
}

// Get selected products for this promotion (if editing)
$selectedProductIds = [];
if ($id > 0) {
    $selectedProductsQuery = "SELECT ProductId FROM promotion_products WHERE PromotionId = $id";
    $selectedProductsResult = mysqli_query($conn, $selectedProductsQuery);
    if ($selectedProductsResult) {
        while ($row = mysqli_fetch_assoc($selectedProductsResult)) {
            $selectedProductIds[] = $row['ProductId'];
        }
    }
}

// Get all active products for selection
$productsQuery = "SELECT p.ProductId, p.Name, p.Image, p.SellPrice, c.CategoryName, b.BrandName
                  FROM products p
                  LEFT JOIN category c ON p.CategoriId = c.CategoryId
                  LEFT JOIN brands b ON p.BrandId = b.BrandId
                  WHERE p.status = 1 AND p.is_accept = 1
                  ORDER BY p.Name ASC";
$productsResult = mysqli_query($conn, $productsQuery);
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><?php echo $id > 0 ? 'Sửa' : 'Thêm'; ?> khuyến mãi</h4>
            
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title" 
                                           value="<?php echo $promo ? htmlspecialchars($promo['Title']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Mô tả ngắn</label>
                                    <textarea class="form-control" name="description" rows="3"><?php echo $promo ? htmlspecialchars($promo['Description']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nội dung chi tiết</label>
                                    <textarea class="form-control" name="content" rows="10"><?php echo $promo ? htmlspecialchars($promo['Content']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                            <select class="form-select" name="discount_type" required>
                                                <option value="percentage" <?php echo (!$promo || $promo['DiscountType'] == 'percentage') ? 'selected' : ''; ?>>Phần trăm (%)</option>
                                                <option value="fixed" <?php echo ($promo && $promo['DiscountType'] == 'fixed') ? 'selected' : ''; ?>>Số tiền cố định</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá trị giảm giá <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="discount_value" 
                                                   value="<?php echo $promo ? $promo['DiscountValue'] : ''; ?>" 
                                                   step="0.01" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                                            <input type="number" class="form-control" name="min_purchase" 
                                                   value="<?php echo $promo ? $promo['MinPurchase'] : '0'; ?>" step="0.01">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giảm giá tối đa (VNĐ)</label>
                                            <input type="number" class="form-control" name="max_discount" 
                                                   value="<?php echo $promo ? $promo['MaxDiscount'] : ''; ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="start_date" 
                                                   value="<?php echo $promo ? date('Y-m-d\TH:i', strtotime($promo['StartDate'])) : ''; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="end_date" 
                                                   value="<?php echo $promo ? date('Y-m-d\TH:i', strtotime($promo['EndDate'])) : ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <?php if ($promo && $promo['Image']) : ?>
                                        <div class="mb-2">
                                            <img src="../../admin/uploads/promotions/<?php echo htmlspecialchars($promo['Image']); ?>" 
                                                 alt="" class="img-thumbnail" style="max-width: 100%;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               <?php echo (!$promo || $promo['IsActive']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Đang hoạt động</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" 
                                               <?php echo ($promo && $promo['IsFeatured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Nổi bật</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Thứ tự hiển thị</label>
                                    <input type="number" class="form-control" name="sort_order" 
                                           value="<?php echo $promo ? $promo['SortOrder'] : '0'; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Products Selection -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Chọn sản phẩm áp dụng khuyến mãi</h5>
                                        <small class="text-muted">Chọn các sản phẩm sẽ được hiển thị trong trang chi tiết khuyến mãi</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <input type="text" id="product-search" class="form-control" 
                                                   placeholder="Tìm kiếm sản phẩm..." 
                                                   onkeyup="filterProducts()">
                                        </div>
                                        <div class="product-selection-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                                            <?php if ($productsResult && mysqli_num_rows($productsResult) > 0) : ?>
                                                <div class="product-grid-row">
                                                    <?php while ($product = mysqli_fetch_assoc($productsResult)) : 
                                                        $isSelected = in_array($product['ProductId'], $selectedProductIds);
                                                    ?>
                                                        <div class="product-grid-item product-item" 
                                                             data-product-name="<?php echo strtolower(htmlspecialchars($product['Name'])); ?>">
                                                            <div class="card h-100 product-card <?php echo $isSelected ? 'border-primary' : ''; ?>" 
                                                                 style="transition: all 0.3s; cursor: pointer;"
                                                                 data-product-id="<?php echo $product['ProductId']; ?>">
                                                                <div class="card-body p-2">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input product-checkbox" 
                                                                               type="checkbox" 
                                                                               name="products[]" 
                                                                               value="<?php echo $product['ProductId']; ?>"
                                                                               id="product_<?php echo $product['ProductId']; ?>"
                                                                               <?php echo $isSelected ? 'checked' : ''; ?>
                                                                               onchange="updateCardStyle(<?php echo $product['ProductId']; ?>)"
                                                                               onclick="event.stopPropagation();">
                                                                        <label class="form-check-label w-100" 
                                                                               for="product_<?php echo $product['ProductId']; ?>"
                                                                               style="cursor: pointer;"
                                                                               onclick="event.stopPropagation();">
                                                                            <?php if ($product['Image']) : ?>
                                                                                <img src="../../admin/uploads/<?php echo htmlspecialchars($product['Image']); ?>" 
                                                                                     alt="" 
                                                                                     style="width: 100%; height: 80px; object-fit: cover; border-radius: 3px; margin-bottom: 5px;">
                                                                            <?php endif; ?>
                                                                            <div class="small">
                                                                                <strong><?php echo htmlspecialchars($product['Name']); ?></strong><br>
                                                                                <span class="text-muted">
                                                                                    <?php echo number_format($product['SellPrice'], 0, ',', '.'); ?> VNĐ
                                                                                </span>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile; ?>
                                                </div>
                                            <?php else : ?>
                                                <p class="text-muted text-center">Không có sản phẩm nào</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <strong id="selected-count">0</strong> sản phẩm đã được chọn
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <a href="promotion_list.php" class="btn btn-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateCardStyle(productId) {
    const checkbox = document.getElementById('product_' + productId);
    if (!checkbox) return;
    
    const card = checkbox.closest('.product-card');
    if (card) {
        if (checkbox.checked) {
            card.classList.add('border-primary');
            card.style.borderWidth = '2px';
        } else {
            card.classList.remove('border-primary');
            card.style.borderWidth = '1px';
        }
    }
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('input[name="products[]"]:checked');
    document.getElementById('selected-count').textContent = checkedBoxes.length;
}

function filterProducts() {
    const searchTerm = document.getElementById('product-search').value.toLowerCase();
    const productItems = document.querySelectorAll('.product-item');
    
    productItems.forEach(function(item) {
        const productName = item.getAttribute('data-product-name');
        if (productName.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Initialize selected count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Update card styles for pre-selected products
    document.querySelectorAll('input[name="products[]"]:checked').forEach(function(checkbox) {
        const productId = checkbox.value;
        updateCardStyle(productId);
    });
    
    // Handle card click (but not checkbox/label click)
    document.querySelectorAll('.product-card').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Don't toggle if clicking directly on checkbox or inside label
            if (e.target.classList.contains('product-checkbox') || 
                e.target.closest('label') || 
                e.target.closest('.form-check-input')) {
                return;
            }
            
            const productId = this.getAttribute('data-product-id');
            const checkbox = document.getElementById('product_' + productId);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateCardStyle(productId);
            }
        });
    });
});
</script>

<style>
.product-selection-container .product-grid-row {
    display: flex !important;
    flex-wrap: wrap !important;
    margin: 0 -8px;
    width: calc(100% + 16px);
}

.product-selection-container .product-grid-item {
    flex: 0 0 100%;
    max-width: 100%;
    padding: 0 8px;
    margin-bottom: 15px;
    box-sizing: border-box;
}

@media (min-width: 768px) {
    .product-selection-container .product-grid-item {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
}

@media (min-width: 992px) {
    .product-selection-container .product-grid-item {
        flex: 0 0 25%;
        max-width: 25%;
    }
}

.product-selection-container .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-selection-container .card.border-primary {
    background-color: #f0f8ff;
}
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
?>

