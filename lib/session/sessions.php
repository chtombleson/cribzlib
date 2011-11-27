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
class CribzSessions {
    function set($name, $value) {
        @session_start();
        $_SESSION[$name] = $value;
        session_write_close();
        return true;
    }

    function get($name) {
        @session_start();
        $info = $_SESSION[$name];
        session_write_close();
        return $info;
    }

    function remove($name) {
        @session_start();
        unset($_SESSION[$name]);
        session_write_close();
        return true;
    }
}
?>
