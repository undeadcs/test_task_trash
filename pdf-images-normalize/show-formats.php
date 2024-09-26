<?php

foreach( Imagick::queryFormats( ) as $name ) {
	echo $name."\n";
}
