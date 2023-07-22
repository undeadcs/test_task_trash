<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\types\StringType;
use copyindata\types\IntType;
use copyindata\types\FloatType;
use copyindata\types\BoolType;
use copyindata\types\PhoneNumberType;
use copyindata\types\StructType;

/**
 * Тесты структур
 */
final class StructTests extends BaseTests {
	public static function invalidInputProvider( ) : array {
		return [
			[ 1 ],
			[ 'invalid input' ],
			[ new \stdClass ],
			[ true ],
			[ [ 1, 2, 3 ] ],
			[ [ 'field1' => 'value', 'field' => 'value' ] ],
			// в среднем режиме берутся конкретные поля, в легком только если найдутся
			[ [ 'field1' => 'value', 'field' => 'value' ], [ 'field' => 'value' ], false, StructType::KEYS_EXISTS ]
		];
	}
	
	#[ DataProvider( 'invalidInputProvider' ) ]
	public function testInvalidInput( $input, ?array $expected = null, bool $errorsRequired = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		// в данном контексте нас не интересует тип элементов и их валидация
		$this->TplTest( new StructType( [ 'field' => new StringType ], true, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public static function stringProvider( ) : array {
		return [
			[ [ 'field' => 'value' ], 'field', [ 'field' => 'value' ] ],
			// invalid input
			[ [ 'field' => new \stdClass ], 'field', [ ], true ],
			// non-strict mode
			[ [ 'field' => new \stdClass ], 'field', [ 'field' => null ], true, false ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, string $fieldName, ?array $expected, bool $errorsRequired = false, bool $strict = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		$this->TplTest( new StructType( [ $fieldName => new StringType ], $strict, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public static function boolProvider( ) : array {
		return [
			[ [ 'field' => true		], 'field', [ 'field' => true ] ],
			[ [ 'field' => 'true'	], 'field', [ 'field' => true ] ],
			[ [ 'field' => 'yes'	], 'field', [ 'field' => true ] ],
			[ [ 'field' => 'on'		], 'field', [ 'field' => true ] ],
			[ [ 'field' => false	], 'field', [ 'field' => false ] ],
			[ [ 'field' => 'false'	], 'field', [ 'field' => false ] ],
			[ [ 'field' => 'no'		], 'field', [ 'field' => false ] ],
			[ [ 'field' => 'off'	], 'field', [ 'field' => false ] ],
			// invalid input
			[ [ 'field' => new \stdClass ], 'field', [ ], true ],
			// non-strict mode
			[ [ 'field' => new \stdClass ], 'field', [ 'field' => null ], true, false ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, string $fieldName, ?array $expected, bool $errorsRequired = false, bool $strict = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		$this->TplTest( new StructType( [ $fieldName => new BoolType ], $strict, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ [ 'field' => 3.14		], 'field', [ 'field' => 3		] ],
			[ [ 'field' => 42		], 'field', [ 'field' => 42		] ],
			[ [ 'field' => '-6379'	], 'field', [ 'field' => -6379	] ],
			// invalid input
			[ [ 'field' => '123абв' ], 'field', [ ], true ],
			// non-strict mode
			[ [ 'field' => '123абв' ], 'field', [ 'field' => null ], true, false ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, string $fieldName, ?array $expected, bool $errorsRequired = false, bool $strict = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		$this->TplTest( new StructType( [ $fieldName => new IntType ], $strict, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public static function floatProvider( ) : array {
		return [
			[ [ 'field' => 3.14		], 'field', [ 'field' => 3.14	] ],
			[ [ 'field' => 42		], 'field', [ 'field' => 42.0	] ],
			[ [ 'field' => '3.14'	], 'field', [ 'field' => 3.14	] ],
			[ [ 'field' => '9.9E-5'	], 'field', [ 'field' => 9.9E-5	] ],
			[ [ 'field' => '-3.14'	], 'field', [ 'field' => -3.14	] ],
			// invalid input
			[ [ 'field' => '123абв' ], 'field', [ ], true ],
			// non-strict mode
			[ [ 'field' => '123абв' ], 'field', [ 'field' => null ], true, false ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, string $fieldName, ?array $expected, bool $errorsRequired = false, bool $strict = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		$this->TplTest( new StructType( [ $fieldName => new FloatType ], $strict, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			[ [ 'field' => '8 (950) 288-56-23'	], 'field', [ 'field' => '79502885623' ] ],
			[ [ 'field' => '+79502885623'		], 'field', [ 'field' => '79502885623' ] ],
			[ [ 'field' => '89502885623'		], 'field', [ 'field' => '79502885623' ] ],
			// invalid input
			[ [ 'field' => '260557' ], 'field', [ ], true ],
			// non-strict mode
			[ [ 'field' => '260557' ], 'field', [ 'field' => null ], true, false ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, string $fieldName, ?array $expected, bool $errorsRequired = false, bool $strict = true, int $keysMode = StructType::KEYS_EQUAL ) : void {
		$this->TplTest( new StructType( [ $fieldName => new PhoneNumberType ], $strict, $keysMode ), $input, $expected, $errorsRequired );
	}
	
	public function testMultiFields( ) : void {
		// в строгом режиме: не допускается отсутствие или лишние
		$spec = new StructType( [
			'foo' => new IntType,
			'bar' => new StringType,
			'baz' => new PhoneNumberType,
			'invalidInt' => new IntType,
			'invalidPhone' => new PhoneNumberType
		], false );
		
		// все поля в наличии
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557' ];
		$expected = [ 'foo' => 123, 'bar' => 'asd', 'baz' => '79502885623', 'invalidInt' => null, 'invalidPhone' => null ];
		$this->TplTest( $spec, $input, $expected, true );
		
		// отсутствие
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв' ];
		$this->TplTest( $spec, $input, null, true );
		
		// лишние поля
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557', 'extraField' => 15 ];
		$this->TplTest( $spec, $input, null, true );
		
		// в среднем режиме: не допускается отсутствие, допускаются лишние
		$spec->SetKeysMode( StructType::KEYS_EXISTS );
		
		// все поля в наличии
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557' ];
		$expected = [ 'foo' => 123, 'bar' => 'asd', 'baz' => '79502885623', 'invalidInt' => null, 'invalidPhone' => null ];
		$this->TplTest( $spec, $input, $expected, true );
		
		// отсутствие
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв' ];
		$this->TplTest( $spec, $input, null, true );
		
		// лишние поля
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557', 'extraField' => 15 ];
		$expected = [ 'foo' => 123, 'bar' => 'asd', 'baz' => '79502885623', 'invalidInt' => null, 'invalidPhone' => null ];
		$this->TplTest( $spec, $input, $expected, true );
	}
}
