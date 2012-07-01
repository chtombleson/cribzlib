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
* @subpackage   Cribz Model
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzModel {
    /**
    * Data
    *
    * @var array
    */
    private $Data = array();

    /**
    * Intrans
    *
    * @var boolean
    */
    private $Intrans = true;

    /**
    * Database
    *
    * @var Cribz Database
    */
    private $Database;

    /**
    * Table
    *
    * @var string
    */
    public $Table;

    /**
    * Table Definition
    *
    * @var array
    */
    public $Tabledefinition = array();

    /**
    * PK (Primary Key)
    *
    * @var string
    */
    public $Pk;

    /**
    * Constructor
    * Create a new instance of CribzModel.
    *
    * @param CribzDatabase $database    CribzDatabase Object.
    */
    function __construct($database) {
        if (empty($this->Table) && empty($this->Tabledefinition) && empty($this->Pk)) {
            throw new CribzModelException("Please define a table name, table definition & primary key.", 0);
        }

        $this->Database = $database;
        $this->Database->connect();
        $this->Database->beginTransaction();
        self::tabledef();
    }

    /**
    * Set
    * Set class var.
    *
    * @param string $name   Name of Var.
    * @param mixed  $value  Value of Var.
    */
    function __set($name, $value) {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            self::set_data($name, $value);
        }
    }

    /**
    * Get
    * Get the value of a class var.
    *
    * @param strin $name    Name of class Var.
    *
    * @return value of class Var.
    */
    function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return $this->Data[$name];
        }
    }

    /**
    * Load Data
    * Load record from database based on id.
    *
    * @param int $id    Id of record to get.
    */
    function load_data($id) {
        if ($this->Database->select($this->Table, array('id' => $id))) {
            $this->Data[$this->Pk] = $id;
            $result = $this->Database->fetch();

            if (!empty($result)) {
                $this->Data = (array) $result;
            }
        } else {
            throw new CribzModelException("Unable to load data from table: {$this->Table}.", 7);
        }
    }

    /**
    * Commit
    * Commit a record to the database.
    *
    * @return true on success or false on error.
    */
    function commit() {
        if (!isset($this->Data[$this->Pk])) {
            self::create_record();
        }
        $this->Intrans = false;
        return $this->Database->commit();
    }

    /**
    * Rollback
    * Rollback an action.
    *
    * @return true on success or false on failure.
    */
    function rollback() {
        return $this->Database->rollback();
    }

    /**
    * Set Data
    * Set the data and update database.
    *
    * @param string $name   Name of class var.
    * @param mixed  $value  Value of class var.
    */
    private function set_data($name, $value) {
        if (!$this->Intrans) {
            $this->Database->beginTransaction();
            $this->Intrans = true;
        }

        $this->Data[$name] = $value;

        if (isset($this->Data[$this->Pk])) {
            $update = $this->Database->update($this->Table, (object) $this->Data);

            if (!$update) {
                throw new CribzModelException("Unable to update record in database.", 5);
            }
        }
    }

    /**
    * Create Record
    * Create a new record in the database.
    */
    private function create_record() {
        if (!$this->Intrans) {
            $this->Database->beginTransaction();
            $this->Intrans = true;
        }

        if ($this->Database->insert($this->Table, (object) $this->Data)) {
            $driver = $this->Database->getAttribute(PDO::ATTR_DRIVER_NAME);

            if ($driver == 'pgsql') {
                $this->Data[$this->Pk] = $this->Database->lastInsertId($this->Table.'_id_seq');
            } else {
                $this->Data[$this->Pk] = $this->Database->lastInsertId();
            }
        } else {
            throw new CribzModelException("Unable to create new record in database.", 6);
        }
    }

    /**
    * Table Def
    * Create a new table if it doesn't exist.
    */
    private function tabledef() {
        if (!is_array($this->Tabledefinition)) {
            throw new CribzModelException("Table definition is not an array.", 1);
        }

        $table_exists = $this->Database->check_table_exists($this->Table);

        if ($table_exists === false) {
            $driver = $this->Database->getAttribute(PDO::ATTR_DRIVER_NAME);

            if ($driver == 'pgsql') {
                if ($this->Database->execute_sql("CREATE SEQUENCE {$this->Table}_id_seq")) { 
                    $this->Tabledefinition[$this->Pk] = "int not null default nextval('{$this->Table}_id_seq')";
                } else {
                    throw new CribzModelException("Unable to create sequence.", 8);
                }
            }

            $result = $this->Database->create_table($this->Table, $this->Tabledefinition);

            if (!$result) {
                throw new CribzModelException("Unable to create table: {$this->Table}.", 2);
            }

            $this->Database->commit();
            $this->Database->beginTransaction();
        }

        if (is_null($table_exists)) {
            throw new CribzModelException("Unable to check if table exists.", 3);
        }
    }
}

class CribzModelException extends CribzException {}
?>
