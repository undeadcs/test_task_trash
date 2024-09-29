<?php
namespace images;

/**
 * Action with image
 */
interface Action {
	/**
	 * Apply action to image
	 *
	 * @param \Imagick $image Image to apply action to
	 * @return bool Success of action
	 */
	public function Apply( \Imagick $image ) : bool;
}
