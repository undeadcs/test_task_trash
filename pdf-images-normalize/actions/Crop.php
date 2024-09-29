<?php
namespace images\actions;

use images\Action;

/**
 * Crop image
 *
 * @see https://www.php.net/manual/en/imagick.cropimage.php
 */
class Crop implements Action {
	/**
	 * X coordinate of left top corner of region
	 */
	protected int $sourceX;

	/**
	 * Y coordinate of left top corner of region
	 */
	protected int $sourceY;

	/**
	 * Width of region
	 */
	protected int $width;

	/**
	 * Height of region
	 */
	protected int $height;

	public function __construct( int $x, int $y, int $width, int $height ) {
		$this->sourceX = $x;
		$this->sourceY = $y;
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * Apply action to image
	 *
	 * @param \Imagick $image Image to apply action to
	 * @return bool Success of action
	 */
	public function Apply( \Imagick $image ) : bool {
		foreach( $image as $obj ) {
			if ( !$obj->cropImage( $this->width, $this->height, $this->sourceX, $this->sourceY ) ||
				!$obj->setImagePage( $this->width, $this->height, 0, 0 )
			) {
				return false;
			}
		}

		return true;
	}
}
