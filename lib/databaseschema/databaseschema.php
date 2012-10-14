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
* @subpackage   CribzDatabaseSchema
* @author       Christopher Tombleson <chris@cribznetwork.com>
* @copyright    Copyright 2012 onwards
*/
class CribzDatabaseSchema {
    /**
    * Schema File
    *
    * @var string
    */
    private $schemaFile;

    /**
    * Database
    *
    * @var CribzDatabase
    */
    private $database;

    /**
    * Constructor
    *
    * @param CribzDatabase  $database       CribzDatabase object.
    * @param string         $schemaFile     Path to schema file.
    */
    function __construct(CribzDatabase $database, $schemaFile) {
        $this->database = $database;
        $this->schemaFile = (string) $schemaFile;
    }

    /**
    * Execute
    * Run the import process.
    */
    function execute() {
        $import = new CribzDatabaseImportSchema($this->database, $this->schemaFile);
        $import->execute();
    }
}

class CribzDatabaseSchemaException extends CribzException {}
?>
