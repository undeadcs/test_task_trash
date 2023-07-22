<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\StructConfig;
use copyindata\FieldConfig;

/**
 * Тесты структур
 */
final class StructTests extends BaseTests {
	/**
	 * Общий код тестов
	 */
	protected function TplStructTest( string $type, string $fieldName, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( new StructConfig( [ new FieldConfig( $fieldName, $type ) ] ), $input, $expected, $errorsRequired );
	}
	
	public static function stringProvider( ) : array {
		return [
			[ [ 'field' => 'value' ], 'field', [ 'field' => 'value' ] ],
			[ [ 'field' => new \stdClass ], 'field', [ ], true ]
		];
	}
	
	#[ DataProvider( 'stringProvider' ) ]
	public function testString( $input, string $fieldName, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplStructTest( 'string', $fieldName, $input, $expected, $errorsRequired );
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
			[ [ 'field' => null ], 'field', [ ], true ],
			[ true, 'field', null, true ]
		];
	}
	
	#[ DataProvider( 'boolProvider' ) ]
	public function testBool( $input, string $fieldName, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplStructTest( 'bool', $fieldName, $input, $expected, $errorsRequired );
	}
	
	public static function intProvider( ) : array {
		return [
			[ [ 'field' => 3.14		], 'field', [ 'field' => 3		] ],
			[ [ 'field' => 42		], 'field', [ 'field' => 42		] ],
			[ [ 'field' => '-6379'	], 'field', [ 'field' => -6379	] ],
			// invalid input
			[ [ 'field' => '123абв' ], 'field', [ ], true ],
			[ [ 11, '123абв', 22 ], 'field', null, true ],
			[ 1, 'field', null, true ]
		];
	}
	
	#[ DataProvider( 'intProvider' ) ]
	public function testInt( $input, string $fieldName, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplStructTest( 'int', $fieldName, $input, $expected, $errorsRequired );
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
			[ [ 11.1, '123абв', 22.2 ], 'field', null, true ],
			[ 3.14, 'field', null, true ]
		];
	}
	
	#[ DataProvider( 'floatProvider' ) ]
	public function testFloat( $input, string $fieldName, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplStructTest( 'float', $fieldName, $input, $expected, $errorsRequired );
	}
	
	public static function phoneProvider( ) : array {
		return [
			[ [ 'field' => '8 (950) 288-56-23'	], 'field', [ 'field' => '79502885623' ] ],
			[ [ 'field' => '+79502885623'		], 'field', [ 'field' => '79502885623' ] ],
			[ [ 'field' => '89502885623'		], 'field', [ 'field' => '79502885623' ] ],
			// invalid input
			[ [ 'field' => '260557' ], 'field', [ ], true ],
			[ '8 (950) 288-56-23', 'field', null, true ]
		];
	}
	
	#[ DataProvider( 'phoneProvider' ) ]
	public function testPhone( $input, string $fieldName, ?array $expected, bool $errorsRequired = false ) : void {
		$this->TplStructTest( 'phone', $fieldName, $input, $expected, $errorsRequired );
	}
	
	public function testMultiFields( ) : void {
		$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557' ];
		$spec = new StructConfig( [
			new FieldConfig( 'foo', 'int' ),
			new FieldConfig( 'bar' ),
			new FieldConfig( 'baz', 'phone' ),
			new FieldConfig( 'invalidInt', 'int' ),
			new FieldConfig( 'invalidPhone', 'phone' )
		] );
		$expected = [ 'foo' => 123, 'bar' => 'asd', 'baz' => '79502885623' ];
		$this->TplTest( $spec, $input, $expected, true );
	}
}
