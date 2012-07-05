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
* @subpackage   Cribz Database Log
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
require_once(dirname(dirname(__FILE__)).'/mvc/model.php');

class CribzDatabaseLog {
    /**
    * Date format
    *
    * @var string
    */
    private $dateformat = "Y-m-d H:i:s";

    /**
    * Log table
    *
    * @var string
    */
    private $logtable;

    /**
    * Database
    *
    * @var Cribz Database Object
    */
    private $database;

    /**
    * Construct
    * Create a new instance of CribzDatabaseLog
    *
    * @param string         $logtable       Name of log table in database.
    * @param CribzDatbase   $database       CribzDatabase object.
    * @param string         $dateformat     String for date formatting (optional).
    */
    function __construct($logtable, $database, $dateformat = null) {
        $this->logtable = $logtable;
        $this->database = $database;

        if (!empty($dateformat)) {
            $this->dateformat = $dateformat;
        }
    }

    /**
    * Write Log
    * Add a line to the log file.
    *
    * @param string $log    Message to go into log file.
    * @return true on success or false on failure.
    */
    function write_log($log) {
        if (!empty($log)) {
            $log = trim($log);
            $logmsg = "[".date($this->dateformat, time())."]";
            $logmsg .= $log;

            $model = new CribzLogModel($this->database, $this->logtable);
            $model->message = $logmsg;
            $model->timecreated = time();
            $model->commit();
            return true;
        }
    }
}

class CribzLogModel extends CribzModel {
    public $Table;
    public $Pk = 'id';
    public $Tabledefinition = array(
        'id' => 'int not null primary key',
        'message' => 'text not null',
        'timecreated' => 'int not null default 0',
    );

    function __construct($database, $table) {
        $this->Table = $table;
        parent::__construct($database);
    }
}
?>
