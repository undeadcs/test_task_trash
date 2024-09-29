<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/actions/Greyscale.php' );
require_once( __DIR__.'/actions/Resize.php' );
require_once( __DIR__.'/actions/Rotate.php' );
require_once( __DIR__.'/actions/Crop.php' );
require_once( __DIR__.'/ActionResolver.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;
use images\ActionResolver;

$resolver = new ActionResolver( 'images\\actions' );
$actions = $resolver->Resolve( [
	'greyscale',
	[ 'action' => 'crop', 'arguments' => [ 'x' => 0, 'y' => 20, 'width' => 1550, 'height' => 871 ] ],
	[ 'action' => 'resize', 'arguments' => [ 'width' => 1024, 'height' => 768 ] ],
	[ 'action' => 'rotate', 'arguments' => [ 'degree' => 90 ] ]
] );
var_dump( $actions );
$converter = new Converter( ...$actions );

if ( !$converter->Convert( glob( './*.png' ), 'mixed.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
