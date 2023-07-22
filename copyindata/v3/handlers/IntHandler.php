<?php
namespace copyindata\handlers;

use copyindata\TypeHandler;
use copyindata\attributes\MaxValue;
use copyindata\attributes\MinMaxValue;

/**
 * Обработчик целых чисел
 * @todo проверка строк на выход за границы чисел, ограниченных реализацией пыха
 */
class IntHandler implements TypeHandler {
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		return is_numeric( $value );
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значения из входящих данных
	 */
	public function Filter( $value ) {
		return ( int ) $value;
	}
}
