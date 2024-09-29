<?php
namespace images;

/**
 * Image file info
 */
interface ImageFile {
	/**
	 * Get image file name
	 *
	 * @return string Name
	 */
	public function name( ) : string;

	/**
	 * Get image file path
	 *
	 * @return string Full filesystem path
	 */
	public function path( ) : string;
}
