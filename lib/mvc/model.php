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
        self::tabledef();
    }

    function __set($name, $value) {
        self::set_data($name, $value);
    }

    function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return $this->Data[$name];
        }
    }

    function load_data($id) {
        if ($this->Database->select($this->Table, array('id' => $id))) {
            $result = $this->Database->fetch();

            if (!empty($result)) {
                $this->Data = (array) $result;
            }
        } else {
            throw new CribzModelException("Unable to load data from table: {$this->Table}.", 7);
        }
    }

    function commit() {
        if (!isset($this->Data[$this->Pk])) {
            self::create_record();
        }
        $this->Intrans = false;
        return $this->Database->commit();
    }

    function rollback() {
        return $this->Database->rollback();
    }

    private function set_data($name, $value) {
        if (!$this->Intrans) {
            $this->Database->beginTransaction();
        }

        $this->Data[$name] = $value;

        if (isset($this->Data[$this->Pk])) {
            $update = $this->Database->update_record($this->Table, (object) $this->Data);

            if (!$update) {
                throw new CribzModelException("Unable to update record in database.", 5);
            }
        }
    }

    private function create_record() {
        if (!$this->Intrans) {
            $this->Database->beginTransaction();
        }

        if ($this->Database->insert($this->Table, (object) $this->Data)) {
            $this->Data[$this->pk] = $this->Database->lastInsertId();
        } else {
            throw new CribzModelException("Unable to create new record in database.", 6);
        }
    }

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
