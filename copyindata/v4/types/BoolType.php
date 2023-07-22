<?php
namespace copyindata\types;

use copyindata\ErrorsList;
use copyindata\BaseError;

/**
 * Обработчик булевых значений
 */
class BoolType extends ScalarType {
	/**
	 * Строковые значения, которые превращаются в true
	 */
	protected array $trueValues;
	
	/**
	 * Строковые значения, которые превращаются в false
	 */
	protected array $falseValues;
	
	public function __construct( array $trueValues = [ 'yes', 'true', 'on' ], array $falseValues = [ 'no', 'false', 'off' ] ) {
		$this->trueValues = $trueValues;
		$this->falseValues = $falseValues;
	}
	
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return parent::Validate( $value ) && (
			is_bool( $value ) ||
			is_numeric( $value ) ||
			( is_string( $value ) && ( in_array( $value, $this->trueValues ) || in_array( $value, $this->falseValues ) ) )
		);
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid bool', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		if ( is_numeric( $value ) ) { // это чтобы на строках с числами не срабатывало условие ниже
			return ( bool ) $value;
		}
		if ( is_string( $value ) ) { // ( in_array( $value, $this->trueValues ) || in_array( $value, $this->falseValues ) )
			return in_array( $value, $this->trueValues ) ? true : false;
		}
		
		return ( bool ) $value;
	}
}
