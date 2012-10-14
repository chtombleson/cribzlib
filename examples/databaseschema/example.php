<?php
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');
CribzLib::loadModule('DatabaseSchema');

$dbconf = array(
    'driver' => 'mysql',        // Supported PDO Drivers: Mysql, Pgsql & SQLite
    'host'   => 'localhost',    // Database Host
    'name'   => 'cribzlibdb',   // Database Name
    'user'   => 'user',         // Database Username
    'pass'   => 'passwd',       // Database Password
    'port'   => 3306,           // Database Port
);

// Create a CribzDatabase Object
$database = new CribzDatabase($dbconf['driver'], $dbconf['host'], $dbconf['name'], $dbconf['user'], $dbconf['pass'], $dbconf['port']);

// Create a CribzDatabaseSchema Object to do a schema import from example.xml
$schema = new CribzDatabaseSchema($database, dirname(__FILE_).'/example.xml');

try {
    // Run the execute function to run the import process
    $schema->execute();
} catch (CribzDatabaseImportSchemaException $exception) {
    echo $exception->getMessage();
}
?>
