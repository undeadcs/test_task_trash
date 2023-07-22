<?php
namespace copyindata;

/**
 * Настройки структуры
 * поле типа не используется
 */
class StructConfig extends Config {
	/**
	 * Конфиги полей
	 */
	protected array $fields;
	
	public function __construct( array $fields, string $type = 'struct', ?Config $config = null ) {
		parent::__construct( $type, $config );
		
		$this->SetFields( ...$fields ); // гарантированная проверка типа каждого элемента
	}
	
	public function SetFields( FieldConfig... $fields ) : StructConfig {
		$this->fields = $fields;
		
		return $this;
	}
	
	public function GetFields( ) : array {
		return $this->fields;
	}
}
