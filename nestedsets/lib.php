<?php
namespace lib;

/**
 * Добавляет слэш в конец строки, если надо
 *
 * @param string $value путь строкой
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
 *
 * @return void
 */
function AutoloadTree( $root, $baseDir ) {
    spl_autoload_register( function( $class ) use( $root, $baseDir ) {
        if ( preg_match( '/^'.preg_quote( $root, '/' ).'/', $class ) ) {
            $class = preg_replace( '/^'.preg_quote( $root, '/' ).'/', '', $class );
            $parts = preg_split( '/\\\/', $class, NULL, PREG_SPLIT_NO_EMPTY );
            $filename = AppendTrailingSlash( $baseDir ).join( '/', $parts ).'.php';
            
            if ( file_exists( $filename ) ) {
                require_once( $filename );
            }
        }
    } );
}
