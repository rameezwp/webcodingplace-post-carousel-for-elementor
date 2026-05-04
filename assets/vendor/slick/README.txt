Slick Carousel vendor files
===========================

This plugin uses Slick Carousel (https://kenwheeler.github.io/slick/) on the frontend.
Place the following official files in this directory before submitting to wordpress.org:

  - slick.min.js
  - slick.css
  - slick-theme.css   (optional, only needed if you reference its dot/arrow assets)

Slick is licensed MIT (https://github.com/kenwheeler/slick/blob/master/LICENSE).

The plugin only enqueues these scripts/styles when the files are physically present
in this folder, so the plugin will not 404 if the files are missing — but the
carousel will not initialize until they are added.
