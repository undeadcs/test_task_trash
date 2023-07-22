<?php
namespace lib;

/**
 * Добавляет слэш в конец строки, если надо
 * 
 * @param string $value строка для изменения
 * 
 * @return string
 */
function AppendTrailingSlash( $value ) {
	return preg_match( '/\/$/u', $value ) ? $value : $value.'/' ;
}

/**
 * Регистрирует функцию автозагрузки для дерева каталога
 * 
 * @param string $root корень пространства имен
 * @param string $baseDir корень в файловой системе
 * @param bool $checkFileExists проверять ли наличие файла
 * 
 * @return void
 */
function AutoloadTree( $root, $baseDir, $checkFileExists = false ) {
	spl_autoload_register( function( $class ) use( $root, $baseDir, $checkFileExists ) {
		if ( preg_match( '/^'.preg_quote( $root, '/' ).'/', $class ) ) {
			$class = preg_replace( '/^'.preg_quote( $root, '/' ).'/', '', $class );
			$parts = preg_split( '/\\\/', $class, -1, PREG_SPLIT_NO_EMPTY );
			$filename = AppendTrailingSlash( $baseDir ).join( '/', $parts ).'.php';
			
			if ( $checkFileExists && !file_exists( $filename ) ) {
				return;
			}
			
			require_once( $filename );
		}
	} );
}

/**
 * Проверка, что имя builtin типа это имя скалярного типа
 */
function IsScalarTypeName( string $typeName ) : bool {
	return in_array( $typeName, [ 'bool', 'int', 'float', 'string' ] );
}
