<?php
namespace copyindata;

/**
 * Тип данных для массивов
 */
interface ArrayTypeHandler extends TypeHandler {
	/**
	 * Валидация всех элементов массива на основе типа
	 */
	public function ValidateItems( array $values, TypeHandler $handler ) : bool;
}
