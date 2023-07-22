<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\BaseType;
use copyindata\types\ScalarType;
use copyindata\types\StringType;
use copyindata\types\IntType;
use copyindata\types\FloatType;
use copyindata\types\BoolType;
use copyindata\types\PhoneNumberType;
use copyindata\types\ArrayType;
use copyindata\types\StructType;
use copyindata\BaseError;
use copyindata\ErrorsList;
use copyindata\NestedInfo;

/**
 * Тесты сбора ошибок
 */
final class ErrorsTests extends TestCase {
	public static function intvalidInputProvider( ) : array {
		return [
			[ new StringType,		'Invalid string'	],
			[ new IntType,			'Invalid int'		],
			[ new FloatType,		'Invalid float'		],
			[ new BoolType,			'Invalid bool'		],
			[ new PhoneNumberType,	'Invalid phone'		],
			// внутренняя структура тут не волнует
			[ new ArrayType( new IntType ), 'Invalid array' ],
			[ new StructType( [ 'x' => new IntType ] ), 'Invalid struct' ]
		];
	}
	
	#[ DataProvider( 'intvalidInputProvider' ) ]
	public function testIntvalidInput( BaseType $type, string $message ) : void {
		$input = new \stdClass; // значение, которое гарантированно не валидно для скаляров
		$errorsExpected = [ new BaseError( $type, $message, ErrorsList::ERROR_INVALID_INPUT ) ];
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
	}
	
	public static function arraysProvider( ) : array {
		return [ [
			new ArrayType( new StringType ),
			'Invalid string',
			[ 'one', new \stdClass, 'two', new \stdClass, 'three', 'four' ]
		], [
			new ArrayType( new IntType ),
			'Invalid int',
			[ 1, new \stdClass, 2, new \stdClass, 3, 4 ]
		], [
			new ArrayType( new FloatType ),
			'Invalid float',
			[ 1.1, new \stdClass, 2.2, new \stdClass, 3.3, 4.4 ]
		], [
			new ArrayType( new BoolType ),
			'Invalid bool',
			[ true, new \stdClass, true, new \stdClass, true, true ]
		], [
			new ArrayType( new PhoneNumberType ),
			'Invalid phone',
			[ '8 (950) 288-56-23', new \stdClass, '8 (950) 288-56-23', new \stdClass, '8 (950) 288-56-23', '8 (950) 288-56-23' ]
		] ];
	}
	
	#[ DataProvider( 'arraysProvider' ) ]
	public function testArrays( ArrayType $type, string $message, $input ) : void {
		$errorsExpected = [ ];
		
		// должны быть ошибки на 1 и 3 индекс
		$error = new BaseError( $type->GetItemsType( ), $message, ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 1, $type ) );
		$errorsExpected[ ] = $error;
		
		$error = new BaseError( $type->GetItemsType( ), $message, ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 3, $type ) );
		$errorsExpected[ ] = $error;
		
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
	}
	
	public static function structsProvider( ) : array {
		return [ [
			new StructType( [
				'f1' => new StringType, 'f2' => new StringType,
				'f3' => new StringType, 'f4' => new StringType,
				'f5' => new StringType, 'f6' => new StringType
			] ),
			'Invalid string',
			[ 'f1' => 'one', 'f2' => new \stdClass, 'f3' => 'two', 'f4' => new \stdClass, 'f5' => 'three', 'f6' => 'four' ]
		], [
			new StructType( [
				'f1' => new IntType, 'f2' => new IntType,
				'f3' => new IntType, 'f4' => new IntType,
				'f5' => new IntType, 'f6' => new IntType
			] ),
			'Invalid int',
			[ 'f1' => 1, 'f2' => new \stdClass, 'f3' => 2, 'f4' => new \stdClass, 'f5' => 3, 'f6' => 4 ]
		], [
			new StructType( [
				'f1' => new FloatType, 'f2' => new FloatType,
				'f3' => new FloatType, 'f4' => new FloatType,
				'f5' => new FloatType, 'f6' => new FloatType
			] ),
			'Invalid float',
			[ 'f1' => 1.1, 'f2' => new \stdClass, 'f3' => 2.2, 'f4' => new \stdClass, 'f5' => 3.3, 'f6' => 4.4 ]
		], [
			new StructType( [
				'f1' => new BoolType, 'f2' => new BoolType,
				'f3' => new BoolType, 'f4' => new BoolType,
				'f5' => new BoolType, 'f6' => new BoolType
			] ),
			'Invalid bool',
			[ 'f1' => true, 'f2' => new \stdClass, 'f3' => true, 'f4' => new \stdClass, 'f5' => true, 'f6' => true ]
		], [
			new StructType( [
				'f1' => new PhoneNumberType, 'f2' => new PhoneNumberType,
				'f3' => new PhoneNumberType, 'f4' => new PhoneNumberType,
				'f5' => new PhoneNumberType, 'f6' => new PhoneNumberType
			] ),
			'Invalid phone',
			[
				'f1' => '8 (950) 288-56-23', 'f2' => new \stdClass, 'f3' => '8 (950) 288-56-23',
				'f4' => new \stdClass, 'f5' => '8 (950) 288-56-23', 'f6' => '8 (950) 288-56-23'
			]
		] ];
	}
	
	#[ DataProvider( 'structsProvider' ) ]
	public function testStructs( StructType $type, string $message, $input ) : void {
		$errorsExpected = [ ];
		
		// должны быть ошибки на f2 и f4 ключ
		$error = new BaseError( $type->FindField( 'f2' ), $message, ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 'f2', $type ) );
		$errorsExpected[ ] = $error;
		
		$error = new BaseError( $type->FindField( 'f4' ), $message, ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 'f4', $type ) );
		$errorsExpected[ ] = $error;
		
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
	}
	
	public function testStructFields( ) : void {
		// тип данных нас тут не волнует
		// строгий режим: KEYS_EQUAL
		// лишние ключи
		$type = new StructType( [ 'f1' => new IntType, 'f2' => new IntType, 'f3' => new IntType ], true, StructType::KEYS_EQUAL );
		$input = [ 'f1' => 1, 'f2' => 3, 'f3' => 4, 'f4' => 5 ];
		$errorsExpected = [ new BaseError( $type, 'Invalid struct', ErrorsList::ERROR_INVALID_INPUT ) ];
		$errors = new ErrorsList;
		$value = $type->Filter( $input, $errors );
		
		$this->assertNull( $value );
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// не хватает ключей
		$type = new StructType( [ 'f1' => new IntType, 'f2' => new IntType, 'f3' => new IntType ], true, StructType::KEYS_EQUAL );
		$input = [ 'f1' => 1, 'f2' => 3 ];
		$errorsExpected = [ new BaseError( $type, 'Invalid struct', ErrorsList::ERROR_INVALID_INPUT ) ];
		$errors = new ErrorsList;
		$value = $type->Filter( $input, $errors );
		
		$this->assertNull( $value );
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// средний режим: KEYS_EXISTS
		// не хватает ключей, при этом лишние ни на что не влияют
		$type = new StructType( [ 'f1' => new IntType, 'f2' => new IntType, 'f3' => new IntType ], true, StructType::KEYS_EXISTS );
		$input = [ 'f1' => 1, 'f2' => 3, 'f5' => 4, 'f6' => 5 ];
		$errorsExpected = [ new BaseError( $type, 'Field "f3" not found', ErrorsList::ERROR_FIELD_NOT_FOUND ) ];
		$errors = new ErrorsList;
		$value = $type->Filter( $input, $errors );
		
		$this->assertNull( $value );
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// легкий режим: KEYS_FOUND
		$type = new StructType( [ 'f1' => new IntType, 'f2' => new IntType, 'f3' => new IntType ], true, StructType::KEYS_FOUND );
		$input = [ 'f1' => 1, 'f2' => 3, 'f5' => 4, 'f6' => 5 ];
		$errors = new ErrorsList;
		$value = $type->Filter( $input, $errors );
		
		$this->assertNotNull( $value );
		$this->assertTrue( $errors->IsEmpty( ) );
	}
	
	public function testNested( ) : void {
		// массив массивов
		$type = new ArrayType( new ArrayType( new IntType ) );
		$input = [ [ 1, 2, 3 ], [ 7, 8, 9 ], [ 4, new \stdClass, 6 ], [ 11, 12, 13 ] ];
		$errorsExpected = [ ];
		
		$error = new BaseError( $type->GetItemsType( )->GetItemsType( ), 'Invalid int', ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 1, $type->GetItemsType( ) ) );
		$error->AddNestedInfo( new NestedInfo( 2, $type ) );
		
		$errorsExpected[ ] = $error;
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// массив структур
		$type = new ArrayType( new StructType( [ 'x' => new IntType, 'y' => new IntType ] ) );
		$input = [ [ 'x' => 1, 'y' => 2 ], [ 'x' => 1, 'y' => 'invalid' ], [ 'x' => 1, 'y' => 2 ] ];
		$errorsExpected = [ ];
		
		$error = new BaseError( $type->GetItemsType( )->FindField( 'y' ), 'Invalid int', ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 'y', $type->GetItemsType( ) ) );
		$error->AddNestedInfo( new NestedInfo( 1, $type ) );
		
		$errorsExpected[ ] = $error;
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// структура с массивами
		$type = new StructType( [ 'xs' => new ArrayType( new IntType ), 'ys' => new ArrayType( new IntType ) ] );
		$input = [ 'xs' => [ 1, 1, 1, 1 ], 'ys' => [ 2, 2, 'invalid', 2 ] ];
		$errorsExpected = [ ];
		
		$error = new BaseError( $type->FindField( 'ys' )->GetItemsType( ), 'Invalid int', ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 2, $type->FindField( 'ys' ) ) );
		$error->AddNestedInfo( new NestedInfo( 'ys', $type ) );
		
		$errorsExpected[ ] = $error;
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
		
		// многоуровневая вложенность
		$type = new StructType( [
			'stations' => new ArrayType( new StructType( [
				'name' => new StringType,
				'ip' => new StringType,
				'devices' => new ArrayType( new StructType( [
					'serialNumber' => new StringType,
					'mac' => new StringType
				], false, StructType::KEYS_EXISTS ), false )
			] ) )
		], false );
		$input = [
			'stations' => [
				[
					'name' => 'test-a',
					'ip' => '1.1.1.1',
					'devices' => [
						[ 'serialNumber' => 'SN-A1', 'mac' => 'aa11' ],
						[ 'serialNumber' => 'SN-A2' ],
						[ 'serialNumber' => 'SN-A3', 'mac' => new \stdClass ]
					]
				], [
					'name' => 'test-b',
					'ip' => '2.2.2.2',
					'devices' => [
						[ 'serialNumber' => 'SN-B1', 'mac' => 'bb11' ]
					]
				]
			]
		];
		$errorsExpected = [ ];

		$error = new BaseError( $type->FindField( 'stations' )->GetItemsType( )->FindField( 'devices' )->GetItemsType( ), 'Field "mac" not found', ErrorsList::ERROR_FIELD_NOT_FOUND );
		$error->AddNestedInfo( new NestedInfo( 1, $type->FindField( 'stations' )->GetItemsType( )->FindField( 'devices' ) ) );
		$error->AddNestedInfo( new NestedInfo( 'devices', $type->FindField( 'stations' )->GetItemsType( ) ) );
		$error->AddNestedInfo( new NestedInfo( 0, $type->FindField( 'stations' ) ) );
		$error->AddNestedInfo( new NestedInfo( 'stations', $type ) );
		$errorsExpected[ ] = $error;
		
		$error = new BaseError( $type->FindField( 'stations' )->GetItemsType( )->FindField( 'devices' )->GetItemsType( )->FindField( 'mac' ), 'Invalid string', ErrorsList::ERROR_INVALID_INPUT );
		$error->AddNestedInfo( new NestedInfo( 'mac', $type->FindField( 'stations' )->GetItemsType( )->FindField( 'devices' )->GetItemsType( ) ) );
		$error->AddNestedInfo( new NestedInfo( 2, $type->FindField( 'stations' )->GetItemsType( )->FindField( 'devices' ) ) );
		$error->AddNestedInfo( new NestedInfo( 'devices', $type->FindField( 'stations' )->GetItemsType( ) ) );
		$error->AddNestedInfo( new NestedInfo( 0, $type->FindField( 'stations' ) ) );
		$error->AddNestedInfo( new NestedInfo( 'stations', $type ) );
		$errorsExpected[ ] = $error;
		
		$errors = new ErrorsList;
		$type->Filter( $input, $errors );
		
		$this->assertFalse( $errors->IsEmpty( ) );
		$this->assertEquals( $errorsExpected, $errors->GetAll( ) );
	}
}
