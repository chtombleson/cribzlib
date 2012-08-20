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
* @subpackage   Cribz DataTable
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzDataTable {
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
    * Fields
    *
    * @var array
    */
    private $fields;

    /**
    * Constructor
    * Create a new data table.
    *
    * @param CribzDatabase $database    CribzDatabase Object
    * @param string        $table       Name of table in the database to create table of
    * @param array         $fields      Array of fields, headers to be in the table
    */
    function __construct($database, $table, $fields) {
        $this->database = $database;
        $this->table = $table;
        $this->fields = $fields;
    }

    /**
    * Get Table
    * Create and return the data table.
    *
    * @param array  $sortby     Sort by fields. field => ASC|DESC (Optional)
    * @param int    $limit      Limit results (Optional)
    * @param int    $offset     Offset results (Optional)
    *
    * @return string html structure of the table
    */
    function get_table($sortby=array(), $limit=null, $offset=null) {
        $data = $this->get_data($sortby, $limit, $offset);

        if (empty($data)) {
            return '';
        }

        $html = $this->build_table($data);
        return $html;
    }

    /**
    * Get Data
    * Gets the data that is used to build the table.
    *
    * @param array  $sortby     Sort by fields
    * @param int    $limit      Limit results
    * @param int    $offset     Offset results
    *
    * @return object data from the database
    */
    private function get_data($sortby, $limit, $offset) {
        $this->database->connect();
        $this->database->select($this->table, null, $this->fields, $sortby, $limit, $offset);
        return $this->database->fetchAll();
    }

    /**
    * Build Table
    * Build the html for the table.
    *
    * @param object $data   Return value from get_data function
    *
    * @return string html structure of the table
    */
    private function build_table($data) {
        $html  = "<table class=\"data-table\">\n";

        $tableheader = "\t<tr>\n";
        foreach (array_keys((array) $data[0]) as $header) {
            $tableheader .= "\t\t<th>".$header."</th>\n";
        }
        $tableheader .= "\t</tr>\n";

        $tablerows = '';
        foreach ($data as $info) {
            $tablerows .= "\t<tr>\n";
            foreach ($info as $value) {
                $tablerows .= "\t\t<td>".$value."</td>\n";
            }
            $tablerows .= "\t</tr>\n";
        }

        $html  = $html.$tableheader.$tablerows;
        $html .= "</table>\n";
        return $html;
    }
}
?>
