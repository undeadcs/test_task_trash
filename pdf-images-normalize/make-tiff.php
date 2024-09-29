<?php
require_once( __DIR__.'/ImageFormat.php' );
require_once( __DIR__.'/ImageConverter.php' );

use images\ImageConverter;
use images\ImageFormat;

$converter = new ImageConverter( glob( './*.png' ), 300, 300, ImageFormat::Tiff );
$converter->Flush( 'main.tiff' );
/*$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImages( glob( './*.png' ) );
$image->setFormat( 'tiff' );
$image->writeImages( 'main.tiff', true );
$image->clear( );
$image->destroy( );*/
