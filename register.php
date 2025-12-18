<?php
// Handle POST (and redirects) BEFORE outputting any HTML
include_once $_SERVER['DOCUMENT_ROOT'] . "/classes/user_register.php";

$ad = new user_register();
$insertUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $insertUser = $ad->insert_user($_POST);

    // If register success -> redirect to login page
    if (is_string($insertUser) && stripos($insertUser, 'Register succesfully') !== false) {
        header("Location: login.php?registered=1");
        exit;
    }
}

include_once $_SERVER['DOCUMENT_ROOT'] . "/inc/auth_header.php";
?>
<section class="hero">
    <div class="hero__item set-bg" data-setbg="img/hero/hero-11.jpg">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-6">
                    <div class="class__sidebar">
                        <h5 style="font-family: Callephane; margin-left: 200px;"><img src="img/logo_2.png" alt="">Đăng ký</h5>
                        <?php if (!empty($insertUser)) {
                            echo $insertUser;
                        } ?>
                        <form action="register.php" method="post">
                            <input type="text" placeholder="Nhập tên của bạn" name="Fullname">
                            <input type="text" placeholder="Nhập email của bạn" name="Email">
                            <input type="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" name="Password">
                            <button type="submit" class="site-btn">Đăng ký</button>
                            <button class="site-btn mt-4">
                                <a style="color:white" href="login.php">Đăng nhập</a>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Section End -->

<?php
include_once $_SERVER["DOCUMENT_ROOT"] . '/inc/footer.php';
?>