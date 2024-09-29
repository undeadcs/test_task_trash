<?php
namespace images;

use Imagick;
use ImagickPixel;

/**
 * Image wrapper
 */
class ImageFile {
	/**
	 * Image resource
	 */
	protected ?Imagick $image = null;

	/**
	 * Constructor
	 *
	 * @param string|array $path Path to file or array of paths for multiple files
	 * @param ImageFormat $format Desired file format
	 */
	public function __construct( string|array $path, ImageFormat $format ) {
		$this->image = new Imagick;

		if ( is_array( $path ) ) {
			$this->image->readImages( $path );
		} else {
			$this->image->readImage( $path );
		}

		$this->image->setFormat( $format->value );
	}

	/**
	 * Destructor
	 */
	public function __destruct( ) {
		$this->image->clear( );
		$this->image->destroy( );
		unset( $this->image );
		$this->image = null;
	}

	public function PrintResolutions( ) : void {
		echo 'file resolution: '.$this->image->getImageWidth( ).'x'.$this->image->getImageHeight( )."\n";

		foreach( $this->image as $image ) {
			echo 'image['.$image->getImageIndex( ).'] resolution: '.$image->getImageWidth( ).'x'.$image->getImageHeight( )."\n";
		}
	}

	/**
	 * Flush result to file
	 *
	 * @param string $path Path of file to save to
	 * @return bool Success of operation
	 */
	public function Flush( string $path ) : bool {
		return $this->image->writeImages( $path, true );
	}

	/**
	 * Apply greyscale filter to image
	 *
	 * @return bool Success of operation
	 */
	public function Greyscale( ) : bool {
		foreach( $this->image as $image ) {
			if ( !$image->transformImageColorspace( Imagick::COLORSPACE_GRAY ) ) {
				return false;
			}
		}

		return true;
	}

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
	public function Resize( int $columns, int $rows, int $filter, float $blur, bool $fit = false ) : bool {
		foreach( $this->image as $image ) {
			if ( !$image->resizeImage( $columns, $rows, $filter, $blur, $fit ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Rotate image
	 *
	 * @param float $degree Degree of rotation (clockwise)
	 * @param string|ImagickPixel $background Background color for filling empty space
	 * @return bool Success of operation
	 */
	public function Rotate( float $degree, string|ImagickPixel $background ) : bool {
		foreach( $this->image as $image ) {
			if ( !$image->rotateImage( $background, $degree ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Cropt image
	 *
	 * @param int $width Width of region
	 * @param int $height Height of region
	 * @param int $x X coordinate of left top corner of region
	 * @param int $y Y coordinate of left top corner of region
	 * @return bool Success of operation
	 */
	public function Crop( int $width, int $height, int $x, int $y ) : bool {
		foreach( $this->image as $image ) {
			if ( !$image->cropImage( $width, $height, $x, $y ) || !$image->setImagePage( $width, $height, 0, 0 ) ) {
				return false;
			}
		}

		return true;
	}
}
