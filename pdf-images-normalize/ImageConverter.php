<?php
namespace images;

use Imagick;
use ImagickPixel;

/**
 * Image converter
 */
class ImageConverter {
	protected ?Imagick $image = null;

	public function __construct( string|array $path, int $width, int $height, ImageFormat $format ) {
		$this->image = new Imagick;
		$this->image->setResolution( $width, $height );

		if ( is_array( $path ) ) {
			$this->image->readImages( $path );
		} else {
			$this->image->readImage( $path );
		}

		$this->image->setFormat( $format->value );
	}

	public function __destruct( ) {
		$this->image->clear( );
		$this->image->destroy( );
		unset( $this->image );
		$this->image = null;
	}

	/**
	 * Flush result to file
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
	 * @return bool Success of operation
	 */
	public function Rotate( float $degrees, string|ImagickPixel $background ) : bool {
		foreach( $this->image as $image ) {
			if ( !$image->rotateImage( $background, $degrees ) ) {
				return false;
			}
		}

		return true;
	}
}
