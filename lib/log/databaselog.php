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
* @copyright    Copyright 2011 onwards
*/
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
    * Init
    * Initalize the log file. Create it if it does not exist.
    *
    * @return true on success or false on failure.
    */
    function init() {
        $this->database->connect();
        $driver = $this->database->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver == 'pgsql') {
            if ($this->create_pgsql_logtable()) {
                return true;
            }
            return false;
        } else if ($driver == 'mysql' || $driver == 'sqlite') {
            if ($this->create_mysql_logtable()) {
                return true;
            }
            return false;
        } else {
            return false;
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

            if ($this->database->insert($this->logtable, (object) array('message' => $logmsg, 'timecreated' => time()))) {
                return true;
            }
            return false;
        }
    }

    /**
    * Create PGSQL Log Table
    * Create log table for postgresql database
    *
    * @return true on success or false on failure.
    */
    private function create_pgsql_logtable() {
        $exist_sql = "SELECT table_name, table_type
                      FROM information_schema.tables
                      WHERE Table_Name = ?
                      AND schema_name = ?";

        if ($this->database->execute_sql($exist_sql, array($this->logtable, 'public'))) {
            $result = $this->database->fetch();

            if (empty($result)) {
                if (!$this->database->execute_sql("CREATE SEQUENCE ".$this->logtable."_id_seq")) {
                    return false;
                }

                $table_sql = "CREATE TABLE ".$this->logtable."(
                                id int not null default nextval('cribzlog_id_seq') primary key,
                                message text not null,
                                timecreated int not null default 0
                              )";

                if ($this->database->execute_sql($table_sql)) {
                    return true;
                }
                return false;
            }
            return true;
        }
    }

    /**
    * Create MYSQL Log Table
    * Create log table for mysql database
    *
    * @return true on success or false on failure.
    */
    private function create_mysql_logtable() {
        $exist_sql = "SHOW TABLES LIKE ?";

        if ($this->database->execute_sql($exist_sql, array($this->logtable))) {
            $result = $this->database->fetch();

            if (empty($result)) {
                $table_sql = "CREATE TABLE ".$this->logtable."(
                                id int not null autoincrement primary key,
                                message text not null,
                                timecreated int not null default 0
                              )";

                if ($this->database->execute_sql($table_sql)) {
                    return true;
                }
                return false;
            }
            return true;
        }
    }
}
?>
