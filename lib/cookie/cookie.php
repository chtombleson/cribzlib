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
* @subpackage   CribzCookie
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzCookie {

    /**
    * Set
    * Set a cookie.
    *
    * @see http://php.net/manual/en/function.setcookie.php
    * @param string $name       Name of cookie.
    * @param string $value      Value of the cookie.
    * @param int    $expire     Unix timestamp of when the cookie will expire. Default 30 days [Optional]
    * @param string $path       The path on the server in which the cookie will be available on. [Optional]
    * @param string $domain     The domain that the cookie is available to. [Optional]
    * @param bool   $secure     Send cookie over HTTPS. [Optional]
    * @param bool   $httponly   Only available via HTTP. [Optional]
    * @return bool true on success or false on failure.
    */
    public static function set($name, $value, $expire=0, $path='/', $domain=null, $secure=false, $httponly=false) {
        if (empty($expire)) {
            // Expires in 30 days
            $expire = time() + 60 * 60 * 24 * 30;
        }

        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    /**
    * Get
    * Get the value of a cookie.
    *
    * @param string $name   Name of cookie to get value of.
    * @return string value of cookie or null if cookie doesn't exist.
    */
    public static function get($name) {
        if (self::exists($name)) {
            return $_COOKIE[$name];
        }

        return null;
    }

    /**
    * Exists
    * Check if a cookie exists.
    *
    * @param string $name   Name of cookie to check.
    * @return bool true if it exists of false if it doesn't exist.
    */
    public static function exists($name) {
        if (isset($_COOKIE[$name])) {
            return true;
        }

        return false;
    }
}
?>
