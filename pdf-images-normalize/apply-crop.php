<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/actions/Crop.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;
use images\actions\Crop;

$converter = new Converter( new Crop( 0, 20, 800, 600 ) );

if ( !$converter->Convert( glob( './*.png' ), 'cropped.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
