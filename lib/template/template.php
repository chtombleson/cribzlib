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
* @subpackage   CribzTemplate
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
Twig_Autoloader::register();
class CribzTemplate extends Twig_Environment{

    /**
    * Constructor
    * Create a new instance of Cribz Template.
    *
    * This class is just a simple class that extends the default twig Environment.
    * It provides an easy and fast way to use twig.
    * This custom constructor creates the Loader and initalizes the Twig Evnironment.
    *
    * @param string $path           Path to where templates are stored.
    * @param string $cache          Path to where you want to cache the compiled classes.
    * @param bool   $debug          Activate Twig's debug mode. [Optional]
    * @param bool   $autoreload     Turn on auto reloading of templates. Is activated if debug mode is on. [Optional]
    * @param bool   $strict         Activate Twig's strict variable option. [Optional]
    * @param mixed  $autoescape     Activate autoescaping. See Twig Documentation for options. [Optional]
    */
    function __construct($path, $cache, $debug=false, $autoreload=null, $strict=false, $autoescape=true) {
        Twig_Autoloader::register();

        $options = array(
            'cache' => $cache,
            'debug' => $debug,
            'auto_reload' => $autoreload,
            'strict_variables' => $strict,
            'autoescape' => $autoescape,
        );

        $loader = new Twig_Loader_Filesystem($path);
        parent::__construct($loader, $options);
    }
}
?>
