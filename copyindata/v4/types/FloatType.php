<?php
namespace copyindata\types;

use copyindata\ErrorsList;
use copyindata\BaseError;

/**
 * Обработчик вещественных чисел
 */
class FloatType extends ScalarType {
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_numeric( $value ); // иногда пых превращает 0.0 в int
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid float', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		return ( float ) $value;
	}
}
