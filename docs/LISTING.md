# WordPress.org listing notes

Why `readme.txt` says what it says, and what to change if you disagree. The slug (`webcodingplace-post-carousel-for-elementor`) never changes, whatever display name you pick.

## Plugin name: three options

The name is the strongest ranking signal in the plugin directory search, and it is the first thing people read in search results. Words at the start count most. WordPress.org reviewers push back on names that read like a keyword list, and the name must not start with someone else's trademark ("Elementor ...").

| | Name | Good | Not so good |
|---|---|---|---|
| A | WebCodingPlace Post Carousel for Elementor (current) | Known to existing users, no review risk. | The brand comes first, and nobody searches for it. "Grid" and "slider" are missing, so the plugin is invisible for "elementor post grid". |
| **B (chosen)** | **Post Carousel & Grid for Elementor by WebCodingPlace** | Starts with the main search phrase. Adds "grid", which 2.0 now really offers. Keeps the brand, so it stays distinct from the many plugins called "Post Carousel". 52 characters, shows in full in search results. | A new name for existing users (the slug and widget stay the same, so nothing breaks). |
| C | Post Carousel & Slider for Elementor: Posts, Products and Grid | Covers the most search phrases. | Reads like a keyword list, which reviewers and users dislike. Too long for the search results card, so the end is cut off. Loses the brand. |

To switch, change the `=== ... ===` line in `readme.txt` **and** `Plugin Name:` in the main plugin file. Plugin Check reports an error when they differ.

## Tags

Only the first five tags count, and tags weigh less than the name and the short description. "slick" was dropped: nobody searches for the library name, and new carousels no longer use it.

`elementor, post carousel, post slider, post grid, product carousel`

* `elementor`: the tag users browse for addons.
* `post carousel` and `post slider`: the two most common ways people describe this widget.
* `post grid`: the new layout. Grid plugins get a lot of searches.
* `product carousel`: brings in WooCommerce shop owners, who have different needs than bloggers.

Left out: `slider` and `carousel` alone (huge competition, mostly image sliders), `woocommerce` (crowded, and we are not mainly a WooCommerce plugin), `related posts` (it is in the description instead).

## Short description

137 characters, limit 150. It names what is shown (posts, products, any post type), where (Elementor), the three layouts and the two strongest features. No hype.

## Keywords in the long description

These phrases each appear naturally a few times: post carousel, post slider, post grid, Elementor, WooCommerce product carousel, related posts, news ticker, custom post type, blog carousel. Do not add more of them. WordPress.org search punishes repetition.

## Version and compatibility headers

* `Tested up to: 7.1`, the current WordPress release. Tested on 7.1.2.
* `Requires at least: 6.3` and `Requires PHP: 7.4`, matching the plugin header (Plugin Check compares them).
* `Elementor tested up to: 4.3` in the plugin header (tested on 4.3.2, the current release). Elementor reads this to show its "not tested with your version" warning.
* `Stable tag` stays `1.4` until you release. When you release 2.0, change `Stable tag`, `Version:` and `DPCE_VERSION` together.

## Screenshots

The captions in `readme.txt` match the shot list in `docs/ASSETS-BRIEF.md`. Upload `screenshot-1.png` to `screenshot-9.png` to the SVN `assets` folder, not to the plugin.

## Live preview

`.wordpress-org/blueprints/blueprint.json` drives the "Live Preview" button. Copy it to SVN as `assets/blueprints/blueprint.json`, then turn the preview on in the plugin's Advanced view on WordPress.org. It downloads the demo images from the `main` branch of the GitHub repository, so merge this branch first.
