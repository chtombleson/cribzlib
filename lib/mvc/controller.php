<?php
class CribzController {
    private $bound = array();
    private $actionvar;

    function __construct($actionvar='action') {
        $this->actionvar = $actionvar;
    }

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
