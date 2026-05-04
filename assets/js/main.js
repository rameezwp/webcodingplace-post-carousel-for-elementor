/**
 * Dynamic Post Carousel for Elementor — frontend.
 *
 * Reads slick options from data-slick on the carousel wrapper, then
 * initializes Slick on its child track.
 */
(function ($) {
    'use strict';

    var initCarousel = function ($scope) {
        var $carousel = $scope.find('.dpce-carousel').first();
        if (!$carousel.length) {
            $carousel = $scope.is('.dpce-carousel') ? $scope : $();
        }
        if (!$carousel.length || typeof $.fn.slick !== 'function') {
            return;
        }

        var options = {};
        try {
            options = JSON.parse($carousel.attr('data-slick') || '{}');
        } catch (e) {
            options = {};
        }

        var $track = $carousel.find('.dpce-track');
        if (!$track.length) {
            return;
        }
        if ($track.hasClass('slick-initialized')) {
            $track.slick('unslick');
        }
        $track.slick(options);
    };

    $(window).on('elementor/frontend/init', function () {
        if (typeof elementorFrontend === 'undefined') {
            return;
        }
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/dpce_post_carousel.default',
            initCarousel
        );
    });

    // Also initialize on plain front-end (when not inside Elementor preview).
    $(function () {
        $('.dpce-carousel').each(function () {
            initCarousel($(this));
        });
    });
})(jQuery);
