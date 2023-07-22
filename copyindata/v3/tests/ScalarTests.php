<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\Config;

/**
 * Тесты скалярных типов данных
 */
final class ScalarTests extends BaseTests {
	/**
	 * Общий код тестов
	 */
	protected function TplScalarTest( string $type, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new Config( $type ), $input, $expected, $errorsRequired );
	}
	
	public static function stringProvider( ) : array {
		return [
			[ 'value', 'value' ], [ 42, '42' ], [ 3.14, '3.14' ], [ -42, '-42' ], [ -3.14, '-3.14' ],
			[ [ ], null, true ], [ new \stdClass, null, true ], [ null, null, true ],
			// особенности преобразования из bool в string в пыхе
			[ true, '1' ], [ false, '' ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, ?string $expected, bool $errorsRequired = false ) : void {
		$this->TplScalarTest( 'string', $input, $expected, $errorsRequired );
	}
	
	public static function boolProvider( ) : array {
		return [
			[ true, true ], [ 'true', true ], [ 'yes', true ], [ 'on', true ],
			[ false, false ], [ 'false', false ], [ 'no', false ], [ 'off', false ],
			[ 1, true ], [ 0, false ], [ ( float ) 3.14, true ], [ ( float ) 0.0, false ],
			[ 'wtf', null, true ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, ?bool $expected, bool $errorsRequired = false ) : void {
		$this->TplScalarTest( 'bool', $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ 42, 42 ], [ '42', 42 ], [ 'invalid', null, true ], [ '-42', -42 ],
			[ '123абв', null, true ], [ 117.11, 117 ], [ '117.11', 117 ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, ?int $expected, bool $errorsRequired = false ) : void {
		$this->TplScalarTest( 'int', $input, $expected, $errorsRequired );
	}
	
	public static function floatProvider( ) : array {
		return [
			[ 3.14, 3.14 ], [ '3.14', 3.14 ], [ '9.9E-5', 9.9E-5 ],
			[ 42, 42.0 ], [ '-3.14', -3.14 ], [ '123абв', null, true ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, ?float $expected, bool $errorsRequired = false ) : void {
		$this->TplScalarTest( 'float', $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			[ '8 (950) 288-56-23', '79502885623' ], [ '260557', null, true ], [ '+79502885623', '79502885623' ],
			[ '89502885623', '79502885623' ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, ?string $expected, bool $errorsRequired = false ) : void {
		$this->TplScalarTest( 'phone', $input, $expected, $errorsRequired );
	}
}
