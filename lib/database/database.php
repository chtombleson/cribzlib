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
* @subpackage   Cribz Database
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzDatabase {

    /**
    * Database connection
    *
    * @var PDO
    */
    private $database;
    
    /**
    * Database Statements
    *
    * @var PDO_Statment
    */
    private $statements = array();

    /**
    * Sql Queries
    *
    * @var array
    */
    private $queries = array();

    /**
    * Query Params
    *
    * @var array
    */
    private $query_params = array();
    
    /**
    * Errors
    *
    * @var array
    */
    private $errors = array();

    /**
    * Database Driver
    *
    * @var string
    */
    private $driver;

    /**
    * Database Host
    *
    * @var string
    */
    private $host;

    /**
    * Database Name
    *
    * @var string
    */
    private $name;

    /**
    * Database User
    *
    * @var string
    */
    private $user;

    /**
    * Database Pass
    *
    * @var string
    */
    private $pass;

    /**
    * Database Port
    *
    * @var int
    */
    private $port;

    /**
    * Database Driver Options
    *
    * @var array
    */
    private $options;

    /**
    * Database Connection String
    *
    * @var string
    */
    private $dsn;

    /**
    * Constructor
    *
    * @param string $driver     Database Driver
    * @param string $host       Database Host
    * @param string $name       Database Name
    * @param string $user       Database User
    * @param string $pass       Database Pass
    * @param int    $port       Database Port (Optional)
    * @param array  $options    Database Driver Options (Optional)
    */
    function __construct($driver, $host, $name, $user, $pass, $port = null, $options = array()) {
        $this->setDriver($driver);
        $this->setHost($host);
        $this->setName($name);
        $this->setUser($user);
        $this->setPass($pass);

        if (!empty($port)) {
            $this->setPort($port);
        }

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    /**
    * Connect
    * Connect to the database
    * @return false on error.
    */
    function connect() {
        try {
            if ($this->driver == 'sqlite') {
                $this->dsn = $this->driver . ':' . $this->name;
                $this->database = new PDO($this->dsn);

            } else {
                $this->dsn = $this->driver . ':' . 'host=' . $this->host . ';' . 'dbname=' . $this->name;

                if (!empty($this->port)) {
                    $this->dsn .= ';port=' . $this->port;
                }

                if (!empty($this->options)) {
                    $this->database = new PDO($this->dsn, $this->user, $this->pass, $this->options);
                } else {
                    $this->database = new PDO($this->dsn, $this->user, $this->pass);
                }
            }
        } catch (PDOException $e) {
            $this->errors['PDO_Connect_Error'] = $e->getMessage();
            return false;
        }
    }

    /**
    * Set Attribute
    *
    * @param int $attribute PDO Attribute Option
    * @param mixed $value   Value to set attribute to
    * @return true on success or fail on failure.
    */
    function setAttribute($attribute, $value) {
        return $this->database->setAttribute($attribute, $value);
    }

    /**
    * Get Attribute
    * Get the value of an attribute
    *
    * @param int $attribute PDO Attribute Option
    * @return value of attribute or false.
    */
    function getAttribute($attribute) {
        return $this->database->getAttribute($attribute);
    }

    /**
    * Begin Transaction
    * Start a database transaction.
    * @return true on success or false on failure.
    */
    function beginTransaction() {
        return $this->database->beginTransaction();
    }

    /**
    * Commit
    * Commit a change to the database.
    * @return true on success or false on failure.
    */
    function commit() {
        return $this->database->commit();
    }

    /**
    * Roll Back
    * Roll back a database change.
    * @return true on success or false on failure.
    */
    function rollBack() {
        return $this->database->rollBack();
    }

    /**
    * Last Insert Id
    * Get the id for the last insert statement.
    *
    * @param string $table  Name of table in database.(optional, needed for postgres & mysql)
    * @param string $field  Name of primary key field. (optional, needed for postgres & mysql)
    * @return int of last id or false.
    */
    function lastInsertId($table = null, $field = null) {
        $sql = "SELECT {$field} FROM {$table} ORDER BY {$field} DESC LIMIT 1";
        $this->executeSql($sql);
        $id = $this->fetch();
        return (int) $id->$field;
    }

    /**
    * DB Error Code
    * Get DB Error code
    * @return db error code array.
    */
    function dbErrorCode() {
        return $this->database->errorCode();
    }

    /**
    * DB Error Info
    * Get DB Error info
    * @return db error info array.
    */
    function dbErrorInfo() {
        return $this->database->errorInfo();
    }

    /**
    * Debug
    * Get a list of all errors
    * @return list of errors
    */
    function debug() {
        $debug = '<ul>';
        foreach ($this->errors as $error_name => $error) {
            if (is_array($error)) {
                foreach ($error as $id => $error_msg) {
                    $debug .= '<li>';
                    $debug .= $error_name .'[' . $id . '] : ' . $error_msg;
                    $debug .= '<ul>';
                    $debug .= '<li> Sql Query : ' . $this->queries[$id] . '</li>';

                    if (!empty($this->query_params[$id])) {
                        $debug .= '<li> Sql Query Params : ' . implode(',', $this->query_params[$id]) . '</li>';
                    }

                    $debug .= '</ul>';
                    $debug .= '</li>';

                }
            } else {
                $debug .= '<li>' . $error_name . ':' . $error . '</li>';
            }
        }
        $debug .= '</ul>';
        return $debug;
    }

    /**
    * Execute Sql
    *
    * @param string $sql    Query to be executed
    * @param array  $params Values to replace ? in query
    * @return false on error
    */
    function executeSql($sql, $params = array()) {
        $this->queries[] = $sql;
        $this->query_params[] = $params;

        $this->statements[] = $this->database->prepare($sql);

        if (!empty($this->statements[count($this->statements) - 1])) {
            if ($this->statements[count($this->statements) - 1]->execute($params) === false) {
                $errormsg = $this->lastStatementErrorInfo();
                $this->errors['PDO_Statment_Execute_Error'][count($this->statements) - 1] = $errormsg[2];
                return false;
            }
        } else {
            if (isset($this->statements[count($this->statements) - 1])) {
                unset($this->statements[count($this->statements) - 1]);
            }
        }
        return true;
    }

    /**
    * Last Statement Error Code
    * Get the error code for the last statement.
    * @return error code array for last statement.
    */
    function lastStatementErrorCode() {
        return $this->statements[count($this->statements) - 1]->errorCode();
    }

    /**
    * Last Statement Error Info
    * Get the error info for the last statement.
    * @return error info array for last statement.
    */
    function lastStatementErrorInfo() {
        return $this->statements[count($this->statements) - 1]->errorInfo();
    }

    /**
    * Select
    *
    * @see CribzSqlGenerator::select()
    * @see CribzDatabase::executeSql()
    *
    * @param string $table  Table to query
    * @param array  $where  Array of field => value for where clause [Optional]
    * @param array  $param  Array of values that are to be inserted into the query [Optional]
    * @param array  $fields Array of fields to select [Optional]
    * @param array  $order  Array of field => order by for order clause [Optional]
    * @param int    $limit  Limit for results [Optional]
    * @param int    $offset Offset for records [Optional]
    * @return false on error
    */
    function select($table, $where = null, $params = array(), $fields = null, $order = null, $limit = null, $offset = null) {
        $sql = CribzSqlGenerator::select($table, $where, $fields, $order, $limit, $offset);

        if (!$this->executeSql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Fetch
    * Fetch the next record in the set
    *
    * @param int $fetch PDO Fetch Style, Default PDO::FETCH_OBJ
    * @return record
    */
    function fetch($fetch = PDO::FETCH_OBJ) {
        return $this->statements[count($this->statements) - 1]->fetch($fetch);
    }

    /**
    * Fetch All
    * Fetch All records in the set
    *
    * @param int $fetch PDO Fetch Style, Default PDO::FETCH_OBJ
    * @return array of records
    */
    function fetchAll($fetch = PDO::FETCH_OBJ) {
        return $this->statements[count($this->statements) - 1]->fetchAll($fetch);
    }

    /**
    * Insert
    *
    * @see CribzSqlGenerator::insert()
    * @see CribzDatabase::executeSql()
    *
    * @param string $table  Table to insert record into
    * @param mixed  $record Array or stdClass of the record you want to insert, field => value
    * @return false on error
    */
    function insert($table, $record) {
        if (is_array($record)) {
            $record = (object) $record;
        }

        $sql = CribzSqlGenerator::insert($table, array_keys(get_object_vars($record)));
        $params = array_values(get_object_vars($record));

        if (!$this->executeSql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Update
    *
    * @see CribzSqlGenerator::update()
    * @see CribzDatabase::executeSql()
    *
    * @param string $table  Table to update
    * @param mixed  $record Array of stdClass of the record you want to update, field => value. Must contain id field with id of record
    * @return false on error
    */
    function update($table, $record) {
        if (is_array($record)) {
            $record = (object) $record;
        }

        if (!isset($record->id)) {
            return false;
        }

        $id = $record->id;
        unset($record->id);

        $sql = CribzSqlGenerator::update($table, array_keys(get_object_vars($record)), array('id' => $id));
        $params = array_values(get_object_vars($record));
        $params[] = $id;

        if (!$this->executeSql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Delete
    *
    * @see CribzSqlGenerator::select()
    * @see CribzSqlGenerator::delete()
    * @see CribzDatabase::executeSql()
    *
    * @param string $table      Table to delete record from.
    * @param array  $where      Array of where clauses. [Optional]
    * @param array  $params     Array of values that are to be inserted into the query. [Optional]
    * @param array  $in         Array of in clauses. [Optional]
    * @param array  $like       Array of like clauses. [Optional]
    * @return false on error
    */
    function delete($table, $where = null, $params = array(), $in = null, $like = null) {
        $sql = CribzSqlGenerator::delete($table, $where, $in, $like);

        if (!$this->executeSql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Truncate Table
    *
    * @param string $table  Table to truncate
    * @return false on error
    */
    function truncateTable($table) {
        $sql = 'TRUNCATE TABLE ' . $table;

        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Copy Table
    *
    * @param string $table      Table to copy from
    * @param string $newtable   Table to copy to
    * @return false on error
    */
    function copyTable($table, $newtable) {
        $sql = 'SELECT * INTO ' . $newtable . ' FROM '.$table;

        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Drop Table
    *
    * @param string $table  Table to drop from database
    * @return false on error
    */
    function dropTable($table) {
        $sql = 'DROP TABLE ' . $table;

        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Create Table
    *
    * @see CribzSqlGenerator::createTable()
    * @see CribzDatabase::executeSql()
    *
    * @param  string $table      Name of table.
    * @param  array  $fields     Array of field definitions eg. array('id' => array('type' => 'int', 'size' => 11, 'null' => false)).
    * @param  string $pk         Name of primary key field.
    * @param  array  $fk         Array of Foriegn Key definitons eg. array('user' => 'users.id'). [Optional]
    * @return false on error
    */
    function createTable($table, $fields, $pk, $fk = null) {
        $sql = CribzSqlGenerator::createTable($this->getDriver(), $table, $fields, $pk, $fk);

        if (!$this->executeSql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Restore Sql File
    *
    * @param string $file   Path to sql file.
    * @return false on error
    */
    function restoreSqlFile($file) {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }

        $sql = file_get_contents($file);
        $commands = explode(';', $sql);

        foreach ($commands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                if (!$this->executeSql($command)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
    * Get Driver
    *
    * @return string database driver that is being used.
    */
    function getDriver() {
        return $this->driver;
    }

    /**
    * Check Table Exists
    * Check if a table exists in the database.
    *
    * @param string $table  Table name to check.
    * @return true if table exists, false if table doesn't exists and null on error.
    */
    function checkTableExists($table) {
        switch ($this->driver) {
            case 'pgsql':
                return $this->pgsqlCheckTableExists($table);
                break;

            case 'mysql':
                return $this->mysqlCheckTableExists($table);
                break;

            case 'sqlite':
                return $this->sqliteCheckTableExists($table);
                break;
        }
    }

    /**
    * PGSQL Check Table Exists
    * Check if a table exists in the postgres database.
    *
    * @param string $table  Table name to check.
    * @return true if table exists, false if table doesn't exists and null on error.
    */
    private function pgsqlCheckTableExists($table) {
        $sql = "SELECT * FROM information_schema.tables WHERE table_name=?";
        if ($this->executeSql($sql, array($table))) {
            $result = $this->fetch();

            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        }
        return null;
    }

    /**
    * Mysql Check Table Exists
    * Check if a table exists in the Mysql database.
    *
    * @param string $table  Table name to check.
    * @return true if table exists, false if table doesn't exists and null on error.
    */
    private function mysqlCheckTableExists($table) {
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";

        if ($this->executeSql($sql, array($this->name, $table))) {
            $result = $this->fetch();

            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        }
        return null;
    }

    /**
    * SQLite Check Table Exists
    * Check if a table exists in the SQLite database.
    *
    * @param string $table  Table name to check.
    * @return true if table exists, false if table doesn't exists and null on error.
    */
    private function sqliteCheckTableExists($table) {
        $sql = "SELECT name FROM sqlite_master WHERE type=? AND name=?";

        if ($this->executeSql($sql, array('table', $table))) {
            $result = $this->fetch();

            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        }
        return null;
    }

    /*
    * Setter functions
    */

    private function setDriver($driver) {
        if (!in_array($driver, array('mysql', 'pgsql', 'sqlite'))) {
            throw new CribzDatabaseException("Unsupported database driver used. Please use a supported driver (mysql, pgsql, sqlite)", 0);
        }
        $this->driver = $driver;
    }

    private function setHost($host) {
        $this->host = $host;
    }

    private function setPort($port) {
        $this->port = (int) $port;
    }

    private function setName($name) {
        $this->name = $name;
    }

    private function setUser($user) {
        $this->user = $user;
    }

    private function setPass($pass) {
        $this->pass = $pass;
    }

    private function setOptions($options) {
        $this->options = $options;
    }
}
class CribzDatabaseException extends CribzException {}
?>
