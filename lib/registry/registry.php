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
* @subpackage   CribzRegistry
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzRegistry {
    /**
    * Registry
    *
    * @var array
    */
    protected static $registry = array();

    /**
    * Add
    * Add an item to the registry.
    *
    * @param string $name   Name of item.
    * @param mixed  $value  Value of item.
    * @return throws Cribz Registry Exception on error.
    */
    public static function add($name, $value) {
        if (self::exists($name)) {
            throw new CribzRegistryException("Registry item with name: " . $name . " already exists.", 0);
        }

        self::$registry[$name] = $value;
    }

    /**
    * Get
    * Get the value of an item from the registry.
    *
    * @param string $name   Name of item to get.
    * @return mixed value of item or throws Cribz Registry Exception on error.
    */
    public static function get($name) {
        if (!self::exists($name)) {
            throw new CribzRegistryException("Registry item with name: " . $name . " doesn't exist.", 1);
        }

        return self::$registry[$name];
    }

    /**
    * Remove
    * Remove an item from the registry.
    *
    * @param string $name   Name of item to remove.
    * @return throws Cribz Registry Exception on error.
    */
    public static function remove($name) {
        if (!self::exists($name)) {
            throw new CribzRegistryException("Registry item with name: " . $name . " doesn't exist.", 1);
        }

        unset(self::$registry[$name]);
    }

    /**
    * Exists
    * Check if an item exists in the registry.
    *
    * @param string $name   Name of item to check.
    * @return true if it exists or false if it doesn't exist.
    */
    public static function exists($name) {
        if (isset(self::$registry[$name])) {
            return true;
        }

        return false;
    }

    /**
    * List All
    * Lists all item in the registry.
    *
    * @return an array with details about each item in the registry or an empty array.
    */
    public static function listAll() {
        $registry = self::$registry;
        $list = array();

        if (!empty($registry)) {
            foreach ($registry as $name => $value) {
                $class = is_object($value) ? get_class($value) : null;
                $list[] = array(
                    'name'  => $name,
                    'type'  => gettype($value),
                    'class' => empty($class) ? null : $class,
                    'value' => $value,
                );
            }
        }

        return $list;
    }
}
class CribzRegistryException extends CribzException {}
?>
