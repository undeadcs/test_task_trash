<?php
namespace copyindata;

/**
 * Тип данных и его обработка
 */
interface TypeHandler {
	/**
	 * Валидация входящего значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool;
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Filter( $value );
}
