<?php
namespace images\actions;

use images\Action;

/**
 * Resize image
 *
 * @see https://www.php.net/manual/en/imagick.resizeimage.php
 */
class Resize implements Action {
	/**
	 * Target width of image
	 */
	protected int $targetWidth;

	/**
	 * Target height of image
	 */
	protected int $targetHeight;

	/**
	 * Filter flags
	 * @see \Imagick::FILTER_*
	 */
	protected int $filterFlags;

	/**
	 * Blur factor
	 */
	protected float $blurFactor;

	/**
	 * Fit to new sizes
	 */
	protected bool $fitToTarget;

	/**
	 * Resize image
	 *
	 * @param int $columns Width of resulting image
	 * @param int $rows Height of resulting image
	 * @param int $filter Filter constants (Imagick::FILTER_*)
	 * @param float $blur Blur image factor
	 * @param bool $fit Fit image in new size
	 * @return bool Success of operation
	 */
	public function __construct( int $width, int $height, int $filter = \Imagick::FILTER_LANCZOS, float $blur = 0.9, bool $fit = false ) {
		$this->targetWidth	= $width;
		$this->targetHeight	= $height;
		$this->filterFlags	= $filter;
		$this->blurFactor	= $blur;
		$this->fitToTarget	= $fit;
	}

	/**
	 * {@inheritdoc}
	 */
	public function Apply( \Imagick $image ) : bool {
		foreach( $image as $obj ) {
			if ( !$obj->resizeImage( $this->targetWidth, $this->targetHeight, $this->filterFlags, $this->blurFactor, $this->fitToTarget ) ) {
				return false;
			}
		}

		return true;
	}
}
