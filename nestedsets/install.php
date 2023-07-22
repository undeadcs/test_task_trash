<?php
/*
 * скрипт инициализации хранилища
 * 
 * перед запуском стоит создать саму базу данных и прописать ее в конфиг
 * например:
 * mysql -uusername -p
 * create database treetest default charset utf8 default collate utf8_general_ci;
 */
$config = require_once( __DIR__.'/config.php' );
$db = null;

try {
    $db = new PDO( $config[ 'pdo' ][ 'dsn' ], $config[ 'pdo' ][ 'username' ], $config[ 'pdo' ][ 'password' ] );
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
}
catch( PDOException $e ) {
    echo 'PDO Exception: '.$e->getMessage( )."\n";
    exit;
}

$filename = __DIR__.'/scheme.sql';
if ( !file_exists( $filename ) ) {
    echo "scheme.sql not found\n";
    exit;
}

try {
    $db->exec( file_get_contents( $filename ) );
    
    echo "installation complete\n";
}
catch( Exception $e ) {
    echo $e->getMessage( );
}