<?php
//Rename to config.php
$config = new stdClass();

$config->pgsql = array(
    'driver'    => 'pgsql',
    'host'      => 'DB HOST',
    'name'      => 'DB NAME',
    'user'      => 'DB USER',
    'pass'      => 'DB PASS',
    'port'      => 5432,
);

$config->mysql = array(
    'driver'    => 'mysql',
    'host'      => 'DB HOST'
    'name'      => 'DB NAME',
    'user'      => 'DB USER',
    'pass'      => 'DB PASS',
    'port'      => null,
);
?>
