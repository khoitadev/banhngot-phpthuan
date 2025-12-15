<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$itemsPerPage = 9;
$offset = ($page - 1) * $itemsPerPage;

// Build query
$whereConditions = ["IsPublished = 1"];
if (!empty($category)) {
    $whereConditions[] = "Category = '$category'";
}

$whereClause = implode(" AND ", $whereConditions);

// Get published news
$query = "SELECT * FROM news 
          WHERE $whereClause
          ORDER BY IsFeatured DESC, PublishedAt DESC, CreatedAt DESC
          LIMIT $offset, $itemsPerPage";
$newsResult = mysqli_query($conn, $query);

// Get total count
$totalQuery = "SELECT COUNT(*) as total FROM news WHERE $whereClause";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $itemsPerPage);

// Get categories
$categoriesQuery = "SELECT DISTINCT Category FROM news WHERE IsPublished = 1 AND Category IS NOT NULL AND Category != ''";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Tin tức</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <span>Tin tức</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <!-- Category Filter -->
        <?php if (mysqli_num_rows($categoriesResult) > 0) : ?>
            <div class="news-filter mb-4">
                <div class="btn-group" role="group">
                    <a href="news.php" class="btn btn-outline-primary <?php echo empty($category) ? 'active' : ''; ?>">
                        Tất cả
                    </a>
                    <?php
                    mysqli_data_seek($categoriesResult, 0);
                    while ($cat = mysqli_fetch_assoc($categoriesResult)) :
                        $isActive = $category == $cat['Category'] ? 'active' : '';
                    ?>
                        <a href="?category=<?php echo urlencode($cat['Category']); ?>"
                            class="btn btn-outline-primary <?php echo $isActive; ?>">
                            <?php echo htmlspecialchars($cat['Category']); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($newsResult) > 0) : ?>
            <div class="row">
                <?php while ($news = mysqli_fetch_assoc($newsResult)) : ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="news-card">
                            <?php if ($news['IsFeatured']) : ?>
                                <span class="featured-badge">Nổi bật</span>
                            <?php endif; ?>

                            <?php if ($news['Image']) : ?>
                                <div class="news-image">
                                    <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>">
                                        <img src="../admin/uploads/news/<?php echo htmlspecialchars($news['Image']); ?>"
                                            alt="<?php echo htmlspecialchars($news['Title']); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="news-content">
                                <?php if ($news['Category']) : ?>
                                    <span class="news-category"><?php echo htmlspecialchars($news['Category']); ?></span>
                                <?php endif; ?>

                                <h4>
                                    <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>">
                                        <?php echo htmlspecialchars($news['Title']); ?>
                                    </a>
                                </h4>

                                <?php if ($news['Summary']) : ?>
                                    <p><?php echo htmlspecialchars($news['Summary']); ?></p>
                                <?php endif; ?>

                                <div class="news-meta">
                                    <?php if ($news['Author']) : ?>
                                        <span><i class="fa fa-user"></i> <?php echo htmlspecialchars($news['Author']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($news['PublishedAt']) : ?>
                                        <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($news['PublishedAt'])); ?></span>
                                    <?php endif; ?>
                                    <span><i class="fa fa-eye"></i> <?php echo $news['ViewCount']; ?></span>
                                </div>

                                <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>" class="btn btn-primary btn-sm">
                                    Đọc thêm
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1) : ?>
                <div class="shop__pagination mt-4">
                    <?php
                    $queryParams = $_GET;
                    unset($queryParams['page']);

                    if ($page > 1) {
                        $queryParams['page'] = $page - 1;
                        echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-left"></span></a>';
                    }

                    for ($i = 1; $i <= $totalPages; $i++) {
                        $queryParams['page'] = $i;
                        $activeClass = $i == $page ? 'active' : '';
                        echo '<a href="?' . http_build_query($queryParams) . '" class="' . $activeClass . '">' . $i . '</a>';
                    }

                    if ($page < $totalPages) {
                        $queryParams['page'] = $page + 1;
                        echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-right"></span></a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="alert alert-info text-center">
                <i class="fa fa-newspaper-o fa-3x mb-3"></i>
                <h4>Chưa có tin tức nào</h4>
                <p>Vui lòng quay lại sau!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .news-filter {
        text-align: center;
    }

    .news-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .featured-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #f08632;
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 2;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .news-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .news-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        position: relative;
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .news-card:hover .news-image img {
        transform: scale(1.1);
    }

    .news-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-category {
        display: inline-block;
        background: #f08632;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .news-content h4 {
        margin-bottom: 10px;
    }

    .news-content h4 a {
        color: #333;
        text-decoration: none;
        transition: color 0.3s;
    }

    .news-content h4 a:hover {
        color: #f08632;
    }

    .news-content p {
        color: #666;
        margin-bottom: 15px;
        flex: 1;
    }

    .news-meta {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 15px;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        font-size: 14px;
        color: #666;
    }

    .news-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>