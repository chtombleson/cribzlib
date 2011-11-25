<?php
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
