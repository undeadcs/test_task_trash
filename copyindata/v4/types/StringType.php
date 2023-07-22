<?php
namespace copyindata\types;

use copyindata\ErrorsList;
use copyindata\BaseError;

/**
 * Строки
 */
class StringType extends ScalarType {
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		if ( is_string( $value ) ) {
			// @todo все ли не печатаемые символы юникода в данной регулярке? там их очень много
			return !preg_match( '/[\x00-\x1F\x7F\xA0]/u', $value );
		}
		
		return parent::Validate( $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid string', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		return ( string ) $value;
	}
}
