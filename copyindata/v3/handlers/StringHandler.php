<?php
namespace copyindata\handlers;

use copyindata\TypeHandler;
use copyindata\attributes\MaxLength;

/**
 * Строки
 */
class StringHandler implements TypeHandler {
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		// @todo все ли не печатаемые символы юникода в данной регулярке? там их очень много
		// скалярные типы могут быть преобразованы в строку, для массивов и объектов преобразование не совсем очевидное
		return is_string( $value ) && !preg_match( '/[\x00-\x1F\x7F\xA0]/u', $value ) || is_scalar( $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значения из входящих данных
	 */
	public function Filter( $value ) {
		return ( string ) $value;
	}
}
