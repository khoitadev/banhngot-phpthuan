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
        echo '<script>window.location.href = "promotion_list.php";</script>';
        exit;
    } else {
        $error = "Có lỗi xảy ra: " . mysqli_error($conn);
    }
}
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

<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/footer.php');
?>

