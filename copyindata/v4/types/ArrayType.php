<?php
namespace copyindata\types;

use copyindata\BaseType;
use copyindata\ErrorsList;
use copyindata\BaseError;
use copyindata\NestedInfo;

/**
 * Массив однотипных элементов
 * @todo настройка по ограничению количества элементов входящих данных
 */
class ArrayType extends ContainerType {
	/**
	 * Тип элементов
	 */
	protected BaseType $itemsType;
	
	public function __construct( BaseType $itemsType, bool $strict = true ) {
		parent::__construct( $strict );
		
		$this->itemsType = $itemsType;
		$this->itemsType->parentType = $this;
	}
	
	public function GetItemsType( ) : BaseType {
		return $this->itemsType;
	}
	
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		if ( !parent::Validate( $value ) ) {
			return false;
		}
		
		$keys = array_keys( $value );
		foreach( $keys as $key ) {
			if ( !is_int( $key ) ) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid array', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		
		$ret = [ ];
		$failed = false;
		
		foreach( $value as $index => $item ) {
			$itemErrors = new ErrorsList;
			$tmp = $this->itemsType->Filter( $item, $itemErrors );
			
			// дочерний конфиг ничего не знает о своем положении в массиве, это рантайм инфа
			foreach( $itemErrors as $error ) {
				$error->AddNestedInfo( new NestedInfo( $index, $this ) ); // чтобы потом точно знать какой элемент массива с ошибкой
				$errors->Add( $error );
			}
			
			if ( is_null( $tmp ) ) {
				$failed = true;
			} else {
				$ret[ ] = $tmp;
			}
		}
		
		return ( $this->strict && $failed ) ? [ ] : $ret;
	}
}
