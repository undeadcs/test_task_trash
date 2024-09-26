<?php

$image = new Imagick;
$image->setResolution( 300, 300 );
$image->readImages( glob( './*.png' ) );
$image->setFormat( 'pdf' );
$image->writeImages( 'main.pdf', true );
$image->clear( );
$image->destroy( );
