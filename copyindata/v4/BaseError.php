<?php
namespace copyindata;

/**
 * Описание ошибки
 */
class BaseError {
	protected int $code = 0;
	protected string $message = '';
	
	/**
	 * Тип в котором сгенерирована ошибка
	 */
	protected BaseType $type;
	
	/**
	 * Данные о вложенности
	 */
	protected array $nestedInfos = [ ];
	
	public function __construct( BaseType $type, string $message = '', int $code = 0 ) {
		$this->type		= $type;
		$this->message	= $message;
		$this->code		= $code;
	}
	
	public function GetType( ) : BaseType {
		return $this->type;
	}
	
	public function GetMessage( ) : string {
		return $this->message;
	}
	
	public function GetCode( ) : int {
		return $this->code;
	}
	
	public function GetNestedInfos( ) : array {
		return $this->nestedInfos;
	}
	
	/**
	 * Добавлене информации о вложенности
	 */
	public function AddNestedInfo( NestedInfo $info ) : BaseError {
		$this->nestedInfos[ ] = $info;
		
		return $this;
	}
}
