<?php

$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImage( 'main.pdf' );

foreach( $image as $tmp ) {
	//$image->setImageColorspace( Imagick::COLORSPACE_GRAY );
	$image->transformImageColorspace( Imagick::COLORSPACE_GRAY );
}

$image->setFormat( 'pdf' );
$image->writeImages( 'greyscaled.pdf', true );
$image->clear( );
$image->destroy( );
