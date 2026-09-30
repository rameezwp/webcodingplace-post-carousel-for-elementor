# Roadmap

This roadmap is based on [`docs/AUDIT.md`](AUDIT.md). Everything here is free: no Pro version is planned, so the goal is the strongest free post carousel for Elementor and a steady flow of installs and reviews.

Minimum versions from 2.0: **PHP 7.4, WordPress 6.3, Elementor 3.18.**

## Rules every release follows

1. **Old widgets render the same.** Control IDs, the widget name `dpce_post_carousel`, the `dpce_` prefix, the `dpce_styles` filter, template file names and the theme override path `dpce/style-{id}.php` never change.
2. **New control defaults equal 1.4 behaviour.** Elementor does not save settings that equal the default, so a changed default would silently change old widgets. When new widgets should get a better default, the editor sets it explicitly at creation time (the same trick used for the slider engine).
3. Zero Plugin Check and WPCS errors, CI green on PHP 7.4 to 8.4.
4. Assets load only on pages that use the widget. No new jQuery code.
5. Every string translatable; `.pot` regenerated.
6. Plain, friendly copy. No em dashes, no hype words.

## 2.0: "The free post carousel that does it all" (target: 6 to 8 weeks)

The goal is a release big enough to justify a fresh listing, new screenshots and an announcement push.

### Foundation (Phase 2)

| Item | Why | Audit ref |
|---|---|---|
| Fix escaping, allowlists, and the other PCP findings | Guideline safety, clean Plugin Check | S1 to S8 |
| Raise minimums to PHP 7.4 / WP 6.3 / Elementor 3.18 | Matches reality; fixes the silent failure on Elementor 3.0 to 3.4 | B1 |
| AJAX post and term search control | Removes 10+ queries per page view, fixes the 200 posts cap | B2, P1 |
| Slider engine control: Slick for old widgets, Swiper for new | Lighter pages, no jQuery for new users, zero risk for old widgets | 1.6 |
| Split and minify CSS per template, drop `slick-theme.css` | About 100 KB down to under 10 KB per page | P2 |
| Use Elementor breakpoints (including custom ones) | Correct columns on every device | B6 |
| Image fixes: correct `sizes`, no lazy loading on the first visible slides, primed thumbnail cache | Better LCP and fewer queries | P5, P6 |
| `is_dynamic_content()` returns true | Correct output with Elementor element caching | 1.5 |
| Accessibility pass: focus styles, clone handling, reduced motion, pause control, translated labels | WCAG basics, fewer support threads | A1 to A7 |
| Richer `get_keywords()`, title "Post Carousel (WCP)" | Easier to find in the Elementor panel | 1.5 |
| CI: PHPCS (WPCS 3), PHPStan, PHP 7.4 to 8.4 lint, Plugin Check | Keeps quality from slipping | 1.7 |

### Features (Phase 3)

Ordered by expected impact on installs.

1. **WooCommerce mode.** When the source is `product`: price with sale price, sale badge, star rating, real add to cart button (AJAX for simple products, "Select options" for others), out of stock label, product categories. Filters: featured, on sale, best selling, top rated, in stock only. Works with the existing 51 templates through new hooks; the 4 product templates get the proper button.
2. **Layout switch: Carousel, Grid, List.** Same templates, same query. Opens up "post grid" searches and keeps users who want a grid.
3. **Query builder.** Include and exclude by any taxonomy terms (several at once, with AND / OR), authors, date range, offset, sticky posts (ignore, include, only), order by modified and meta value. Modes: **Current query** (archive and search templates) and **Related posts** (same category or tag as the current post).
4. **News ticker / marquee mode.** Continuous scrolling, pause on hover and focus, respects reduced motion.
5. **Swiper extras.** Center mode, fade, coverflow, progress bar and fraction pagination, custom arrow icons (Elementor Icons control).
6. **Meta row and card polish.** Date, author with avatar, terms as badges, comment count, reading time; category badge on the image; image ratio, object fit, hover effects (zoom, overlay, lift); title tag h1 to h6, div, p, span.
7. **First run experience.** One time welcome redirect (single activation only, never bulk, never on network activation), an admin page under the Elementor menu with a template gallery and a "Getting started" checklist. No upsell (there is no Pro).
8. **Review request.** Admins only, only on our page or the Dashboard, only after 7 days **and** after the widget is used at least once. "Leave a review", "Maybe later" (30 days), "Already did". Nonce protected, stored per user.
9. **Deactivation feedback (optional).** Modal with a Skip button. Nothing is sent unless the user submits **and** the `DPCE_FEEDBACK_ENDPOINT` constant is set; it is empty by default, so nothing leaves the site out of the box.
10. **Developer hooks.** New filters for slider settings, card output and wrapper attributes, documented in `docs/HOOKS.md` along with the existing `dpce_styles`, `dpce_query_args` and template overrides.

### Listing (Phase 4)

New readme (title, 5 tags, short description, long description, 10 to 15 FAQ, screenshot captions, changelog, upgrade notice), `docs/ASSETS-BRIEF.md`, Playground blueprint for Live Preview, `.distignore`, `docs/RELEASE-NOTES.md`.

### Growth (Phase 5)

`docs/GROWTH-PLAN.md`.

## 2.1: "Design your own card" (2.0 + 3 to 5 weeks)

* **Elementor template per slide.** Pick any saved section or container template as the card. Ships with our own dynamic tags (Post Title, Post URL, Featured Image, Excerpt, Date, Author, Avatar, Terms, Custom Field, Comments, Reading Time, Woo Price, Add to Cart URL), so it works with **free** Elementor. This is the "free Loop Carousel" headline. Scoped in audit section 4.
* **Custom field picker** with ACF support (field list when ACF is active, plain meta key otherwise).
* Optional: `has_widget_inner_wrapper()` false for leaner markup, behind a compatibility check.

## 2.2 (about 4 weeks later)

* **Carousel of terms**: category or product category cards with image, name and post count (for "category carousel").
* Grid mode: **Load more** button and numbered pagination.
* Post views from common view counter plugins, when active.
* 5 to 10 new templates driven by support requests (a steady reason to update).

## 2.3 and later

* AJAX filter tabs by category for grid and carousel.
* Gutenberg block using the same renderer (reaches non Elementor sites; big search volume for "post carousel block"). Large, but it reuses the query and template layer.
* Template gallery with live previews inside the editor panel.
* Retire the Slick engine (3.0 at the earliest, with a clear notice and a one click switch).

## Deliberately not doing

* Tracking, usage statistics or remote calls without explicit opt in.
* Admin wide notices or nags. The only notices are the review request (rules above) and the missing Elementor warning.
* Locking or limiting any feature. If plans change and a paid add on ever happens, it must add new things and never take away from the free plugin. The template, hooks and query layers built in 2.0 would make that possible without touching free code.
