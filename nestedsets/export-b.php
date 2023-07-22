<?php
/*
 * скрипт экспорта данных в type_b.txt
 * 
 * [ОТСТУП][Название категории]
 */
$pdo = require_once( __DIR__.'/pdo.php' );

require_once( __DIR__.'/autoload.php' );

use menu\entry\Store;

$store = new Store( $pdo );
// т.к. используется фиктивный корень, то уровень на 1 больше
$entries = $store->GetWithDepth( 2 );

if ( !$entries->count( ) ) {
    echo "[ERROR] menu entries set is empty\n";
    exit;
}

$indent = '    ';

file_put_contents( __DIR__.'/type_b.txt', '' );

for( $entries->rewind( ); $entries->valid( ); $entries->next( ) ) {
    $entry = $entries->current( );
    $depth = $entries->getInfo( );
    
    if ( $depth > 0 ) {
        file_put_contents( __DIR__.'/type_b.txt', str_repeat( $indent, $depth - 1 ).$entry->GetTitle( )."\n", FILE_APPEND );
    }
}
