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
* @package CribzLib
* @subpackage CribzCrypt
* @author Christopher Tombleson <chris@cribznetwork.com>
* @copyright Copyright 2012 onwards
*/
class CribzCrypt {
    /**
    * Hash
    * Hash a password
    *
    * @param string $password   Password to hash.
    * @param string $type       Type of hash. Options [blowfish, md5, sha512], Defaults to blowfish.
    * @return string Hashed password.
    */
    public static function hash($password, $type = 'blowfish') {
        if (CRYPT_BLOWFISH == 1 && $type == 'blowfish') {
            return self::blowfishHash($password);
        }

        if (CRYPT_SHA512 == 1 && $type == 'sha512') {
            return self::shaHash($password);
        }

        if (CRYPT_MD5 == 1 && $type == 'md5') {
            return self::md5Hash($password);
        }
    }

    /**
    * MD5 Hash
    * Create an md5 hash
    *
    * @param string $password   Password to hash.
    * @return string hashed password.
    */
    public static function md5Hash($password) {
        $salt = '$1$' . self::genSalt(8) . '$';
        return crypt($password, $salt);
    }

    /**
    * SHA Hash
    * Create a SHA512 hash
    *
    * @param string $password   Password to hash.
    * @return string hashed password.
    */
    public static function shaHash($password) {
        $salt = '$6$' . self::genSalt(12) . '$';
        return crypt($password, $salt);
    }

    /**
    * Blowfish Hash
    * Create a blowfish hash
    *
    * @param string $password   Password to hash.
    * @return string hashed password.
    */
    public static function blowfishHash($password) {
        $salt = '$2y$07$' . self::genSalt(22) .'$';
        return crypt($password, $salt);
    }

    /**
    * Compare Hash
    * Compare a password to a stored hash
    *
    * @param string $password   Unhashed password.
    * @param string $stored     Stored hashed password to check against.
    * @return true if they match or false if they do not.
    */
    public static function compareHash($password, $stored) {
        if (crypt($password, $stored) != $stored) {
            return false;
        }

        return true;
    }

    /**
    * Gen Salt
    * Generate a random salt
    *
    * @param int $length    Length of salt to generate.
    * @return string random salt.
    */
    private static function genSalt($length) {
        $chars = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        $count = count($chars);
        $salt = "";

        for ($i = 0; $i < $length; $i++) {
            $salt .= $chars[rand(0, ($count - 1))];
        }

        return (strlen($salt) > $length) ? substr($salt, 0, ($length - 1)) : $salt;
    }
}
?>
