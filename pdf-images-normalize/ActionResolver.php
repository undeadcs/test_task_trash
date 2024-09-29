<?php
namespace images;

use images\actions\Greyscale;
use images\actions\Resize;
use images\actions\Rotate;
use images\actions\Crop;

/**
 * Actions pipeline resolver
 *
 * @todo add validation policy (throw|log|ignore|log+throw)
 */
class ActionResolver {
	/**
	 * Action classes namespace
	 */
	protected string $actionsNamespace;

	public function __construct( string $actionsNamespace ) {
		$this->actionsNamespace = $actionsNamespace;
	}

	protected function IsValidConfig( string|array $config ) : bool {
		return ( is_string( $config ) && !empty( $config ) ) ||
			( is_array( $config ) && isset( $config[ 'action' ] ) );
	}

	protected function MakeActionClassName( string|array $config ) : string {
		return ucfirst( strtolower( is_array( $config ) ? $config[ 'action' ] : $config ) );
	}

	protected function RequiredArgumentsExists( array $arguments, array $names ) : bool {
		foreach( $names as $name ) {
			if ( !array_key_exists( $name, $arguments ) ) {
				return false;
			}
		}

		return true;
	}

	protected function IsValidResizeArguments( string|array $config ) : bool {
		return is_array( $config ) &&
			array_key_exists( 'arguments', $config ) &&
			is_array( $config[ 'arguments' ] ) &&
			$this->RequiredArgumentsExists( $config[ 'arguments' ], [ 'width', 'height' ] );
	}

	protected function CreateResizeAction( array $config ) : Resize {
		return new Resize( width: ( int ) $config[ 'arguments' ][ 'width' ], height: ( int ) $config[ 'arguments' ][ 'height' ] );
	}

	protected function IsValidRotateArguments( string|array $config ) : bool {
		return is_array( $config ) &&
			array_key_exists( 'arguments', $config ) &&
			is_array( $config[ 'arguments' ] ) &&
			$this->RequiredArgumentsExists( $config[ 'arguments' ], [ 'degree' ] );
	}

	protected function CreateRotateAction( array $config ) : Rotate {
		return new Rotate( degree: ( float ) $config[ 'arguments' ][ 'degree' ], background: 'white' );
	}

	protected function IsValidCropArguments( string|array $config ) : bool {
		return is_array( $config ) &&
			array_key_exists( 'arguments', $config ) &&
			is_array( $config[ 'arguments' ] ) &&
			$this->RequiredArgumentsExists( $config[ 'arguments' ], [ 'x', 'y', 'width', 'height' ] );
	}

	protected function CreateCropAction( array $config ) : Crop {
		return new Crop(
			x: ( int ) $config[ 'arguments' ][ 'x' ],
			y: ( int ) $config[ 'arguments' ][ 'y' ],
			width: ( int ) $config[ 'arguments' ][ 'width' ],
			height: ( int ) $config[ 'arguments' ][ 'height' ]
		);
	}

	/**
	 * Resolve config to Action object
	 */
	protected function ResolveAction( string|array $config ) : ?Action {
		$className = $this->actionsNamespace.'\\'.$this->MakeActionClassName( $config );
		if ( !@class_exists( $className ) ) {
			return null;
		}

		switch( $className ) {
			case Greyscale::class:	return new Greyscale;
			case Resize::class:		return $this->IsValidResizeArguments( $config ) ? $this->CreateResizeAction( $config ) : null;
			case Rotate::class:		return $this->IsValidRotateArguments( $config ) ? $this->CreateRotateAction( $config ) : null;
			case Crop::class:		return $this->IsValidCropArguments( $config ) ? $this->CreateCropAction( $config ) : null;
		}

		return null;
	}

	/**
	 * Resolve actions to objects of Action
	 *
	 * @return array Array of Action objects
	 */
	public function Resolve( array $actions ) : array {
		$ret = [ ];

		foreach( $actions as $config ) {
			if ( $this->IsValidConfig( $config ) && ( $action = $this->ResolveAction( $config ) ) ) {
				$ret[ ] = $action;
			}
		}

		return $ret;
	}
}
