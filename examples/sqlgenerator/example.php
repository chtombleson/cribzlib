<?php
require_once('../../cribzlib.php');

CribzLib::loadModule('SqlGenerator');

echo "Select Query: \n";
echo CribzSqlGenerator::select(
    'test', array('id', 'job'), array('id', 'name', 'job'), array('id' => 'DESC'),
    10, 1, array('name' => array('jim', 'chris')), array('email' => 'chtombleson'),
    array('table' => 'employee', 'on' => 'employee.id=test.id')
);
echo "\n\n";

echo "Update Query: \n";
echo CribzSqlGenerator::update(
    'test', array('name', 'job'), array('id', 'started' => array('operator' => '<'))
);
echo "\n\n";

echo "Insert Query: \n";
echo CribzSqlGenerator::insert('test', array('name', 'job'));
echo "\n\n";

echo "Delete Query: \n";
echo CribzSqlGenerator::delete('test', array('id'));
echo "\n\n";

echo "Create Table Mysql: \n";
echo CribzSqlGenerator::createTable('mysql', 'test',
    array(
        'id' => array('type' => 'int', 'size' => 11, 'autoincrement' => true, 'null' => false, 'default' => 0),
        'name' => array('type' => 'varchar', 'size' => 255, 'unique' => true),
    ),
    'id',
    array ('userid' => 'users.id')
);
echo "\n\n";

echo "Create Table PostgreSQL: \n";
echo CribzSqlGenerator::createTable('pgsql', 'test',
    array(
        'id' => array('type' => 'serial'),
        'name' => array('type' => 'varchar', 'size' => 255, 'unique' => true),
    ),
    'id',
    array('userid' => 'users.id')
);
echo "\n\n";

echo "Creating Table SQLite: \n";
echo CribzSqlGenerator::createTable('sqlite', 'test',
    array(
        'id' => array('type' => 'integer', 'autoincrement' => true),
        'name' => array('type' => 'varchar', 'size' => 255, 'unique' => true),
    ),
    'id',
    array('userid' => 'users.id')
);
echo "\n\n";
