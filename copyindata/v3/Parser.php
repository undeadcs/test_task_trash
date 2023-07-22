<?php
namespace copyindata;

/**
 * Парсер данных
 * @todo ввести политики обработки вложенных и не скалярных значений
 * @todo рассмотреть возможность настройки формирования префиксов имен полей
 * @todo генератор ошибок (или интерфейс сохранения ошибок) стоит вынести в отдельный класс, чтобы можно было его настраивать
 * @todo если в конфиге прописывать родительский конфиг и конфиг элементов, то в ошибке можно просто его возвращать (пользователь сам сможет собрать всю цепочку)
 */
class Parser {
	/**
	 * Обработчики типов данных
	 */
	protected array $handlers;
	
	/**
	 * Функции обработки конфигов
	 */
	protected array $processors = [ ];
	
	public function __construct( ) {
		// что-то типа dispatch table
		$this->processors = [
			StructConfig::class	=> [ $this, 'ProcStructConfig' ],
			FieldConfig::class	=> [ $this, 'ProcFieldConfig' ],
			ArrayConfig::class	=> [ $this, 'ProcArrayConfig' ]
		];
	}
	
	public function RegisterType( string $name, TypeHandler $handler ) : Parser {
		$this->handlers[ $name ] = $handler;
		
		return $this;
	}
	
	/**
	 * Разбор входящих данных
	 * 
	 * @param array $input входящие данные
	 * @param Config $config спецификация
	 * @param array $errors массив для сохранения ошибок
	 * @throws \RuntimeException
	 */
	public function Parse( Config $config, $input, array &$errors, string $namePrefix = '' ) {
		// комплексный тип, возможны вложенности
		if ( isset( $this->processors[ $config::class ] ) ) {
			return $this->processors[ $config::class ]( $config, $input, $errors, $namePrefix );
		}
		
		// скаляр
		$handler = $this->FindHandler( $config->GetType( ) );
		
		if ( !$handler->Validate( $input ) ) {
			$errors[ ] = new FieldError( $namePrefix, 'Invalid input', FieldError::ERROR_INVALID_VALUE );
			return null;
		}
		
		return $handler->Filter( $input );
	}
	
	/**
	 * Обработка структуры
	 */
	protected function ProcStructConfig( StructConfig $config, $input, array &$errors, string $namePrefix = '' ) {
		$handler = $this->FindHandler( $config->GetType( ) );
		
		if ( !$handler->Validate( $input ) ) {
			$errors[ ] = new FieldError( $namePrefix, 'Invalid input', FieldError::ERROR_INVALID_VALUE );
			return null;
		}
		
		$output = [ ]; // если входящее значение массив, то вернуть массив
		
		if ( $this->AllFieldsExists( $config, $input, $errors, $namePrefix ) ) { // @todo политика: обязательное наличие всех полей
			foreach( $config->GetFields( ) as $fieldConfig ) {
				$value = $this->Parse( $fieldConfig, $input[ $fieldConfig->GetName( ) ], $errors, $this->MakeFieldPrefix( $fieldConfig, $namePrefix ) );
				if ( !is_null( $value ) ) { // @todo политика: пропуск поля или null значение, или некое дефолтное значение в зависимости от типа
					$output[ $fieldConfig->GetName( ) ] = $value;
				}
			}
		}
		
		// @todo политика: если исходящий массив структуры пуст, то возвращать null
		return $output;
	}
	
	/**
	 * Обработка поля структуры
	 */
	protected function ProcFieldConfig( FieldConfig $config, $input, array &$errors, string $namePrefix = '' ) {
		$handler = $this->FindHandler( $config->GetType( ) );
		
		if ( !$handler->Validate( $input ) ) {
			$errors[ ] = new FieldError( $namePrefix, 'Invalid input', FieldError::ERROR_INVALID_VALUE );
			return null;
		}
		
		$fieldConfig = $config->GetConfig( );
		
		return $fieldConfig ? $this->Parse( $fieldConfig, $input, $errors, $namePrefix ) : $handler->Filter( $input );
	}
	
	/**
	 * Обработка конфига массива
	 */
	protected function ProcArrayConfig( ArrayConfig $config, $input, array &$errors, string $namePrefix = '' ) {
		if ( !is_array( $input ) ) { // это нужно, если конфиг корневой
			$errors[ ] = new FieldError( $namePrefix, 'Invalid input', FieldError::ERROR_INVALID_VALUE );
			return null;
		}
		
		$handler = $this->FindHandler( $config->GetType( ) );
		$valid = true;
			
		foreach( $input as $index => $value ) {
			if ( !$handler->Validate( $value ) ) {
				$valid = false;
				$errors[ ] = new FieldError( $namePrefix.'['.$index.']', 'Invalid input in array', FieldError::ERROR_INVALID_VALUE );
			}
		}
		
		if ( !$valid ) { // @todo политика: массив не валиден, если хоть один элемент не валиден
			return [ ];
		}
		
		$output = [ ];
		
		foreach( $input as $index => $value ) {
			$outValue = null;
			
			if ( $itemConfig = $config->GetConfig( ) ) {
				$outValue = $this->Parse( $itemConfig, $value, $errors, $namePrefix.'['.$index.']' );
			} else { // скаляр
				$outValue = $handler->Filter( $value );
			}
			
			if ( !is_null( $outValue ) ) {
				$output[ ] = $outValue;
			}
		}
		
		return $output;
	}
	
	/**
	 * Создание префикса на основе конфига поля и префикса структуры/массива
	 */
	protected function MakeFieldPrefix( FieldConfig $config, string $namePrefix ) : string {
		return ( $namePrefix == '' ) ? $config->GetName( ) : $namePrefix.'.'.$config->GetName( );
	}
	
	/**
	 * Проверка, что во входящих данных имеются все ключи структуры
	 */
	protected function AllFieldsExists( StructConfig $config, $input, &$errors, string $namePrefix ) : bool {
		$allFound = true;
		
		foreach( $config->GetFields( ) as $fieldConfig ) {
			if ( !array_key_exists( $fieldConfig->GetName( ), $input ) ) {
				$allFound = false;
				$errors[ ] = new FieldError( $this->MakeFieldPrefix( $fieldConfig, $namePrefix ), 'Field not found', FieldError::ERROR_NOT_FOUND );
			}
		}
		
		return $allFound;
	}
	
	/**
	 * Получение обработчика типа с проверкой его наличия
	 * @throws \RuntimeException если обработчик отсутствует
	 */
	protected function FindHandler( string $type ) : TypeHandler {
		if ( !isset( $this->handlers[ $type ] ) ) {
			throw new \RuntimeException( "Type '$type' is not supported" );
		}
		
		return $this->handlers[ $type ];
	}
}
