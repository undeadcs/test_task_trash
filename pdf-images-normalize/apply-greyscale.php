<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/ImageConverter.php' );

use images\ImageConverter;
use images\ImageFormat;

$converter = new ImageConverter( glob( './*.png' ), 300, 300, ImageFormat::Pdf );

if ( !$converter->Greyscale( ) ) {
	echo "[ERROR] Failed to apply greyscale filter\n";
}

$converter->Flush( 'greyscaled.pdf' );
/*$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImage( 'main.pdf' );

foreach( $image as $tmp ) {
	//$image->setImageColorspace( Imagick::COLORSPACE_GRAY );
	$image->transformImageColorspace( Imagick::COLORSPACE_GRAY );
}

$image->setFormat( 'pdf' );
$image->writeImages( 'greyscaled.pdf', true );
$image->clear( );
$image->destroy( );*/
