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
* @subpackage   CribzLib
* @author       Christopher Tombleson
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
            foreach ($moduleDetails['thridparty'] as $thirdparty) {
                $path = dirname(__FILE__) . '/lib/thirdparty/' . $thridparty;
                if (file_exists($path)) {
                    require_once($path);
                } else {
                    if (!file_exists($thridparty)) {
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
