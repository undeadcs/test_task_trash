<?php
namespace images;

/**
 * Image converter
 */
class Converter {
	/**
	 * Action to apply to image
	 */
	protected array $actions;

	public function __construct( Action ...$actions ) {
		$this->actions = $actions;
	}

	protected function CreateImage( string|array $path ) : \Imagick {
		$image = new \Imagick;

		if ( is_array( $path ) ) {
			$image->readImages( $path );
		} else {
			$image->readImage( $path );
		}

		return $image;
	}

	/**
	 * Convert image
	 *
	 * @param string $src Source image file path
	 * @param string $dst Destination image file path
	 * @param ImageFormat $format Output image format
	 * @return bool Success of operation
	 */
	public function Convert( string|array $src, string $dst, ImageFormat $format ) : bool {
		$image = $this->CreateImage( $src );

		foreach( $this->actions as $action ) {
			if ( !$action->Apply( $image ) ) {
				return false;
			}
		}

		$result = $image->setFormat( $format->value ) && $image->writeImages( $dst, true );

		$image->clear( );
		$image->destroy( );
		unset( $image );

		return $result;
	}
}
