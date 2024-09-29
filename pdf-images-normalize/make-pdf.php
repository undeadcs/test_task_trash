<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/Action.php' );
require_once( __DIR__.'/Converter.php' );

use images\ImageFormat;
use images\Converter;

$converter = new Converter;

if ( !$converter->Convert( glob( './*.png' ), 'main.pdf', ImageFormat::Pdf ) ) {
	echo "[ERROR] Failed to convert\n";
}
/*$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImages( glob( './*.png' ) );
$image->setFormat( 'pdf' );
$image->writeImages( 'main.pdf', true );
$image->clear( );
$image->destroy( );*/
