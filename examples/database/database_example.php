<?php
require_once(dirname(__FILE__).'/../../cribzlib.php');

$cribzlib = new CribzLib();
$cribzlib->loadModule('Database');

// Connect to mysql server
$mysql_db = new CribzDatabase('mysql', 'localhost', 'dbname', 'user', 'pass');
$mysql_db->connect();

// Connect to postgreSQL server
$pgsql_db = new CribzDatabase('pgsql', 'localhost', 'dbname', 'user', 'pass', 5432);
$pgsql_db->connect();

// Connect to sqlite db on disk
$sqlite_db = new CribzDatabase('sqlite', '', '/home/database.db', '', '');
$sqlite_db->connect();

// Connect to sqlite db in memory
$sqlite_db_memory = new CribzDatabase('sqlite', '', ':memory:', '', '');
$sqlite_db_memory->connect();

unset($psql_db, $sqlite_db, $sqlite_db_memory);

// Execute some sql commands
$sql = "CREATE TABLE test (id int not null autoincrement primary key, key varchar(255) not null unique, value text not null)";
$mysql_db->execute_sql($sql);

// Insert a record
$mysql_db->insert('test', array('key' => 'key 1', 'value' => 'value 1'));

// Select a record where key equals key 1
$mysql_db->select('test', array('key' => 'key 1'), '*');
$key = $mysql_db->fetch();
print_r($key);
?>
