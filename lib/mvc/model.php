<?php
class CribzModel {
    private $Data = array();
    private $Tabledefinition = array();
    private $Intrans = true;
    private $Database;
    public $Table;
    public $Pk;

    function __construct($database) {
        if (empty($this->Table) && empty($this->Tabledefinition) && empty($this->Pk)) {
            throw new CribzModelException("Please define a table name, table definition & primary key.", 0);
        }

        $this->Database = $database;
        $this->Database->connect();
        $this->Database->beginTransaction();
        $this->tabledef();
    }

    function __set($name, $value) {
        $this->set_data($name, $value)
    }

    function __get($name) {
        return $this->Data[$name];
    }

    function load_data($id) {
        if ($this->database->select($this->Table, array('id' => $id))) {
            $result = $this->database->fetch();

            if (!empty($result)) {
                $this->Data = (array) $result;
            }
        } else {
            throw new CribzModelException("Unable to load data from table: {$this->Table}.", 7);
        }
    }

    function commit() {
        if (!isset($this->Data[$this->Pk])) {
            $this->create_record();
        }
        $this->Intrans = false;
        return $this->database->commit();
    }

    function rollback() {
        return $this->database->rollback();
    }

    private function set_data($name, $value) {
        if (!$this->Intrans) {
            $this->database->beginTransaction();
        }

        $this->Data[$name] = $value;

        if (isset($this->Data[$this->Pk])) {
            $update = $this->database->update_record($this->Table, (object) $this->Data);

            if (!$update) {
                throw new CribzModelException("Unable to update record in database.", 5);
            }
        }
    }

    private function create_record() {
        if (!$this->Intrans) {
            $this->database->beginTransaction();
        }

        if ($this->database->insert_record($this->Table, (object) $this->Data)) {
            $this->Data[$this->pk] = $this->database->lastInsertId();
        } else {
            throw new CribzModelException("Unable to create new record in database.", 6);
        }
    }

    private function tabledef() {
        if (!is_array($this->Tabledefinition)) {
            throw new CribzModelException("Table definition is not an array.", 1);
        }

        $table_exists = $this->database->check_table_exists($this->Table);

        if ($table_exists === false) {
            $result = $this->database->create_table($this->Table, $this->Tabledefinition);

            if (!$result) {
                throw new CribzModelException("Unable to create table: {$this->Table}.", 2);
            }
        }

        if (is_null($table_exists)) {
            throw new CribzModelException("Unable to check if table exists.", 3);
        }
    }
}

class CribzModelException extends CribzException {}
?>
