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
* @subpackage   Cribz Exception
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzException extends Exception {
    /**
    * Construct
    *
    * @param string     $message    Message for exception.
    * @param int        $code       Code number for exception.
    * @param exception  $previous   Previous exception if any.
    */
    public function __construct($message, $code=0, Exception $previous=null) {
        parent::__construct($message, $code, $previous);
    }

    /**
    * To String
    *
    * @return formatted string of exception.
    */
    public function __toString() {
        return __CLASS__ . "[{$this->code}]: {$this->message}\n";
    }
}
?>
