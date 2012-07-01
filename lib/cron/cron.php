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
* @subpackage   Cribz Cron
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
require_once(dirname(dirname(__FILE__)).'/mvc/model.php');

class CribzCron {
    /**
    * Database
    *
    * @var CribzDatabase
    */
    private $database;

    /**
    * Table
    *
    * @var string
    */
    private $table;

    /**
    * Jobs
    *
    * @var array
    */
    private $jobs = array();

    /**
    * Db Connected
    *
    * @var bool
    */
    private $dbconnected = false;

    /**
    * Construct
    * Create a new instance of Cribz Cron
    *
    * @param CribzDatabase  $database   Cribz Database Object.
    * @param string         $table      Name of table in the database.
    */
    function __construct($database, $table) {
        $this->database = $database;
        $this->table = $table;
    }

    /**
    * Add Job
    * Add a job to the cron queue
    *
    * @param string  $name      Name for cron job.
    * @param array   $function  Array with function name and any data to be passed to function.
    *
    * @return bool true
    */
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

    /**
    * Update Job
    * Update a Job
    *
    * @param string $name       Name of cron job to update.
    * @param array  $function   Array with function name and any data to be passed to function.
    *
    * @return bool true
    */
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

    /**
    * Remove Job
    * Remove a job from the cron queue
    *
    * @param string $name   Name of cron job to remove.
    *
    * @return true on success or false on error
    */
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

    /**
    * Run Job
    * Run a job
    *
    * @param string $name   Name of cron job to run.
    *
    * @return mixed return value from the cron job functions
    */
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

    /**
    * Run All
    * Run all cron jobs
    *
    * @return array of return values from functions or null on empty run
    */
    function runAll() {
        if (!$this->dbconnected) {
            $this->database->connect();
            $this->dbconnected = true;
        }

        $results = $this->database->select($this->table, array(), 'name');

        if (!empty($results)) {
            $returnval = array();
            foreach ($results as $result) {
                $returnval[$result->name] = $this->runJob($result->name);
            }
            return $returnval;
        }
        return null;
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
