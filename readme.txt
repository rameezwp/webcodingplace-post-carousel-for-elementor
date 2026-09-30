=== WebCodingPlace Post Carousel for Elementor ===
Contributors: webcodingplace
Tags: elementor, carousel, slider, posts, slick
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display posts, custom post types or taxonomy terms in a beautiful, fully responsive carousel widget for Elementor with 50+ ready-made templates.

== Description ==

WebCodingPlace Post Carousel for Elementor adds a single, deeply configurable carousel widget that lets you slide any post type, including WooCommerce products, portfolios, events, custom post types and taxonomy terms through ready-made templates. Pick a heading source from any post field or meta key, trim it to a word count, swap the read-more text and target, choose your image size, lazy-load thumbnails, enable social sharing and apply your own CSS, all from the Elementor editor.

### Quick Links

[Live Demos](https://classicaddons.com/elementor/posts-carousel-slider/).
[More Details](https://webcodingplace.com/post-carousel-for-elementor).

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
* Social sharing buttons, placeholder image and "hide current post" support

== Screenshots ==

1. Post and Content Settings
2. Styles and Typography Settings 
3. Slider Settings
4. Advanced Settings

= Developers =

Templates fire action hooks (`dpce_carousel_thumbnail`, `dpce_carousel_title`, `dpce_carousel_desc`, `dpce_carousel_read_more`, `dpce_carousel_meta`, `dpce_carousel_share`) so you can override individual pieces without forking. Drop a `dpce/style-{id}.php` file into your theme to override a bundled template.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **WebCodingPlace Post Carousel for Elementor** through the *Plugins* menu in WordPress.
3. Edit any page with Elementor and search for **WebCodingPlace Post Carousel** in the widget panel.

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

No. It works with the free Elementor plugin (3.0 or newer).

= Can I add my own templates? =

Yes. Hook into the `dpce_styles` filter to register a new style, then add a `templates/style-{id}.php` file (or override one from your theme).

== External services ==

This plugin generates outbound share URLs for the optional **Social Sharing** feature in the carousel widget. When the *Enable Social Sharing* setting is turned on (it is OFF by default), the carousel renders a row of links that point to the share endpoints listed below. **No data is sent automatically.** Data is only transmitted to a third party when an end-user clicks one of those share links in their browser, and the only data appended to the URL is the post's public permalink and post title.

= Facebook =
Used to let visitors share a post on Facebook.
Endpoint: https://www.facebook.com/sharer/sharer.php
Data sent on click: post URL.
Terms of service: https://www.facebook.com/legal/terms
Privacy policy: https://www.facebook.com/privacy/policy

= X (Twitter) =
Used to let visitors share a post on X.
Endpoint: https://twitter.com/intent/tweet
Data sent on click: post URL, post title.
Terms of service: https://twitter.com/en/tos
Privacy policy: https://twitter.com/en/privacy

= LinkedIn =
Used to let visitors share a post on LinkedIn.
Endpoint: https://www.linkedin.com/sharing/share-offsite/
Data sent on click: post URL.
Terms of service: https://www.linkedin.com/legal/user-agreement
Privacy policy: https://www.linkedin.com/legal/privacy-policy

= WhatsApp =
Used to let visitors share a post on WhatsApp.
Endpoint: https://api.whatsapp.com/send
Data sent on click: post URL, post title.
Terms of service: https://www.whatsapp.com/legal/terms-of-service
Privacy policy: https://www.whatsapp.com/legal/privacy-policy

= Pinterest =
Used to let visitors pin a post on Pinterest.
Endpoint: https://pinterest.com/pin/create/button/
Data sent on click: post URL, post title.
Terms of service: https://policy.pinterest.com/en/terms-of-service
Privacy policy: https://policy.pinterest.com/en/privacy-policy

The Email option uses a standard `mailto:` link handled by the visitor's local email client and does not contact any third-party server.

== Third-party libraries ==

This plugin bundles **Slick Carousel** (https://kenwheeler.github.io/slick/) by Ken Wheeler, distributed under the MIT license. Files are loaded locally from `assets/vendor/slick/` and no remote requests are made.

== Changelog ==

= 1.4 =
* 51 carousel templates added.
* New "Link Area" setting: link the whole card or only the Read More button.
* Replaced Font Awesome with bundled inline SVG icons so no external icon font is required.

= 1.2 =
* Documented external services (social-share endpoints).

= 1.1 =
* Bug fixes.

= 1.0 =
* Initial release.
