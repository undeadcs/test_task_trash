<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\ArrayConfig;

/**
 * Тесты массивов
 */
final class ArrayTests extends BaseTests {
	/**
	 * Общий код тестов
	 */
	protected function TplArrayTest( string $type, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new ArrayConfig( $type ), $input, $expected, $errorsRequired );
	}
	
	public static function stringProvider( ) : array {
		return [
			[ [ 'Vladivostok', 'Nakhodka' ], [ 'Vladivostok', 'Nakhodka' ] ],
			// invalid input
			[ [ '1', new \stdClass, '3' ], [ ], true ], [ 'invalid input', null, true ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplArrayTest( 'string', $input, $expected, $errorsRequired );
	}
	
	public static function boolProvider( ) : array {
		return [
			[ [ true, 'true', 'yes', 'on' ], [ true, true, true, true ] ],
			[ [ false, 'false', 'no', 'off' ], [ false, false, false, false ] ],
			[ [ true, false, 'asfdasdf' ], [ ], true ],
			// invalid input
			[ true, null, true ], [ false, null, true ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplArrayTest( 'bool', $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ [ 3.14, 42, '-6379' ], [ 3, 42, -6379 ] ],
			// invalid input
			[ [ 11, '123абв', 22 ], [ ], true ], [ 1, null, true ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplArrayTest( 'int', $input, $expected, $errorsRequired );
	}
	
	public static function floatProvider( ) : array {
		return [
			[  [ 3.14, 42, '3.14', '9.9E-5', '-3.14' ], [ 3.14, 42.0, 3.14, 9.9E-5, -3.14 ] ],
			// invalid input
			[ [ '123абв' ], [ ], true ], [ 3.14, null, true ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplArrayTest( 'float', $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			[ [ '8 (950) 288-56-23', '+79502885623', '89502885623' ], [ '79502885623', '79502885623', '79502885623' ] ],
			// invalid input
			[ [ '+79502885623', '260557' ], [ ], true ], [ '8 (950) 288-56-23', null, true ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplArrayTest( 'phone', $input, $expected, $errorsRequired );
	}
}
