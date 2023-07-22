<?php
/*
 * cкрипт иморта из categories.json
 */
$filename = __DIR__.'/categories.json';
if ( !file_exists( $filename ) ) {
    echo "categories.json not found\n";
    exit;
}

$json = json_decode( file_get_contents( $filename ) );
if ( is_null( $json ) ) {
    echo 'invalid json: '.json_last_error_msg()."\n";
    exit;
}

$pdo = require_once( __DIR__.'/pdo.php' );

require_once( __DIR__.'/autoload.php' );

use menu\Tree;
use menu\entry\Store;

$tree = new Tree;
$tree->ImportFromJson( $json );
$tree->BuildNestedSet( );

$nodes = $tree->GetNodes( );

$store = new Store( $pdo );

for( $nodes->rewind( ); $nodes->valid( ); $nodes->next( ) ) {
    $entry = $nodes->current( )->GetEntry( );
    
    echo "title='{$entry->GetTitle( )}' name='{$entry->GetName( )}' left='{$entry->GetLeftIndex( )}' right='{$entry->GetRightIndex( )}'\n";
    
    $store->Save( $entry );
}
