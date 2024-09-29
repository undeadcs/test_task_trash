<?php

$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImages( glob( './*.png' ) );
$image->setFormat( 'tiff' );
$image->writeImages( 'main.tiff', true );
$image->clear( );
$image->destroy( );
