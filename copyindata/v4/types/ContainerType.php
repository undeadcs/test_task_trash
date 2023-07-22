<?php
namespace copyindata\types;

use copyindata\BaseType;

/**
 * Контейнерный тип данных
 */
abstract class ContainerType extends BaseType {
	/**
	 * Строгий режим проверки значений
	 */
	protected bool $strict;
	
	public function __construct( bool $strict = true ) {
		$this->strict = $strict;
	}
	
	/**
	 * Режим проверки значений
	 */
	public function IsStrict( ) : bool {
		return $this->strict;
	}
	
	/**
	 * Задание режима строгости проверки значений
	 */
	public function SetStrict( bool $strict ) : StructType {
		$this->strict = $strict;
		
		return $this;
	}
	
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_array( $value );
	}
}
