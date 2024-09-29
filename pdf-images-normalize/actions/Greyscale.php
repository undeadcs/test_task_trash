<?php
namespace images\actions;

use images\Action;

/**
 * Greyscale filter
 *
 * @see https://www.php.net/manual/en/imagick.transformimagecolorspace.php
 */
class Greyscale implements Action {
	/**
	 * {@inheritdoc}
	 */
	public function Apply( \Imagick $image ) : bool {
		foreach( $image as $obj ) {
			if ( !$obj->transformImageColorspace( \Imagick::COLORSPACE_GRAY ) ) {
				return false;
			}
		}

		return true;
	}
}
