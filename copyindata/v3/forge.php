<?php
// кузница
require_once( __DIR__.'/autoload.php' );

use copyindata\Parser;
use copyindata\handlers\IntHandler;
use copyindata\handlers\StringHandler;
use copyindata\handlers\FloatHandler;
use copyindata\handlers\PhoneNumberHandler;
use copyindata\handlers\BoolHandler;
use copyindata\handlers\ArrayHandler;
use copyindata\handlers\StructHandler;
use copyindata\StructConfig;
use copyindata\FieldConfig;
use copyindata\ArrayConfig;
use copyindata\Config;

$parser = new Parser;
$parser->RegisterType( 'int', new IntHandler );
$parser->RegisterType( 'string', new StringHandler );
$parser->RegisterType( 'float', new FloatHandler );
$parser->RegisterType( 'bool', new BoolHandler );
$parser->RegisterType( 'phone', new PhoneNumberHandler );
$parser->RegisterType( 'array', new ArrayHandler );
$parser->RegisterType( 'struct', new StructHandler );

// $spec_v1 - канула в лету
$spec_v2 = [
	'foo',
	'bar',
	[ 'indexInput' => 'baz', 'type' => 'phone' ],
	[ 'indexInput' => 'invalidValue1', 'type' => 'int' ],
	[ 'indexInput' => 'invalidValue2', 'type' => 'phone' ],
	[ 'indexInput' => 'cities', 'type' => 'array', 'config' => [ 'type' => 'string' ] ],
	[ 'indexInput' => 'coordinates', 'type' => 'struct', 'config' => [
		[ 'indexInput' => 'x', 'type' => 'float' ],
		[ 'indexInput' => 'y', 'type' => 'float' ]
	] ],
	[ 'indexInput' => 'stations', 'type' => 'array', 'config' => [
		'type' => 'struct', 'config' => [
			'name',
			'ip',
			[ 'indexInput' => 'devices', 'type' => 'array', 'config' => [
				'type' => 'struct', 'config' => [ 'serialNumber', 'mac' ]
			] ]
		]
	] ],
	[ 'indexInput' => 'calibrationMatrix', 'type' => 'array', 'config' => [
		'type' => 'array', 'config' => [ 'type' => 'float' ]
	] ]
];

$spec_v3 = new StructConfig( [
	new FieldConfig( 'foo' ),
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
	'coordinates' => [ 'x' => 43.111839, 'y' => 131.936894 ],
	'stations' => [
		[
			'name' => 'test-a',
			'ip' => '1.1.1.1',
			'devices' => [
				[ 'serialNumber' => 'SN-A1', 'mac' => 'aa11' ],
				[ 'serialNumber' => 'SN-A2' ] // ошибка
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

$json = json_encode( $input );//'{"foo": "123", "bar": "asd", "baz": "8 (950) 288-56-23", "invalidValue1": "123абв", "invalidValue2": "260557", "cities": ["Vladivostok", "Nakhodka"]}';
$input = json_decode( $json, true );
//var_dump( $input );
$errors = [ ];

/*$spec1 = new StructConfig( [ new FieldConfig( 'field', 'string' ) ] );
$input = [ 'field' => new \stdClass ];*/

/*$spec1 = new StructConfig( [
	new FieldConfig( 'vertex', 'struct', new StructConfig( [
		new FieldConfig( 'x', 'float' ),
		new FieldConfig( 'y', 'float' ),
		new FieldConfig( 'z', 'float' )
	] ) )
] );
$input = [ 'vertex' => [ 'x' => 1.1, 'y' => 2.2, 'z' => 'invalid value' ] ];*/

$output = $parser->Parse( $spec_v3, $input, $errors );
var_dump( $errors, $output );

/*var_dump(
	json_encode( 'somevalue' ),
	json_decode( '"somevalue"' ),
	json_decode( '[1,2,3]' ),
	json_decode( '1' ),
	json_decode( '1.1' )
);*/
