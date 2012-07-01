<?php
require_once(dirname(dirname(__FILE__)).'/mvc/model.php');

class CribzCron {
    private $database;
    private $table;
    private $jobs = array();
    private $dbconnected = false;

    function __construct($database, $table) {
        $this->database = $database;
        $this->table = $table;
    }

    function addJob($name, $function) {
        if (isset($this->jobs[$name])) {
            throw new CribzCronException("Job with the name {$name} already exists.", 0);
        }

        $this->jobs[$name] = new CribzCronModel($this->database, $this->table);
        $this->jobs[$name]->name = $name;
        $this->jobs[$name]->function = serialize($function);
        $this->jobs[$name]->timecreated = time();
        $this->jobs[$name]->commit();

        return true;
    }

    function updateJob($name, $function) {
        if (!$this->dbconnected) {
            $this->database->connect();
            $this->dbconnected = true;
        }

        $result = $this->database->select($this->table, array('name' => $name), 'id');

        if (empty($result)) {
            throw new CribzCronException("Cannot update a job that does not exist.", 1);
        }

        if (isset($this->jobs[$name])) {
            $this->jobs[$name]->name = $name;
            $this->jobs[$name]->function = serialize($function);
            $this->jobs[$name]->timemodified = time();
            $this->jobs[$name]->commit();
        } else {
            $this->jobs[$name] = new CribzCronModel($this->database, $this->table, $result->id);
            $this->jobs[$name]->name = $name;
            $this->jobs[$name]->function = serialize($function);
            $this->jobs[$name]->timemodified = time();
            $this->jobs[$name]->commit();
        }

        return true;
    }

    function removeJob($name) {
        if (!$this->dbconnected) {
            $this->database->connect();
            $this->dbconnected = true;
        }

        $result = $this->database->select($this->table, array('name' => $name), 'id');

        if (empty($result)) {
            throw new CribzCronException("Cannot remove a job that does not exist.", 1);
        }

        if (isset($this->jobs[$name])) {
            unset($this->jobs[$name]);
        }

        return $this->database->delete($this->table, array('id' => $result->id));
    }

    function runJob($name) {
        if (isset($this->jobs[$name])) {
            $data = unserialize($this->jobs[$name]->function);

            if (function_exists($data['function'])) {
                if (!empty($data['data'])) {
                    return call_user_func_array($data['function'], $data['data']);
                } else {
                    return call_user_func($data['function']);
                }
            } else {
                throw new CribzCronException("Cannot run a job when the function for the job does not exist.", 3);
            }

        } else {
            if (!$this->dbconnected) {
                $this->database->connect();
                $this->dbconnected = true;
            }

            $result = $this->database->select($this->table, array('name' => $name), 'id');

            if (empty($result)) {
                throw new CribzCronException("Cannot run a job that does not exist.", 2);
            }

            $this->jobs[$name] = new CribzCronModel($this->database, $this->table, $result->id);
            $this->runJob($name);
        }
    }

    function runAll() {
        if (!$this->dbconnected) {
            $this->database->connect();
            $this->dbconnected = true;
        }

        $results = $this->database->select($this->table, array(), 'name');

        if (!empty($results)) {
            foreach ($results as $result) {
                $this->runJob($result->name);
            }
        }
    }
}

class CribzCronModel extends CribzModel {
    public $Table;
    public $Pk = 'id';
    public $Tabledefiniton = array(
        'id' => 'int not null primary key',
        'name' => 'varchar(100) not null unique',
        'function' => 'text not null',
        'timecreated' => 'int not null default 0',
        'timemodified' => 'int not null default 1'
    );

    function __construct($database, $table, $id=null) {
        $this->Table = $table;
        parent::__construct($database);

        if (!empty($id)) {
            $this->loadData($id);
        }
    }
}

class CribzCronException extends CribzException {}
?>
