<?php
namespace copyindata;

/**
 * Базовый тип данных
 */
abstract class BaseType {
	/**
	 * Тип, внутри которого находится текущий
	 */
	protected ?BaseType $parentType = null;
	
	public function GetParentType( ) : ?BaseType {
		return $this->parentType;
	}
	
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	abstract public function Validate( $value ) : bool;
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	abstract public function Filter( $value, ErrorsList $errors );
}
