<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\types\StringType;
use copyindata\types\IntType;
use copyindata\types\FloatType;
use copyindata\types\BoolType;
use copyindata\types\PhoneNumberType;
use copyindata\types\ArrayType;

/**
 * Тесты массивов
 */
final class ArrayTests extends BaseTests {
	public static function invalidInputProvider( ) : array {
		return [
			[ 1 ],
			[ 'invalid input' ],
			[ new \stdClass ],
			[ true ],
			[ [ 'i' => 1, 'j' => 2, 'k' => 3 ] ]
		];
	}
	
	#[ DataProvider( 'invalidInputProvider' ) ]
	public function testInvalidInput( $input ) : void {
		// в данном контексте нас не интересует тип элементов
		$this->TplTest( new ArrayType( new StringType, true ), $input, null, true );
		$this->TplTest( new ArrayType( new StringType, false ), $input, null, true );
	}
	
	public static function stringProvider( ) : array {
		return [
			[ [ 'Vladivostok', 'Nakhodka' ], [ 'Vladivostok', 'Nakhodka' ] ],
			// invalid input
			[ [ '1', new \stdClass, '3' ], [ ], true ],
			// non-strict mode
			[ [ '1', new \stdClass, '3', chr( 8 ).chr( 8 ), '42' ], [ '1', '3', '42' ], true, false ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, ?array $expected, bool $errorsRequired = false, bool $strict = true ) : void {
		$this->TplTest( new ArrayType( new StringType, $strict ), $input, $expected, $errorsRequired );
	}
	
	public static function boolProvider( ) : array {
		return [
			[ [ true, 'true', 'yes', 'on' ], [ true, true, true, true ] ],
			[ [ false, 'false', 'no', 'off' ], [ false, false, false, false ] ],
			[ [ true, false, 'asfdasdf' ], [ ], true ],
			// non-strict mode
			[ [ true, false, 'asfdasdf' ], [ true, false ], true, false ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, ?array $expected, bool $errorsRequired = false, bool $strict = true ) : void {
		$this->TplTest( new ArrayType( new BoolType, $strict ), $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ [ 3.14, 42, '-6379' ], [ 3, 42, -6379 ] ],
			// invalid input
			[ [ 11, '123абв', 22 ], [ ], true ], [ 1, null, true ],
			// non-strict mode
			[ [ 11, '123абв', 22 ], [ 11, 22 ], true, false ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, ?array $expected, bool $errorsRequired = false, bool $strict = true ) : void {
		$this->TplTest( new ArrayType( new IntType, $strict ), $input, $expected, $errorsRequired );
	}
	
	public static function floatProvider( ) : array {
		return [
			[  [ 3.14, 42, '3.14', '9.9E-5', '-3.14' ], [ 3.14, 42.0, 3.14, 9.9E-5, -3.14 ] ],
			// invalid input
			[ [ 3.14, '123абв', 42.42 ], [ ], true ], [ 3.14, null, true ],
			// non-strict mode
			[ [ 3.14, '123абв', 42.42 ], [ 3.14, 42.42 ], true, false ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, ?array $expected, bool $errorsRequired = false, bool $strict = true ) : void {
		$this->TplTest( new ArrayType( new FloatType, $strict ), $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			[ [ '8 (950) 288-56-23', '+79502885623', '89502885623' ], [ '79502885623', '79502885623', '79502885623' ] ],
			// invalid input
			[ [ '+79502885623', '260557' ], [ ], true ], [ '8 (950) 288-56-23', null, true ],
			// non-strict mode
			[ [ '+79502885623', '260557' ], [ '79502885623' ], true, false ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, ?array $expected, bool $errorsRequired = false, bool $strict = true ) : void {
		$this->TplTest( new ArrayType( new PhoneNumberType, $strict ), $input, $expected, $errorsRequired );
	}
}
