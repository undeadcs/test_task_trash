<?php
namespace copyindata;

/**
 * Информация о вложенности
 */
class NestedInfo {
	protected $index;
	protected BaseType $type;
	
	public function __construct( int|string $index, BaseType $type ) {
		$this->index = $index;
		$this->type = $type;
	}
	
	public function GetIndex( ) : int|string {
		return $this->index;
	}
	
	public function GetType( ) : BaseType {
		return $this->type;
	}
}
