<?php
namespace copyindata\types;

use copyindata\BaseType;
use copyindata\ErrorsList;
use copyindata\BaseError;
use copyindata\NestedInfo;

/**
 * Структура
 * режимы проверок ключей:
 * 1. полное соответствие массивов ключей (не допускается отсутствие или лишние)
 * 2. наличие всех ключей полей (не допускается отсутствие, допускаются лишние)
 * 3. если найдено поле по ключу (допускается отсутствие, допускаются лишние)
 * 
 * @todo добавить поддержку объекта как входящих данных
 */
class StructType extends ContainerType {
	const // режимы проверки ключей
		KEYS_EQUAL	= 0,	// полное совпадение ключей между полями и входящими данными
		KEYS_EXISTS	= 1,	// все ключи полей должны присутствовать во входящих данных
		KEYS_FOUND	= 2;	// если ключ найден во входящих данных
	
	/**
	 * Режим проверки ключей
	 */
	protected int $keysMode;
	
	/**
	 * Поля структуры {имя} => {тип}
	 */
	protected array $fields;
	
	public function __construct( array $fields, bool $strict = true, int $keysMode = self::KEYS_EQUAL ) {
		parent::__construct( $strict );
		
		$this->keysMode	= $keysMode;
		
		foreach( $fields as $name => $type ) {
			$this->AddField( $name, $type );
		}
	}
	
	/**
	 * Добавление поля
	 */
	public function AddField( string $name, BaseType $type ) : StructType {
		$this->fields[ $name ] = $type;
		
		$type->parentType = $this;
		
		return $this;
	}
	
	/**
	 * Поиск поля по имени
	 */
	public function FindField( string $name ) : ?BaseType {
		return isset( $this->fields[ $name ] ) ? $this->fields[ $name ] : null;
	}
	
	/**
	 * Получение режима проверки ключей
	 */
	public function GetKeysMode( ) : int {
		return $this->keysMode;
	}
	
	/**
	 * Задание режима проверки ключей
	 */
	public function SetKeysMode( int $keysMode ) : StructType {
		$this->keysMode = $keysMode;
		
		return $this;
	}
	
	/**
	 * Валидация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 */
	public function Validate( $value ) : bool {
		if ( !$this->fields ) {
			throw new \RuntimeException( 'Struct Fields are empty' );
		}
		if ( !parent::Validate( $value ) ) {
			return false;
		}
		
		$keys = array_keys( $value );
		foreach( $keys as $key ) {
			if ( !is_string( $key ) ) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Проверка массивов ключей
	 */
	protected function ValidateKeys( $input, ErrorsList $errors ) : bool {
		if ( $this->keysMode == self::KEYS_FOUND ) { // допускается отсутствие, допускаются лишние
			return true;
		}
		
		$fieldKeys = array_keys( $this->fields );
		$inputKeys = array_keys( $input );
		
		if ( $fieldKeys === $inputKeys ) { // если идентичны, то режим не имеет значения
			return true;
		}
		if ( $this->keysMode == self::KEYS_EQUAL ) {
			$errors->Add( new BaseError( $this, 'Invalid struct', ErrorsList::ERROR_INVALID_INPUT ) );
			return false;
		}
		
		return $this->FindFields( $input, $errors );
	}
	
	/**
	 * Поиск полей
	 */
	protected function FindFields( $input, ErrorsList $errors ) : bool {
		$allFound = true;
		
		foreach( $this->fields as $name => $type ) {
			if ( !array_key_exists( $name, $input ) ) {
				$allFound = false;
				$errors->Add( new BaseError( $this, 'Field "'.$name.'" not found', ErrorsList::ERROR_FIELD_NOT_FOUND ) );
			}
		}
		
		return $allFound;
	}
	
	/**
	 * Фильтрация значения
	 * 
	 * @param mixed $value значение из входящих данных
	 * @param Errors $errors контейнер ошибок валидации
	 */
	public function Filter( $value, ErrorsList $errors ) {
		if ( !$this->Validate( $value ) ) {
			$errors->Add( new BaseError( $this, 'Invalid struct', ErrorsList::ERROR_INVALID_INPUT ) );
			return null;
		}
		if ( !$this->ValidateKeys( $value, $errors ) ) {
			return null;
		}
		
		$ret = [ ];
		$failed = false;
		
		foreach( $this->fields as $name => $type ) {
			if ( array_key_exists( $name, $value ) ) {
				$itemErrors = new ErrorsList;
				$tmp = $type->Filter( $value[ $name ], $itemErrors );
				
				foreach( $itemErrors as $error ) {
					$error->AddNestedInfo( new NestedInfo( $name, $this ) );
					$errors->Add( $error );
				}
			
				if ( is_null( $tmp ) ) {
					$failed = true;
				}
				if ( !$this->strict || !is_null( $tmp ) ) {
					$ret[ $name ] = $tmp;
				}
			}
		}
		
		return ( $this->strict && $failed ) ? [ ] : $ret;
	}
}
