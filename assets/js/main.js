(function($) {
    'use strict';

    var DynamicPostSlider = function($scope, $) {
        
    };

    // Make sure you run this code under Elementor.
    $(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction('frontend/element_ready/dps_post_slider.default', DynamicPostSlider);
    });

})(jQuery);