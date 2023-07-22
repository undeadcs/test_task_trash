<?php
$config = require_once( __DIR__.'/config.php' );

try {
    $pdo = new PDO( $config[ 'pdo' ][ 'dsn' ], $config[ 'pdo' ][ 'username' ], $config[ 'pdo' ][ 'password' ] );
    $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    
    return $pdo;
}
catch( PDOException $e ) {
    echo 'PDO Exception: '.$e->getMessage( )."\n";
    exit;
}
