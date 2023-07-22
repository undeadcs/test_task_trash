<?php
namespace copyindata\types;

use copyindata\ErrorsList;
use copyindata\BaseError;

/**
 * Обработчик типа телефонных номеров
 */
class PhoneNumberType extends StringType {
	/**
	 * Регулярка для preg_match
	 */
	protected string $pattern;
	
	public function __construct( string $pattern = '/^\+?[78] *\(?\d{3}\)? *\d{3}[- ]*\d{2}[- ]*\d{2}$/' ) {
		$this->pattern = $pattern;
	}
	
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_string( $value ) && preg_match( $this->pattern, $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid phone', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		return ( string ) preg_replace( [ '/[^0-9]/', '/^8/' ], [ '', '7' ], $value );
	}
}
