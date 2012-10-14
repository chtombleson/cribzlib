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
* @subpackage   CribzSqlGenerator
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzSqlGenerator {

    /**
    * Select
    * Generate a select query.
    *
    * @param string $table      Name of table to select from.
    * @param array  $where      Array of where clauses eg. array('id' => array('operator' => '='), 'name'). [Optional]
    * @param array  $fields     Fields to select eg. array('id', 'name', 'created') default is *. [Optional]
    * @param array  $order      Array of order by statements eg. array('id' => 'DESC', 'name' => 'ASC'). [Optional]
    * @param int    $limit      Limit result by. [Optional]
    * @param int    $offset     Offset results by. [Optional]
    * @param array  $in         Array of in statements eg. array('id' => array(1, 2, 3, 4, 5, 6, 7, 8, 9)). [Optional]
    * @param array  $like       Array of like statements eg. array('site' => 'google', 'user' => 'jim'). [Optional]
    * @param array  $join       Array defining a single join eg. array('type' => 'INNER', 'table' => 'users', 'on' => 'users.id=post.user'). [Optional]
    * @param array  $joins      Array containing more than one join definition. [Optional]
    * @return string SELECT sql query.
    */
    public static function select($table, array $where = null, array $fields = null, array $order = null,
    $limit = null, $offset = null, array $in = null, array $like = null, array $join=null, array $joins = null) {
        $sql = 'SELECT ';
        $sql .= self::processFields($fields);
        $sql .= ' FROM ' . $table;

        if (!empty($join)) {
            $sql .= self::join($join);
        } else if (!empty($joins)) {
            foreach ($joins as $join) {
                $sql .= self::join($join) . ' ';
            }

            $sql = rtrim($sql);
        }

        $sql .= self::processWhere($where, $in, $like);

        if (!empty($limit)) {
            $sql .= ' LIMIT ' . $limit;
        }

        if (!empty($offset)) {
            $sql .= ' OFFSET ' . $offset;
        }

        if (!empty($order)) {
            $sql .= ' ORDER BY ';
            foreach ($order as $field => $sort) {
                $sql .= $field . ' ' . strtoupper($sort) .',';
            }
            $sql = rtrim($sql, ',');
        }

        return $sql;
    }

    /**
    * Update
    * Generate an update query.
    *
    * @param  string $table      Name of table to update.
    * @param  array  $fields     Array of fields to update.
    * @param  array  $where      Array of where clauses. [Optional]
    * @param  array  $in         Array of in clauses. [Optional]
    * @param  array  $like       Array of like clauses. [Optional]
    * @return string UPDATE query.
    */
    public static function update($table, array $fields, array $where = null, array $in = null, array $like = null) {
        $sql = 'UPDATE ' . $table . ' SET ';

        foreach ($fields as $field) {
            $sql .= $field . '=?,';
        }

        $sql = rtrim($sql, ','). ' ';

        $sql .= self::processWhere($where, $in, $like);
        return $sql;
    }

    /**
    * Insert
    * Create an insert query.
    *
    * @param  string $table      Table to insert into.
    * @param  array  $fields     Fields your inserting data into.
    * @return string INSERT query.
    */
    public static function insert($table, array $fields) {
        $sql  = 'INSERT INTO ' . $table . '(' . self::processFields($fields) . ')';
        $sql .= ' VALUES (';

        for ($i = 0; $i < count($fields); $i++) {
            $sql .= '?,';
        }

        $sql = rtrim($sql, ',') . ')';
        return $sql;
    }

    /**
    * Delete
    * Generate a delete query.
    *
    * @param  string $table      Name of table to delete from.
    * @param  array  $where      Array of where clauses. [Optional]
    * @param  array  $in         Array of in clauses. [Optional]
    * @param  array  $like       Array of like clauses. [Optional]
    * @return string DELETE query.
    */
    public static function delete($table, array $where = null, array $in = null, array $like = null) {
        $sql  = 'DELETE FROM ' . $table;
        $sql .= self::processWhere($where, $in, $like);
        return $sql;
    }

    /**
    * Create Table
    * Generate a create table query.
    *
    * @param  string $driver     PDO Database driver (mysql, sqlite, pgsql).
    * @param  string $table      Name of table.
    * @param  array  $fields     Array of field definitions eg. array('id' => array('type' => 'int', 'size' => 11, 'null' => false)).
    * @param  string $pk         Name of primary key field.
    * @param  array  $fk         Array of Foriegn Key definitons eg. array('user' => 'users.id'). [Optional]
    * @return string CREATE TABLE query.
    */
    public static function createTable($driver, $table, array $fields, $pk, array $fk = null) {
        if (!in_array($driver, array('mysql', 'sqlite', 'pgsql'))) {
            throw new CribzSqlGeneratorException('Invalid database driver used. Must be either mysql, sqlite or pgsql', 0);
        }

        switch ($driver) {
            case 'mysql':
                return self::createMysqlTable($table, $fields, $pk, $fk);
                break;

            case 'pgsql':
                return self::createPgsqlTable($table, $fields, $pk, $fk);
                break;

            case 'sqlite':
                return self::createSqliteTable($table, $fields, $pk, $fk);
                break;
        }
    }

    /**
    * Drop Table
    * Generate a drop table query.
    * @param  string $table Name of table to drop.
    * @return string DROP TABLE query.
    */
    public static function dropTable($table) {
        return 'DROP TABLE ' . $table;
    }

    /**
    * Join
    * Create the join section of a sql query.
    *
    * @see select()
    * @return string join sql query.
    */
    private static function join(array $join) {
        $sql  = !empty($join['type']) ? strtoupper($join['type']) . ' JOIN ' : ' JOIN ';
        $sql .= $join['table'] . ' ON ';

        if (is_array($join['on'])) {
            foreach ($join['on'] as $on) {
                $sql .= $on . ' AND ';
            }

            $sql = rtrim($sql, ' AND ');
        } else {
            $sql .= $join['on'];
        }

        return $sql;
    }

    /**
    * Process Fields
    * Create the fields part of a query.
    *
    * @param  array  $fields     Array of table columns/fields.
    * @return string fields part of query.
    */
    private static function processFields(array $fields = null) {
        if (!empty($fields)) {
            return implode(',', $fields);
        } else {
             return '*';
        }
    }

    /**
    * Process Where
    * Create the where part of a query.
    *
    * @see select()
    * @param  array  $where   Array of where clauses.
    * @param  array  $in      Array of in clauses.
    * @param  array  $like    Array of like clauses.
    * @return string where part of query.
    */
    private static function processWhere(array $where = null, array $in = null, array $like = null) {
        $sql = ' WHERE ';
        $sql_where = '';
        $sql_in = '';
        $sql_like = '';

        if (!empty($where)) {
            foreach ($where as $field => $info) {
                if (is_array($info)) {
                    $sql_where .= $field;
                    $sql_where .= !empty($info['operator']) ? $info['operator'] : '=';
                    $sql_where .= '? AND ';
                } else {
                    $sql_where .= $info . '=? AND ';
                }
            }

            $sql_where = rtrim($sql_where, ' AND ');
        }

        if (!empty($in)) {
            foreach ($in as $field => $value) {
                $sql_in .= $field . ' IN (';

                foreach ($value as $inval) {
                    if (preg_match('#[0-9]+#', $inval)) {
                        $sql_in .= $inval . ',';
                    } else {
                        $sql_in .= '\'' . $inval . '\',';
                    }
                }

                $sql_in = rtrim($sql_in, ',') . ') AND ';
            }

            $sql_in = rtrim($sql_in, ' AND ');
        }

        if (!empty($like)) {
            foreach (array_keys($like) as $field) {
                $sql_like .= $field . ' LIKE \'%?%\' AND ';
            }
            $sql_like = rtrim($sql_like, ' AND ');
        }

        $sql .= !empty($sql_where) ? $sql_where . ' AND ' : '';
        $sql .= !empty($sql_like) ? $sql_like . ' AND ' : '';
        $sql .= !empty($sql_in) ? $sql_in . ' AND ' : '';
        $sql = rtrim($sql, ' AND ');

        return (trim($sql) == 'WHERE') ? '' : $sql;
    }

    /**
    * Create Mysql Table
    * Generate a query to create a table for Mysql
    *
    * @see createTable()
    * @return string CREATE TABLE query.
    */
    private static function createMysqlTable($table, array $fields, $pk, array $fk = null) {
        $sql = 'CREATE TABLE ' . $table .'(';
        $index = 'PRIMARY KEY(' . $pk . '),';
        $datatypes = self::mysqlDataTypes();

        foreach ($fields as $name => $field) {
            if (empty($field['type'])) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' does not have a type set.', 1);
            }

            $sql .= $name . ' ';

            if (!in_array(strtolower($field['type']), $datatypes)) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' has an invalid data type.', 2);
            }

            $sql .= $field['type'];

            if (in_array($field['type'], array('double', 'decimal', 'foat'))) {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ',' : '(10,';
                $sql .= !empty($field['precision']) ? $field['precision'] . ')' : '5)';
            } else {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ')' : '';
            }

            if (isset($field['null'])) {
                $sql .= !$field['null'] ? ' NOT NULL ' : ' NULL ';
            }

            if (isset($field['default']) && $name != $pk) {
                if (preg_match('#[0-9]+#', $field['default'])) {
                    $sql .= ' DEFAULT ' . $field['default'];
                } else {
                    $sql .= ' DEFAULT \'' . $field['default'] . '\'';
                }
            }

            if (!empty($field['autoincrement'])) {
                $sql .= ' AUTO_INCREMENT';
            }

            $sql .= ',';

            if (!empty($field['unique'])) {
                $index .= ' UNIQUE (' . $name . '),';
            }
        }

        if (!empty($fk)) {
            foreach ($fk as $name => $ref) {
                $parts  = explode('.', $ref);
                $index .= ' FOREIGN KEY (' . $name . ')';
                $index .= ' REFERENCES ' . $parts[0] . '(' . $parts[1] . '),';
            }
        }

        $sql .= ' ' . rtrim($index, ',') . ')';
        return $sql;
    }

    /**
    * Create Pgsql Table
    * Generate a query to create a table for Pgsql
    *
    * @see createTable()
    * @return string CREATE TABLE query.
    */
    private static function createPgsqlTable($table, array $fields, $pk, array $fk = null) {
        $sql = 'CREATE TABLE ' . $table .'(';
        $index = 'PRIMARY KEY(' . $pk .'),';
        $datatypes = self::pgsqlDataTypes();

        foreach ($fields as $name => $field) {
            if (empty($field['type'])) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' does not have a type set.', 1);
            }

            $sql .= $name . ' ';

            if (!in_array(strtolower($field['type']), $datatypes)) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' has an invalid data type.', 2);
            }

            $sql .= $field['type'];

            if (in_array($field['type'], array('double', 'decimal', 'foat'))) {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ',' : '(10,';
                $sql .= !empty($field['precision']) ? $field['precision'] . ')' : '5)';
            } else {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ')' : '';
            }

            if (isset($field['null'])) {
                $sql .= !$field['null'] ? ' NOT NULL ' : ' NULL ';
            }

            if (isset($field['default']) && $name != $pk) {
                if (preg_match('#[0-9]+#', $field['default'])) {
                    $sql .= ' DEFAULT ' . $field['default'];
                } else {
                    $sql .= ' DEFAULT \'' . $field['default'] . '\'';
                }
            }

            $sql .= ',';

            if (!empty($field['unique'])) {
                $index .= ' UNIQUE (' . $name . '),';
            }
        }

        if (!empty($fk)) {
            foreach ($fk as $name => $ref) {
                $parts  = explode('.', $ref);
                $index .= ' FOREIGN KEY (' . $name . ')';
                $index .= ' REFERENCES ' . $parts[0] . '(' . $parts[1] . '),';
            }
        }

        $sql .= ' ' . rtrim($index, ',') . ')';
        return $sql;
    }

    /**
    * Create SQLite Table
    * Generate a query to create a table for SQLite
    *
    * @see createTable()
    * @return string CREATE TABLE query.
    */
    private static function createSqliteTable($table, array $fields, $pk, array $fk = null) {
        $sql = 'CREATE TABLE ' . $table .'(';
        $index = 'PRIMARY KEY(' . $pk . '),';
        $datatypes = self::mysqlDataTypes();

        foreach ($fields as $name => $field) {
            if (empty($field['type'])) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' does not have a type set.', 1);
            }

            $sql .= $name . ' ';

            if (!in_array(strtolower($field['type']), $datatypes)) {
                throw new CribzSqlGeneratorException('Create Table: field, ' . $name . ' has an invalid data type.', 2);
            }

            $sql .= $field['type'];

            if (in_array($field['type'], array('double', 'decimal', 'foat'))) {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ',' : '(10,';
                $sql .= !empty($field['precision']) ? $field['precision'] . ')' : '5)';
            } else {
                $sql .= !empty($field['size']) ? '(' . $field['size'] . ')' : '';
            }

            if (isset($field['null'])) {
                $sql .= !$field['null'] ? ' NOT NULL ' : ' NULL ';
            }

            if (isset($field['default']) && $name != $pk) {
                if (preg_match('#[0-9]+#', $field['default'])) {
                    $sql .= ' DEFAULT ' . $field['default'];
                } else {
                    $sql .= ' DEFAULT \'' . $field['default'] . '\'';
                }
            }

            if (!empty($field['autoincrement'])) {
                $sql .= ' AUTOINCREMENT';
            }

            $sql .= ',';

            if (!empty($field['unique'])) {
                $index .= ' UNIQUE (' . $name . '),';
            }
        }

        if (!empty($fk)) {
            foreach ($fk as $name => $ref) {
                $parts  = explode('.', $ref);
                $index .= ' FOREIGN KEY (' . $name . ')';
                $index .= ' REFERENCES ' . $parts[0] . '(' . $parts[1] . '),';
            }
        }

        $sql .= ' ' . rtrim($index, ',') . ')';
        return $sql;
    }

    /**
    * Mysql Data Types
    * @return array of Mysql Data Types.
    */
    public static function mysqlDataTypes() {
        return array(
            'int', 'integer', 'tinyint', 'smallint', 'mediumint',
            'bigint', 'float', 'double', 'decimal', 'date', 'datetime',
            'timestamp', 'time', 'year', 'char', 'varchar', 'blob', 'text',
            'tinyblob', 'tinytext', 'mediumblob', 'mediumtext', 'longblob',
            'longtext', 'enum', 'bit',
        );
    }

    /**
    * Pgsql Data Types
    * @return array of PostgreSQL Data types.
    */
    public static function pgsqlDataTypes() {
        return array(
            'bigint', 'bigserial', 'bit', 'boolean', 'bool',
            'bytea', 'varchar', 'char', 'date', 'double precision',
            'float8', 'integer', 'int', 'money', 'numeric', 'decimal',
            'real', 'float4', 'smallint', 'serial', 'text', 'time',
            'timestamp', 'float', 'double', 'datetime',
        );
    }

    /**
    * Sqlite Data Types
    * @return array of SQLite Data types.
    */
    public static function sqliteDataTypes() {
        return array(
            'int', 'integer', 'tinyint', 'smallint', 'mediumint',
            'bigint', 'unsigned big int', 'int2', 'int8', 'character',
            'varchar', 'varying character', 'nchar', 'native character',
            'nvarchar', 'text', 'clob', 'blob', 'real', 'double', 'double precision',
            'float', 'numeric', 'decimal', 'boolean', 'date', 'datetime', 'timestamp',
        );
    }
}

class CribzSqlGeneratorException extends CribzException {}
?>
