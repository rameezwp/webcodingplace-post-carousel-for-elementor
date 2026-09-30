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

* WordPress 7.1.2 with Elementor 3.32.0 on PHP 8.4, using a test site with 8 posts, 6 featured images, 3 categories and 69 carousel pages (all 51 templates plus option combinations).
* **HTML of every 1.4 carousel** rendered by 1.4 and by this branch, compared line by line. The only differences are the intended ones listed above.
* **Screenshots of every 1.4 carousel** at 1280 px and 390 px wide, compared pixel by pixel against 1.4. Identical apart from the star icon now showing (template 48 in the test set), the placeholder alt text, and one case of sub-pixel text smoothing.
* **Modern engine** in Chromium: arrows, dots, loop, no loop, vertical, RTL, autoplay with pause button, hover and focus pause, hidden loop copies, no console errors.
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
