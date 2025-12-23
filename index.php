<?php
include($_SERVER['DOCUMENT_ROOT'] . "/inc/header.php");
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$query = "SELECT products.ProductId, products.Name, products.Image ,products.Quantity ,products.Description ,products.BuyPrice ,products.SellPrice ,products.Status, products.CountView ,products.CategoriId ,products.BrandId ,products.is_accept, orderdetails.Quantity 
from products 
JOIN orderdetails 
ON products.ProductId = orderdetails.ProductId 
ORDER BY orderdetails.Quantity DESC LIMIT 8";
$Products = mysqli_query($conn, $query);

?>
<!-- Hero Section Begin -->
<section class="hero">
    <div class="hero__slider owl-carousel">
        <div class="hero__item set-bg" data-setbg="img/hero/anh1.jpg">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <div class="hero__text">
                            <h2>Làm cho cuộc sống của bạn ngọt ngào hơn</h2>
                            <a href="list_product.php" class="primary-btn">Bánh của chúng tôi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero__item set-bg" data-setbg="img/hero/anh2.jpg">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <div class="hero__text">
                            <h2>Hãy khám phá những bí mật nho nhỏ cùng Cake Shop nhé!</h2>
                            <a href="list_product.php" class="primary-btn">Khám phá ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero__item set-bg" data-setbg="img/hero/hero-11.jpg">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-8">
                        <div class="hero__text">
                            <h2>Bánh ngọt tươi mỗi ngày - Chất lượng đảm bảo</h2>
                            <a href="promotions.php" class="primary-btn">Xem khuyến mãi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- Hero Section End -->

<!-- About Section Begin -->
<section class="about spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="about__text">
                    <div class="section-title">
                        <span>Giới thiệu về tiệm bánh</span> <br>
                        <h2>Làm bánh là nghệ thuật, là cách để thư giãn <br> và tận hưởng cuộc sống!</h2>
                    </div>
                    <p> Cake Shop với hành trình 16 năm hình thành và phát triển, <br> dưới sự nỗ lực không ngừng nghỉ Cake Shop đã mang lại <br> những dấu ấn khó phai trong lòng khách hàng.</p>
                    <p>“ Chúng tôi tự tin dẫn đầu với uy tín và chất lượng,<br>
                        Chúng tôi am hiểu ẩm thực bánh và khẩu vị khách hàng,<br>
                        Chúng tôi không ngừng đổi mới và sáng tạo”.</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <img src="img/instagram/anh2.jpg" alt="" class="img-fluid" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
    </div>
    </div>
</section>
<!-- About Section End -->

<!-- Categories Section Begin -->
<!-- Categories Section End -->
<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="section-title">
                <span>Sản phẩm nổi bật</span>
            </div>
        </div>
    </div>
</div>
<!-- Product Section Begin -->
<section class="product spad">
    <div class="container">
        <div class="row">
            <?php foreach ($Products as $key => $value) : ?>
                <div class="col-lg-3 col-md-6 col-sm-6">
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
</section>
<!-- Team Section End -->

<!-- Promotions Section Begin -->
<?php
// Get featured promotions
$promoQuery = "SELECT * FROM promotions 
               WHERE IsActive = 1 AND IsFeatured = 1
               AND StartDate <= NOW() AND EndDate >= NOW()
               ORDER BY SortOrder ASC, CreatedAt DESC
               LIMIT 3";
$promoResult = mysqli_query($conn, $promoQuery);

if ($promoResult && mysqli_num_rows($promoResult) > 0) :
?>
    <section class="promotions spad" style="background: #fdf3ea;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <span>KHUYẾN MÃI</span>
                        <h2>Ưu đãi đặc biệt dành cho bạn</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php while ($promo = mysqli_fetch_assoc($promoResult)) :
                    $daysLeft = floor((strtotime($promo['EndDate']) - time()) / 86400);
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="promotion-card-home">
                            <?php if ($promo['Image']) : ?>
                                <div class="promotion-image-home">
                                    <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>">
                                        <img src="admin/uploads/promotions/<?php echo htmlspecialchars($promo['Image']); ?>"
                                            alt="<?php echo htmlspecialchars($promo['Title']); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="promotion-content-home">
                                <div class="promotion-discount-home">
                                    <?php if ($promo['DiscountType'] == 'percentage') : ?>
                                        <span class="discount-badge-home">-<?php echo $promo['DiscountValue']; ?>%</span>
                                    <?php elseif ($promo['DiscountType'] == 'fixed') : ?>
                                        <span class="discount-badge-home">-<?php echo number_format($promo['DiscountValue'], 0, ',', '.'); ?> VNĐ</span>
                                    <?php endif; ?>
                                </div>
                                <h4>
                                    <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>">
                                        <?php echo htmlspecialchars($promo['Title']); ?>
                                    </a>
                                </h4>
                                <?php if ($daysLeft > 0) : ?>
                                    <p class="countdown-home"><i class="fa fa-clock-o"></i> Còn <?php echo $daysLeft; ?> ngày</p>
                                <?php endif; ?>
                                <a href="promotion_detail.php?id=<?php echo $promo['PromotionId']; ?>" class="btn btn-primary btn-sm">
                                    Xem ngay
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center mt-4">
                    <a href="promotions.php" class="btn btn-outline-primary">Xem tất cả khuyến mãi</a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<!-- Promotions Section End -->

<!-- News Section Begin -->
<?php
// Get featured news
$newsQuery = "SELECT * FROM news 
              WHERE IsPublished = 1 AND IsFeatured = 1
              ORDER BY PublishedAt DESC, CreatedAt DESC
              LIMIT 3";
$newsResult = mysqli_query($conn, $newsQuery);

if ($newsResult && mysqli_num_rows($newsResult) > 0) :
?>
    <section class="news spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-title">
                        <span>TIN TỨC</span>
                        <h2>Cập nhật mới nhất từ chúng tôi</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php while ($news = mysqli_fetch_assoc($newsResult)) : ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="news-card-home">
                            <?php if ($news['Image']) : ?>
                                <div class="news-image-home">
                                    <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>">
                                        <img src="admin/uploads/news/<?php echo htmlspecialchars($news['Image']); ?>"
                                            alt="<?php echo htmlspecialchars($news['Title']); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="news-content-home">
                                <?php if ($news['Category']) : ?>
                                    <span class="news-category-home"><?php echo htmlspecialchars($news['Category']); ?></span>
                                <?php endif; ?>
                                <h4>
                                    <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>">
                                        <?php echo htmlspecialchars($news['Title']); ?>
                                    </a>
                                </h4>
                                <?php if ($news['Summary']) : ?>
                                    <p><?php echo htmlspecialchars(substr($news['Summary'], 0, 100)); ?><?php echo strlen($news['Summary']) > 100 ? '...' : ''; ?></p>
                                <?php endif; ?>
                                <div class="news-meta-home">
                                    <?php if ($news['PublishedAt']) : ?>
                                        <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($news['PublishedAt'])); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="news_detail.php?id=<?php echo $news['NewsId']; ?>" class="btn btn-outline-primary btn-sm">
                                    Đọc thêm
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center mt-4">
                    <a href="news.php" class="btn btn-primary">Xem tất cả tin tức</a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<!-- News Section End -->

<!-- Testimonial Section Begin -->
<section class="testimonial spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="section-title">
                    <span>CẢM NHẬN CỦA KHÁCH HÀNG</span>
                    <h2>Khách hàng của chúng tôi nói</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="testimonial__slider owl-carousel">
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-1.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Thu Hương</h5>
                                <span>Nghi Phú</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Thử vì tiệm gần nhà và mê đến giờ. Nhân viên thân thiện, nhiệt tình và đặc biệt là bánh ngon</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-2.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Thanh Thúy</h5>
                                <span>Trung Đô</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Ghiền thật sự. Mình không thích ngọt mấy, nên chưa thử qua bánh ngọt ở đây nhưng mà cái bánh nhân chà bông phải nói là đỉnh. Mấy loại bánh khác cũng ngon. </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-1.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Trà Giang</h5>
                                <span>Hà Huy Tập</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Cảm ơn Cake Shop. Bánh ở đây rất ngon và giao hàng cũng nhanh.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-2.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Nhật Anh</h5>
                                <span>Hưng Dũng</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Hiiiii bánh ngon quá. Trang trí đơn giản mà đẹp.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-1.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Phương Anh</h5>
                                <span>Trung Đô</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Bánh vừa đẹp vừa ngon. Mình sẽ quay lại mua thêm nữa. </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="testimonial__item">
                        <div class="testimonial__author">
                            <div class="testimonial__author__pic">
                                <img src="img/testimonial/ta-2.jpg" alt="">
                            </div>
                            <div class="testimonial__author__text">
                                <h5>Minh Thư</h5>
                                <span>Nghi Kim</span>
                            </div>
                        </div>
                        <div class="rating">
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star"></span>
                            <span class="icon_star-half_alt"></span>
                        </div>
                        <p>Cảm ơn Cake Shop nhé. Bánh ở đây rất ngon và rẻ tymmmm.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonial Section End -->

<!-- Instagram Section Begin -->
<section class="instagram spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 p-0">
                <div class="instagram__text">
                    <div class="section-title">
                        <span>Hãy theo dõi </span>
                        <h2>Những khoảnh khắc ngọt <br> ngào được lưu lại làm kỷ <br> niệm.</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic">
                            <img src="img/instagram/instagram-1.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic middle__pic">
                            <img src="img/instagram/instagram-2.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic">
                            <img src="img/instagram/instagram-3.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic">
                            <img src="img/instagram/instagram-4.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic middle__pic">
                            <img src="img/instagram/instagram-5.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                        <div class="instagram__pic">
                            <img src="img/instagram/instagram-3.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Instagram Section End -->

<!-- Map Begin -->
<div class="map">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-7">
                <div class="map__inner">
                    <h6>Cake Shop</h6>
                    <ul>
                        <li>số 11, Nghi Phú, Tp.Vinh, Nghệ An</li>
                        <li>cakeshop@gmail.com</li>
                        <li>0364383435</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="map__iframe">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d30231.667056892187!2d105.66215846313152!3d18.71068132278218!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3139d1fe7c3779eb%3A0xdc076f1bb1825ca9!2zTmdoaSBQaMO6LCBUcC4gVmluaCwgTmdo4buHIEFuLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2sus!4v1699533679073!5m2!1svi!2sus" height="300" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </div>
</div>
<!-- Map End -->
<style>
    .promotion-card-home {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
    }

    .promotion-card-home:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .promotion-image-home {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .promotion-image-home img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .promotion-card-home:hover .promotion-image-home img {
        transform: scale(1.1);
    }

    .promotion-content-home {
        padding: 20px;
        text-align: center;
    }

    .discount-badge-home {
        display: inline-block;
        background: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .promotion-content-home h4 {
        margin-bottom: 10px;
    }

    .promotion-content-home h4 a {
        color: #333;
        text-decoration: none;
    }

    .promotion-content-home h4 a:hover {
        color: #f08632;
    }

    .countdown-home {
        color: #dc3545;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .news-card-home {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
    }

    .news-card-home:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transform: translateY(-5px);
    }

    .news-image-home {
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .news-image-home img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .news-card-home:hover .news-image-home img {
        transform: scale(1.1);
    }

    .news-content-home {
        padding: 20px;
    }

    .news-category-home {
        display: inline-block;
        background: #f08632;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .news-content-home h4 {
        margin-bottom: 10px;
    }

    .news-content-home h4 a {
        color: #333;
        text-decoration: none;
    }

    .news-content-home h4 a:hover {
        color: #f08632;
    }

    .news-content-home p {
        color: #666;
        margin-bottom: 15px;
    }

    .news-meta-home {
        font-size: 14px;
        color: #666;
        margin-bottom: 15px;
    }
</style>

<?php
include($_SERVER["DOCUMENT_ROOT"] . '//inc/footer.php');
?>

<script>
    // Force autoplay for hero slider - Simple and reliable
    // This script runs after jQuery is loaded (in footer.php)
    (function() {
        function initHeroSlider() {
            if (typeof jQuery === 'undefined') {
                // Wait for jQuery to load
                setTimeout(initHeroSlider, 100);
                return;
            }

            var $ = jQuery;

            $(document).ready(function() {
                // Ensure set-bg runs for hero items
                $('.hero__slider .set-bg').each(function() {
                    var bg = $(this).data('setbg');
                    if (bg) {
                        $(this).css('background-image', 'url(' + bg + ')');
                    }
                });

                // Note: Autoplay is handled by main.js and hero-autoplay.js
                // Don't create duplicate autoplay here to avoid conflicts

                // Ensure hero buttons are clickable
                // Just prevent Owl Carousel from blocking clicks, but allow default link behavior
                $('.hero__slider').on('click', '.primary-btn', function(e) {
                    // Stop event from bubbling to Owl Carousel
                    e.stopPropagation();
                    // Allow default link behavior (don't prevent default)
                    // The link will navigate normally
                });
            });
        }

        // Start initialization
        initHeroSlider();
    })();
</script>