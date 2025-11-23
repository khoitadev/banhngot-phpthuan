<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

// Lấy filter từ URL
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$brandId = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'popular'; // popular, price_low, price_high, newest
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

$itemsPerPage = 12; // Số sản phẩm trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Xây dựng query với filter
$whereConditions = ["p.status = 1", "p.is_accept = 1"];
if ($categoryId > 0) {
    $whereConditions[] = "p.CategoriId = $categoryId";
}
if ($brandId > 0) {
    $whereConditions[] = "p.BrandId = $brandId";
}
if ($minPrice > 0) {
    $whereConditions[] = "p.SellPrice >= $minPrice";
}
if ($maxPrice > 0) {
    $whereConditions[] = "p.SellPrice <= $maxPrice";
}

$whereClause = implode(" AND ", $whereConditions);

// Sắp xếp
$orderBy = "p.CountView DESC";
switch ($sortBy) {
    case 'price_low':
        $orderBy = "p.SellPrice ASC";
        break;
    case 'price_high':
        $orderBy = "p.SellPrice DESC";
        break;
    case 'newest':
        $orderBy = "p.ProductId DESC";
        break;
    default:
        $orderBy = "p.CountView DESC";
}

// Truy vấn sản phẩm cho trang hiện tại
$start = ($page - 1) * $itemsPerPage;
$query = "SELECT p.*, c.CategoryName, b.BrandName 
          FROM products p 
          LEFT JOIN category c ON p.CategoriId = c.CategoryId 
          LEFT JOIN brands b ON p.BrandId = b.BrandId 
          WHERE $whereClause 
          ORDER BY $orderBy 
          LIMIT $start, $itemsPerPage";
$Products = mysqli_query($conn, $query);

// Truy vấn số lượng sản phẩm tổng cộng
$totalQuery = "SELECT COUNT(*) as total FROM products p WHERE $whereClause";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult)['total'];

// Tính toán số trang
$totalPages = ceil($totalRow / $itemsPerPage);

// Truy vấn danh mục với số lượng sản phẩm
$query1 = "SELECT c.*, COUNT(p.ProductId) as product_count 
           FROM category c 
           LEFT JOIN products p ON c.CategoryId = p.CategoriId AND p.status = 1 AND p.is_accept = 1 
           WHERE c.status = 1 
           GROUP BY c.CategoryId 
           ORDER BY c.SortOrder ASC, c.CategoryName ASC";
$Category = mysqli_query($conn, $query1);

// Truy vấn brands với số lượng sản phẩm
$query2 = "SELECT b.*, COUNT(p.ProductId) as product_count 
           FROM brands b 
           LEFT JOIN products p ON b.BrandId = p.BrandId AND p.status = 1 AND p.is_accept = 1 
           WHERE b.Status = 1 
           GROUP BY b.BrandId 
           ORDER BY b.BrandName ASC";
$Brands = mysqli_query($conn, $query2);

// Lấy giá min/max
$priceQuery = "SELECT MIN(SellPrice) as min_price, MAX(SellPrice) as max_price 
               FROM products 
               WHERE status = 1 AND is_accept = 1";
$priceResult = mysqli_query($conn, $priceQuery);
$priceData = mysqli_fetch_assoc($priceResult);
$globalMinPrice = (int)$priceData['min_price'];
$globalMaxPrice = (int)$priceData['max_price'];
?>
<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__text">
                    <h2>Cửa hàng</h2>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="breadcrumb__links">
                    <a href="./index.php">Trang chủ</a>
                    <span>Cửa hàng</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 col-md-4">
                <div class="shop__sidebar">
                    <!-- Category Filter -->
                    <div class="shop__sidebar__accordion">
                        <div class="accordion" id="accordionExample">
                            <div class="card">
                                <div class="card-heading">
                                    <a data-toggle="collapse" data-target="#collapseOne">Danh mục</a>
                                </div>
                                <div id="collapseOne" class="collapse show" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="shop__sidebar__categories">
                                            <ul>
                                                <li>
                                                    <a href="list_product.php" class="<?php echo $categoryId == 0 ? 'active' : ''; ?>">
                                                        Tất cả <span>(<?php echo $totalRow; ?>)</span>
                                                    </a>
                                                </li>
                                                <?php 
                                                mysqli_data_seek($Category, 0); // Reset pointer
                                                while ($cat = mysqli_fetch_assoc($Category)) : 
                                                    $isActive = $categoryId == $cat['CategoryId'] ? 'active' : '';
                                                ?>
                                                    <li>
                                                        <a href="?category=<?php echo $cat['CategoryId']; ?>&page=1" class="<?php echo $isActive; ?>">
                                                            <?php echo htmlspecialchars($cat['CategoryName']); ?> 
                                                            <span>(<?php echo $cat['product_count']; ?>)</span>
                                                        </a>
                                                    </li>
                                                <?php endwhile; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Brand Filter -->
                            <div class="card">
                                <div class="card-heading">
                                    <a data-toggle="collapse" data-target="#collapseTwo">Thương hiệu</a>
                                </div>
                                <div id="collapseTwo" class="collapse show" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="shop__sidebar__brand">
                                            <ul>
                                                <li>
                                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['brand' => 0, 'page' => 1])); ?>" 
                                                       class="<?php echo $brandId == 0 ? 'active' : ''; ?>">
                                                        Tất cả
                                                    </a>
                                                </li>
                                                <?php 
                                                mysqli_data_seek($Brands, 0); // Reset pointer
                                                while ($brand = mysqli_fetch_assoc($Brands)) : 
                                                    $isActive = $brandId == $brand['BrandId'] ? 'active' : '';
                                                    $brandParams = array_merge($_GET, ['brand' => $brand['BrandId'], 'page' => 1]);
                                                ?>
                                                    <li>
                                                        <a href="?<?php echo http_build_query($brandParams); ?>" class="<?php echo $isActive; ?>">
                                                            <?php echo htmlspecialchars($brand['BrandName']); ?> 
                                                            <span>(<?php echo $brand['product_count']; ?>)</span>
                                                        </a>
                                                    </li>
                                                <?php endwhile; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price Filter -->
                            <div class="card">
                                <div class="card-heading">
                                    <a data-toggle="collapse" data-target="#collapseThree">Khoảng giá</a>
                                </div>
                                <div id="collapseThree" class="collapse show" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="shop__sidebar__price">
                                            <form method="GET" action="">
                                                <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                                                <input type="hidden" name="brand" value="<?php echo $brandId; ?>">
                                                <input type="hidden" name="sort" value="<?php echo $sortBy; ?>">
                                                <div class="form-group">
                                                    <label>Từ (VNĐ):</label>
                                                    <input type="number" name="min_price" class="form-control" 
                                                           value="<?php echo $minPrice > 0 ? $minPrice : $globalMinPrice; ?>" 
                                                           min="<?php echo $globalMinPrice; ?>" max="<?php echo $globalMaxPrice; ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>Đến (VNĐ):</label>
                                                    <input type="number" name="max_price" class="form-control" 
                                                           value="<?php echo $maxPrice > 0 ? $maxPrice : $globalMaxPrice; ?>" 
                                                           min="<?php echo $globalMinPrice; ?>" max="<?php echo $globalMaxPrice; ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">Áp dụng</button>
                                                <a href="list_product.php" class="btn btn-secondary btn-sm">Xóa bộ lọc</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product List -->
            <div class="col-lg-9 col-md-8">
                <!-- Sort and Search -->
                <div class="shop__option">
                    <div class="row">
                        <div class="col-lg-7 col-md-7">
                            <div class="shop__option__search">
                                <form action="search_product.php" method="POST">
                                    <input type="text" name="search" class="form-control rounded" placeholder="Tìm kiếm sản phẩm..."/>
                                    <button class="btn btn-primary" type="submit" name="submit">Tìm kiếm</button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5">
                            <div class="shop__option__right">
                                <select onchange="updateSort(this.value)">
                                    <option value="popular" <?php echo $sortBy == 'popular' ? 'selected' : ''; ?>>Phổ biến</option>
                                    <option value="price_low" <?php echo $sortBy == 'price_low' ? 'selected' : ''; ?>>Giá: Thấp đến Cao</option>
                                    <option value="price_high" <?php echo $sortBy == 'price_high' ? 'selected' : ''; ?>>Giá: Cao đến Thấp</option>
                                    <option value="newest" <?php echo $sortBy == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <div class="row">
                    <?php 
                    if (mysqli_num_rows($Products) > 0) :
                        while ($value = mysqli_fetch_assoc($Products)) : 
                    ?>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="product__item">
                                <div class="product__item__pic set-bg">
                                    <a href="product_detail.php?id=<?php echo $value['ProductId']; ?>">
                                        <img src="../admin/uploads/<?php echo htmlspecialchars($value['Image']); ?>" 
                                             alt="<?php echo htmlspecialchars($value['Name']); ?>">
                                    </a>
                                    <?php if ($value['Quantity'] == 0) : ?>
                                        <div class="product__label">
                                            <span class="badge badge-danger">Hết hàng</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product__item__text">
                                    <h6>
                                        <a href="product_detail.php?id=<?php echo $value['ProductId']; ?>">
                                            <?php echo htmlspecialchars($value['Name']); ?>
                                        </a>
                                    </h6>
                                    <?php if ($value['CategoryName']) : ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($value['CategoryName']); ?></small>
                                    <?php endif; ?>
                                    <h5><?php echo number_format($value['SellPrice'], 0, ',', '.'); ?> VNĐ</h5>
                                    <div>
                                        <button class="btn primary-btn mt-2">
                                            <a style="color: white" href="product_detail.php?id=<?php echo $value['ProductId']; ?>">
                                                Chi tiết
                                            </a>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                        endwhile;
                    else :
                    ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <h4>Không tìm thấy sản phẩm nào</h4>
                                <p>Vui lòng thử lại với bộ lọc khác.</p>
                                <a href="list_product.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- Pagination -->
                <?php if ($totalPages > 1) : ?>
                <div class="shop__last__option">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="shop__pagination">
                                <?php
                                $queryParams = $_GET;
                                unset($queryParams['page']);
                                
                                // Previous button
                                if ($page > 1) {
                                    $queryParams['page'] = $page - 1;
                                    echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-left"></span></a>';
                                }
                                
                                // Page numbers
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1) {
                                    $queryParams['page'] = 1;
                                    echo '<a href="?' . http_build_query($queryParams) . '">1</a>';
                                    if ($startPage > 2) echo '<span>...</span>';
                                }
                                
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $queryParams['page'] = $i;
                                    $activeClass = $i == $page ? 'active' : '';
                                    echo '<a href="?' . http_build_query($queryParams) . '" class="' . $activeClass . '">' . $i . '</a>';
                                }
                                
                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) echo '<span>...</span>';
                                    $queryParams['page'] = $totalPages;
                                    echo '<a href="?' . http_build_query($queryParams) . '">' . $totalPages . '</a>';
                                }
                                
                                // Next button
                                if ($page < $totalPages) {
                                    $queryParams['page'] = $page + 1;
                                    echo '<a href="?' . http_build_query($queryParams) . '"><span class="arrow_carrot-right"></span></a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->

<script>
function updateSort(sortValue) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', sortValue);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}
</script>

<!-- Map End -->
<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>