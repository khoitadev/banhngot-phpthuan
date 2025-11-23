<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: news.php");
    exit;
}

// Get news
$query = "SELECT * FROM news WHERE NewsId = $id AND IsPublished = 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: news.php");
    exit;
}

$news = mysqli_fetch_assoc($result);

// Update view count
mysqli_query($conn, "UPDATE news SET ViewCount = ViewCount + 1 WHERE NewsId = $id");

// Get related news
$relatedQuery = "SELECT * FROM news 
                 WHERE NewsId != $id 
                 AND IsPublished = 1 
                 AND (Category = '{$news['Category']}' OR IsFeatured = 1)
                 ORDER BY PublishedAt DESC
                 LIMIT 3";
$relatedResult = mysqli_query($conn, $relatedQuery);
?>

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2><?php echo htmlspecialchars($news['Title']); ?></h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <a href="./news.php">Tin tức</a>
                    <span>Chi tiết</span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <article class="news-detail-card">
                    <?php if ($news['Image']) : ?>
                        <div class="news-detail-image mb-4">
                            <img src="../admin/uploads/news/<?php echo htmlspecialchars($news['Image']); ?>" 
                                 alt="<?php echo htmlspecialchars($news['Title']); ?>" class="img-fluid">
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-header mb-4">
                        <?php if ($news['Category']) : ?>
                            <span class="news-category"><?php echo htmlspecialchars($news['Category']); ?></span>
                        <?php endif; ?>
                        <h1><?php echo htmlspecialchars($news['Title']); ?></h1>
                        <div class="news-meta">
                            <?php if ($news['Author']) : ?>
                                <span><i class="fa fa-user"></i> <?php echo htmlspecialchars($news['Author']); ?></span>
                            <?php endif; ?>
                            <?php if ($news['PublishedAt']) : ?>
                                <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($news['PublishedAt'])); ?></span>
                            <?php endif; ?>
                            <span><i class="fa fa-eye"></i> <?php echo $news['ViewCount']; ?> lượt xem</span>
                        </div>
                    </div>
                    
                    <?php if ($news['Summary']) : ?>
                        <div class="news-summary">
                            <p class="lead"><?php echo nl2br(htmlspecialchars($news['Summary'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="news-content">
                        <?php echo nl2br(htmlspecialchars($news['Content'])); ?>
                    </div>
                </article>
            </div>
            
            <div class="col-lg-4">
                <div class="news-sidebar">
                    <?php if (mysqli_num_rows($relatedResult) > 0) : ?>
                        <div class="sidebar-card mb-4">
                            <h5>Tin tức liên quan</h5>
                            <div class="related-news">
                                <?php while ($related = mysqli_fetch_assoc($relatedResult)) : ?>
                                    <div class="related-news-item">
                                        <?php if ($related['Image']) : ?>
                                            <div class="related-news-image">
                                                <a href="news_detail.php?id=<?php echo $related['NewsId']; ?>">
                                                    <img src="../admin/uploads/news/<?php echo htmlspecialchars($related['Image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($related['Title']); ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="related-news-content">
                                            <h6>
                                                <a href="news_detail.php?id=<?php echo $related['NewsId']; ?>">
                                                    <?php echo htmlspecialchars($related['Title']); ?>
                                                </a>
                                            </h6>
                                            <small><?php echo date('d/m/Y', strtotime($related['PublishedAt'])); ?></small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="sidebar-card">
                        <h5>Thông tin</h5>
                        <ul class="list-unstyled">
                            <li><i class="fa fa-calendar"></i> Ngày đăng: <?php echo date('d/m/Y', strtotime($news['PublishedAt'])); ?></li>
                            <li><i class="fa fa-eye"></i> Lượt xem: <?php echo $news['ViewCount']; ?></li>
                            <?php if ($news['Category']) : ?>
                                <li><i class="fa fa-folder"></i> Danh mục: <?php echo htmlspecialchars($news['Category']); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.news-detail-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 30px;
}

.news-detail-image img {
    width: 100%;
    border-radius: 8px;
}

.news-header h1 {
    margin: 15px 0;
    color: #333;
}

.news-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    color: #666;
    font-size: 14px;
}

.news-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.news-summary {
    background: #f8f9fa;
    padding: 20px;
    border-left: 4px solid #f08632;
    margin-bottom: 30px;
    border-radius: 4px;
}

.news-content {
    line-height: 1.8;
    color: #333;
    font-size: 16px;
}

.news-sidebar {
    position: sticky;
    top: 20px;
}

.sidebar-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 20px;
}

.related-news-item {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.related-news-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.related-news-image {
    width: 80px;
    height: 80px;
    flex-shrink: 0;
    border-radius: 5px;
    overflow: hidden;
}

.related-news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-news-content h6 {
    margin-bottom: 5px;
}

.related-news-content h6 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s;
}

.related-news-content h6 a:hover {
    color: #f08632;
}

.sidebar-card ul li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.sidebar-card ul li:last-child {
    border-bottom: none;
}

.sidebar-card ul li i {
    margin-right: 10px;
    color: #f08632;
    width: 20px;
}
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>

