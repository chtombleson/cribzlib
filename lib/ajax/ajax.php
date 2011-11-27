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
* @subpackage   Cribz Ajax
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/
class CribzAjax {

    /**
    * Route
    * Use this function to route ajax calls to functions in a class.
    *
    * @param string $class      Name of class that function is in.
    * @param string $function   Name of function in class to call.
    * @param array  $args       Arguments to be passed to function(Optional)
    *
    * @return json with error message on failure, json with return value of function.
    */
    function route($class, $function, $args = array()) {
        if (class_exists($class)) {
            if (method_exists($class, $function)) {
                $inst = new $class();
                if (!empty($args)) {
                    if ($result = call_user_func_array(array($inst, $function), $args)) {
                        $this->sendJson($result);
                    } else {
                        $this->sendJson(array('error' => 'Error calling function'));
                    }
                } else {
                    if ($result = call_user_func(array($inst, $function))) {
                        $this->sendJson($result);
                    } else {
                        $this->sendJson(array('error' => 'Error calling function'));
                    }
                }
            }
        }
    }

    /**
    * Send Json
    * Send json data
    *
    * @param array $data    Array to be json encoded then outputed.
    *
    * @return json encoded data.
    */
    function sendJson($data) {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
