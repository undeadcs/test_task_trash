<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/actions/Resize.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;
use images\actions\Resize;

$converter = new Converter( new Resize( 1024, 768 ) );

if ( !$converter->Convert( glob( './*.png' ), 'resized.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
