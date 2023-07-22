<?php
namespace copyindata\types;

use copyindata\ErrorsList;
use copyindata\BaseError;

/**
 * Целые числа
 */
class IntType extends ScalarType {
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_numeric( $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid int', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		return ( int ) $value;
	}
}
