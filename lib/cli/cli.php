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
* @subpackage   Cribz Cli
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzCli {

    /**
    * Parse Options
    * Parse the contents from $argv
    *
    * @param array  $args    Contents of $argv
    * @param array  $types   Array with details about options and the type. array('help' => null, 'hello' => 'string)
    * @param string $switch  Switch used of options eg. -help
    *
    * @return array of parse options
    */
    function parse_options($args, $types, $switch='-') {
        $options = array();

        foreach ($args as $key => $arg) {
            if (preg_match('#'.$switch.'[A-Za-z]+#', $arg)) {
                $arg = trim(str_replace($swtitch, '', $arg));

                if (in_array($arg, array_keys($types))) {
                    switch ($types[$arg]) {
                        case 'string':
                            $options[$arg] = (string) $args[$key + 1];
                            break;

                        case 'int':
                            $options[$arg] = (int) $args[$key + 1];
                            break;

                        default:
                            $options[$arg] = $arg;
                    }
                }
            }
        }
        return $options;
    }

    /**
    * Input
    * Read one line from STDIN
    *
    * @return string line from STDIN
    */
    function input() {
        return trim(fgets(STDIN));
    }

    /**
    * Ouput
    * Print a message to the command line
    *
    * @param string $message    Message to output
    */
    function output($message) {
        echo $message."\n";
    }
}
class CribzCliException extends CribzException {}
?>
