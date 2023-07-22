<?php
namespace copyindata;

/**
 * Список ошибок
 */
class ErrorsList implements \Iterator, \Countable {
	const // стандартные коды ошибок
		ERROR_INVALID_INPUT		= 1,
		ERROR_FIELD_NOT_FOUND	= 2;
	
	protected int $currentIndex = 0;
	protected array $errors = [ ];
	
	public function GetAll( ) : array {
		return $this->errors;
	}
	
	public function Add( BaseError $error ) : ErrorsList {
		$this->errors[ $this->currentIndex++ ] = $error;
		
		return $this;
	}
	
	public function IsEmpty( ) : bool {
		return empty( $this->errors );
	}
	
	public function current( ) : mixed {
		return $this->errors[ $this->currentIndex ];
	}
	
	public function key( ) : mixed {
		return $this->currentIndex;
	}
	
	public function next( ) : void {
		++$this->currentIndex;
	}
	
	public function rewind( ) : void {
		$this->currentIndex = 0;
	}
	
	public function valid( ) : bool {
		return isset( $this->errors[ $this->currentIndex ] );
	}
	
	public function count( ) : int {
		return count( $this->errors );
	}
}
