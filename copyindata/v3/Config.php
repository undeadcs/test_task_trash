<?php
namespace copyindata;

/**
 * Спецификация обработки данных
 */
class Config {
	/**
	 * Имя типа поля
	 */
	protected string $type;
	
	/**
	 * Спецификация вложенного типа
	 */
	protected ?Config $config = null;
	
	public function __construct( string $type = 'string', ?Config $config = null ) {
		$this->type		= $type;
		$this->config	= $config;
	}
	
	public function GetType( ) : string {
		return $this->type;
	}
	
	public function GetConfig( ) : ?Config {
		return $this->config;
	}
}
