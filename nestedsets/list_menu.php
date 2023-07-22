<?php
/*
 * скрипт показа меню
 */
echo '<!DOCTYPE html><meta charset="utf8"/>';

$pdo = require_once( __DIR__.'/pdo.php' );

require_once( __DIR__.'/autoload.php' );

use menu\entry\Store;

$store = new Store( $pdo );
// т.к. используется фиктивный корень, то уровень на 1 больше
$entries = $store->GetWithDepth( );

if ( !$entries->count( ) ) {
    echo "<h1>menu entries set is empty</h1>\n";
    exit;
}

$currentDepth = 0;

for( $entries->rewind( ); $entries->valid( ); $entries->next( ) ) {
    $entry = $entries->current( );
    $depth = $entries->getInfo( );
    
    if ( $depth > 0 ) {
        if ( $depth > $currentDepth ) {
            echo '<ul>';
        } else if ( $depth < $currentDepth ) {
            echo '</ul>';
        }
        
        echo '<li>'.$entry->GetTitle( ).'</li>';
        
        $currentDepth = $depth;
    }
}