<?php
namespace images\actions;

use images\Action;

/**
 * Rotate image
 *
 * @see https://www.php.net/manual/en/imagick.rotateimage.php
 */
class Rotate implements Action {
	/**
	 * Degree of rotation (clockwise)
	 */
	protected float $degree;

	/**
	 * Background color
	 */
	protected \ImagickPixel $background;

	/**
	 * Rotate image
	 *
	 * @param float $degree Degree of rotation (clockwise)
	 * @param string|\ImagickPixel $background Background color for filling empty space
	 * @return bool Success of operation
	 */
	public function __construct( float $degree, string|\ImagickPixel $background ) {
		$this->degree = $degree;
		$this->background = is_string( $background ) ? new \ImagickPixel( $background ) : $background;
	}

	/**
	 * {@inheritdoc}
	 */
	public function Apply( \Imagick $image ) : bool {
		foreach( $image as $obj ) {
			if ( !$obj->rotateImage( $this->background, $this->degree ) ) {
				return false;
			}
		}

		return true;
	}
}
