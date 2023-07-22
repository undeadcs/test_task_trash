<?php
/*
 * скрипт экспорта данных в type_a.txt
 * 
 * [ОТСТУП][Название категории][Пробел][URL от корня до категории]
 */
$pdo = require_once( __DIR__.'/pdo.php' );

require_once( __DIR__.'/autoload.php' );

use menu\entry\Store;

$store = new Store( $pdo );
$entries = $store->GetWithDepth( );

if ( !$entries->count( ) ) {
    echo "[ERROR] menu entries set is empty\n";
    exit;
}

$currentDepth = 0;
$path = [ ];
$indent = '    ';

file_put_contents( __DIR__.'/type_a.txt', '' );

for( $entries->rewind( ); $entries->valid( ); $entries->next( ) ) {
    $entry = $entries->current( );
    $depth = $entries->getInfo( );
    
    // т.к. хранится дерево с единым корнем для всех, то фиктивный корень игнорировать
    if ( $depth > 0 ) {
        if ( $depth <= $currentDepth ) {
            array_pop( $path ); // для замены последней части адреса на текущем уровне
            
            if ( $depth < $currentDepth ) { // если спустились на уровень ниже, то еще перешли на замену родителя
                array_pop( $path );
            }
        }
        
        $path[ ] = $entry->GetName( );
        file_put_contents( __DIR__.'/type_a.txt', str_repeat( $indent, $depth - 1 ).$entry->GetTitle( ).' /'.join( '/', $path )."\n", FILE_APPEND );
        
        $currentDepth = $depth;
    }
}
