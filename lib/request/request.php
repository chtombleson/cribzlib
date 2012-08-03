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
* @subpackage   Cribz Request
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzRequest {

    /**
    * Post
    *
    * @var array
    */
    private $post;

    /**
    * Get
    *
    * @var array
    */
    private $get;

    /**
    * Constructor
    * Gets $_POST & $_GET and places it into class vars.
    */
    function __construct() {
        $this->post = $_POST;
        $this->get = $_GET;
    }

    /**
    * Optional Param
    * Get a param and if it doesn't exist return the default.
    * 
    * @param string $name       Name of post/get variable.
    * @param string $type       Data type for input. String, int, float, double.
    * @param mixed  $default    Default value if param not set. (Optional)
    *
    * @return mixed param value if set of default if set or null.
    */
    function optional_param($name, $type, $default=null) {
        if ($this->get_param($name) !== false) {
            return $this->check_type($type, $this->get_param($name));
        } else {
            if (!empty($default)) {
                return $default;
            } else {
                return null;
            }
        }

        if ($this->post_param($name) !== false) {
            return $this->check_type($type, $this->post_param($name));
        } else {
            if (!empty($default)) {
                return $default;
            } else {
                return null;
            }
        }
    }

    /**
    * Required Param
    * Check if a POST/GET variable is set if not throw an exception.
    *
    * @param string $name       Name of POST/GET variable.
    * @param string $type       Data type for input. String, int, float, double.
    *
    * @return value of param or throws CribzRequestException
    */
    function required_param($name, $type) {
        if (($this->get_param($name) === false) && ($this->post_param($name) === false)) {
            throw new CribzRequestException('Error POST/GET Param: '.$name.' is not set.', 001);
        }

        if ($this->get_param($name) !== false) {
            return $this->check_type($type, $this->get_param($name));
        }

        if ($this->post_param($name) !== false) {
            return $this->check_type($type, $this->post_param($name));
        }
    }

    /**
    * Check Type
    * Check for a valid type and type cast the data to the type.
    *
    * @param string $type       Type to check.
    * @param mixed  $data       Data to type cast.
    *
    * @return type cast data.
    */
    private function check_type($type, $data) {
        $type = strtolower($type);

        switch ($type) {
            case 'string':
                return (string) $data;
                break;

            case 'int':
                return (int) $data;
                break;

            case 'float':
                return (float) $data;
                break;

            case 'double':
                return (double) $data;
                break;

            case 'file':
                return $data;
                break;
        }
    }

    /**
    * Get Param
    * Check to see if a GET param is set.
    *
    * @param string $name       Name of GET param.
    *
    * @return mixed the data from the GET param or false if not set.
    */
    private function get_param($name) {
        if (isset($this->get[$name])) {
            return $this->get[$name];
        } else {
            return false;
        }
    }

    /**
    * Post Param
    * Check to see if a POST param is set.
    *
    * @param string $name       Name of POST param.
    *
    * @return mixed the data from the POST param or false if not set.
    */
    private function post_param($name) {
        if (isset($this->post[$name])) {
            return $this->post[$name];
        } else {
            return false;
        }
    }
}

class CribzRequestException extends CribzException {}
?>
