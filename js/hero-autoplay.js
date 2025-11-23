/**
 * Hero Slider Autoplay - Standalone script
 * This ensures autoplay works regardless of Owl Carousel version
 */
(function() {
    'use strict';
    
    function initHeroAutoplay() {
        var slider = jQuery('.hero__slider');
        
        if (!slider.length) {
            return;
        }
        
        // Wait for slider to be initialized
        var checkInterval = setInterval(function() {
            if (slider.data('owl.carousel')) {
                clearInterval(checkInterval);
                startAutoplay();
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(function() {
            clearInterval(checkInterval);
            if (slider.data('owl.carousel')) {
                startAutoplay();
            }
        }, 5000);
        
        function startAutoplay() {
            var autoplayTimer = null;
            var isHovered = false;
            
            // Start autoplay
            function play() {
                if (autoplayTimer) {
                    clearInterval(autoplayTimer);
                }
                
                autoplayTimer = setInterval(function() {
                    if (!isHovered && slider.length && slider.data('owl.carousel')) {
                        // Use 1 second transition
                        slider.trigger('next.owl.carousel', [1000]);
                    }
                }, 5000); // 5 seconds between slides (each slide shows for 5 seconds)
            }
            
            // Pause on hover
            slider.on('mouseenter', function() {
                isHovered = true;
                if (autoplayTimer) {
                    clearInterval(autoplayTimer);
                    autoplayTimer = null;
                }
            });
            
            // Resume on mouse leave
            slider.on('mouseleave', function() {
                isHovered = false;
                play();
            });
            
            // Start playing
            play();
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroAutoplay);
    } else {
        initHeroAutoplay();
    }
    
    // Also try after jQuery is ready
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function() {
            setTimeout(initHeroAutoplay, 1000);
        });
    }
})();

