<?php
namespace copyindata\types;

use copyindata\BaseType;

/**
 * Скалярный тип данных
 */
abstract class ScalarType extends BaseType {
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_scalar( $value );
	}
}
