<?php
/*
*   This file is part of CribzLib.
*
*    CribzLib is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    CribzLib is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with CribzLib.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
* @package      CribzLib
* @subpackage   Cribz Database
* @author       Christopher Tombleson
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
    *
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
    *
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
    *
    * @return value of attribute or false.
    */
    function getAttribute($attribute) {
        return $this->database->getAttribute($attribute);
    }

    /**
    * Begin Transaction
    * Start a database transaction.
    *
    * @return true on success or false on failure.
    */
    function beginTransaction() {
        return $this->database->beginTransaction();
    }

    /**
    * Commit
    * Commit a change to the database.
    *
    * @return true on success or false on failure.
    */
    function commit() {
        return $this->database->commit();
    }

    /**
    * Roll Back
    * Roll back a database change.
    *
    * @return true on success or false on failure.
    */
    function rollBack() {
        return $this->database->rollBack();
    }

    /**
    * Last Insert Id
    * Get the id for the last insert statement.
    *
    * @param string $name   Name of id or sequence.(optional)
    *
    * @return int of last id or false.
    */
    function lastInsertId($name = null) {
        return $this->database->lastInsertId($name);
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
    *
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
    *
    * @return false on error
    */
    function execute_sql($sql, $params = array()) {
        $this->queries[] = $sql;
        $this->query_params[] = $params;

        $this->statements[] = $this->database->prepare($sql);

        if ($this->statements[count($this->statements) - 1]->execute($params) === false) {
            $errormsg = $this->lastStatementErrorInfo();
            $this->errors['PDO_Statment_Execute_Error'][count($this->statements) - 1] = $errormsg[2];
            return false;
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
    * @param string $table  Table to query
    * @param array  $where  Array of field => value for where clause (Optional)
    * @param mixed  $fields Array or string of fields to select (Optional)
    * @param array  $order  Array of field => order by for order clause (Optional)
    * @param int    $limit  Limit for results (Optional)
    * @param int    $offset Offset for records (Optional)
    *
    * @return false on error
    */
    function select($table, $where = array(), $fields = '*', $order = array(), $limit = null, $offset = null) {
        $sql = 'SELECT ';
        $params = array();

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $sql .= $field . ', ';
            }
            $sql = trim($sql, ', ');

        } else {
            $sql .= $fields;
        }

        $sql .= ' FROM ' . $table;

        if (!empty($where)) {
            $sql .= ' WHERE ';
            foreach ($where as $field => $value) {
                $sql .= $field . '=? AND ';
                $params[] = $value;
            }
            $sql = trim($sql, ' AND ');
        }

        if (!empty($order)) {
            $sql .= ' ORDER BY ';
            foreach ($order as $field => $value) {
                if (strtoupper($value) == 'ASC' || strtoupper($value) == 'DESC') {
                    $sql .= $field . ' ' . strtoupper($value) .', ';
                }
            }
            $sql = trim($sql, ', ');
        }

        if (!empty($limit)) {
            $limit = (int) $limit;
            $sql .= empty($limit) ? '' : ' LIMIT ' . $limit;
        }

        if (!empty($offset)) {
            $offset = (int) $offset;
            $sql .= empty($offset) ? '' : ' OFFSET ' . $offset;
        }

        if (!$this->execute_sql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Fetch
    * Fetch the next record in the set
    *
    * @param int $fetch PDO Fetch Style, Default PDO::FETCH_OBJ
    *
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
    *
    * @return array of records
    */
    function fetchAll($fetch = PDO::FETCH_OBJ) {
        return $this->statements[count($this->statements) - 1]->fetchAll($fetch);
    }

    /**
    * Insert
    *
    * @param string $table  Table to insert record into
    * @param mixed  $record Array or stdClass of the record you want to insert, field => value
    *
    * @return false on error
    */
    function insert($table, $record) {
        $sql = 'INSERT INTO ' . $table .'(';
        $values = 'VALUES (';
        $params = array();

        foreach ($record as $field => $value) {
            $sql .= $field . ', ';
            $values .= '?, ';
            $params[] = $value;
        }
        $sql = trim($sql, ', ') . ')';
        $values = trim($values, ', ') . ')';
        $sql = $sql . $values;

        if (!$this->execute_sql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Update
    *
    * @param string $table  Table to update
    * @param mixed  $record Array of stdClass of the record you want to update, field => value. Must contain id field with id of record
    *
    * @return false on error
    */
    function update($table, $record) {
        if (!isset($record->id)) {
            return false;
        }

        if (is_array($record) && !isset($record['id'])) {
            return false;
        }

        $sql = 'UPDATE ' . $table . ' SET ';
        $params = array();

        foreach ($record as $field => $value) {
            if ($field != 'id') {
                $sql .= $field . '=?, ';
                $params[] = $value;
            }
        }

        $sql = trim($sql, ', ');
        $sql .= ' WHERE id=?';
        $params[] = (is_array($record) && isset($record['id'])) ? $record['id'] : $record->id;

        if (!$this->execute_sql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Delete
    *
    * @param string $table  Table to delete record from
    * @param mixed  $where  Array of string for where clause. Array is field => value
    *
    * @return false on error
    */
    function delete($table, $where) {
        $sql = 'DELETE FROM ' . $table . ' WHERE ';
        $params = array();

        foreach ($where as $field => $value) {
            $sql .= $field . '=? AND ';
            $params[] = $value;
        }
        $sql = trim($sql, ' AND ');

        if (!$this->execute_sql($sql, $params)) {
            return false;
        }

        return true;
    }

    /**
    * Truncate Table
    *
    * @param string $table  Table to truncate
    *
    * @return false on error
    */
    function truncate_table($table) {
        $sql = 'TRUNCATE TABLE ' . $table;

        if (!$this->execute_sql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Copy Table
    *
    * @param string $table      Table to copy from
    * @param string $newtable   Table to copy to
    *
    * @return false on error
    */
    function copy_table($table, $newtable) {
        $sql = 'SELECT * INTO ' . $newtable . ' FROM '.$table;

        if (!$this->execute_sql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Drop Table
    *
    * @param string $table  Table to drop from database
    *
    * @return false on error
    */
    function drop_table($table) {
        $sql = 'DROP TABLE ' . $table;

        if (!$this->execute_sql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Create Table
    *
    * @param string $name       Name of new table
    * @param array  $tabledef   Array of column name => column definition
    *
    * @return false on error
    */
    function create_table($name, $tabledef) {
        $sql = 'CREATE TABLE ' . $name . ' (';

        foreach ($tabledef as $field => $def) {
            $sql .= $field . ' ' . $def.',';
        }
        $sql = trim($sql, ',') . ')';

        if (!$this->execute_sql($sql)) {
            return false;
        }

        return true;
    }

    /**
    * Restore Sql File
    *
    * @param string $file   Path to sql file. Sql file should be readable.
    *
    * @return false on error
    */
    function restore_sql_file($file) {
        if (!file_exists($file) || !is_readable($file)) {
            return false;
        }

        $sql = file_get_contents($file);
        $commands = explode(';', $sql);

        foreach ($commands as $command) {
            if (!$this->execute_sql($command)) {
                return false;
            }
        }

        return true;
    }

    /**
    * Check Table Exists
    * Check if a table exists in the database.
    *
    * @param string $table  Table name to check.
    * @return true if table exists, false if table doesn't exists and null on error.
    */
    function check_table_exists($table) {
        switch ($this->driver) {
            case 'pgsql':
                return $this->pgsql_check_table_exists($table);
                break;

            case 'mysql':
                return $this->mysql_check_table_exists($table);
                break;

            case 'sqlite':
                return $this->sqlite_check_table_exists($table);
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
    private function pgsql_check_table_exists($table) {
        $sql = "SELECT * FROM information_schema.tables WHERE table_name=?";
        if ($this->execute_sql($sql, array($table))) {
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
    private function mysql_check_table_exists($table) {
        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ?";

        if ($this->execute($sql, array($this->name, $table))) {
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
    private function sqlite_check_table_exists($table) {
        $sql = "SELECT name FROM sqlite_master WHERE type=? AND name=?";

        if ($this->execute_sql($sql, array('table', $table))) {
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
        if ($driver != 'mysql' && $driver != 'sqlite' && $driver != 'pgsql') {
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
