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
* @subpackage   Cribz Controller
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzController {
    /**
    * Bound
    *
    * @var array
    */
    private $bound = array();

    /**
    * Action Var
    *
    * @var string
    */
    private $actionvar;

    /**
    * Constructor
    * Create a new instance of Cribz Controller.
    *
    * @param string $actionvar  Name of variable to look for in POST/GET data that contains the info for what controller to use.
    */
    function __construct($actionvar='action') {
        $this->actionvar = $actionvar;
    }

    /**
    * Bind
    * Bind an action to a function.
    *
    * @param string $action     Action to trigger function.
    * @param mixed  $function   Function to call.
    *
    * @return true on success or throws CribzControllerException on failure.
    */
    function bind($action, $function) {
        if (isset($this->bound[$action])) {
            throw new CribzControllerException("Function is already binded to {$action}.", 1);
        }

        if (function_exists($function)) {
            $this->bound[$action] = $function;
            return true;
        } else {
            throw new CribzControllerException("The function does not exist, {$function}.", 2);
        }
    }

    /**
    * Run
    * Check if any controllers need to be ran.
    */
    function run() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Request');
        $request = new CribzRequest();

        $action = $request->optional_param($this->actionvar, 'string', '');

        if (!empty($action)) {
            if (isset($this->bound[$action])) {
                $function = $this->bound[$action];
                call_user_func($function);
            } else {
                throw new CribzControllerException("The action {$action} does not exist.", 3);
            }
        }
    }
}
class CribzControllerException extends CribzException {};
?>
