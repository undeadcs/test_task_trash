<?php
namespace copyindata\handlers;

use copyindata\attributes\PrependCharIfNotFound;

/**
 * Обработчик типа телефонных номеров
 */
class PhoneNumberHandler extends StringHandler {
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_string( $value ) && preg_match( '/^\+?[78] *\(?\d{3}\)? *\d{3}[- ]*\d{2}[- ]*\d{2}$/', $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значения из входящих данных
	 */
	public function Filter( $value ) {
		return ( string ) preg_replace( [ '/[^0-9]/', '/^8/' ], [ '', '7' ], $value );
	}
}
