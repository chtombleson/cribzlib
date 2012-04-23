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
* @subpackage   Cribz Sessions
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzSessions {
    /**
    * Set
    * Set session.
    *
    * @param string $name   Name for session.
    * @param mixed  $value  Value of session.
    *
    * @return true
    */
    function set($name, $value) {
        @session_start();
        $_SESSION[$name] = $value;
        session_write_close();
        return true;
    }

    /**
    * Get
    * Get Session.
    *
    * @param string $name   Name of session.
    *
    * @return session data
    */
    function get($name) {
        @session_start();
        $info = $_SESSION[$name];
        session_write_close();
        return $info;
    }

    /**
    * Remove
    * Remove a session.
    *
    * @param string $name   Name of session to remove.
    *
    * @return true
    */
    function remove($name) {
        @session_start();
        unset($_SESSION[$name]);
        session_write_close();
        return true;
    }
}
?>
