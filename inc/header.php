<?php
session_start();
error_reporting(0);
include($_SERVER['DOCUMENT_ROOT'] . "/database/connect.php");

$cart = (isset($_SESSION['cart'])) ? $_SESSION['cart'] : [];

$user = ((isset($_SESSION['user']))) ? $_SESSION['user'] : [];

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Cake Template">
    <meta name="keywords" content="Cake, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cake Shop</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <!-- Css Styles -->
    <link rel="stylesheet" href="css/boottrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/flaticon.css" type="text/css">
    <link rel="stylesheet" href="css/barfiller.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slickna.min.css" type="text/css">
    <link rel="stylesheet" href="css/styllee.css" type="text/css">
    <link rel="stylesheet" href="css/chatbot.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Offcanvas Menu Begin -->
    <div class="offcanvas-menu-overlay"></div>
    <div class="offcanvas-menu-wrapper">
        <div class="offcanvas__cart">
            <div class="offcanvas__cart__links">
                <a href="#" class="search-switch"><img src="img/icon/search.png" alt=""></a>
                <a href="#"><img src="img/icon/heart.png" alt=""></a>
            </div>
            <div class="offcanvas__cart__item">
                <a href="#"><img src="img/icon/cart.png" alt=""> <span>0</span></a>
                <div class="cart__price">Giỏ hàng: <span>$0.00</span></div>
            </div>
        </div>
        <div class="offcanvas__logo">
            <a href="./index.php"><img src="img/logo.png" alt=""></a>
        </div>
        <div id="mobile-menu-wrap"></div>
        <div class="offcanvas__option">
            <ul>
                <li>VND <span class="arrow_carrot-down"></span>
                    <ul>
                        <li>VND</li>
                        <li>VND</li>
                    </ul>
                </li>
                <li>ENG <span class="arrow_carrot-down"></span>
                    <ul>
                        <li>Spanish</li>
                        <li>ENG</li>
                    </ul>
                </li>
                <li><a href="#">Đăng nhập</a> <span class="arrow_carrot-down"></span></li>
            </ul>
        </div>
    </div>
    <!-- Offcanvas Menu End -->

    <!-- Header Section Begin -->
    <header class="header">
        <div class="header__top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="header__top__inner">
                            <div class="header__top__left">
                            </div>
                            <div class="header__logo" style="left: 5%;">
                                <a href="#"><img src="img/11.JPG" alt=""></a>
                            </div>
                            <div class="header__top__right">

                                <div class="header__top__left">
                                    <ul style="margin-right: 30px;">
                                        <?php if (isset($user['Email'])) { ?>
                                            <li><?php echo $user['Fullname'] ?>
                                                <ul>
                                                    <li><a style="color:#ffffff; white-space: nowrap;" href="logout.php">Đăng xuất</a></li>
                                                </ul>
                                            </li><?php } else { ?><li>
                                                Tài khoản
                                                <ul>
                                                    <li><a href="register.php" style="color:#ffffff; white-space: nowrap;">Đăng ký </a></li>
                                                    <li><a href="login.php" style="color:#ffffff; white-space: nowrap;">Đăng nhập </a></li>
                                                </ul>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="header__top__right__cart">
                                    <a href="view_cart.php"><img src="img/icon/cart.png" alt=""> <span>0</span></a>
                                    <div class="cart__price">(<?php echo count($cart) ?>)

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="canvas__open"><i class="fa fa-bars"></i></div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li><a href="./index.php">Trang chủ</a></li>
                            <li><a href="./list_product.php">Cửa hàng</a></li>
                            <li><a href="./promotions.php">Khuyến mãi</a></li>
                            <li><a href="./news.php">Tin tức</a></li>
                            <li><a href="./history_order.php">Lịch sử đơn hàng</a> </li>
                            <li><a href="./contact_add.php">Liên hệ</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- Header Section End -->