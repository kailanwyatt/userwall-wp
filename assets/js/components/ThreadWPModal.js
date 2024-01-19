(function($) {
    $.fn.ThreadWPModal = function(options) {
        var settings = $.extend({
            content: '',
            showCloseBtn: true,
            modalType: 'single-image',
            openTransition: 'fade',
            closeTransition: 'fade',
            openSpeed: 300,
            closeSpeed: 300,
            onClose: function() {},
            onOpen: function() {}
        }, options);

        return this.each(function() {
            var $modal = $(this);

            // Open the modal
            var openModal = function(content) {
                if (settings.openTransition === 'fade') {
                    $modal.fadeIn(settings.openSpeed);
                } else if (settings.openTransition === 'animation') {
                    // Add custom animation here
                    $modal.show();
                } else {
                    $modal.show();
                }
                settings.onOpen();
            };

            // Close the modal
            var closeModal = function() {
                if (settings.closeTransition === 'fade') {
                    $modal.fadeOut(settings.closeSpeed);
                } else if (settings.closeTransition === 'animation') {
                    // Add custom animation here
                    $modal.hide();
                } else {
                    $modal.hide();
                }
                settings.onClose();
            };

            // Update content and close button visibility
            $modal.find('.userwall-wp-modal-right').html(settings.content);
            if (!settings.showCloseBtn) {
                $modal.find('.userwall-wp-close').hide();
            }

            // Public methods
            $modal.on('click', '.userwall-wp-close', function() {
                closeModal();
            });

            $modal[0].openModal = function() {
                openModal();
            };

            $modal[0].closeModal = function() {
                closeModal();
            };
        });
    };

    // Usage:
    
})(jQuery);
