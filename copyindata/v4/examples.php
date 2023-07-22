<?php
// примеры спецификаций и использования с выводом ошибок
require_once( __DIR__.'/autoload.php' );

use copyindata\BaseType;
use copyindata\BaseError;
use copyindata\types\ContainerType;
use copyindata\types\IntType;
use copyindata\types\StringType;
use copyindata\types\PhoneNumberType;
use copyindata\types\StructType;
use copyindata\types\ArrayType;
use copyindata\types\FloatType;
use copyindata\ErrorsList;

$errors = new ErrorsList;

/*$input = 'invalid value';
$spec = new IntType;
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

/*$input = [ 'field' => 'value' ];
$spec = new StructType( [ 'field' => new StringType ] );
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

/*$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв', 'invalidPhone' => '260557' ];
$spec = new StructType( [
	'foo' => new IntType,
	'bar' => new StringType,
	'baz' => new PhoneNumberType
] );
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

/*$input = [ 'field1' => 'value', 'field' => 'value' ];
$spec = new StructType( [ 'field' => new StringType ], true, StructType::KEYS_EQUAL );
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

/*$spec = new StructType( [
	'foo' => new IntType,
	'bar' => new StringType,
	'baz' => new PhoneNumberType,
	'invalidInt' => new IntType,
	'invalidPhone' => new PhoneNumberType
], false );

// все поля в наличии
$input = [ 'foo' => '123', 'bar' => 'asd', 'baz' => '8 (950) 288-56-23', 'invalidInt' => '123абв' ];
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

/*$spec = new ArrayType( new StructType( [
	'x' => new FloatType,
	'y' => new FloatType,
	'z' => new FloatType
], false ), false );
$input = [ [ 'x' => 1.1, 'y' => 2.1, 'z' => 3.1 ], [ 'x' => 11.1, 'y' => 12.1, 'z' => 13.1 ], [ 'x' => 11.1, 'y' => 12.1 ] ];
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value, $errors );*/

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
		], false, StructType::KEYS_EXISTS ), false )
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
				[ 'serialNumber' => 'SN-A2' ], // ошибка, но при KEYS_FOUND будет точно такая же, а при KEY_EQUAL отсутствовать
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
$value = $spec->Filter( $input, $errors );
var_dump( $input, $value );

/*$input = [ 'one', new \stdClass, 'two', new \stdClass, 'three', 'four' ];
$spec = new ArrayType( new StringType );
$value = $spec->Filter( $input, $errors );*/

$phpFieldFormat = '->%s';
$jsFieldFormat = '.%s';

foreach( $errors as $error ) {
	$className = GetClassName( $error->GetType( ) );
	$fullTypePath = GetTypePath( $error->GetType( ) );
	
	echo 'input'.str_pad( GetNestedInfo( $error, $jsFieldFormat ), 30 ).' ('.$error->GetCode( ).') '.str_pad( $error->GetMessage( ), 30 ).' '.$fullTypePath."\n";
}

function GetClassName( BaseType $type ) : string {
	$className = get_class( $type );
	$className = explode( '\\', $className );
	$className = array_pop( $className );
	
	return $className;
}

function GetTypePath( BaseType $type ) : string {
	$tmp = [ GetClassName( $type ) ];
	
	while( $type ) {
		if ( $parentType = $type->GetParentType( ) ) {
			$tmp[ ] = GetClassName( $parentType );
		}
		
		$type = $type->GetParentType( );
	}
	
	$tmp = array_reverse( $tmp );
	
	return join( ' / ', $tmp );
}

/**
 * Пример сборки вложенности ошибки
 * преобразуется в полный путь переменной
 * символ поля для PHP: ->
 * символ поля для JS: .
 */
function GetNestedInfo( BaseError $error, string $fieldFormat = '->%s' ) : string {
	$tmp = [ ];
	$infos = $error->GetNestedInfos( );
	$infos = array_reverse( $infos );
	
	foreach( $infos as $info ) {
		if ( $info->GetType( ) instanceof ArrayType ) {
			$tmp[ ] = '['.$info->GetIndex( ).']';
		} else if ( $info->GetType( ) instanceof StructType ) {
			$tmp[ ] = sprintf( $fieldFormat, $info->GetIndex( ) );
		}
	}
	
	return join( '', $tmp );
}
