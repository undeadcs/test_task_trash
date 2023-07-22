<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use copyindata\Config;
use copyindata\ArrayConfig;
use copyindata\StructConfig;
use copyindata\FieldConfig;

/**
 * Тесты вложенных данных
 */
final class NestedTests extends BaseTests {
	public static function arrayOfArrayProvider( ) : array {
		$calibrationMatrix = [
			[ 30.9677419354839,	0.0,				-0.0685483870967742	],
			[ 0.0,				40.4210526315789,	-0.388157894736842	],
			[ 0.000000,			0.000000,			1.000000			]
		];
		
		return [ [
			new ArrayConfig( 'array', new ArrayConfig( 'float' ) ),
			$calibrationMatrix,
			$calibrationMatrix
		], [
			new ArrayConfig( 'array', new ArrayConfig( 'bool' ) ),
			[ [ true, 'on', 'yes' ], [ 'wtf', false ] ],
			[ [ true, true, true ], [ ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'arrayOfArrayProvider' ) ]
	public function testArrayOfArray( Config $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function arrayOfStructProvider( ) : array {
		return [ [
			new ArrayConfig( 'struct', new StructConfig( [
				new FieldConfig( 'x', 'float' ), new FieldConfig( 'y', 'float' ), new FieldConfig( 'z', 'float' )
			] ) ),
			[ [ 'x' => 1.1, 'y' => 2.1, 'z' => 3.1 ], [ 'x' => 11.1, 'y' => 12.1, 'z' => 13.1 ], [ 'x' => 11.1, 'y' => 12.1 ] ],
			[ [ 'x' => 1.1, 'y' => 2.1, 'z' => 3.1 ], [ 'x' => 11.1, 'y' => 12.1, 'z' => 13.1 ], [ ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'arrayOfStructProvider' ) ]
	public function testArrayOfStruct( Config $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function structWithArrayProvider( ) : array {
		return [ [
			new StructConfig( [
				new FieldConfig( 'x', 'array', new ArrayConfig( 'float' ) ),
				new FieldConfig( 'y', 'array', new ArrayConfig( 'float' ) ),
				new FieldConfig( 'z', 'array', new ArrayConfig( 'float' ) )
			] ),
			[ 'x' => [ 1.1, 2.1, 3.1 ], 'y' => [ 11.1, 'invalid value' ], 'z' => 'invalid value' ],
			[ 'x' => [ 1.1, 2.1, 3.1 ], 'y' => [ ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'structWithArrayProvider' ) ]
	public function testStructWithArray( Config $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function structWithStructProvider( ) : array {
		return [ [
			new StructConfig( [
				new FieldConfig( 'vertex', 'struct', new StructConfig( [
					new FieldConfig( 'x', 'float' ),
					new FieldConfig( 'y', 'float' ),
					new FieldConfig( 'z', 'float' )
				] ) )
			] ),
			[ 'vertex' => [ 'x' => 1.1, 'y' => 2.2, 'z' => 'invalid value' ] ],
			[ 'vertex' => [ 'x' => 1.1, 'y' => 2.2 ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'structWithStructProvider' ) ]
	public function testStructWithStruct( Config $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public function testDeepConfig( ) : void {
		$spec = new StructConfig( [
			new FieldConfig( 'foo', 'int' ),
			new FieldConfig( 'bar' ),
			new FieldConfig( 'baz', 'phone' ),
			new FieldConfig( 'invalidValue1', 'int' ),
			new FieldConfig( 'invalidValue2', 'phone' ),
			new FieldConfig( 'cities', 'array', new ArrayConfig( 'string' ) ),
			new FieldConfig( 'coordinates', 'struct', new StructConfig(
				[ new FieldConfig( 'x', 'float' ), new FieldConfig( 'y', 'float' ) ]
			) ),
			new FieldConfig( 'stations', 'array', new ArrayConfig( 'struct', new StructConfig( [
				new FieldConfig( 'name' ),
				new FieldConfig( 'ip' ),
				new FieldConfig( 'devices', 'array', new ArrayConfig( 'struct', new StructConfig( [
					new FieldConfig( 'serialNumber' ),
					new FieldConfig( 'mac' )
				] ) ) )
			] ) ) ),
			new FieldConfig( 'calibrationMatrix', 'array', new ArrayConfig( 'array', new ArrayConfig( 'float' ) ) )
		] );
		
		$input = [
			'foo' => '123',
			'bar' => 'asd',
			'baz' => '8 (950) 288-56-23',
			'invalidValue1' => '123абв', // ошибка
			'invalidValue2' => '260557', // ошибка
			'cities' => [ 'Vladivostok', 'Nakhodka' ],
			'coordinates' => [ 'x' => 43.111839, 'y' => 131.936894, 'z' => 'invalid value' ],
			'stations' => [
				[
					'name' => 'test-a',
					'ip' => '1.1.1.1',
					'devices' => [
						[ 'serialNumber' => 'SN-A1', 'mac' => 'aa11' ],
						[ 'serialNumber' => 'SN-A2' ], // ошибка
						[ 'serialNumber' => 'SN-A3', 'mac' => new \stdClass ]
					]
				], [
					'name' => 'test-b',
					'ip' => '2.2.2.2',
					'devices' => [
						[ 'serialNumber' => 'SN-B1', 'mac' => 'bb11' ]
					]
				]
			],
			'calibrationMatrix' => [
				[ 30.9677419354839,	0.0,				-0.0685483870967742	],
				[ 0.0,				40.4210526315789,	-0.388157894736842	],
				[ 0.000000,			0.000000,			1.000000			]
			]
		];
		$expected = $input;
		$expected[ 'foo' ] = ( int ) $expected[ 'foo' ];
		$expected[ 'baz' ] = '79502885623';
		$expected[ 'stations' ][ 0 ][ 'devices' ][ 1 ] = [ ]; // не валиден состав входящих данных, но валиден формат
		unset(
			$expected[ 'invalidValue1' ],
			$expected[ 'invalidValue2' ],
			$expected[ 'coordinates' ][ 'z' ],
			$expected[ 'stations' ][ 0 ][ 'devices' ][ 2 ][ 'mac' ] // не валидное поле в результат не попадает
		);
		
		$this->TplTest( $spec, $input, $expected, true );
	}
}
