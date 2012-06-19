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
* @subpackage   Cribz Event Queue
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzEventQueue {
    /**
    * Events
    *
    * @var array
    */
    private $events = array();

    /**
    * Events Data
    *
    * @var array
    */
    private $eventsdata = array();

    /**
    * Add Event
    * Add event to the event queue.
    *
    * @param string $name       Name of Event
    * @param string $function   Function to call for event
    * @param array  $data       Array of data to be parsed to function (Optional)
    *
    * @return true on success
    */
    function addEvent($name, $function, $data = array()) {
        if (isset($this->events[$name])) {
            throw new CribzEventQueueException("Event with name: {$name} already exists.", 0);
        }

        if (is_array($function)) {
            if (!method_exists($function[0], $function[1])) {
                throw new CribzEventQueueException("Method: {$function[1]} does not exist in class ".get_class($function[1]).".", 1);
            }
        } else {
            if (!function_exists($function)) {
                throw new CribzEventQueueException("Function: {$function} does not exist.", 2);
            }
        }

        if (!empty($data)) {
            $this->eventsdata[$name] = $data;
        }

        $this->events[$name] = $function;
        return true;
    }

    /**
    * Process Events
    * Process all current events in the events queue
    *
    * @return true on success or array of Errors of failure
    */
    function processEvents() {
        if (!empty($this->events)) {
            $result = false;
            $errors = array();

            foreach ($this->events as $name => $function) {
                if (!empty($this->eventsdata[$name])) {
                    $result = call_user_func_array($function, $this->eventsdata[$name]);
                } else {
                    $result = call_user_func($function);
                }

                if ($result === false) {
                    $errors[] = 'Event, '.$name.': failed.';
                }
            }
            return empty($errors) ? true : $errors;
        }
    }
}
class CribzEventQueueException extends CribzException {}
?>
