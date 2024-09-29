<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/actions/Rotate.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;
use images\actions\Rotate;

$converter = new Converter( new Rotate( 90, 'white' ) );

if ( !$converter->Convert( glob( './*.png' ), 'rotated.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
