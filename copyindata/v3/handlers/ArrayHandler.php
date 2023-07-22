<?php
namespace copyindata\handlers;

use copyindata\ArrayTypeHandler;
use copyindata\TypeHandler;

/**
 * Массив элементов
 */
class ArrayHandler implements ArrayTypeHandler {
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
			if ( !is_int( $key ) ) {
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
	
	/**
	 * Валидация всех элементов массива на основе типа
	 */
	public function ValidateItems( array $values, TypeHandler $handler ) : bool {
		foreach( $values as $value ) {
			if ( !$handler->Validate( $value ) ) {
				return false;
			}
		}
		
		return true;
	}
}
