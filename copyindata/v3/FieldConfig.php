<?php
namespace copyindata;

/**
 * Настройка ассоциативного поля
 */
class FieldConfig extends Config {
	/**
	 * Ключ в ассоциативном массиве входных данных
	 */
	protected string $name;
	
	public function __construct( string $name, string $type = 'string', ?Config $config = null ) {
		parent::__construct( $type, $config );
		
		$this->name = $name;
	}
	
	public function GetName( ) : string {
		return $this->name;
	}
}
