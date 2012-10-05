<?php
class CribzDatabaseSchema {
    private $schemaFile;
    private $database;

    function __construct(CribzDatabase $database, $schemaFile, $import = true, $export = false) {
        $this->database = $database;
        $this->schemaFile = $schemaFile;
        $this->import = $import;
        $this->export = $export;
    }

    function execute() {
        if ($this->import) {
            $import = new CribzDatabaseImportSchema($this->database, $this->schemaFile);
            $import->execute();
        } else if ($this->export) {

        }
    }
}

class CribzDatabaseSchemaException extends CribzException {}
?>
