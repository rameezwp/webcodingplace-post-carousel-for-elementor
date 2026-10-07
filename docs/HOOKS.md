# Developer hooks and template overrides

Everything here is public and kept stable. Hooks marked **2.0** are new; the rest have existed since 1.x and keep working.

All examples go in a small plugin or your theme's `functions.php`.

## Contents

* [Templates](#templates)
* [Query](#query)
* [Card output](#card-output)
* [Slider settings](#slider-settings)
* [Images](#images)
* [Lists used in the editor](#lists-used-in-the-editor)
* [Admin](#admin)
* [Asset handles](#asset-handles)

---

## Templates

### Override a bundled template from your theme

Copy `templates/style-{id}.php` from the plugin to `your-theme/dpce/style-{id}.php` and edit it. The theme copy wins. For example, `your-theme/dpce/style-12.php` replaces template 12, and `your-theme/dpce/style-card.php` replaces the Card template.

Inside a template you have two variables:

* `$post_id` (int): the post being shown. The global post is also set, so template tags such as `get_the_title()` work.
* `$carousel_settings` (array): the widget settings, cleaned. See [dpce_carousel_settings](#dpce_carousel_settings).

### Register a new template: `dpce_styles` (filter)

Adds a template to the **Template Style** list. Put the markup in `your-theme/dpce/style-{id}.php`.

```php
add_filter( 'dpce_styles', function ( $styles ) {
	$styles['magazine'] = array(
		'id'       => 'magazine',
		'name'     => __( 'Magazine', 'my-theme' ),
		'thumb'    => '',      // Optional preview image URL.
		'settings' => array(), // Optional extra controls, see below.
	);
	return $styles;
} );
```

Optional per template controls go in `settings`. Each entry becomes an Elementor control that only shows when that template is selected:

```php
'settings' => array(
	array(
		'id'        => 'overlay',
		'label'     => __( 'Overlay color', 'my-theme' ),
		'type'      => 'color', // color, number, slider, icon or text.
		'selectors' => array( '{{WRAPPER}} .magazine-overlay' => 'background: {{VALUE}};' ),
	),
),
```

The control ID is `style_{template id}_{setting id}`, for example `style_magazine_overlay`.

Only registered templates can be loaded, and the file name must match `style-{id}.php`.

### Template building blocks (actions)

Templates print their parts through actions, so you can change one part without copying the whole template. Each action receives `$post_id` and `$carousel_settings`.

| Action | Prints | Default callback |
|---|---|---|
| `dpce_carousel_thumbnail` | Featured image (with badges when switched on) | `DPCE_Renderer::render_thumbnail` |
| `dpce_carousel_title` | Title text (the tag comes from `dpce_render_title()`) | `DPCE_Renderer::render_title` |
| `dpce_carousel_after_title` **2.0** | After the title element; the meta row is printed here | `DPCE_Renderer::render_meta_row` |
| `dpce_carousel_desc` | Description or excerpt | `DPCE_Renderer::render_desc` |
| `dpce_carousel_read_more` | Read more link (only when Link Area is "Read More Button Only") | `DPCE_Renderer::render_read_more` |
| `dpce_carousel_overlay` | Whole card link; at priority 5 the optional product block | `DPCE_Renderer::render_overlay`, `render_product_extras` |
| `dpce_carousel_meta` | Compact date and author | `DPCE_Renderer::render_meta` |
| `dpce_carousel_share` | Share buttons | `DPCE_Renderer::render_share` |
| `dpce_carousel_icon` | Template icon (4 args: `$post_id, $settings, $icon, $css_class`) | `DPCE_Renderer::render_icon` |

Replace a part:

```php
add_action( 'init', function () {
	remove_action( 'dpce_carousel_desc', array( DPCE_Renderer::instance(), 'render_desc' ), 10 );
	add_action( 'dpce_carousel_desc', function ( $post_id ) {
		echo esc_html( get_post_meta( $post_id, 'subtitle', true ) );
	}, 10, 2 );
}, 20 );
```

Helpers you can call in your own templates:

* `dpce_render_title( $post_id, $carousel_settings, $args )`: prints the title with the tag chosen in the widget (h1 to h6, div, p, span).
* `DPCE_Woo::price( $post_id )`, `DPCE_Woo::rating()`, `DPCE_Woo::sale_badge()`, `DPCE_Woo::stock_label()`, `DPCE_Woo::add_to_cart()`: WooCommerce parts, returned as escaped HTML. They return an empty string when the post is not a product.
* `dpce_get_post_terms_for_display( $post_id, $taxonomy = 'auto', $limit = 1 )`: the main terms of a post.
* `dpce_get_reading_time( $post_id )`: minutes, at least 1.

---

## Query

### `dpce_query_args` (filter)

The `WP_Query` arguments, after every widget setting has been applied.

```php
add_filter( 'dpce_query_args', function ( $args, $settings ) {
	// Only posts that have a featured image.
	$args['meta_query'] = array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) );
	return $args;
}, 10, 2 );
```

`$settings` contains every widget setting plus normalized query keys (`display_by`, `post_type`, `taxonomy`, `terms`, `posts_per_page`, `orderby`, `order`, `exclude_ids`, `disable_current_post`). Check `$settings['query_mode']` (`custom`, `current` or `related`) if your change should only apply to one source.

For related posts, the filter runs a second time with the fallback query when nothing matched and the fallback is on.

### `dpce_max_posts` (filter) **2.0**

The largest number of posts one carousel can query. Default 100. A carousel saved with "all posts" (-1) shows this many.

```php
add_filter( 'dpce_max_posts', function () {
	return 200;
} );
```

---

## Card output

### `dpce_carousel_settings` (filter) **2.0**

The settings array passed to every template as `$carousel_settings`. Add your own keys here and read them in your template.

```php
add_filter( 'dpce_carousel_settings', function ( $carousel_settings, $settings ) {
	$carousel_settings['show_price_note'] = ! empty( $settings['my_custom_control'] );
	return $carousel_settings;
}, 10, 2 );
```

Useful keys: `style_id`, `title_tag`, `link_area`, `read_more_txt`, `read_more_target` (always `_self` or `_blank`), `image_size`, `lazy_load`, `show_meta`, `meta_items`, `show_badge`, `show_sale_badge`, `show_product_extras`, `card` (array of Card template parts: `image`, `badge`, `meta`, `excerpt`, `button`, `rating`, `price`, `sale_badge`, `add_to_cart`), `slide_index`.

### `dpce_slide_html` (filter) **2.0**

The finished HTML of one card, before it is wrapped in the slide markup.

```php
add_filter( 'dpce_slide_html', function ( $html, $post_id ) {
	if ( has_term( 'featured', 'post_tag', $post_id ) ) {
		$html = '<div class="is-featured">' . $html . '</div>';
	}
	return $html;
}, 10, 2 );
```

Return safe HTML: the value is printed as is.

### `dpce_meta_row_items` (filter) **2.0**

The items of the meta row, as an array of HTML strings (already escaped).

```php
add_filter( 'dpce_meta_row_items', function ( $parts, $post_id ) {
	$views = (int) get_post_meta( $post_id, 'views', true );
	$parts[] = '<span class="dpce-meta__item">' . esc_html( sprintf( '%d views', $views ) ) . '</span>';
	return $parts;
}, 10, 2 );
```

### `dpce_badges_html` (filter) **2.0**

The badges printed on the image (`<div class="dpce-badges">...</div>`), or change them completely.

### `dpce_words_per_minute` (filter) **2.0**

Reading speed for "min read". Default 200.

---

## Slider settings

### `dpce_swiper_options` (filter) **2.0**

Options passed to Swiper (modern engine). See the [Swiper API](https://swiperjs.com/swiper-api) for what you can set. Elementor ships Swiper 8 (or 5 on older sites that have not switched on Elementor's "Upgrade Swiper Library" option), so stick to options both versions support if you want to be safe.

```php
add_filter( 'dpce_swiper_options', function ( $options, $settings ) {
	$options['grabCursor'] = true;
	return $options;
}, 10, 2 );
```

Navigation, keyboard control, accessibility labels and autoplay pausing are added by the plugin's script and are not part of this array.

### `dpce_slick_options` (filter) **2.0**

Options passed to Slick (classic engine), for carousels that still use it.

---

## Images

### `dpce_image_sizes_attr` (filter) **2.0**

The `sizes` attribute of carousel images. By default it is built from the column settings and Elementor's breakpoints, for example `(max-width: 767px) 100vw, (max-width: 1024px) 50vw, 34vw`. Receives `$sizes` and `$columns` (`desktop`, `tablet`, `mobile`).

### `dpce_image_sizes` (filter)

The image size choices shown in the widget (`slug => label`).

---

## Lists used in the editor

| Filter | Changes |
|---|---|
| `dpce_post_types` | Post types in **Choose Post Type** (`slug => label`). |
| `dpce_taxonomies` | Taxonomies in **Choose Taxonomy**, **Related By** and **Badge and Categories From**. |
| `dpce_field_choices` | Sources in **Heading Source** and **Description Source**. |

---

## Admin

### `dpce_feedback_endpoint` (filter) **2.0**

The HTTPS address that receives the optional deactivation feedback. Empty by default, which turns the feedback form off. You can also set the `DPCE_FEEDBACK_ENDPOINT` constant. If you turn this on in a release, describe it in the readme's External services section.

---

## Asset handles

Useful if you want to dequeue or extend styles and scripts.

| Handle | Type | Loaded when |
|---|---|---|
| `dpce-base` | style | Every carousel, grid or list |
| `dpce-style-{id}` | style | The chosen template (for example `dpce-style-12`, `dpce-style-card`) |
| `dpce-swiper` | style and script | Carousels on the modern engine |
| `swiper` | style and script | Elementor's Swiper, for the modern engine |
| `dpce-slick`, `dpce-slick-theme`, `dpce-frontend` | style and script | Carousels on the classic engine (`dpce-frontend` CSS is the full stylesheet, used in the editor) |
| `dpce-editor` | script | Elementor editor only |
| `dpce-admin` | style and script | Getting Started page, Dashboard (review request) and Plugins screen |

Front end CSS lives in `assets/css/main.css`. After editing it, run `node bin/build-css.js` to rebuild the split files in `assets/css/dist/`.
