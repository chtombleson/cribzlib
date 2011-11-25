<?php
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
