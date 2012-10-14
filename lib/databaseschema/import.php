<?php
/*
* Copyright (c) 2012 onwards Christopher Tombleson <chris@cribznetwork.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
* documentation files (the "Software"), to deal in the Software without restriction, including without limitation
* the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
* and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
* TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
* THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
* CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
* DEALINGS IN THE SOFTWARE.
*/
/**
* @package      CribzLib
* @subpackage   CribzDatabaseImportSchema
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzDatabaseImportSchema {
    /**
    * INTEGER
    *
    * @var const int
    */
    const INTEGER = 1;

    /**
    * NUMERIC
    *
    * @var const int
    */
    const NUMERIC = 2;

    /**
    * CHARACTER
    *
    * @var const int
    */
    const CHARACTER = 3;

    /**
    * DATETIME
    *
    * @var const int
    */
    const DATETIME = 4;

    /**
    * TEXT
    *
    * @var const int
    */
    const TEXT = 5;

    /**
    * AUTOINCREMENT
    *
    * @var const int
    */
    const AUTOINCREMENT = 6;

    /**
    * Types
    *
    * @var array
    */
    protected $types = array(
        1 => array('integer', 'smallint', 'mediumint', 'bigint'),
        2 => array('float', 'double', 'decimal'),
        3 => array('varchar', 'char'),
        4 => array('date', 'datetime', 'timestamp'),
        5 => array('text'),
        6 => array('pgsql' => 'serial', 'mysql' => 'AUTO_INCREMENT', 'sqlite' => 'AUTOINCREMENT'),
    );

    /**
    * Database
    *
    * @var CribzDatabase
    */
    protected $database;

    /**
    * Schema File
    *
    * @var string
    */
    protected $schemaFile;

    /**
    * Constructor
    *
    * @param CribzDatabase  $database       CribzDatabase Object.
    * @param string         $schemaFile     Path to schema file to import.
    * @return throws CribzDatabaseImportSchemaException on error
    */
    function __construct(CribzDatabase $database, $schemaFile) {
        if (!file_exists($schemaFile)) {
            throw new CribzDatabaseImportSchemaException('Schema file: ' . $schemaFile . 'does not exist.', 0);
        }

        $this->database = $database;
        $this->schemaFile = $schemaFile;
    }

    /**
    * Execute
    * Run the import process
    */
    function execute() {
        $schema = $this->parse();
        $queries = $this->buildQueries($schema);
        $this->executeQueries($queries);
    }

    /**
    * Parse
    * Parse the xml file and create the schema.
    *
    * @return array with table and record definitions
    */
    protected function parse() {
        $allowed = $this->getAllowedTypes();
        $xml = simplexml_load_file($this->schemaFile);
        $tables = array();

        foreach ($xml->table as $table) {
            $data = new stdClass();
            $data->name = $table->attributes()->name;
            $data->columns = array();
            $data->keys = array();
            $data->records = array();

            foreach ($table->columns->column as $column) {
                if (!in_array(strval($column->attributes()->type), $allowed)) {
                    $msg  = 'Invalid data type: ' . strval($column->attributes()->type);
                    $msg .= '. Please use the following valid data types: ' . implode(', ', $allowed) . '.';
                    throw new CribzDatabaseImportSchemaException($msg, 1);
                }

                $data->columns[] = (object) array(
                    'name' => strval($column->attributes()->name),
                    'type' => strval($column->attributes()->type),
                    'size' => !empty($column->attributes()->size) ? strval($column->attributes()->size) : null,
                    'null' => !empty($column->attributes()->null) ? strval($column->attributes()->null) : null,
                    'default' => isset($column->attributes()->default) ? strval($column->attributes()->default) : null,
                    'autoincrement' => !empty($column->attributes()->autoincrement) ? strval($column->attributes()->autoincrement) : null,
                    'precision' => !empty($column->attributes()->precision) ? strval($column->attributes()->precision) : null,
                );
            }

            foreach ($table->keys->key as $key) {
                $data->keys[] = (object) array(
                    'type' => strval($key->attributes()->type),
                    'column' => strval($key->attributes()->column),
                    'ref_table' => !empty($key->attributes()->referencetable) ? strval($key->attributes()->referencetable) : null,
                    'ref_column' => !empty($key->attributes()->referencecolumn) ? strval($key->attributes()->referencecolumn) : null,
                );
            }

            foreach ($table->records->record as $record) {
                $recordData = array();
                foreach ($record->children() as $name => $value) {
                    $recordData[$name] = strval($value);
                }
                $data->records[] = (object) $recordData;
            }

            $tables[] = $data;
        }

        switch ($this->database->getDriver()) {
            case 'mysql':
                $this->parseMysql($tables);
                break;

            case 'pgsql':
                $this->parsePgsql($tables);
                break;

            case 'sqlite':
                $this->parseSqlite($tables);
                break;
        }

        return $tables;
    }

    /**
    * Parse Mysql
    * Do any database specfic transformations here
    *
    * @param reference array $tables    Tables array from parse function
    */
    protected function parseMysql(&$tables) {

    }

    /**
    * Parse Pgsql
    * Do any database specfic transformations here
    *
    * @param reference array $tables    Tables array from parse function
    */
    protected function parsePgsql(&$tables) {
        foreach ($tables as &$table) {
            foreach ($table->columns as &$column) {
                if ($column->autoincrement) {
                    $column->type = $this->types[self::AUTOINCREMENT]['pgsql'];
                }

                if ($column->type == 'double') {
                    $column->type = 'double precision';

                    if (!empty($column->size)) {
                        $column->size = null;
                    }

                    if (!empty($column->precision)) {
                        $column->precision = null;
                    }
                }
            }
        }
    }

    /**
    * Parse SQLite
    * Do any database specfic transformations here
    *
    * @param reference array $tables    Tables array from parse function
    */
    protected function parseSqlite(&$tables) {

    }

    /**
    * Build Queries
    * Take the schema a generate sql queries
    *
    * @param array  $schema     Array returned from the parse function
    * @return array of sql queries
    */
    protected function buildQueries(array $schema) {
        $queries = array('tables' => array(), 'records' => array());

        foreach ($schema as $table) {
            $queries['tables'][]  = $this->getTableQuery($table);

            foreach ($table->records as $record) {
                $queries['records'][] = $this->getRecordQuery($table->name, $record);
            }
        }

        return $queries;
    }

    /**
    * Execute Queries
    * Run the queries to create tables and insert records
    *
    * @param array  $queries    Array returned from buildQueries function
    */
    protected function executeQueries($queries) {
        $this->database->connect();

        if (!empty($queries['tables'])) {
            foreach ($queries['tables'] as $sql) {
                $this->database->executeSql($sql);
            }
        }

        if (!empty($queries['records'])) {
            foreach ($queries['records'] as $record) {
                $this->database->executeSql($record['query'], $record['parameters']);
            }
        }
    }

    /**
    * Get Table Query
    * Gets the create table query
    *
    * @param object $table  Object with table definition
    * @return string create table sql query
    */
    protected function getTableQuery($table) {
        $fields = array();
        $pk = '';
        $fk = array();

        foreach ($table->columns as $column) {
            $fields[$column->name] = array(
                'type' => $column->type,
                'size' => (int) $column->size,
                'default' => $column->default,
                'autoincrement' => ($column->autoincrement == 'true') ? true : false,
            );

            if (!is_null($column->null)) {
                $fields[$column->name]['null'] = ($column->autoincrement == 'true') ? true : false;
            }
        }

        foreach ($table->keys as $key) {
            if ($key->type == 'primary') {
                $pk = $key->column;
            }

            if ($key->type == 'unique') {
                $fields[$key->column]['unique'] = true;
            }

            if ($key->type == 'foreign') {
                if (empty($key->ref_table) || empty($key->ref_column)) {
                    throw new CribzDatabaseImportSchemaException('Foreign keys require referencetable & referencecolumn attributes.', 2);
                }
                $fk[$key->ref_table] = $key->ref_column;
            }
        }

        if (empty($pk)) {
            throw new CribzDatabaseImportSchemaException('No primary key defined for table, ' . $table->name, 3);
        }

        return CribzSqlGenerator::createTable($this->database->getDriver(), $table->name, $fields, $pk, $fk);
    }

    /**
    * Get Record Query
    * Get sql query for insert a record
    *
    * @param string $table  Name of table to insert record into
    * @param object $record Object with record definition
    * @return array with sql query and parameters
    */
    protected function getRecordQuery($table, $record) {
        $parameters = array();
        $fields = array();
        foreach ($record as $name => $value) {
            $fields[] = $name;
            $parameters[] = $value;
        }

        $query = CribzSqlGenerator::insert($table, $fields);
        return array('query' => $query, 'parameters' => $parameters);
    }

    /**
    * Get Allowed Types
    *
    * @return array of allowed data types
    */
    public function getAllowedTypes() {
        return array(
            'integer', 'smallint', 'mediumint', 'bigint',
            'float', 'double', 'decimal', 'varchar', 'char',
            'date', 'datetime', 'timestamp', 'text',
        );
    }
}

class CribzDatabaseImportSchemaException extends CribzException {}
?>
