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
use copyindata\types\StructType;

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
			new ArrayType( new ArrayType( new FloatType ) ),
			$calibrationMatrix,
			$calibrationMatrix
		] ];
	}
	
	#[ DataProvider( 'arrayOfArrayProvider' ) ]
	public function testArrayOfArray( ArrayType $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function arrayOfStructProvider( ) : array {
		return [ [
			new ArrayType( new StructType( [
				'x' => new FloatType,
				'y' => new FloatType,
				'z' => new FloatType
			], false ), false ), // если у массива строгий режим проверки, то структура не валидна, следовательно весь массив не валиден
			[ [ 'x' => 1.1, 'y' => 2.1, 'z' => 3.1 ], [ 'x' => 11.1, 'y' => 12.1, 'z' => 13.1 ], [ 'x' => 11.1, 'y' => 12.1 ] ],
			[ [ 'x' => 1.1, 'y' => 2.1, 'z' => 3.1 ], [ 'x' => 11.1, 'y' => 12.1, 'z' => 13.1 ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'arrayOfStructProvider' ) ]
	public function testArrayOfStruct( ArrayType $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function structWithArrayProvider( ) : array {
		return [ [
			new StructType( [
				'x' => new ArrayType( new FloatType ),
				'y' => new ArrayType( new FloatType ),
				'z' => new ArrayType( new FloatType )
			], false ),
			[ 'x' => [ 1.1, 2.1, 3.1 ], 'y' => [ 11.1, 'invalid value' ], 'z' => 'invalid value' ],
			[ 'x' => [ 1.1, 2.1, 3.1 ], 'y' => [ ], 'z' => null ],
			true
		] ];
	}
	
	#[ DataProvider( 'structWithArrayProvider' ) ]
	public function testStructWithArray( StructType $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public static function structWithStructProvider( ) : array {
		return [ [
			new StructType( [
				'vertex' => new StructType( [
					'x' => new FloatType,
					'y' => new FloatType,
					'z' => new FloatType
				], false )
			] ),
			[ 'vertex' => [ 'x' => 1.1, 'y' => 2.2, 'z' => 'invalid value' ] ],
			[ 'vertex' => [ 'x' => 1.1, 'y' => 2.2, 'z' => null ] ],
			true
		] ];
	}
	
	#[ DataProvider( 'structWithStructProvider' ) ]
	public function testStructWithStruct( StructType $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$this->TplTest( $spec, $input, $expected, $errorsRequired );
	}
	
	public function testDeepSpecification( ) : void {
		$spec = new StructType( [
			'foo' => new IntType,
			'bar' => new StringType,
			'baz' => new PhoneNumberType,
			'invalidValue1' => new IntType,
			'invalidValue2' => new PhoneNumberType,
			'cities' => new ArrayType( new StringType ),
			'coordinates' => new StructType( [ 'x' => new FloatType, 'y' => new FloatType ], true, StructType::KEYS_EXISTS ),
			'stations' => new ArrayType( new StructType( [
				'name' => new StringType,
				'ip' => new StringType,
				'devices' => new ArrayType( new StructType( [
					'serialNumber' => new StringType,
					'mac' => new StringType
				], false, StructType::KEYS_FOUND ), false )
			] ) ),
			'calibrationMatrix' => new ArrayType( new ArrayType( new FloatType ) )
		], false );
		
		$input = [
			'foo' => '123',
			'bar' => 'asd',
			'baz' => '8 (950) 288-56-23',
			'invalidValue1' => '123абв', // ошибка, в результате null
			'invalidValue2' => '260557', // ошибка, в результате null
			'cities' => [ 'Vladivostok', 'Nakhodka' ],
			'coordinates' => [ 'x' => 43.111839, 'y' => 131.936894, 'z' => 'invalid value' ],
			'stations' => [
				[
					'name' => 'test-a',
					'ip' => '1.1.1.1',
					'devices' => [
						[ 'serialNumber' => 'SN-A1', 'mac' => 'aa11' ],
						[ 'serialNumber' => 'SN-A2' ], // ошибка
						[ 'serialNumber' => 'SN-A3', 'mac' => new \stdClass ] // ошибка, в результате null
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
		$expected[ 'invalidValue1' ] = null;
		$expected[ 'invalidValue2' ] = null;
		$expected[ 'foo' ] = ( int ) $expected[ 'foo' ];
		$expected[ 'baz' ] = '79502885623';
		$expected[ 'stations' ][ 0 ][ 'devices' ][ 2 ][ 'mac' ] = null;
		unset(
			$expected[ 'coordinates' ][ 'z' ]
		);
		
		$this->TplTest( $spec, $input, $expected, true );
	}
}
