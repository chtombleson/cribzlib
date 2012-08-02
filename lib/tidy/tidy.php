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
* @subpackage   Cribz Tidy
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzTidy {
    /**
    * Clean Output
    * Tidy and format html.
    *
    * @param string $html       Html to tidy.
    * @param array  $config     Config array for tidy(Optional).
    *
    * @return tidy object;
    */
    function clean_output($html, $config = array()) {
        if (!extension_loaded('tidy')) {
            throw new CribzTidyException("Please install the tidy extension for php.", 0);
        }

        if (empty($config)) {
            $config = array(
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200
            );
        }

        $tidy = new tidy();
        $tidy->parseString($html, $config, 'utf8');
        $tidy->cleanRepair();
        return $tidy;
    }
}
class CribzTidyException extends CribzException {}
?>
