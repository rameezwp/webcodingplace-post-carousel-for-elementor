# Audit: WebCodingPlace Post Carousel for Elementor 1.4

Date: 2026-09-30
Scope: the whole repository at commit `d381fd5` (branch `claude/gracious-mccarthy-pg8zd8`).
Status: Phase 1, no code changed yet.

## Decisions already made

| Topic | Decision |
|---|---|
| Pro version | None planned. Everything goes into the free plugin. Wherever this document mentions "Pro", it only describes what competitors charge for. |
| Minimum versions for 2.0 | PHP 7.4, WordPress 6.3, Elementor 3.18 |
| Slider engine | New **Engine** control. Existing widgets stay on Slick, new widgets default to Swiper (Elementor's own copy). See section 1.6. |
| Live Preview images | Stored in this repo under `.wordpress-org/` and loaded by the Playground blueprint from `raw.githubusercontent.com` (the repo must be public). |

## How to read this file

Severity levels:

* **High**: a security issue, a Plugin Check (PCP) error, a real bug that users can hit, or a guideline risk.
* **Medium**: a compatibility, performance or accessibility problem that users will notice.
* **Low**: tidying, consistency, or a small gain.

Line numbers refer to commit `d381fd5`.

---

## 1. Code health

### 1.1 Architecture in one paragraph

A single bootstrap class (`DPCE_Plugin`) waits for Elementor, loads four include files, and registers one widget (`dpce_post_carousel`). The widget builds settings, runs `DPCE_Query::run()`, and includes one PHP template per slide (`templates/style-{id}.php`, 51 of them). Templates render their pieces through action hooks (`dpce_carousel_thumbnail`, `_title`, `_desc`, `_read_more`, `_overlay`, `_meta`, `_share`, `_icon`) whose default callbacks live in `DPCE_Renderer`. Styles are registered in `DPCE_Styles` behind the `dpce_styles` filter, and a theme can override a template with `dpce/style-{id}.php`. Front end: jQuery plus Slick 1.8.1 and one 98 KB stylesheet that covers every template.

The structure is sound and small (about 2,300 lines of PHP outside templates). The hook based template system is a good foundation for extension and is worth keeping as is. The issues below are mostly local fixes, not a rewrite.

### 1.2 Security and escaping

| # | Sev | Where | Finding | Fix |
|---|---|---|---|---|
| S1 | High | `templates/style-1,2,3,10,11,12.php` (`get_the_date( 'd' / 'M' )`), `style-6,7,11,12.php` (`get_the_author()`), `style-45.php:64` (`get_the_date()`) | Output echoed without escaping. PCP reports `WordPress.Security.EscapeOutput.OutputNotEscaped` as an **error**. Templates are loaded on every render, so this blocks a clean PCP run. | Wrap in `esc_html()`. |
| S2 | Medium | `class-dpce-renderer.php:128` | `do_shortcode( wp_kses_post( $value ) )` echoes shortcode output unescaped. That is expected for shortcodes, but it needs a documented `phpcs:ignore`. The option is also misleading: `wp_trim_words()` strips all tags **before** output, so "Render Shortcodes / HTML" never keeps HTML when a word limit is set. | Run shortcodes first, then trim the rendered text when a limit is set, and keep HTML only when the limit is 0. Add a justified `phpcs:ignore`. |
| S3 | Medium | `class-dpce-renderer.php:155,179`, `style-45.php` | `read_more_target` is used as is. It is escaped, but not limited to `_self` / `_blank`. | Allow only those two values. |
| S4 | Medium | `widget.php:1005`, `class-dpce-styles.php:92` | `style_id` goes through `sanitize_file_name()` before the include, so path traversal is not possible. It is still not checked against the registered styles, so any `style-*.php` file in the theme `dpce/` folder can be included. | Check that the ID is in `DPCE_Styles::all()` and fall back to style 1. |
| S5 | Low | `class-dpce-query.php:27-28` | `orderby` and `order` pass through unchecked. WP_Query sanitizes them, but Plugin Check and WPCS reviewers prefer explicit allowlists. | Add allowlists. |
| S6 | Low | `class-dpce-renderer.php:158,182` | `rel="follow"` is not a valid link type. | Leave `rel` out for `_self`. |
| S7 | Low | `style-5,6,7,11,12,45.php` | `wp_count_comments( $id )->total_comments` counts pending comments too, and is echoed with `esc_attr()` in a text context. It also runs one query per slide (see P5). | Use `get_comments_number()` with `esc_html()` / `number_format_i18n()`. |
| S8 | Low | `style-20.php:27`, `style-21.php:25` | `current_time( 'timestamp' )` triggers the WPCS `DateTime.CurrentTimeTimestamp` warning. | Use `time()` and `get_post_time( 'U', true )`. |
| S9 | n/a | whole plugin | Nothing saves data today, so no nonces or capability checks are needed yet. The planned admin page, review notice and feedback modal **will** need nonces, `current_user_can( 'manage_options' )` and sanitized input. | Build them in from the start in Phase 3. |

No direct SQL, no `$_GET`/`$_POST` access, no `eval`, no remote requests. Share links only build URLs, and the readme documents them correctly.

### 1.3 Bugs found while reading

| # | Sev | Where | Finding |
|---|---|---|---|
| B1 | **High** | main file `:42`, `:102`, `:127` | The plugin claims Elementor 3.0+, but `elementor/widgets/register` and `$widgets_manager->register()` only exist from **Elementor 3.5**. On 3.0 to 3.4 the widget never appears and no message is shown. Resolved by the new 3.18 minimum. |
| B2 | **High** | `widget.php:133-182`, `helpers.php:167,201` | The "Select posts" control loads the first **200 posts alphabetically** for every public post type, and the "Select terms" control loads 500 terms for every public taxonomy. On larger sites, posts after number 200 cannot be picked. These queries also run whenever Elementor builds the control stack, which includes front end renders of pages that use the widget, not only the editor. (Also listed as P1.) |
| B3 | Medium | `widget.php:799-805` | The `style_icon_color` selectors array uses the same key twice, so `color` is overwritten by `fill`. Font icons (`<i>`) never get the chosen color. |
| B4 | Medium | `widget.php:786-789` | The Icons control default uses `'library' => 'solid'`. Elementor expects `fa-solid`. |
| B5 | Medium | `main.js:36-51` | Every carousel is initialized twice on the front end: once by `frontend/element_ready` and once on DOM ready. The second pass runs `unslick` and rebuilds, which wastes work and can cause a visible jump. |
| B6 | Medium | `widget.php:1022-1035` | Responsive breakpoints are hard coded to 1024 and **600** px. Elementor's mobile breakpoint is 767 px and can be customized, so between 600 and 767 px the tablet column count shows while the Elementor mobile view is active. Custom breakpoints (laptop, wide screen, mobile extra) are ignored. |
| B7 | Medium | `style-49.php:26`, `style-50.php` | "Add to cart" is a `<span>` with `ajax_add_to_cart` classes. It is not focusable. It only works if WooCommerce's `wc-add-to-cart` script is already on the page, only works for simple products, and in "Whole Card" mode it sits under the card overlay link. |
| B8 | Medium | `class-dpce-styles.php:78-91` vs readme | The code comment says themes override with `webcodingplace-post-carousel-for-elementor/style-{id}.php`, the readme Developers section says `dpce/style-{id}.php` (which is what the code does), and the FAQ says `templates/style-{id}.php`. The docs need to agree. |
| B9 | Low | `class-dpce-styles.php:50` | Each style points to `assets/images/style-N.svg` thumbnails, but that folder does not exist. |
| B10 | Low | `style-5.php` | The description is wrapped in an `<h3>`, so every card has two headings. `style-45.php` hard codes `<h3>` and ignores the Heading HTML Tag control. |
| B11 | Low | many templates | The description is printed inside `<p>`. With shortcodes on, block elements end up inside `<p>`, which is invalid HTML and browsers will "repair" the layout. Use `<div>`. |
| B12 | Low | `helpers.php:67-70` | The excerpt fallback reads `post_content`. For posts built with Elementor the useful text lives in Elementor data, so excerpts can be empty or stale. Document this and suggest writing a manual excerpt. |
| B13 | Low | `class-dpce-renderer.php:196` | `render_icon()` has no visibility keyword and its docblock describes a different signature. |
| B14 | Low | `widget.php:1020` | "RTL Mode" is a manual switch that defaults to off. On RTL sites (`is_rtl()`) the carousel runs in the wrong direction until the user notices. The default should follow the site, while an existing saved value is still respected. |
| B15 | Low | readme / header | The listing says "apply your own CSS" (Custom CSS was removed in 1.2), "Per-style appearance controls" (no style defines any), and "Font Awesome removed" (templates still render FA icons through `Icons_Manager`, so Elementor's local Font Awesome CSS loads unless its inline SVG icons experiment is on). "Taxonomy Slider" suggests a carousel of categories, but the plugin shows posts filtered by term. |

### 1.4 PHP 8.x and 8.4

I found no uses of removed functions, dynamic properties, `${}` interpolation, implicit nullable parameters (deprecated in 8.4), or `strftime`. All string functions receive strings. Risk is low. The CI matrix planned for Phase 2 (PHP 7.4 to 8.4 with `php -l` plus PHPStan) will confirm this.

### 1.5 Elementor API

| Check | Status |
|---|---|
| `_register_controls` (deprecated) | Not used. `register_controls()` is correct. |
| `Scheme_*` classes | Not used. |
| `register_widget_type()` | Not used. `register()` is correct (3.5+, see B1). |
| `get_keywords()` | Weak: `carousel, slider, posts, slick, taxonomy, cpt`. "slick" and "cpt" are not words people type. |
| `get_title()` | "WebCodingPlace Post Carousel". Brand first, so in the panel it sorts and reads as a brand, not as a job. Proposed: **"Post Carousel (WCP)"**. Keep `get_name()` unchanged. |
| `get_categories()` | `general`. Fine for finding the widget. Optionally add our own category **as well** (search still works). |
| `has_widget_inner_wrapper()` (3.24+, optimized markup) | Not declared. Leave it `true` for now. Our CSS does not depend on `.elementor-widget-container`, but third party CSS might. Revisit in 2.1. |
| `is_dynamic_content()` (3.22+, element caching) | Not declared. **Must return `true`**, otherwise "Hide current post", random order and new posts can be served from Elementor's element cache. |
| `get_style_depends()` / `get_script_depends()` | Used correctly, so assets already load only on pages with the widget. The CSS bundle is simply too large (see P2). |
| Breakpoints | Hard coded (B6). Use `\Elementor\Plugin::$instance->breakpoints->get_active_breakpoints()` and `add_responsive_control()` for columns. |

### 1.6 Slider engine: Slick to Swiper

**Facts**

* Slick 1.8.1 (2017) needs jQuery, is no longer maintained, and has known accessibility problems (`aria-hidden` on clones that contain focusable links, and `role="tablist"` misuse on dots).
* Elementor has shipped Swiper since 2.x and upgraded it to 8.4.5 in 3.11. It exposes `elementorFrontend.utils.swiper`, an async loader that returns a promise with a Swiper instance and loads the library only when needed. Elementor's own carousels use it, so on many Elementor pages Swiper is already cached.
* Depending on the Elementor version and the "Upgrade Swiper library" experiment, the loaded Swiper can be 5.3.6 or 8.x. **To verify in Phase 2:** the exact script and style handles at Elementor 3.18 and at the current release. Our init code should only use options common to both, or require v8.

**Options compared**

| | A. Full migration | B. Engine control (chosen) |
|---|---|---|
| Existing widgets | Switch to Swiper and must be mapped option by option | Stay on Slick, byte for byte |
| Visual risk on live sites | Medium: arrows, dots, vertical mode, adaptive height and custom CSS that targets `.slick-*` | None |
| Code paths | One | Two for a while (Slick path is frozen, only bug fixes) |
| Weight for new users | Light | Light |
| Weight for old users | Light | Unchanged until they switch |

**How B works, and one Elementor detail to watch.** When Elementor saves a widget, it drops every setting that still equals the control default. An "absent" setting on an old widget therefore reads as whatever the default is **today**. So:

1. Add a control `slider_engine` with options `slick` and `swiper` and **default `slick`**. Every existing widget keeps Slick with no migration step.
2. A small editor script listens for new `dpce_post_carousel` elements being created and sets `slider_engine = swiper` on them. Because that value differs from the default, Elementor saves it. New widgets use Swiper; old ones never change.
3. Show a notice inside the control for Slick widgets: "This carousel uses the classic engine. Switch to the modern engine for a lighter page." It is one click, and reversible.
4. Register Slick assets as before, but have the widget request them only when `slider_engine = slick`. That means `get_script_depends()` returns the Swiper path by default, and the Slick handles are enqueued from `render()`. In the editor both are loaded.
5. The Swiper path outputs the same wrapper classes (`dpce-carousel`, `dpce-arrows-*`, `dpce-dots-*`, `dpce-wrapper-*`) and the same slide markup, so all 51 templates and their CSS work unchanged. Arrows and dots get our own CSS, written to match the Slick look.
6. Plan to retire Slick no earlier than 3.0, and only with a migration notice.

The same "default equals legacy behaviour" rule applies to **every new control** in 2.0. A new default can only differ from 1.4 behaviour through the new widget flag in step 2.

### 1.7 WPCS / Plugin Check (expected findings, not yet run)

PHPCS is not installed in this container yet; Phase 2 adds it along with the CI workflow. From reading the code, expect:

* **Errors:** S1 (unescaped output in templates).
* **Warnings:** short array syntax `[]` in `class-dpce-renderer.php` and `widget.php:780-810` (WPCS 3 disallows it); non-prefixed variables in templates (`$comments`, `$dpce_` is prefixed but meaningless); `current_time( 'timestamp' )`; mixed tabs and spaces in templates; missing docblocks on `render_icon()`; `load_plugin_textdomain` is correctly **not** called (WP.org loads translations automatically since 4.6).
* **PCP plugin header / readme checks:** `Tested up to` must match the current WordPress major at release; the readme currently has screenshot captions, then a `= Developers =` block under **Screenshots**, so on WordPress.org the developer notes appear inside the Screenshots tab. There is no `== Upgrade Notice ==`.
* The `.pot` file is from May 2026 and its line references are out of date.

### 1.8 Dead code and duplication

* `build_carousel_settings()` receives `$raw` and throws it away; `render()` calls `get_settings()` just to pass it.
* `DPCE_Styles` supports per style `settings`, but no style uses them. Keep this: it is a documented extension point.
* The `get_the_date( 'd' )` / `'M'` date badge is copied into 7 templates. Move it into one helper (`dpce_render_date_badge()`), keeping identical markup.
* The WooCommerce price block is copied into 4 templates with slightly different variable names. Move it into a `dpce_carousel_price` action.
* The overlay link plus read more logic is sound, but `style-45` and `style-47` do not fire `dpce_carousel_overlay`, so "Link Area: Whole Card" silently does nothing there.

---

## 2. Performance

| # | Sev | Finding | Fix |
|---|---|---|---|
| P1 | **High** | B2: `get_posts( 200 )` for each public post type plus `get_terms( 500 )` for each public taxonomy, every time the control stack is built (editor and front end renders). On a store with products, pages and several taxonomies this is 10+ extra queries per page view that has the widget. | Replace with an AJAX search control (a custom Elementor control based on the core SELECT2 with a REST endpoint, capability `edit_posts`, nonce). Only saved IDs are resolved to labels. Existing saved values keep working because the stored value format (array of IDs) does not change. |
| P2 | **High** | `main.css` is 98 KB unminified and covers all 51 styles, but a page uses one or two. `slick-theme.css` also loads the Slick icon font (eot/woff/ttf/svg) and `ajax-loader.gif`, which our arrows do not need because they use Unicode characters. | Split into `base.css` (wrapper, arrows, dots) plus `styles/style-{id}.css`. Register each style file and enqueue only the one used from `render()`. Load everything in the editor. Minify at build time. Drop `slick-theme.css` and copy the few dot rules we actually need. Expected CSS per page: about 5 to 8 KB instead of about 100 KB. |
| P3 | Medium | jQuery plus Slick (43 KB minified) on every carousel page. | Swiper through Elementor for new widgets (1.6). Our init script becomes a small vanilla JS file. |
| P4 | Medium | `posts_per_page` accepts `-1` (unlimited). | New UI range 1 to 50. Saved `-1` values keep working but are capped by a filter, `dpce_max_posts` (default 100), so an old widget never runs an unbounded query. Mention this in the release notes. |
| P5 | Medium | N+1 queries: `wp_count_comments()` per slide (6 templates), and featured image metadata per slide because `update_post_thumbnail_cache()` is only primed for the main loop. | Call `update_post_thumbnail_cache( $query )` after the query; use `get_comments_number()` (reads the post object, no query). |
| P6 | Medium | Images: `loading="lazy"` is forced on every slide, including the first visible ones (bad for LCP when the carousel is above the fold). `sizes` is the core default, which assumes a full width image, so a 4 column carousel downloads images about 4 times too large. The placeholder image has no `width`/`height` (layout shift). | Lazy load only slides after the visible count, or leave it to core `wp_get_loading_optimization_attributes()`. Compute `sizes` from the column settings. Output the placeholder through `wp_get_attachment_image()` when it has an ID. |
| P7 | Low | Slick clones slides in infinite mode, duplicating images and links in the DOM. | Swiper `loop` also duplicates slides; accepted cost, but with correct `aria-hidden` and `inert` handling. |
| P8 | Low | No query caching. | Not needed for the typical 8 to 12 posts with `no_found_rows`. Add an `dpce_query_args` based opt-in later if requested. |

What is already good: `no_found_rows => true`, a single `WP_Query`, `wp_reset_postdata()`, assets declared through widget dependencies (so nothing loads on pages without the widget), local assets only.

---

## 3. Accessibility

| # | Sev | Finding | Fix |
|---|---|---|---|
| A1 | High | Slick clones contain focusable links but are `aria-hidden="true"`. That is an axe "aria-hidden-focus" violation on every infinite carousel. | Swiper path: add `inert` to duplicate slides. Slick path: add `tabindex="-1"` to links inside `.slick-cloned` after init. |
| A2 | High | No visible focus styles for arrows, dots, the card overlay link or buttons. | Add `:focus-visible` outlines that also work on dark cards (two color outline). |
| A3 | High | Autoplay has no pause or stop control (WCAG 2.2.2) and ignores `prefers-reduced-motion`. | Pause on hover and focus by default; add an optional visible pause button; do not autoplay when the user prefers reduced motion. |
| A4 | Medium | Arrow labels "Previous" / "Next" come from Slick in English. | Pass translated labels (`Previous slide`, `Next slide`, `Go to slide %d`). Swiper's a11y module takes the same strings. |
| A5 | Medium | "Whole Card" mode puts an empty `<a>` with `aria-label` over the card. Share links and add to cart buttons inside the card sit under it, which is both confusing for screen readers and possibly not clickable. | Keep the overlay, but raise real interactive elements above it (`position: relative; z-index`) and give the carousel region `role="region"` and `aria-roledescription="carousel"` with a label. |
| A6 | Medium | Add to cart in styles 49 and 50 is a `<span>` (B7). | Real `<a>` or `<button>` from WooCommerce's own add to cart markup. |
| A7 | Low | The placeholder image uses the post title as `alt`, and the title is repeated right after it. | `alt=""` for the placeholder. |
| A8 | Low | Headings: see B10. | Fix as described. |
| A9 | Medium, needs manual check | Contrast in the default templates. Several templates put text over images without a scrim, or use light accent backgrounds with white text. | Run axe and a contrast checker on all 51 templates with 3 sample images in Phase 2; adjust default colors only where the user has not set a color. |

---

## 4. Competitor gap analysis

**Source note.** `wordpress.org` and its API are blocked from this container, so I could not read live listings. The table combines web search results (September 2026) with my own knowledge. Cells marked **?** are ones I am not sure about. Please spot check those before relying on them.

Competitors:

* **EP**: Elementor Pro, Loop Carousel and Posts widgets (paid). Loop Carousel is Pro only; there is no free version.
* **EA**: Essential Addons. Post Grid is free; Post Carousel is Pro **?**
* **PA**: Premium Addons. The Blog widget is free and has a carousel option.
* **HA**: Happy Addons. Post Grid free; Post Carousel is Pro (their docs list it under "Happy Addons Pro").
* **EK / UAE / TP**: ElementsKit, Ultimate Addons for Elementor, The Plus Addons. Post carousels are mostly in their paid tiers **?**
* **UE**: Unlimited Elements. Several free post carousel widgets from its library.
* **SA**: standalone free plugins that rank for "post carousel" / "post slider", such as Post Carousel Slider for Elementor (3,000+ installs per search results), Smart Post Show, Post Grid / Slider / Carousel Ultimate, Custom Post Carousels with Owl (2,000+).

Legend for "Us": ✅ have it, ◐ partly, ❌ missing. "Effort": S (under a day), M (1 to 3 days), L (a week or more). "Release" replaces the "free or Pro candidate" column since no Pro is planned. Where competitors charge for a feature, it says "(paid elsewhere)"; those are the strongest selling points for a free plugin.

| Feature | Who has it | Us | Effort | Impact on installs | Release |
|---|---|---|---|---|---|
| Works with free Elementor | EA, PA, HA, UE, SA (not EP) | ✅ | n/a | High (core message) | now |
| Filter by categories / tags / any taxonomy terms | all | ◐ one taxonomy at a time | M | High | 2.0 |
| Filter by author | EP, EA, PA, HA | ❌ | S | Med | 2.0 |
| Date range (last N days, custom) | EP, PA **?**, HA | ❌ | S | Low | 2.0 |
| Offset, sticky posts handling | EP, EA, PA, HA | ❌ | S | Med | 2.0 |
| Order by modified, meta value | EP, PA, HA | ◐ | S | Low | 2.0 |
| Current query (archives, search) | EP, PA **?** | ❌ | M | High (theme builder users) | 2.0 |
| Related posts (same category or tag) | EP, EA **?**, HA **?**, SA | ❌ | M | High ("related posts carousel" is a common search) | 2.0 |
| Manual selection with AJAX search | EP, EA, HA | ◐ capped at 200 | M | Med | 2.0 (fixes B2) |
| Meta row: date, author, avatar, terms, comments | all | ◐ only in some templates | M | High | 2.0 |
| Reading time | HA **?**, TP **?** | ❌ | S | Low | 2.0 |
| Post views (via popular views plugins) | TP **?**, SA **?** | ❌ | S | Low | 2.2 |
| ACF / custom field picker | EP (dynamic tags), UE, TP **?** | ◐ raw meta key | M | Med | 2.1 |
| Category badge on image | EA, PA, HA, UE | ◐ style 45 only | S | Med | 2.0 |
| Image ratio, object fit, hover effects | EP, EA, PA, HA | ❌ | S | Med | 2.0 |
| Title tag h1 to h6, div, p | all | ◐ h2 to h6 | S | Low | 2.0 |
| WooCommerce: price, sale badge, rating, add to cart, stock | EP, EA (paid elsewhere), HA (paid), PA **?** | ◐ price in 4 templates | M | **High** | 2.0 |
| Woo filters: featured, on sale, best selling, top rated, in stock | EP, HA (paid), EA **?** | ❌ | M | High | 2.0 |
| Loop, slides to scroll, speed, autoplay, pause on hover | all | ✅ | n/a | n/a | now |
| Center mode | EP, EA, PA, HA | ❌ | S | Med | 2.0 (Swiper) |
| Fade effect | EP, EA, PA | ❌ | S | Low | 2.0 (Swiper) |
| Coverflow effect | EA (paid elsewhere), HA **?** | ❌ | S | Med (visual demo value) | 2.0 (Swiper) |
| Marquee / news ticker (continuous) | UE, SA, TP **?** | ❌ | M | **High** ("news ticker Elementor" is a big search) | 2.0 |
| Progress bar / fraction pagination | EP, UE | ❌ | S | Low | 2.0 (Swiper) |
| Custom arrow icons, positions | EP, EA, PA, HA | ◐ 4 styles, 7 positions | S | Low | 2.0 |
| Responsive tied to Elementor (custom) breakpoints | EP, HA, EA | ❌ (B6) | M | Med | 2.0 |
| Switch to static grid or list (same templates) | EP (Posts / Loop Grid), EA, PA, HA | ❌ | M | **High** (doubles the audience: "post grid" searches) | 2.0 |
| Load more / pagination (grid mode) | EP, EA, PA | ❌ | M | Med | 2.2 |
| AJAX category filter tabs (grid mode) | EA (paid), PA, HA (paid) | ❌ | L | Med | 2.3 |
| Elementor template per slide ("free Loop Carousel") | EP only (paid), UE **?** | ❌ | L | **High** (unique among free options) | 2.1 |
| Carousel of terms (category cards with image and count) | UE, SA **?** | ❌ | M | Med ("category carousel") | 2.2 |
| Ready made designs | UE (many), EA / PA 3 to 5 skins | ✅ 51 | n/a | High (already a strength, needs to be **shown**) | now |
| Theme overridable templates plus hooks | rare (EP no, EA partial) | ✅ | n/a | Low for installs, High for developers | now, document |
| No jQuery, small footprint | EP, UE partly | ❌ | M | Med (performance is a selling point) | 2.0 |
| Accessibility (keyboard, reduced motion) | EP partly, others weak | ❌ | M | Low for installs, good for reviews | 2.0 |
| Live Preview on WordPress.org | few competitors | ❌ | S | High (conversion) | 2.0 |
| Template gallery / getting started screen | EA, PA, HA | ❌ | M | Med (activation to first use) | 2.0 |

**Positioning.** The large addon packs (EA, PA, HA, EK, UAE, TP) are 50 to 160 widgets each. Many people who only want a post carousel do not want a pack of that size, and the best carousel features in those packs are often paid. The standalone free plugins are small but mostly dated (Owl or Slick, few templates, weak WooCommerce support). The opening is: **"The free, focused way to build post, product and news carousels in Elementor: 51 designs, WooCommerce ready, no jQuery, works with free Elementor."** The 2.1 "Elementor template per slide" feature then answers the most common reason people buy Elementor Pro for this job.

### Scoping note: Elementor template per slide

* **How:** a new `Card source` option: `Built in template` (default, current behaviour) or `Elementor template`. The saved template is rendered once per post with `\Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true )`, with the global `$post` switched to the current slide.
* **The catch:** free Elementor has the dynamic tags *system* but ships no post tags (Post Title, Featured Image and so on are Pro). Without them every slide would show the same static content. So we register our own small set of dynamic tags through `elementor/dynamic_tags/register`: Post Title, Post URL, Featured Image, Excerpt, Date, Author, Author Avatar, Terms, Custom Field, Comments Count, Reading Time, WooCommerce Price and Add to Cart URL. These are namespaced (`dpce-*`) so they do not clash with Pro's tags if Pro is active.
* **Other work:** template CSS enqueue (Elementor handles it per template once), editor preview (render inside the editor with the first few posts), a guard against a template that contains the carousel itself (infinite loop), and a "Create a card template" link that opens a new Elementor template.
* **Effort:** L (about 1.5 to 2 weeks with testing). **Risk:** Medium; depends on Elementor internals that change between versions, so it gets its own release (2.1) instead of making 2.0 late.

---

## 5. WordPress.org listing audit

How WordPress.org search works, in short: the ranking uses text relevance (weighted plugin **name** highest, then **short description**, **tags**, and the long description), then boosts by active installs, rating, recent updates, "Tested up to" being current, and support resolution. At 10+ installs, text relevance is the main lever we control, and conversion (listing view to install) is the main lever for everything else.

| Element | Current | Effect | Recommendation (details in Phase 4) |
|---|---|---|---|
| Name (readme title + header) | Header: "WebCodingPlace Post Carousel for Elementor". Listing: "Post Carousel for Elementor, Responsive Posts, Products and Taxonomy Slider" | The listing title is decent for search but long, and "Taxonomy Slider" is inaccurate (B15). The plugin header name differs from the readme title, which is allowed, but the readme title is what search sees. | Lead with "Post Carousel" / "Post Slider", include "for Elementor", mention WooCommerce products once. Three options with trade offs in Phase 4. |
| Short description | "Display posts, custom post types or taxonomy terms in a beautiful, fully responsive carousel widget for Elementor with 50+ ready-made templates." (145 chars) | Main keyword appears late; "taxonomy terms" is inaccurate; "beautiful" is filler. | Keyword in the first 5 words, mention WooCommerce and free Elementor, under 150 chars. |
| Tags | elementor, carousel, slider, posts, **slick** | Only the first 5 count. "slick" gets almost no searches. Missing "post carousel", "post slider", WooCommerce. | Candidates: post carousel, post slider, elementor, woocommerce, carousel. Justified in Phase 4. |
| Long description | One dense paragraph, then a feature list. Mentions removed Custom CSS. | Weak for skimming and for secondary keywords (blog carousel, news slider, product carousel, related posts). | Rewrite as planned in Phase 4. |
| FAQ | 2 questions | FAQ text is indexed and answers objections before install. | 10 to 15 real questions. |
| Screenshots | 4 editor panels, no front end results | Screenshots are the biggest conversion factor after the title. People want to see what they get. | Lead with 6 front end shots, then 2 to 3 editor shots. Brief in `docs/ASSETS-BRIEF.md`. |
| Banner / icon | Not in this repo (stored in SVN `assets/`). I cannot see them from here. | A generic or missing banner lowers trust. | Brief included in Phase 4; stored in `.wordpress-org/`. |
| Live Preview | None | A "Live Preview" button lets people try without installing; a strong conversion aid for visual plugins. | Playground blueprint in `.wordpress-org/blueprints/blueprint.json`. |
| Changelog | Short, 1.1 is "Bug fixes." | Shows activity; vague entries do not build trust. | Specific, user focused entries. |
| Upgrade Notice | Missing | Shown in the WordPress update screen; important for a major version. | Add for 2.0. |
| Tested up to | 6.9 in the readme | An outdated value shows a warning banner and lowers ranking. | Set to the current WordPress major at release time, after testing. Bump on every WordPress release even without a code change. |
| Developers section | Placed under Screenshots by mistake (1.7) | Shows up in the wrong tab. | Move into Description. |
| Support forum | Reviews: 0. Threads: unknown from here. | Resolution rate and the review count are shown on the listing and affect ranking. | Review notice (Phase 3) plus the habits in `docs/GROWTH-PLAN.md`. |
| Last updated | About 4 months ago | "Freshness" is a ranking and trust signal. | Release every 2 to 4 weeks after 2.0. |

---

## 6. Roadmap

See [`docs/ROADMAP.md`](ROADMAP.md).
