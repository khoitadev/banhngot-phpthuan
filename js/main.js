/*  ---------------------------------------------------
    Theme Name: Cake
    Description: Cake e-commerce tamplate
    Author: Colorib
    Author URI: https://www.colorib.com/
    Version: 1.0
    Created: Colorib
---------------------------------------------------------  */

'use strict';

(function ($) {

    /*------------------
        Preloader
    --------------------*/
    $(window).on('load', function () {
        $(".loader").fadeOut();
        $("#preloder").delay(200).fadeOut("slow");
    });

    /*------------------
        Background Set
    --------------------*/
    $('.set-bg').each(function () {
        var bg = $(this).data('setbg');
        $(this).css('background-image', 'url(' + bg + ')');
    });

    //Search Switch
    $('.search-switch').on('click', function () {
        $('.search-model').fadeIn(400);
    });

    $('.search-close-switch').on('click', function () {
        $('.search-model').fadeOut(400, function () {
            $('#search-input').val('');
        });
    });

    //Canvas Menu
    $(".canvas__open").on('click', function () {
        $(".offcanvas-menu-wrapper").addClass("active");
        $(".offcanvas-menu-overlay").addClass("active");
        $("body").css("overflow", "hidden");
    });

    $(".offcanvas-menu-overlay").on('click', function () {
        $(".offcanvas-menu-wrapper").removeClass("active");
        $(".offcanvas-menu-overlay").removeClass("active");
        $("body").css("overflow", "");
    });

    $(".offcanvas__close").on('click', function () {
        $(".offcanvas-menu-wrapper").removeClass("active");
        $(".offcanvas-menu-overlay").removeClass("active");
        $("body").css("overflow", "");
    });


    /*------------------
		Navigation
	--------------------*/
    $(".mobile-menu").slicknav({
        prependTo: '#mobile-menu-wrap',
        allowParentLinks: true
    });

    /*-----------------------
        Hero Slider - Smooth Transition with Manual Autoplay
    ------------------------*/
    var heroSlider = $(".hero__slider");
    var heroAutoplayInterval = null;
    var heroIsPaused = false;
    
    if (heroSlider.length) {
        // Initialize slider WITHOUT autoplay (we'll handle it manually)
        heroSlider.owlCarousel({
            loop: true,
            margin: 0,
            items: 1,
            dots: true,
            nav: true,
            navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            smartSpeed: 1000,
            autoHeight: false,
            autoplay: false,
            navSpeed: 1000,
            dotsSpeed: 1000,
            mouseDrag: true,
            touchDrag: true,
            pullDrag: true
        });
        
        // Manual autoplay function - SIMPLE AND RELIABLE
        function startHeroAutoplay() {
            // Clear any existing interval first
            if (heroAutoplayInterval) {
                clearInterval(heroAutoplayInterval);
                heroAutoplayInterval = null;
            }
            
            // Reset pause flag
            heroIsPaused = false;
            
            // Start new interval - this will trigger after 5 seconds
            heroAutoplayInterval = setInterval(function() {
                if (!heroIsPaused && heroSlider.length && heroSlider.data('owl.carousel')) {
                    try {
                        // Trigger next slide
                        heroSlider.trigger('next.owl.carousel', [1000]); // 1 second transition
                    } catch(e) {
                        console.log('Autoplay error:', e);
                    }
                }
            }, 5000); // 5 seconds between slides (each slide shows for 5 seconds)
        }
        
        // Start autoplay when slider is ready
        heroSlider.on('initialized.owl.carousel', function() {
            setTimeout(function() {
                startHeroAutoplay();
            }, 1000);
        });
        
        // Pause on hover
        heroSlider.on('mouseenter', function() {
            heroIsPaused = true;
        });
        
        // Resume on mouse leave
        heroSlider.on('mouseleave', function() {
            heroIsPaused = false;
        });
        
        // Fallback: Start autoplay after 2 seconds regardless
        setTimeout(function() {
            if (heroSlider.data('owl.carousel')) {
                if (!heroAutoplayInterval) {
                    startHeroAutoplay();
                }
            }
        }, 2500);
        
        // Additional fallback: Start autoplay after page load
        $(window).on('load', function() {
            setTimeout(function() {
                if (heroSlider.data('owl.carousel') && !heroAutoplayInterval) {
                    startHeroAutoplay();
                }
            }, 3000);
        });
    }

    /*--------------------------
        Categories Slider
    ----------------------------*/
    $(".categories__slider").owlCarousel({
        loop: true,
        margin: 22,
        items: 5,
        dots: false,
        nav: true,
        navText: ["<span class='arrow_carrot-left'><span/>", "<span class='arrow_carrot-right'><span/>"],
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: false,
        responsive: {
            0: {
                items: 1,
                margin: 0
            },
            480: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });

    /*-----------------------------
        Testimonial Slider
    -------------------------------*/
    $(".testimonial__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 2,
        dots: true,
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            }
        }
    });

    /*---------------------------------
        Related Products Slider
    ----------------------------------*/
    $(".related__products__slider").owlCarousel({
        loop: true,
        margin: 0,
        items: 4,
        dots: false,
        nav: true,
        navText: ["<span class='arrow_carrot-left'><span/>", "<span class='arrow_carrot-right'><span/>"],
        smartSpeed: 1200,
        autoHeight: false,
        autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
        }
    });

    /*--------------------------
        Select
    ----------------------------*/
    $("select").niceSelect();

    /*------------------
		Magnific
	--------------------*/
    $('.video-popup').magnificPopup({
        type: 'iframe'
    });

    /*------------------
        Barfiller
    --------------------*/
    $('#bar1').barfiller({
        barColor: '#111111',
        duration: 2000
    });
    $('#bar2').barfiller({
        barColor: '#111111',
        duration: 2000
    });
    $('#bar3').barfiller({
        barColor: '#111111',
        duration: 2000
    });


    /*------------------
		Single Product
	--------------------*/
    $('.product__details__thumb img').on('click', function () {
        $('.product__details__thumb .pt__item').removeClass('active');
        $(this).addClass('active');
        var imgurl = $(this).data('imgbigurl');
        var bigImg = $('.big_img').attr('src');
        if (imgurl != bigImg) {
            $('.big_img').attr({
                src: imgurl
            });
        }
    });

    /*-------------------
		Quantity change
	--------------------- */
    var proQty = $('.pro-qty');
    proQty.prepend('<span class="dec qtybtn">-</span>');
    proQty.append('<span class="inc qtybtn">+</span>');
    proQty.on('click', '.qtybtn', function () {
        var $button = $(this);
        var oldValue = $button.parent().find('input').val();
        if ($button.hasClass('inc')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            // Don't allow decrementing below zero
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        $button.parent().find('input').val(newVal);
    });

    

    $(".product__details__thumb").niceScroll({
        cursorborder: "",
        cursorcolor: "rgba(0, 0, 0, 0.5)",
        boxzoom: false
      });

})(jQuery);