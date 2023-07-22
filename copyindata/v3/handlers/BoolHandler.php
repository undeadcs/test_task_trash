<?php
namespace copyindata\handlers;

use copyindata\TypeHandler;

/**
 * Обработчик булевых значений
 */
class BoolHandler implements TypeHandler {
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
		return is_bool( $value ) || is_numeric( $value ) || (
			is_string( $value ) && ( in_array( $value, $this->trueValues ) || in_array( $value, $this->falseValues ) )
		);
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значения из входящих данных
	 */
	public function Filter( $value ) {
		if ( is_string( $value ) ) {
			return in_array( $value, $this->trueValues ) ? true : false; // @todo стоит ли кидать исключение, если значение не из falseValues ?
		}
		
		return ( bool ) $value;
	}
}
