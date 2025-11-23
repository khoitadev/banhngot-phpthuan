<?php
include($_SERVER["DOCUMENT_ROOT"] . '/admin/inc/header.php');
include($_SERVER['DOCUMENT_ROOT'] . "/admin/inc/navbar.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM news WHERE NewsId = $id");
    header("Location: news_list.php");
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total
$totalQuery = "SELECT COUNT(*) as total FROM news";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Get news
$query = "SELECT * FROM news 
          ORDER BY CreatedAt DESC
          LIMIT $offset, $limit";
$news = mysqli_query($conn, $query);
?>

<div class="layout-page">
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Quản lý tin tức</h4>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Danh sách tin tức</h5>
                    <a href="news_add.php" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Thêm tin tức
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Tiêu đề</th>
                                <th>Danh mục</th>
                                <th>Tác giả</th>
                                <th>Ngày đăng</th>
                                <th>Trạng thái</th>
                                <th>Lượt xem</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($news) > 0) : ?>
                                <?php while ($item = mysqli_fetch_assoc($news)) : ?>
                                    <tr>
                                        <td><?php echo $item['NewsId']; ?></td>
                                        <td>
                                            <?php if ($item['Image']) : ?>
                                                <img src="../../admin/uploads/news/<?php echo htmlspecialchars($item['Image']); ?>" 
                                                     alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                            <?php else : ?>
                                                <span class="text-muted">Không có ảnh</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['Title']); ?></strong>
                                            <?php if ($item['IsFeatured']) : ?>
                                                <br><span class="badge bg-warning">Nổi bật</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $item['Category'] ? htmlspecialchars($item['Category']) : '-'; ?></td>
                                        <td><?php echo $item['Author'] ? htmlspecialchars($item['Author']) : '-'; ?></td>
                                        <td>
                                            <?php if ($item['PublishedAt']) : ?>
                                                <?php echo date('d/m/Y H:i', strtotime($item['PublishedAt'])); ?>
                                            <?php else : ?>
                                                <span class="text-muted">Chưa đăng</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['IsPublished']) : ?>
                                                <span class="badge bg-success">Đã xuất bản</span>
                                            <?php else : ?>
                                                <span class="badge bg-warning">Bản nháp</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $item['ViewCount']; ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="news_add.php?id=<?php echo $item['NewsId']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $item['NewsId']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Xóa tin tức này?')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9" class="text-center">Chưa có tin tức nào</td>
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

