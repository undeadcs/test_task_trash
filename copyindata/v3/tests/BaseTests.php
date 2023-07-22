<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use copyindata\Parser;
use copyindata\handlers\IntHandler;
use copyindata\handlers\StringHandler;
use copyindata\handlers\FloatHandler;
use copyindata\handlers\BoolHandler;
use copyindata\handlers\PhoneNumberHandler;
use copyindata\handlers\ArrayHandler;
use copyindata\handlers\StructHandler;
use copyindata\Config;

/**
 * Общий код для тестов
 */
abstract class BaseTests extends TestCase {
	protected function MakeParser( ) : Parser {
		$parser = new Parser;
		$parser->RegisterType( 'int', new IntHandler );
		$parser->RegisterType( 'string', new StringHandler );
		$parser->RegisterType( 'float', new FloatHandler );
		$parser->RegisterType( 'bool', new BoolHandler );
		$parser->RegisterType( 'phone', new PhoneNumberHandler );
		$parser->RegisterType( 'array', new ArrayHandler );
		$parser->RegisterType( 'struct', new StructHandler );
		
		return $parser;
	}
	
	/**
	 * Общий код тестов с заданной спецификацией
	 */
	protected function TplTest( Config $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$parser = $this->MakeParser( );
		$errors = [ ];
		$value = $parser->Parse( $spec, $input, $errors );
		$this->assertEquals( $expected, $value );
		
		if ( $errorsRequired ) {
			$this->assertNotEmpty( $errors );
		} else {
			$this->assertEmpty( $errors );
		}
	}
}
