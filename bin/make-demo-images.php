<?php
/**
 * Generate the abstract demo images used by the Live Preview and the
 * template gallery. They are drawn from scratch, so there are no licensing
 * questions. Run: php bin/make-demo-images.php
 *
 * @package DPCE
 */

$out     = __DIR__ . '/../.wordpress-org/blueprints/images';
$palette = array(
	array( array( 255, 154, 139 ), array( 255, 106, 136 ), array( 255, 213, 170 ) ),
	array( array( 67, 206, 162 ), array( 24, 90, 157 ), array( 180, 245, 220 ) ),
	array( array( 250, 208, 196 ), array( 177, 151, 252 ), array( 255, 255, 255 ) ),
	array( array( 255, 195, 113 ), array( 255, 95, 109 ), array( 255, 240, 200 ) ),
	array( array( 132, 250, 176 ), array( 143, 211, 244 ), array( 255, 255, 255 ) ),
	array( array( 48, 43, 99 ), array( 36, 198, 220 ), array( 160, 240, 255 ) ),
);

if ( ! is_dir( $out ) ) {
	mkdir( $out, 0755, true );
}

foreach ( $palette as $n => $colors ) {
	$w   = 1200;
	$h   = 800;
	$img = imagecreatetruecolor( $w, $h );
	imagealphablending( $img, true );

	// Diagonal gradient.
	for ( $y = 0; $y < $h; $y++ ) {
		for ( $x = 0; $x < $w; $x += 4 ) {
			$t = ( $x / $w + $y / $h ) / 2;
			$c = array();
			for ( $i = 0; $i < 3; $i++ ) {
				$c[ $i ] = (int) round( $colors[0][ $i ] * ( 1 - $t ) + $colors[1][ $i ] * $t );
			}
			imagefilledrectangle( $img, $x, $y, $x + 3, $y, imagecolorallocate( $img, $c[0], $c[1], $c[2] ) );
		}
	}

	// Soft circles.
	mt_srand( 42 + $n );
	for ( $i = 0; $i < 7; $i++ ) {
		$r     = mt_rand( 120, 420 );
		$alpha = mt_rand( 88, 112 );
		$col   = imagecolorallocatealpha( $img, $colors[2][0], $colors[2][1], $colors[2][2], $alpha );
		imagefilledellipse( $img, mt_rand( 0, $w ), mt_rand( 0, $h ), $r * 2, $r * 2, $col );
	}

	imagejpeg( $img, sprintf( '%s/demo-%d.jpg', $out, $n + 1 ), 82 );
	imagedestroy( $img );
	echo 'demo-' . ( $n + 1 ) . ".jpg\n";
}
