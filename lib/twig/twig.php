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
class CribzTwig {
    /**
    * Twig
    *
    * @var Twig Object
    */
    private $twig;

    /**
    * Twig Loader
    *
    * @var Twig Loader
    */
    private $twig_loader;

    /**
    * Twig Path
    *
    * @var string
    */
    private $twig_path;

    /**
    * Template Directory
    *
    * @var string
    */
    private $templatedir;

    /**
    * Cache Directory
    *
    * @var string
    */
    private $cachedir;

    /**
    * Debug
    *
    * @var bool
    */
    private $debug;

    /**
    * Constructor
    * Create a new instance of Cribz Twig.
    *
    * @param string $twigpath       Path to directory that contains twigs Autoloader.php
    * @param string $templatedir    Path to directory that contains the template files.
    * @param string $cachedir       Path to cache directory. (Optional)
    * @param bool   $debug          Put Twig into debug mode
    */
    function __construct($twigpath, $templatedir, $cachedir='', $debug=false) {
        $twigpath = rtrim($twigpath, '/').'/';
        $templatedir = rtrim($templatedir, '/').'/';
        $cachedir = rtrim($cachedir, '/').'/';

        if (!file_exists($twigpath.'Autoloader.php')) {
            throw new CribzTwigException("Could not find path to Twig's Autoloader.php,  please check the path.", 0);
        }

        if (!file_exists($templatedir)) {
            throw new CribzTwigException("Template directory does not exist, {$templatedir}.", 1);
        }

        if (!empty($cachedir) && !file_exists($cachedir)) {
            throw new CribzTwigException("Cache directory does not exist, {$cachedir}.", 2);
        }

        $this->twig_path = $twigpath;
        $this->templatedir = $templatedir;
        $this->cachedir = $cachedir;
        $this->debug = $debug;
    }

    /**
    * Init
    * Initalize a twig environment.
    */
    function init() {
        require_once($this->twig_path.'Autoloader.php');
        Twig_Autoloader::register();

        $this->twig_loader = new Twig_Loader_Filesystem($this->templatedir);

        $options = array();
        if (!empty($this->cachedir)) {
            $options['cache'] = $this->cachedir;
        }

        if ($this->debug) {
            $options['debug'] = $this->debug;
        }

        $this->twig = new Twig_Environment($this->twig_loader, $options);
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
        $this->twig->addExtension(new Twig_Extension_Sandbox($policy));
    }

    /**
    * Init Escaper Ext
    * Initalize Twigs Escaper Extension.
    *
    * @param bool $global   Turn on global escaping. (Optional)
    */
    function init_escaper_ext($global=true) {
        $this->twig->addExtension(new Twig_Extension_Escaper($global));
    }

    /**
    * Init Optimizer Ext
    * Initalize Twigs Optimizer Extension.
    *
    * @param int $optimize  Twigs Optimizer option. (Optional)
    */
    function init_optimizer_ext($optimize=null) {
        if (!empty($optimize)) {
            $this->twig->addExtension(new Twig_Extension_Optimizer($optimize));
        } else {
             $this->twig->addExtension(new Twig_Extension_Optimizer());
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

        return $this->twig->render($template, $data);
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

        $this->twig->display($template, $data);
    }

    /**
    * Get Twig
    * Return the Twig Object.
    *
    * @return object Instance of Twig.
    */
    function get_twig() {
        return $this->twig;
    }
}
class CribzTwigException extends CribzException {}
?>
