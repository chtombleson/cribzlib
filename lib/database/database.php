<?php
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
    */
    function __construct($driver, $host, $name, $user, $pass, $port = null) {
        $this->setDriver($driver);
        $this->setHost($host);
        $this->setName($name);
        $this->setUser($user);
        $this->setPass($pass);

        if (!empty($port)) {
            $this->setPort($port);
        }
    }

    /**
    * Connect
    * Connect to the database
    */
    function connect() {
        try {
            $this->dsn = $this->driver . ':' . 'host=' . $this->host . ';' .
                         'dbname=' . $this->name . ';';

            if (!empty($this->port)) {
                $this->dsn .= 'port=' . $this->port;
            }

            $this->database = new PDO($dsn, $this->user, $this->pass);

        } catch (PDOException $e) {
            $this->errors['PDO_Connect_Error'] = $e->getMessage();
        }
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
        $this->query_params = $params;

        $this->statments[] = $this->database->prepare($sql);

        if ($this->statements[count($this->statements) - 1]->execute($params) === false) {
            $this->errors['PDO_Statment_Execute_Error'][count($this->statments) - 1] = 'Query Could Not Be Executed';
            return false;
        }
        return true;
    }

    /**
    * Select
    *
    * @param string $table  Table to query
    * @param array  $where  Array of field => value for where clause (Optional)
    * @param mixed  $fields Array or string of fields to select (Optional)
    *
    * @return false on error
    */
    function select($table, $where = null, $fields = '*') {
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

        $sql .= 'FROM ' . $table;

        if (!empty($where)) {
            $sql .= ' WHERE ';
            foreach ($where as $field => $value) {
                $sql .= $field . '=? AND ';
                $params[] = $value;
            }
            $sql = trim($sql, ' AND ');
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
        return $this->statements[count($this->statments) - 1]->fetch($fetch);
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
        return $this->statements[count($this->statments) - 1]->fetchAll($fetch);
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
        if (!isset($record->id) || !isset($record['id'])) {
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
        $params[] = isset($record['id']) ? $record['id'] : $record->id;

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
    * @param array  $tabledef   Array of column => column definition
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

        if (!$this->execute_sql($sql)) {
            return false;
        }

        return true;
    }

    /*
    * Setter functions
    */

    private function setDriver($driver) {
        $this->driver = $driver;
    }

    private function setHost($host) {
        $this->host = $host;
    }

    private function setPort($port) {
        $this->port = $port;
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
}
?>
