<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$news = null;

if ($id > 0) {
    $query = "SELECT * FROM news WHERE NewsId = $id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $news = mysqli_fetch_assoc($result);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $summary = mysqli_real_escape_string($conn, $_POST['summary']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $sortOrder = (int)$_POST['sort_order'];
    $publishedAt = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    
    // Handle image upload
    $imageName = $news ? $news['Image'] : '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/admin/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = 'news_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $imageName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // Delete old image if exists
            if ($news && $news['Image'] && file_exists($uploadDir . $news['Image'])) {
                unlink($uploadDir . $news['Image']);
            }
        } else {
            $imageName = $news ? $news['Image'] : '';
        }
    }
    
    // Generate slug from title
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    if ($id > 0) {
        // Update
        $query = "UPDATE news SET 
                  Title = '$title',
                  Slug = '$slug',
                  Summary = '$summary',
                  Content = '$content',
                  Category = '$category',
                  Author = '$author',
                  IsPublished = $isPublished,
                  IsFeatured = $isFeatured,
                  SortOrder = $sortOrder,
                  Image = '$imageName',
                  PublishedAt = " . ($publishedAt ? "'$publishedAt'" : 'NULL') . ",
                  UpdatedAt = NOW()
                  WHERE NewsId = $id";
    } else {
        // Insert
        $query = "INSERT INTO news (Title, Slug, Summary, Content, Category, Author, IsPublished, IsFeatured, SortOrder, Image, PublishedAt) 
                  VALUES ('$title', '$slug', '$summary', '$content', '$category', '$author', $isPublished, $isFeatured, $sortOrder, '$imageName', " . ($publishedAt ? "'$publishedAt'" : 'NULL') . ")";
    }
    
    if (mysqli_query($conn, $query)) {
        echo '<script>window.location.href = "news_list.php";</script>';
        exit;
    } else {
        $error = "Có lỗi xảy ra: " . mysqli_error($conn);
    }
}
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><?php echo $id > 0 ? 'Sửa' : 'Thêm'; ?> tin tức</h4>
            
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
                                           value="<?php echo $news ? htmlspecialchars($news['Title']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Tóm tắt</label>
                                    <textarea class="form-control" name="summary" rows="3"><?php echo $news ? htmlspecialchars($news['Summary']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="content" rows="15" required><?php echo $news ? htmlspecialchars($news['Content']) : ''; ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Danh mục</label>
                                            <input type="text" class="form-control" name="category" 
                                                   value="<?php echo $news ? htmlspecialchars($news['Category']) : ''; ?>" 
                                                   placeholder="VD: Tin tức, Sự kiện, Công thức...">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tác giả</label>
                                            <input type="text" class="form-control" name="author" 
                                                   value="<?php echo $news ? htmlspecialchars($news['Author']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Ngày xuất bản</label>
                                    <input type="datetime-local" class="form-control" name="published_at" 
                                           value="<?php echo $news && $news['PublishedAt'] ? date('Y-m-d\TH:i', strtotime($news['PublishedAt'])) : ''; ?>">
                                    <small class="form-text text-muted">Để trống nếu muốn đăng ngay khi lưu</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện</label>
                                    <?php if ($news && $news['Image']) : ?>
                                        <div class="mb-2">
                                            <img src="../../admin/uploads/news/<?php echo htmlspecialchars($news['Image']); ?>" 
                                                 alt="" class="img-thumbnail" style="max-width: 100%;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_published" 
                                               <?php echo (!$news || $news['IsPublished']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Đã xuất bản</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" 
                                               <?php echo ($news && $news['IsFeatured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Nổi bật</label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Thứ tự hiển thị</label>
                                    <input type="number" class="form-control" name="sort_order" 
                                           value="<?php echo $news ? $news['SortOrder'] : '0'; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            <a href="news_list.php" class="btn btn-secondary">Hủy</a>
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

