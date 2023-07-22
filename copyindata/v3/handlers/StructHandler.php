<?php
namespace copyindata\handlers;

use copyindata\StructTypeHandler;

/**
 * Данные в виде структуры
 */
class StructHandler implements StructTypeHandler {
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		if ( !is_array( $value ) ) {
			return false;
		}
		
		$keys = array_keys( $value );
		foreach( $keys as $key ) {
			if ( !is_string( $key ) ) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Filter( $value ) {
		return $value;
	}
}
