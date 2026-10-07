<?php
/**
 * Convert template screenshots (PNG) into small WebP thumbnails for the
 * Getting Started page. Usage: php bin/make-gallery-thumbs.php SOURCE_DIR
 *
 * The screenshots are taken from a test site with the demo posts from
 * .wordpress-org/blueprints/images, one full width page per template.
 *
 * @package DPCE
 */

$src = isset( $argv[1] ) ? rtrim( $argv[1], '/' ) : '';
$out = __DIR__ . '/../assets/images/templates';
if ( ! is_dir( $src ) ) {
	fwrite( STDERR, "Usage: php bin/make-gallery-thumbs.php SOURCE_DIR\n" );
	exit( 1 );
}
if ( ! is_dir( $out ) ) {
	mkdir( $out, 0755, true );
}

$tw = 480;
$th = 300;
foreach ( glob( $src . '/style-*.png' ) as $file ) {
	$img = imagecreatefrompng( $file );
	$w   = imagesx( $img );
	$h   = imagesy( $img );

	// Fit the whole width into the 8:5 frame and pad with the page background.
	$scale = min( $tw / $w, $th / $h );
	$dw    = (int) round( $w * $scale );
	$dh    = (int) round( $h * $scale );

	$thumb = imagecreatetruecolor( $tw, $th );
	$bg    = imagecolorat( $img, 2, 2 );
	imagefill( $thumb, 0, 0, $bg );
	imagecopyresampled( $thumb, $img, (int) ( ( $tw - $dw ) / 2 ), (int) ( ( $th - $dh ) / 2 ), 0, 0, $dw, $dh, $w, $h );
	imagewebp( $thumb, $out . '/' . basename( $file, '.png' ) . '.webp', 72 );
	imagedestroy( $img );
	imagedestroy( $thumb );
}
echo "done\n";
