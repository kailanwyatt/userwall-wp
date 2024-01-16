(function($) {
    $.fn.ThreadWPSlider = function(options) {
        var settings = $.extend({
            slideClass: 'slide',
            slideWrapperClass: 'slider-wrapper',
            showArrows: true,
            leftArrowHTML: '<span class="prev" data-slide-left>&#8592;</span>',
            rightArrowHTML: '<span class="next" data-slide-right>&#8594;</span>',
            showPagination: true,
            slideSpeed: 500,
            autoPlay: false,
            autoPlayInterval: 3000,
            currentIndex: 0,
            draggable: true,
            onSlideChange: function() {}
        }, options);

        return this.each(function() {
            var $slider = $(this);
            console.log( $slider );
            var $slides = $slider.find('.' + settings.slideClass);
            var currentIndex = settings.currentIndex;
            var slideWidth = 100;
            var isDragging = false;

            if (settings.slideWrapperClass) {
                $slider.wrap('<div class="' + settings.slideWrapperClass + '"></div>');
            }

            var $sliderParent = $slider.closest( '.' + settings.slideWrapperClass ).length ? $slider.closest( '.' + settings.slideWrapperClass ) : $slider;

            function updatePagination() {
                if (settings.showPagination) {
                    $slider.find('.pagination-dot').removeClass('active');
                    $slider.find('.pagination-dot').eq(currentIndex).addClass('active');
                }
            }

            function moveToSlide(index) {
                
                if (index >= 0 && index < $slides.length) {
                    currentIndex = index;
                    console.log( currentIndex );
                    $slides.hide();
                    $slides.each( function( index, el ) {
                        if ( currentIndex == parseInt( jQuery(this).attr('data-index') ) ) {
                            jQuery( this ).show();
                        }
                    });
                    //$slides.find('.' + settings.slideClass + '[data-index="' + currentIndex + '"]').show();
                    settings.onSlideChange(currentIndex);
                    var leftPosition = -currentIndex * slideWidth + "%";
                    /*$slider.animate({ left: leftPosition }, settings.slideSpeed, function() {
                        // Hide other slides
                        $slides.not(':eq(' + currentIndex + ')').hide();
                        // Show current slide
                        $slides.eq(currentIndex).show();
                        settings.onSlideChange(currentIndex);
                        updatePagination();
                    });*/
                }
            }

            function slideLeft() {
                moveToSlide(currentIndex - 1);
            }

            function slideRight() {
                moveToSlide(currentIndex + 1);
            }

            // Initialize pagination
            if (settings.showPagination) {
                $slider.find('.' + settings.slideClass).each(function(index) {
                    var dot = $('<span class="pagination-dot"></span>');
                    dot.on('click', function() {
                        moveToSlide(index);
                    });
                    $slider.find('.slider-pagination').append(dot);
                });
            }

            // Initialize navigation arrows
            if (settings.showArrows) {
                $sliderParent.append('<div class="slider-nav">' + settings.leftArrowHTML + settings.rightArrowHTML + '</div>');
                $sliderParent.find('[data-slide-left]').on('click', slideLeft);
                $sliderParent.find('[data-slide-right]').on('click', slideRight);
            }

            // Initialize auto-play
            if (settings.autoPlay) {
                setInterval(function() {
                    if (!isDragging) {
                        slideRight();
                    }
                }, settings.autoPlayInterval);
            }

            // Initialize draggable slides
            if (settings.draggable) {
                $slides.draggable({
                    axis: "x",
                    snap: true,
                    snapTolerance: 100,
                    start: function() {
                        isDragging = true;
                    },
                    drag: function(event, ui) {
                        // Update the currentIndex based on the drag position
                        currentIndex = Math.round(ui.position.left / $slider.width() * $slides.length);
                    },
                    stop: function() {
                        isDragging = false;
                        moveToSlide(currentIndex);
                    }
                });
            }

            // Initialize the slider's wrapper
            $slider.find('.slider-wrapper').css('width', $slides.length * slideWidth + "%");

            // Initially hide all slides except the current one
            $slides.each( function( index, el ) {
                jQuery(this).attr('data-index', index );
            });
            $slides.hide();
            //$slides.eq(currentIndex).show();
            $slider.find('.' + settings.slideClass + '[data-index="' + currentIndex + '"]');
            // Set the initial slide position
            moveToSlide(currentIndex);
        });
    };

    window.ThreadWPSlider = $.fn.ThreadWPSlider;
}(jQuery));
