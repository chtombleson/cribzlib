<?php
spl_autoload_register(function ($class) {
    $regex = '#^CribzTemplate_([A-z a-z]+)_([A-Z a-z]+)#';

    if (preg_match($regex, $class, $matches)) {
        $folder = strtolower($matches[1]);
        $file = strtolower($matches[2]);
        $path = dirname(__FILE__).'/'.$folder.'/'.$file.'.php';

        if (file_exists($path)) {
            require_once($path);
        }
    }
});
?>
