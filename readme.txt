=== Post Carousel & Grid for Elementor by WebCodingPlace ===
Contributors: webcodingplace
Tags: elementor, post carousel, post slider, post grid, product carousel
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Show posts, WooCommerce products or any post type in an Elementor carousel, grid or list. 52 templates, related posts and a news ticker.

== Description ==

Post Carousel & Grid for Elementor adds one widget to Elementor that shows your posts in a slider, a grid or a list. Pick posts, pages, WooCommerce products or any custom post type, choose one of 52 templates, and style it in the Elementor editor. Everything is free, and the plugin works with the free version of Elementor.

[See the live demos](https://classicaddons.com/elementor/posts-carousel-slider/) or [read the documentation](https://webcodingplace.com/post-carousel-for-elementor).

= What you can build =

* A blog post carousel on your home page
* A related posts slider under every article
* A WooCommerce product carousel with prices, ratings and Add to cart
* A grid of the latest news, events or portfolio items
* A news ticker that scrolls your latest headlines
* A carousel of categories or any other taxonomy terms

= Choose the posts =

* **Posts I choose:** any post type, with search fields to pick or leave out single posts. You can also show taxonomy terms (categories, tags, product categories) instead of posts.
* **Related posts:** on a single post, show posts that share its categories, tags or any other taxonomy. If nothing is related, it can fall back to your latest posts.
* **Current query:** on an archive, category or search page, show the posts of that page. Handy in theme builder templates.
* **Filters:** include or leave out terms (match any or all), include or leave out authors, limit by date (past day, week, month, 3 months, year or a custom range), skip the first posts, and decide what happens to sticky posts.
* **Order:** by date, title, last modified, menu order, comment count, random, ID, author or a custom field.

= WooCommerce product carousel =

When WooCommerce is active you can show featured, on sale, best selling or top rated products and hide the ones that are out of stock. The Card template shows the price with the sale price, a Sale badge, star rating, an Out of stock label and an Add to cart button that works without leaving the page. Variable products get a "Select options" button.

= 52 templates and a Card you can shape =

51 ready made templates cover classic blog cards, overlays, side by side layouts, minimal lists and product cards. The **Card** template lets you switch each part on or off: image, category badge, meta row, excerpt, button, and the product parts.

* **Meta row:** date, author, author picture, categories, comment count and reading time.
* **Image options:** aspect ratio, fit, corner radius and a hover effect (zoom, lift, darken or color on hover). These work on every template.
* **Text:** take the heading and description from any post field or custom field, trim them to a word count, and choose the heading tag (H1 to H6, div, p or span).
* **Link:** link the whole card or only the button, in the same tab or a new one.
* **Social sharing:** optional share links for Facebook, X, LinkedIn, WhatsApp, Pinterest and email.

= Carousel, grid or list =

Switch the layout of any widget. The grid follows Elementor's own breakpoints for desktop, tablet and phone columns. The list layout puts the image next to the text. Grid and list load no slider script at all.

The carousel offers:

* Columns per device and the number of slides to scroll
* Autoplay with a pause button, arrows, dots, loop, RTL and vertical mode
* Fade, coverflow and centered slide effects
* Dots, numbers (2 / 8) or a progress bar
* News ticker mode that scrolls at a steady speed and stops when you hover
* Your own arrow icons from the Elementor icon library

= Light and fast =

* CSS and JavaScript only load on pages that use the widget, and only for the template you picked.
* New carousels use the Swiper library that already comes with Elementor, so nothing extra is downloaded and no jQuery code is added.
* With lazy loading on, images in view still load right away and only the rest wait. The browser gets the right image size for each screen.
* The post and term pickers search as you type instead of loading every post into the page.

= Accessible =

Arrows and dots work with the keyboard and show a clear focus outline. Autoplay stops while the mouse or keyboard focus is inside the carousel, there is a pause button, and autoplay turns off for visitors who ask their device to reduce motion. Copies of slides used for looping are hidden from screen readers.

= Built for developers =

Every template can be overridden from your theme by copying it to `your-theme/dpce/style-{id}.php`. Register your own templates with the `dpce_styles` filter. Change the query, the slider options, the settings passed to templates or the HTML of a single card with filters such as `dpce_query_args`, `dpce_swiper_options`, `dpce_carousel_settings` and `dpce_slide_html`. See the [developer guide on GitHub](https://github.com/rameezwp/webcodingplace-post-carousel-for-elementor/blob/main/docs/HOOKS.md).

== Installation ==

1. In your dashboard go to **Plugins > Add New**, search for "WebCodingPlace Post Carousel" and click **Install Now**, then **Activate**. Elementor must be installed and active.
2. You land on the **Getting Started** page (also under **Elementor > Post Carousel**) with a short checklist and a gallery of all templates.
3. Edit a page with Elementor, search for **Post Carousel** in the widget panel and drag it onto the page.
4. Pick the source of your posts under **Post / Content**, a template under **Appearance > Template Style**, and the layout under **Slider > Layout**.

== Frequently Asked Questions ==

= Do I need Elementor Pro? =

No. The plugin works with the free Elementor plugin, version 3.18 or newer. If you have Elementor Pro, you can also use the widget in Theme Builder templates.

= I used version 1.4. Will my carousels change after the update? =

No. Carousels you made before keep the same template, settings and slider script, and they look the same. The only visible change is a bug fix: the default star icon in some templates was invisible and now shows. You can move an old carousel to the new slider engine with one setting when you are ready.

= What is the difference between the Classic and Modern slider engine? =

Classic uses the Slick library and jQuery, as version 1.4 did. Modern uses the Swiper library that comes with Elementor and needs no jQuery code from us, so pages are lighter. Modern also adds fade and coverflow effects, number and progress bar pagination, the news ticker and keyboard control. New carousels use Modern. You can switch any carousel in **Slider > Slider Engine**.

= Can I show WooCommerce products? =

Yes. Set **Choose Post Type** to Products. You can limit the carousel to featured, on sale, best selling or top rated products and hide out of stock ones. Use the Card template to show price, rating, Sale badge and an Add to cart button.

= How do I show related posts under my articles? =

Set **Source** to "Related posts" and choose what makes posts related: categories, tags, any other taxonomy, or any shared term. Place the widget in a single post template (Elementor Pro Theme Builder or a similar tool) or in a post built with Elementor. The current post is never shown in its own related list.

= Can I use it in an archive or category template? =

Yes. Set **Source** to "Current query" and the widget shows the posts of the archive, category, tag or search page it is on. In the editor you see your latest posts as a preview.

= Can I show a grid instead of a slider? =

Yes. Set **Layout** to Grid or List. You choose the number of columns for desktop, tablet and phone.

= Does it work with custom post types and custom fields? =

Yes. Any public post type appears in the list. The heading and description can come from any post field or custom field key, and you can order posts by a custom field.

= Will it slow down my site? =

The plugin loads nothing on pages without the widget. On pages with the widget it loads one small stylesheet for the base layout and one for the chosen template. The Modern engine reuses Elementor's own Swiper, and grid and list layouts load no script.

= Can I change a template or make my own? =

Yes. Copy `templates/style-{id}.php` from the plugin to `your-theme/dpce/style-{id}.php` and edit the copy. To add a new template, use the `dpce_styles` filter. The developer guide on GitHub shows examples.

= Why does a carousel show at most 100 posts? =

Showing hundreds of posts in one carousel makes pages slow. Carousels saved with "show all posts" show the first 100. A developer can raise this with the `dpce_max_posts` filter.

= Does it work on RTL sites and in my language? =

Yes. The carousel supports right to left languages, and all text in the editor and on the front end can be translated. You can help translate it on translate.wordpress.org.

= Does the plugin collect any data or add tracking? =

No. The plugin sends no data anywhere. Share links only contact a social network when a visitor clicks them. See External services below.

= Where can I get help? =

Ask in the [support forum](https://wordpress.org/support/plugin/webcodingplace-post-carousel-for-elementor/). Please include your Elementor version and the template number you use.

== Screenshots ==

1. The Card template on the Modern engine with category badge, meta row and excerpt.
2. A WooCommerce product carousel with price, sale badge, rating and Add to cart.
3. A few of the 52 templates: overlays, side by side cards and minimal lists.
4. Grid layout with three columns on desktop.
5. News ticker mode and number pagination.
6. Choose posts in the editor: Source, related posts and filters.
7. Switch Card parts on and off and set the image ratio and hover effect.
8. Slider settings: engine, columns per device, effects and pagination.
9. The Getting Started page with a gallery of every template.

== Changelog ==

= 2.0 =
* New: Source setting with "Current query" for archive pages and "Related posts" for single posts.
* New: Filters for terms, authors, date range, offset and sticky posts, and ordering by last modified, ID, author or a custom field.
* New: WooCommerce filters (featured, on sale, best selling, top rated, hide out of stock) and product parts: price, sale badge, rating, stock label and AJAX Add to cart.
* New: Card template with parts you switch on and off.
* New: Meta row with date, author, author picture, categories, comments and reading time.
* New: Image ratio, fit, corner radius and hover effects for every template.
* New: Grid and List layouts.
* New: Modern slider engine based on the Swiper library in Elementor. Carousels made before 2.0 stay on the Classic engine.
* New: Fade, coverflow and centered effects, number and progress bar pagination, news ticker mode, custom arrow icons and a pause button.
* New: Getting Started page under Elementor > Post Carousel.
* Improved: Only the CSS of the chosen template loads (about 6 KB instead of 98 KB).
* Improved: Post and term pickers search as you type and run fewer database queries.
* Improved: Images in view are no longer lazy loaded, and images get a sizes attribute.
* Improved: Keyboard focus outlines, translated arrow labels, reduced motion support, hidden duplicate slides.
* Improved: On the Modern engine and in grids, columns follow Elementor's tablet and phone breakpoints.
* Fixed: The default star icon in templates 1, 2, 10, 15, 24, 26, 30, 37, 38, 48, 49 and 50 did not show.
* Fixed: The Add to cart icon in templates 49 and 50 now adds products to the cart.
* Fixed: Share icons can be clicked when the whole card is a link.
* Fixed: Comment counts include approved comments only.
* Fixed: The carousel script ran twice on each page.
* Fixed: The heading tag setting now also works in template 45.
* Changed: Requires PHP 7.4, WordPress 6.3 and Elementor 3.18 or newer.
* Changed: A carousel set to show all posts shows up to 100.

= 1.4 =
* 51 carousel templates added.
* New "Link Area" setting: link the whole card or only the Read More button.
* Replaced Font Awesome with bundled inline SVG icons so no external icon font is required.

= 1.2 =
* Documented external services (social share links).

= 1.1 =
* Bug fixes.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 2.0 =
Big free update: related posts, WooCommerce product cards, grid and list layouts and a lighter slider engine. Existing carousels keep working and look the same. Needs PHP 7.4, WordPress 6.3 and Elementor 3.18.

== External services ==

The plugin does not contact any outside service on its own. It sends no usage data and has no tracking.

The only outside services involved are social networks, through the optional **Social Sharing** setting of the widget (off by default). When it is on, the carousel shows share links that point to the addresses below. Nothing is sent when the page loads. Data goes to a network only when a visitor clicks one of its links, and it is limited to the public address and title of the post.

= Facebook =
Lets visitors share a post on Facebook.
Address: https://www.facebook.com/sharer/sharer.php
Sent on click: post URL.
Terms of service: https://www.facebook.com/legal/terms
Privacy policy: https://www.facebook.com/privacy/policy

= X (Twitter) =
Lets visitors share a post on X.
Address: https://twitter.com/intent/tweet
Sent on click: post URL and title.
Terms of service: https://x.com/en/tos
Privacy policy: https://x.com/en/privacy

= LinkedIn =
Lets visitors share a post on LinkedIn.
Address: https://www.linkedin.com/sharing/share-offsite/
Sent on click: post URL.
Terms of service: https://www.linkedin.com/legal/user-agreement
Privacy policy: https://www.linkedin.com/legal/privacy-policy

= WhatsApp =
Lets visitors share a post on WhatsApp.
Address: https://api.whatsapp.com/send
Sent on click: post URL and title.
Terms of service: https://www.whatsapp.com/legal/terms-of-service
Privacy policy: https://www.whatsapp.com/legal/privacy-policy

= Pinterest =
Lets visitors pin a post on Pinterest.
Address: https://pinterest.com/pin/create/button/
Sent on click: post URL and title.
Terms of service: https://policy.pinterest.com/en/terms-of-service
Privacy policy: https://policy.pinterest.com/en/privacy-policy

The Email share link is a standard `mailto:` link. It opens the visitor's own email program and contacts no server.

The Getting Started page and the review request contain plain links to WordPress.org, webcodingplace.com and classicaddons.com. They only open when you click them.

== Third-party libraries ==

* **Slick Carousel** 1.8.1 by Ken Wheeler, MIT license, https://github.com/kenwheeler/slick. Bundled in `assets/vendor/slick/` and loaded from your site. Used by carousels on the Classic engine.
* **Swiper**, MIT license, https://swiperjs.com. Not bundled: the Modern engine uses the copy that comes with Elementor.

No file is loaded from a CDN.
