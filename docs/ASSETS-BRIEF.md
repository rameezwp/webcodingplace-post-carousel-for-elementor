# WordPress.org assets brief

What to make for the plugin page on WordPress.org: a banner, an icon and nine screenshots. Give this file to a designer, or follow it yourself.

All files go in the SVN `assets` folder (next to `trunk` and `tags`), never in the plugin zip. WordPress.org picks them up within a few minutes of the commit.

## The idea in one line

"Your posts, in a neat row of cards." Every asset shows real looking cards (image, category badge, title, short text) arranged as a carousel or grid. No abstract shapes that could belong to any plugin.

## Look and feel

* **Colors** (taken from the plugin's own templates, so the screenshots and the brand match):

  | Use | Color |
  |---|---|
  | Ink, text and buttons | `#111827` |
  | Deep teal, accent | `#065F57` |
  | Soft gray, backgrounds | `#F3F4F6` |
  | White, cards | `#FFFFFF` |
  | Warm coral, a single highlight | `#FF8A65` |

* **Type:** one clean sans serif, for example Inter or Manrope (both free). Bold for the name, regular for the line under it.
* **Card images:** use the six soft gradient images in `.wordpress-org/blueprints/images/`. They are made for this, carry no license questions, and match the Live Preview.
* **Avoid:** the Elementor logo or its pink color (it is their trademark and WordPress.org reviewers ask for it to be removed), stock photos with people, star ratings or "5 stars" badges, words like "best", "ultimate" or "#1", and fake browser chrome with other brands.

## Banner

| File | Size |
|---|---|
| `banner-772x250.png` (or `.jpg`) | 772 × 250 |
| `banner-1544x500.png` (or `.jpg`) | 1544 × 500, the same design at 2x |

Keep each file under 500 KB. JPG is usually smaller for designs with gradients.

**Layout:**

* Left 40%: the name on two lines, "Post Carousel & Grid" (large) and "for Elementor" (smaller), with one line under it: "Posts, products and any post type, in a carousel, grid or list." Ink on the soft gray background.
* Right 60%: three cards from the Card template in a row, slightly overlapping the right edge so it reads as a carousel that continues. Small left and right arrow buttons on the cards' middle line and a row of dots under them. One card can show a product price and an "Add to cart" button to hint at WooCommerce.
* **Keep the bottom left corner calm.** WordPress.org prints the plugin name over the lower left part of the banner on the plugin page. Put your own text in the upper half, or accept that the name shows twice. Check the result on a real plugin page before you commit.

Optional: `banner-772x250-rtl.png` and `banner-1544x500-rtl.png`, mirrored, for right to left languages. Only if you have time.

## Icon

| File | Size |
|---|---|
| `icon-128x128.png` | 128 × 128 |
| `icon-256x256.png` | 256 × 256 |
| `icon.svg` | optional, used where supported |

**Design:** three rounded card shapes side by side, the middle one larger and in front (the "active slide"), each with a small image block on top and two text lines below. Below them three small dots, the middle one filled. Teal cards on white, or white cards on teal. No text, no letters: they are unreadable at 128 px and the name is printed next to the icon anyway.

Test it at 64 px (the size in search results). If the three cards blur into one block, drop the text lines and keep only the image blocks.

## Screenshots

The captions are already in `readme.txt`; keep the order, it matches the numbers. Front end shots come first because people decide in the first two images.

**Format:** PNG or JPG, 1280 px wide (or 2560 for sharp results on large screens, then save as JPG at about 80% to stay under 1 MB). Crop to the content; no empty browser margins. Same browser width and zoom for all front end shots.

**Easy setup:** open the Live Preview (or run the blueprint in `.wordpress-org/blueprints/blueprint.json` at playground.wordpress.net). It already has the six demo posts and the front page with the Card carousel, template 21, a grid and a ticker. For screenshot 2, install WooCommerce in the same Playground and import its sample products (**Tools > Import > WooCommerce products**, file `sample-data/sample_products.csv` from the WooCommerce plugin folder).

| # | File | What to show | Setup |
|---|---|---|---|
| 1 | `screenshot-1.png` | Card template carousel: three cards with category badge, date, reading time, excerpt and button, arrows and dots. | Front page of the demo, first carousel. Pause autoplay first so nothing moves. |
| 2 | `screenshot-2.png` | WooCommerce product carousel: price with a crossed out sale price, Sale badge, stars, Add to cart. | New Post Carousel widget, Post Type: Products, Products filter: On sale, Card template, 4 columns. |
| 3 | `screenshot-3.png` | A few templates at a glance. | A 2 × 3 collage of templates 5, 12, 21, 28, 45 and Card, each cropped to three cards. The Getting Started page gallery has small versions to choose from. |
| 4 | `screenshot-4.png` | Grid layout. | The demo's "Grid layout, template 5" section, full width, 3 columns. |
| 5 | `screenshot-5.png` | News ticker and number pagination. | Two strips stacked: the demo ticker, and template 21 with Pagination set to Numbers (2 / 8). |
| 6 | `screenshot-6.png` | Choosing posts in the editor. | Elementor editor, panel open on **Post / Content**: Source set to "Related posts", Related By "Categories", and the **Filters** section open with a term chosen. The preview visible on the right. |
| 7 | `screenshot-7.png` | Card parts and image options. | The **Card Elements** section with its switches visible, plus the **Image** section on the Style tab with Image Ratio 4:3 and Hover Effect Zoom. |
| 8 | `screenshot-8.png` | Slider settings. | Panel on **Slider**: Layout Carousel, Slider Engine Modern, columns, Effect, Pagination. |
| 9 | `screenshot-9.png` | The Getting Started page. | **Elementor > Post Carousel**, scrolled so the checklist and the first rows of the gallery show. |

Tips:

* Use the same six demo posts in every front end shot so the listing feels like one product.
* In editor shots, close the Elementor navigator and notices, and hide the WordPress admin bar if it overlaps.
* Do not annotate with arrows and circles; the captions explain the shot. If you want callouts, keep them to one short label per image in the ink color.

## Optional extras

* **Short video** (60 to 90 seconds, no music needed): add the widget, pick Related posts, switch to Grid, switch to the Card template, publish. Upload to YouTube and paste the link on its own line at the top of the readme description; WordPress.org shows it as a player.
* **Animated GIF** for GitHub and social posts: the ticker or the Card carousel sliding, 800 px wide, under 3 MB.

## Final checklist

* [ ] Banner text readable at 772 px and does not clash with the name WordPress.org prints.
* [ ] Icon still clear at 64 px.
* [ ] Nine screenshots, numbered to match the readme captions.
* [ ] No Elementor logo, no third party brands, no stars or "best" claims.
* [ ] Every file under 1 MB, banners under 500 KB.
* [ ] Committed to SVN `assets/`, not to `trunk/`.
