=== Dynamic Post Carousel for Elementor ===
Contributors: webcodingplace
Tags: elementor, carousel, slider, posts, slick
Requires at least: 5.6
Tested up to: 6.7
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display posts, custom post types or taxonomy terms in a beautiful, fully responsive carousel widget for Elementor with 50+ ready-made templates.

== Description ==

Dynamic Post Carousel for Elementor adds a single, deeply configurable carousel widget that lets you slide any post type — including WooCommerce products, portfolios, events, custom post types and taxonomy terms — through ready-made templates. Pick a heading source from any post field or meta key, trim it to a word count, swap the read-more text and target, choose your image size, lazy-load thumbnails, enable social sharing and apply your own CSS, all from the Elementor editor.

= Highlights =

* Choose source: Post Type or Taxonomy
* Multi-select posts or terms, plus exclude-by-ID
* Heading & description from any built-in field or custom meta key
* Word-count trimming with custom suffix
* Optional shortcode / HTML rendering inside the description
* Read-more text, classes and link target
* Slick-powered slider with full responsive column control, autoplay, dots, arrows, RTL, vertical mode and adaptive height
* Pluggable template registry — add your own styles via the `dpce_styles` filter and drop a PHP template in your theme
* Per-style appearance controls auto-generated from the styles array
* Custom CSS box, social sharing buttons, placeholder image and "hide current post" support

= Developers =

Templates fire action hooks (`dpce_carousel_thumbnail`, `dpce_carousel_title`, `dpce_carousel_desc`, `dpce_carousel_read_more`, `dpce_carousel_meta`, `dpce_carousel_share`) so you can override individual pieces without forking. Drop a `dynamic-post-carousel-for-elementor/style-{id}.php` file into your theme to override a bundled template.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **Dynamic Post Carousel for Elementor** through the *Plugins* menu in WordPress.
3. Edit any page with Elementor and search for **Dynamic Post Carousel** in the widget panel.

= Required vendor files =

This plugin uses Slick Carousel for the slider. Place the official Slick distribution files at:

* `assets/vendor/slick/slick.min.js`
* `assets/vendor/slick/slick.css`
* `assets/vendor/slick/slick-theme.css` (optional)

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

No. It works with the free Elementor plugin (3.0 or newer).

= Can I add my own templates? =

Yes. Hook into the `dpce_styles` filter to register a new style, then add a `templates/style-{id}.php` file (or override one from your theme).

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
