<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/actions/Greyscale.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;
use images\actions\Greyscale;

$converter = new Converter( new Greyscale );

if ( !$converter->Convert( glob( './*.png' ), 'greyscaled.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
