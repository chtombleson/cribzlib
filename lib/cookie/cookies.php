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
class CribzCookies {
    function set($name, $value, $expire = 0, $path = '', $domain = '', $secure=false, $httponly = false) {
        if (!$expire) {
            $expire = (time() + 3600);
        }
        
        if (empty($domain)) {
            $domain = $_SERVER['SERVER_NAME'];
        }

        if (setcookie($name, $value, $expire, $path, $domain, $secure, $httponly)) {
            return true;
        }
        return false;
    }

    function get($name) {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return false;
    }

    function remove($name) {
        unset($_COOKIE[$name]);
        return true;
    }
}
?>
