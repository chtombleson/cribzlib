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
/**
* @package      CribzLib
* @subpackage   Cribz Twig
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
require_once(dirname(__FILE__).'/lib/twig/lib/Twig/Autoloader.php');
Twig_Autoloader::register();

class CribzTwig extends Twig_Environment {

    /**
    * Constructor
    * Create a new instance of Cribz Twig.
    *
    * @param string $templatedir    Path to directory that contains the template files.
    * @param string $cachedir       Path to cache directory. (Optional)
    * @param bool   $debug          Put Twig into debug mode
    */
    function __construct($templatedir, $cachedir='', $debug=false) {
        $templatedir = realpath($templatedir).'/';
        $cachedir = realpath($cachedir).'/';

        if (!file_exists($templatedir)) {
            throw new CribzTwigException("Template directory does not exist, {$templatedir}.", 1);
        }

        if (!empty($cachedir) && !file_exists($cachedir)) {
            throw new CribzTwigException("Cache directory does not exist, {$cachedir}.", 2);
        }

        $options = array();
        $options['cache'] = $cachedir;
        $options['debug'] = $debug;

        $loader = new Twig_Loader_Filesystem($templatedir);
        parent::__construct($loader, $options);
    }

    /**
    * Init Sandbox Ext
    * Initalize Twigs Sandbox Extension.
    *
    * @param array $tags        Array of allowed tags
    * @param array $filters     Array of allowed filters
    * @param array $methods     Array of allowed methods
    * @param array $properties  Array of allowed properties
    * @param array $functions   Array of allowed functions
    */
    function init_sandbox_ext($tags, $filters, $methods, $properties, $functions) {
        $policy = new Twig_Sandbox_SecurityPolicy($tags, $filters, $methods, $properties, $functions);
        $this->addExtension(new Twig_Extension_Sandbox($policy));
    }

    /**
    * Init Escaper Ext
    * Initalize Twigs Escaper Extension.
    *
    * @param bool $global   Turn on global escaping. (Optional)
    */
    function init_escaper_ext($global=true) {
        $this->addExtension(new Twig_Extension_Escaper($global));
    }

    /**
    * Init Optimizer Ext
    * Initalize Twigs Optimizer Extension.
    *
    * @param int $optimize  Twigs Optimizer option. (Optional)
    */
    function init_optimizer_ext($optimize=null) {
        if (!empty($optimize)) {
            $this->addExtension(new Twig_Extension_Optimizer($optimize));
        } else {
             $this->addExtension(new Twig_Extension_Optimizer());
        }
    }

    /**
    * Render
    * Render a template.
    *
    * @param string $template   Template file to render.
    * @param array  $data       Data to parse into the template. (Optional)
    *
    * @return string of the compiled template.
    */
    function render($template, $data=array()) {
        if (!file_exists($this->templatedir.$template) && !is_file($this->templatedir.$template)) {
            throw new CribzTwigException("Template does not exists or is not a file, {$this->templatedir}{$template}.", 3);
        }

        return $this->render($template, $data);
    }

    /**
    * Display
    * Render then directly output a template.
    *
    * @param string $template   Template file to render and display.
    * @param array  $data       Data to parse into the template. (Optional)
    */
    function display($template, $data=array()) {
        if (!file_exists($this->templatedir.$template) && !is_file($this->templatedir.$template)) {
            throw new CribzTwigException("Template does not exists or is not a file, {$this->templatedir}{$template}.", 3);
        }

        $this->display($template, $data);
    }
}
class CribzTwigException extends CribzException {}
?>
