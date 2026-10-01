# Release notes (work in progress for 2.0)

This file collects what changed, what to test by hand before releasing, and migration notes. It grows with each phase. The version number is not bumped in code; do that yourself when you release.

## Phase 2: engineering foundation

### What changed for site owners

* **Requirements:** PHP 7.4, WordPress 6.3 and Elementor 3.18 or newer. Sites below this stay on 1.4 (WordPress.org will not offer the update). The old "Elementor 3.0" minimum was never accurate: the widget has needed Elementor 3.5 since 1.0.
* **New "Slider Engine" setting** (Content tab, Slider section).
  * Carousels created before 2.0 keep the **Classic (Slick)** engine and look exactly as before.
  * New carousels use the **Modern (Swiper)** engine: the Swiper library that comes with Elementor, no jQuery code of ours, smaller pages, keyboard support, and an optional pause button for autoplay.
  * Switching is one click and can be undone.
* **Much less CSS per page:** only the stylesheet of the template in use is loaded (about 6 KB instead of 98 KB).
* **Fewer database queries:** the "Select posts" and "Select terms" fields now search as you type. Before, every page with a carousel ran a query per post type and taxonomy to fill those lists, and only the first 200 posts could be picked.
* **Faster images:** the images visible on load are no longer lazy loaded, and the browser gets a `sizes` hint based on the column count, so it downloads smaller files.
* **Bug fixes:**
  * The default star icon (templates 1, 2, 10, 15, 24, 26, 30, 37, 38, 48, 49, 50) was invisible on the front end because it was saved with a wrong library name. It now shows. **This is a visible change on live sites.**
  * Icon Color now sets both `color` and `fill`, so it also works for font icons.
  * Share icons in "Whole Card" mode sit above the card link and can be clicked.
  * Comment counts show approved comments only (before, comments waiting for moderation were counted too).
  * The carousel script started twice on every page load.
  * The Heading HTML Tag setting now also applies to template 45, and offers H1, div, p and span.
* **Accessibility:** visible keyboard focus, translated arrow labels, cloned slides no longer contain focusable links, and autoplay is off for visitors who ask their system for reduced motion.
* **Widget name in the panel:** "Post Carousel (WCP)", with more search keywords (post, blog, news, product, woocommerce, portfolio, related, and more). The internal widget name `dpce_post_carousel` is unchanged, so saved pages are not affected.

### Migration notes

* No database migration. All control IDs, the `dpce_` prefix, the `dpce_styles` filter, template file names and the theme override path (`dpce/style-{id}.php`) are unchanged.
* **Number of posts:** the editor accepts 1 to 100. A carousel saved with `-1` (all posts) still works, but shows at most 100 posts. Raise the cap with the `dpce_max_posts` filter.
* On the first request after the update, the plugin clears Elementor's generated CSS and asset cache once (the same thing **Elementor > Tools > Regenerate files and data** does), so pages pick up the new, smaller asset list.
* Markup changes on classic engine carousels, none of them visible: `rel="follow"` is removed from same tab links, share links print on one line, placeholder images get `alt=""`, and `data-slick` carries translated arrow labels.
* Asset handles: `dpce-frontend` (CSS) is now only loaded in the editor. Front end pages load `dpce-base` plus `dpce-style-{id}`. If your theme dequeued `dpce-frontend`, dequeue those instead.
* Kept but deprecated: `dpce_get_posts_for_select()` and `dpce_get_terms_for_select()`.

### New developer hooks

| Hook | Type | Purpose |
|---|---|---|
| `dpce_max_posts` | filter | Largest number of posts one carousel may query (default 100). |
| `dpce_image_sizes_attr` | filter | The `sizes` attribute for carousel images. |
| `dpce_slick_options` | filter | Options passed to Slick (classic engine). |
| `dpce_swiper_options` | filter | Options passed to Swiper (modern engine). |

Full hook documentation follows in `docs/HOOKS.md` (Phase 3).

### What I verified

* WordPress 7.1.2 on PHP 8.4, using a test site with 8 posts, 6 featured images, 3 categories and 69 carousel pages (all 51 templates plus option combinations).
* Elementor versions: **3.18.3** (the new minimum), **3.32.0**, **4.0.8** and **4.3.2** (the latest release, tested in Phase 4). On each one: HTML compared with 1.4, the modern engine tested in Chromium, and the editor tested. Screenshots compared on 3.32.0 and 4.0.8. The plugin header now says "Elementor tested up to: 4.3". Elementor Pro was not tested (not available in the test environment), so its "tested up to" value is unchanged.
* **HTML of every 1.4 carousel** rendered by 1.4 and by this branch, compared line by line. The only differences are the intended ones listed above.
* **Screenshots of every 1.4 carousel** at 1280 px and 390 px wide, compared pixel by pixel against 1.4. Identical apart from the star icon now showing (template 48 in the test set), the placeholder alt text, and one case of sub-pixel text smoothing.
* **Modern engine** in Chromium (including Elementor 3.18, where Swiper is loaded on demand): arrows, dots, loop, no loop, vertical, RTL, autoplay with pause button, hover and focus pause, hidden loop copies, no console errors.
* **Editor** (Elementor 3.32): search field lists and labels saved posts, old carousels stay on the classic engine (also when duplicated), new carousels get the modern engine, both engines render in the preview, no JavaScript errors.
* CI on GitHub: PHP 7.4 to 8.4 syntax, PHPCS (WordPress Coding Standards 3), PHPStan level 5 and the official Plugin Check action all pass.

### Manual test cases before release

Run these on a staging copy of a real site that used 1.4.

1. **Old carousels look the same.** Before updating, take screenshots of three pages with carousels (desktop and phone). Update. Compare. Only the star icon (if you use one of the templates listed above) should differ.
2. **Old carousels keep the classic engine.** Open one in Elementor. Slider section shows "Slider Engine: Classic (Slick)" and an info notice.
3. **Switch an old carousel to Modern.** Same columns, arrows, dots and colors; no jQuery Slick file in the page source.
4. **Add a new carousel.** Slider Engine shows "Modern". Autoplay on shows the pause button; pressing it stops the slides.
5. **Selected posts.** A carousel that had posts picked in 1.4 still shows those posts, and the field shows their titles. Search finds a post that is not among the first 200 alphabetically.
6. **Taxonomy mode.** A carousel filtered by category still shows the same posts.
7. **Number of posts -1.** Shows all posts up to 100.
8. **Link Area: Whole Card** with sharing enabled: clicking a share icon opens the share window, clicking elsewhere opens the post.
9. **Keyboard.** Tab to the arrows and dots: a visible outline appears. On the modern engine, arrow keys move the slides.
10. **Reduced motion.** Turn on "reduce motion" in the operating system: autoplay stops on both engines.
11. **RTL site** (for example Arabic): modern engine carousels slide in the right direction.
12. **Page with no carousel:** no `dpce-` CSS or JS in the page source.
13. **Elementor > Tools > Regenerate files** once more, then repeat test 1.

## Phase 3: features

### What is new for site owners

* **Source** (top of Post / Content):
  * *Posts I choose*: the 1.4 behaviour.
  * *Current query*: the posts of the archive, category or search page the carousel is on. Useful in Elementor theme templates.
  * *Related posts*: same type, sharing categories, tags or any taxonomy with the post being viewed, with an optional fallback to the latest posts.
* **Filters section:** include or leave out any terms (searchable across taxonomies, match any or all), include or leave out authors, date range (past day to past year, or custom), skip first posts, sticky posts (normal, leave out, only), and order by last modified, ID, author or a custom field.
* **WooCommerce:**
  * Filters: featured, on sale, best selling, top rated, hide out of stock.
  * The Card template shows rating, price with sale price, a Sale badge, Out of stock, and a working AJAX add to cart button ("Select options" for variable products).
  * Other templates can add a product block with the same parts.
  * The add to cart icon in templates 49 and 50 used to do nothing. For products it is now a real add to cart link. For other posts the markup is unchanged.
* **Card (customizable) template:** a clean card whose parts switch on and off (image, category badge, meta row, excerpt, button, and the product parts). New carousels start with it.
* **Meta row:** date, author, author picture, categories, comments and reading time. On the Card it is on by default; other templates can switch it on.
* **Image options:** ratio, fit, hover effect (zoom, lift, darken, color on hover) and corner radius. They work on every template.
* **Layout:** Carousel, Grid or List. Grid and List load no slider files at all. Grid columns follow Elementor's breakpoints; List makes the Card horizontal.
* **Modern engine extras:**
  * Effects: fade, coverflow, centered active slide.
  * Pagination: numbers (2 / 8) or a progress bar.
  * News ticker mode: continuous, freezes on hover and keyboard focus.
  * Custom arrow icons.
* **Getting Started page** (Elementor > Post Carousel):
  * A one time welcome redirect after a single activation (never on bulk or network activation).
  * A checklist, help links and a gallery of all 52 templates.
* **Review request:**
  * Admins only, on the Dashboard or the Getting Started page, 7 days after install and after a carousel was used.
  * Leave a review, Maybe later (30 days) or I already did.
* **Deactivation feedback:** built but **off**. `DPCE_FEEDBACK_ENDPOINT` is empty, so no form is shown and nothing is sent. Setting an HTTPS address turns on a form with Skip and Submit; only Submit sends the reason, comment and plugin version. If you turn it on, add it to the readme's External services section.

### New developer hooks

`dpce_carousel_settings`, `dpce_slide_html`, `dpce_carousel_after_title`, `dpce_badges_html`, `dpce_meta_row_items`, `dpce_words_per_minute`, `dpce_feedback_endpoint`. Everything is documented in `docs/HOOKS.md`.

### Migration notes

* Nothing to migrate. Every new option defaults to the 1.4 behaviour, except in two places where no saved carousel can be affected:
  * The new Card template's own switches.
  * The settings the editor gives to newly created carousels: modern engine, pause button, Card template.
* Three new options (`dpce_installed_at`, `dpce_first_use`, `dpce_version`) and one user meta key (`dpce_review_state`). All are removed on uninstall.
* Plugin size grows by about 0.3 MB of template thumbnails (`assets/images/templates/`).

### What I verified

Test site: WordPress 7.1.2, Elementor 3.32, WooCommerce 11.1.2, PHP 8.4.

* Each query option returned the expected posts:
  * Terms (any and all), authors, date ranges, offset, sticky modes, custom field order.
  * All WooCommerce filters.
  * Related posts with and without fallback.
  * Current query on a category archive.
* The editor shows and hides the new controls correctly, and term and author search work.
* Add to cart works through AJAX from the Card, the product block, template 49 and template 50. The page stays put, and WooCommerce adds its "View cart" link.
* Grid, list, fade, coverflow, centered, numbers, progress bar, ticker (moves, freezes on hover and focus, resumes) and custom arrows all work in Chromium, desktop and phone widths, with no console errors.
* Admin screens:
  * The redirect happens on single activation only.
  * The review notice appears only on the Dashboard, after 7 days, and its choices are saved with and without JavaScript.
  * The feedback form is absent by default. With a test address it opens, closes with Escape, Skip sends nothing, and Submit sends only the reason and comment.
* Repeated on Elementor 3.18.3, 4.0.8 and 4.3.2: editor controls and search, grid and list, every effect and pagination type, the ticker, and the admin redirect and menu.
* Every 1.4 test carousel still renders the same HTML as 1.4. The screenshot comparison shows only the known intended differences.

### More manual test cases

14. **Card template with products:** switch Post Type to Products. Check prices, sale badge, rating, Out of stock, and that Add to cart adds the product without leaving the page.
15. **Related posts:** put a carousel set to Related posts in a single post template (Elementor Pro theme builder, or any single post built with Elementor). It shows posts from the same category and never the post itself.
16. **Current query:** in an archive template, the carousel shows the posts of the category being viewed.
17. **Grid and list:** switch Layout to Grid. Columns change on tablet and phone. No `swiper` or `slick` files in the page source.
18. **News ticker:** turn it on. Slides move continuously and stop when you hover or tab into the carousel.
19. **Review request:** set your site clock (or the `dpce_installed_at` option) back 8 days, view a page with a carousel, then open the Dashboard. The notice appears; each button hides it.
20. **Fresh install:** activate the plugin from the Plugins screen. You land on Getting Started once. Activate it again with the bulk action: no redirect.
