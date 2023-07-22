<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\types\StringType;
use copyindata\types\IntType;
use copyindata\types\FloatType;
use copyindata\types\BoolType;
use copyindata\types\PhoneNumberType;

/**
 * Тесты скалярных типов данных
 */
final class ScalarTests extends BaseTests {
	public static function stringProvider( ) : array {
		return [
			[ 'value', 'value' ],
			[ 42, '42' ],
			[ 3.14, '3.14' ],
			[ -42, '-42' ],
			[ -3.14, '-3.14' ],
			// особенности преобразования из bool в string в пыхе
			[ true, '1' ],
			[ false, '' ],
			// invalid input
			[ [ ], null, true ],
			[ new \stdClass, null, true ],
			[ null, null, true ],
			// ASCII 8 - Backspace - управляющий символ, например в терминале
			[ chr( 8 ).chr( 8 ), null, true ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, ?string $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new StringType, $input, $expected, $errorsRequired );
	}
	
	public static function boolProvider( ) : array {
		return [
			[ true, true ],
			[ 'true', true ],
			[ 'yes', true ],
			[ 'on', true ],
			[ '42', true ],
			[ '9.9E-5', true ],
			[ '3.14', true ],
			[ false, false ],
			[ 'false', false ],
			[ 'no', false ],
			[ 'off', false ],
			[ 1, true ],
			[ 0, false ],
			[ ( float ) 3.14, true ],
			[ ( float ) 0.0, false ],
			[ 'wtf', null, true ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, ?bool $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new BoolType, $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ 42, 42 ],
			[ '42', 42 ],
			[ 'invalid', null, true ],
			[ '-42', -42 ],
			[ '123абв', null, true ],
			[ 117.11, 117 ],
			[ '117.11', 117 ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, ?int $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new IntType, $input, $expected, $errorsRequired );
	}
	
	public static function floatProvider( ) : array {
		return [
			[ 3.14, 3.14 ],
			[ '3.14', 3.14 ],
			[ '9.9E-5', 9.9E-5 ],
			[ 42, 42.0 ],
			[ '-3.14', -3.14 ],
			[ '123абв', null, true ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, ?float $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new FloatType, $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			'human-readable' => [ '8 (950) 288-56-23', '79502885623' ],
			'short-number' => [ '260557', null, true ],
			'plus-7-number' => [ '+79502885623', '79502885623' ],
			'full-8-number' => [ '89502885623', '79502885623' ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, ?string $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new PhoneNumberType, $input, $expected, $errorsRequired );
	}
}
