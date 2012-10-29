<?php
/*
* Copyright (c) 2012 onwards Christopher Tombleson <chris@cribznetwork.com>
*
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
* documentation files (the "Software"), to deal in the Software without restriction, including without limitation
* the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
* and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
* TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
* THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
* CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
* DEALINGS IN THE SOFTWARE.
*/
/**
* @package      CribzLib
* @subpackage   CribzSession
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzSession {
    /**
    * Set
    * Store some info in a session.
    *
    * @param string $name   Name of session.
    * @param mixed  $value  Info to be stored.
    */
    public static function set($name, $value) {
        @session_start();
        $_SESSION[$name] = $value;
        @session_write_close();
    }

    /**
    * Get
    * Get the value from a session.
    *
    * @param string $name   Name of session to get value from.
    * @return mixed the value that is stored in the session.
    */
    public static function get($name) {
        @session_start();
        $value = $_SESSION[$name];
        @session_write_close();
        return $value;
    }

    /**
    * Exists
    * Check if a session exists.
    *
    * @param string $name   Session to check.
    * @return bool true if exists, false if it doesn't.
    */
    public static function exists($name) {
        @session_start();

        if (isset($_SESSION[$name])) {
            return true;
        }

        return false;
    }

    /**
    * Remove
    * Remove a session.
    *
    * @param string $name   Session to remove.
    */
    public static function remove($name) {
        @session_start();
        unset($_SESSION[$name]);
        @session_write_close();
    }

    /**
    * Get ID
    * Get the current session id.
    *
    * @return string session id.
    */
    public static function getId() {
        return session_id();
    }

    /**
    * Set ID
    * Set the session id.
    *
    * @param string $id     Session id.
    * @return string session id.
    */
    public static function setId($id) {
        return session_id($id);
    }

    /**
    * Regenerate Session ID
    * Rengenerate the current session id.
    *
    * @param bool $delete   Delete the old session. [Optional]
    * @return bool true on success or false on failure.
    */
    public static function regenerateId($delete=false) {
        @session_start();
        $regen = session_regenerate_id($delete);
        @session_write_close();
        return $regen;
    }
}
class CribzSessionException extends CribzException {}
?>
