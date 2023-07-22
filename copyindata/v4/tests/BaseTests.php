<?php
namespace copyindata\tests;

use PHPUnit\Framework\TestCase;
use copyindata\BaseType;
use copyindata\ErrorsList;

/**
 * Общий код для тестов
 */
abstract class BaseTests extends TestCase {
	/**
	 * Общий код тестов с заданной спецификацией
	 */
	protected function TplTest( BaseType $spec, $input, $expected, bool $errorsRequired = false ) : void {
		$errors = new ErrorsList;
		$value = $spec->Filter( $input, $errors );
		
		$this->assertEquals( $expected, $value );
		
		if ( $errorsRequired ) {
			$this->assertFalse( $errors->IsEmpty( ) );
		} else {
			$this->assertTrue( $errors->IsEmpty( ) );
		}
	}
}
