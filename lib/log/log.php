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
* @subpackage   Cribz Log
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzLog {
    /**
    * Date format
    *
    * @var string
    */
    private $dateformat = "Y-m-d H:i:s";
    
    /**
    * Log File
    *
    * @var string
    */
    private $logfile;

    /**
    * Construct
    * Create a new instance of CribzLog
    *
    * @param string $logfile        Path to log file
    * @param string $dateformat     String for date formatting (optional).
    */
    function __construct($logfile, $dateformat = null) {
        $this->logfile = $logfile;

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
        if (!file_exists($this->logfile)) {
            if ($this->create_log_file()) {
                return true;
            }
            return false;
        }
        return true;
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
            $logmsg .= PHP_EOL;

            if (file_put_contents($this->logfile, $logmsg, FILE_APPEND)) {
                return true;
            }
            return false;
        }
    }

    /**
    * Create Log File
    * Create the log file.
    *
    * @access private
    * @return true on success or false on failure
    */
    private function create_log_file() {
        if (file_exists(dirname($this->logfile)) && is_writeable(dirname($this->logfile))) {
            if (file_put_contents($this->logfile, '#log file created: '.date($this->dateformat, time()).PHP_EOL)) {
                return true;
            }
            return false;
        }

        if (@mkdir(dirname($this->logfile), 0777, true)) {
            if (file_put_contents($this->logfile, '#log file created: '.date($this->dateformat, time()).PHP_EOL)) {
                return true;
            }
            return false;
        }
        return false;
    }
}
?>
