<?php
namespace copyindata;

/**
 * Ошибка при обработке поля
 */
class FieldError {
	const // коды ошибок
		ERROR_NOT_FOUND		= 1,
		ERROR_INVALID_VALUE	= 2;
	
	/**
	 * @var string Имя поля
	 */
	protected string $fieldName;
	
	/**
	 * @var string Сообщение
	 */
	protected string $message;
	
	/**
	 * @var int Код
	 */
	protected int $code;
	
	public function __construct( string $fieldName, string $message, int $code ) {
		$this->fieldName	= $fieldName;
		$this->message		= $message;
		$this->code			= $code;
	}
	
	public function GetFieldName( ) : string {
		return $this->fieldName;
	}
	
	public function GetMessage( ) : string {
		return $this->message;
	}
	
	public function GetCode( ) : int {
		return $this->code;
	}
}
