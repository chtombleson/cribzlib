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
* @subpackage   CribzLib
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
require_once(dirname(__FILE__) . '/lib/exception/exception.php');
class CribzLib {
    protected static $versionRelease = '2.0';
    protected static $versionReleaseDate = '2012-10-03';
    protected static $modules = array(
        'Database'  => array(
            'files' => array('database/database.php'),
            'dependencies' => array('SqlGenerator')
        ),
        'Exception' => array('files' => array('exception/exception.php')),
        'SqlGenerator' => array('files' => array('sqlgenerator/sqlgenerator.php')),
        'DatabaseSchema' => array(
            'files' => array('databaseschema/databaseschema.php', 'databaseschema/import.php'),
            'dependencies' => array('SqlGenerator', 'Database')
        ),
        'Template' => array(
            'files' => array('template/template.php'),
            'thirdparty' => array('twig/lib/Twig/Autoloader.php')
        ),
    );

    public static function loadModule($name) {
        if (!self::moduleExists($name)) {
            throw new CribzLibException('Module with name: ' . $name . ', does not exist.', 0);
        }

        $moduleDetails = self::getModuleDetails($name);

        if (!empty($moduleDetails['dependencies'])) {
            foreach ($moduleDetails['dependencies'] as $module) {
                self::loadModule($module);
            }
        }

        if (!empty($moduleDetails['thirdparty'])) {
            foreach ($moduleDetails['thirdparty'] as $thirdparty) {
                $path = dirname(__FILE__) . '/lib/thirdparty/' . $thirdparty;
                if (file_exists($path)) {
                    require_once($path);
                } else {
                    if (!file_exists($thirdparty)) {
                        throw new CribzLibException('File: ' . $path . ' does not exist.', 1);
                    }

                    require_once($thirdparty);
                }
            }
        }

        foreach ($moduleDetails['files'] as $file) {
            $path = dirname(__FILE__) . '/lib/' . $file;
            if (file_exists($path)) {
                require_once($path);
            } else {
                if (!file_exists($file)) {
                    throw new CribzLibException('File: ' . $path . ' does not exist.', 1);
                }

                require_once($file);
            }
        }

        return true;
    }

    public static function addModule($name, array $files, array $dependencies = null, array $thirdparty = null) {
        if (self::moduleExists($name)) {
            throw new CribzLibException('Module with name: ' . $name . ', already exists.', 2);
        }

        self::$modules[$name] = array(
            'files' => $files,
            'dependencies' => $dependencies,
            'thirdparty' => $thirdparty,
        );
        return true;
    }

    public static function getModuleDetails($name) {
        return !empty(self::$modules[$name]) ? self::$modules[$name] : null;
    }

    public static function getModules() {
        return array_keys(self::$modules);
    }

    public static function getVersion() {
        return (object) array('release' => self::$versionRelease, 'release_date' => self::$versionReleaseDate);
    }

    public static function moduleExists($name) {
        return in_array($name, self::getModules());
    }
}

class CribzLibException extends CribzException {}
?>
