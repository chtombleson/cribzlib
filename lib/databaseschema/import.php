<?php
class CribzDatabaseImportSchema {
    const INTEGER = 1;
    const NUMERIC = 2;
    const CHARACTER = 3;
    const DATETIME = 4;
    const TEXT = 5;
    const AUTOINCREMENT = 6;
    protected $types = array(
        1 => array('integer', 'smallint', 'mediumint', 'bigint'),
        2 => array('float', 'double', 'decimal'),
        3 => array('varchar', 'char'),
        4 => array('date', 'datetime', 'timestamp'),
        5 => array('text'),
        6 => array('pgsql' => 'serial', 'mysql' => 'AUTO_INCREMENT', 'sqlite' => 'AUTOINCREMENT'),
    );
    protected $database;
    protected $schemaFile;

    function __construct(CribzDatabase $database, $schemaFile) {
        if (!file_exists($schemaFile)) {
            throw new CribzDatabaseImportSchemaException('Schema file: ' . $schemaFile . 'does not exist.', 0);
        }

        $this->database = $database;
        $this->schemaFile = $schemaFile;
    }

    function execute() {
        $schema = $this->parse();
        $queries = $this->buildQueries($schema);
        $this->executeQueries($queries);
    }

    private function parse() {
        $allowed = $this->getAllowedTypes();
        $xml = simplexml_load_file($this->schemaFile);
        $tables = array();

        foreach ($xml->table as $table) {
            $data = new stdClass();
            $data->name = $table->attributes()->name;
            $data->columns = array();
            $data->keys = array();
            $data->records = array();

            foreach ($table->columns->column as $column) {
                if (!in_array(strval($column->attributes()->type), $allowed)) {
                    $msg  = 'Invalid data type: ' . strval($column->attributes()->type);
                    $msg .= '. Please use the following valid data types: ' . implode(', ', $allowed) . '.';
                    throw new CribzDatabaseImportSchemaException($msg, 1);
                }

                $data->columns[] = (object) array(
                    'name' => strval($column->attributes()->name),
                    'type' => strval($column->attributes()->type),
                    'size' => !empty($column->attributes()->size) ? strval($column->attributes()->size) : null,
                    'null' => !empty($column->attributes()->null) ? strval($column->attributes()->null) : null,
                    'default' => isset($column->attributes()->default) ? strval($column->attributes()->default) : null,
                    'autoincrement' => !empty($column->attributes()->autoincrement) ? strval($column->attributes()->autoincrement) : null,
                    'precision' => !empty($column->attributes()->precision) ? strval($column->attributes()->precision) : null,
                );
            }

            foreach ($table->keys->key as $key) {
                $data->keys[] = (object) array(
                    'type' => strval($key->attributes()->type),
                    'column' => strval($key->attributes()->column),
                    'ref_table' => !empty($key->attributes()->referencetable) ? strval($key->attributes()->referencetable) : null,
                    'ref_column' => !empty($key->attributes()->referencecolumn) ? strval($key->attributes()->referencecolumn) : null,
                );
            }

            foreach ($table->records->record as $record) {
                $recordData = array();
                foreach ($record->children() as $name => $value) {
                    $recordData[$name] = strval($value);
                }
                $data->records[] = (object) $recordData;
            }

            $tables[] = $data;
        }

        switch ($this->database->get_driver()) {
            case 'mysql':
                $this->parseMysql($tables);
                break;

            case 'pgsql':
                $this->parsePgsql($tables);
                break;

            case 'sqlite':
                $this->parseSqlite($tables);
                break;
        }

        return $tables;
    }

    private function parseMysql(&$tables) {

    }

    private function parsePgsql(&$tables) {
        foreach ($tables as &$table) {
            foreach ($table->columns as &$column) {
                if ($column->autoincrement) {
                    $column->type = $this->types[self::AUTOINCREMENT]['pgsql'];
                }

                if ($column->type == 'double') {
                    $column->type = 'double precision';

                    if (!empty($column->size)) {
                        $column->size = null;
                    }

                    if (!empty($column->precision)) {
                        $column->precision = null;
                    }
                }
            }
        }
    }

    private function parseSqlite(&$tables) {

    }

    private function buildQueries(array $schema) {
        $queries = array('tables' => array(), 'records' => array());

        foreach ($schema as $table) {
            $queries['tables'][]  = $this->getTableQuery($table);

            foreach ($table->records as $record) {
                $queries['records'][] = $this->getRecordQuery($table->name, $record);
            }
        }

        return $queries;
    }

    private function executeQueries($queries) {
        $this->database->connect();

        if (!empty($queries['tables'])) {
            foreach ($queries['tables'] as $sql) {
                $this->database->execute_sql($sql);
            }
        }

        if (!empty($queries['records'])) {
            foreach ($queries['records'] as $record) {
                $this->database->execute_sql($record['query'], $record['parameters']);
            }
        }
    }

    private function getTableQuery($table) {
        $fields = array();
        $pk = '';
        $fk = array();

        foreach ($table->columns as $column) {
            $fields[$column->name] = array(
                'type' => $column->type,
                'size' => (int) $column->size,
                'default' => $column->default,
                'autoincrement' => ($column->autoincrement == 'true') ? true : false,
            );

            if (!is_null($column->null)) {
                $fields[$column->name]['null'] = ($column->autoincrement == 'true') ? true : false;
            }
        }

        foreach ($table->keys as $key) {
            if ($key->type == 'primary') {
                $pk = $key->column;
            }

            if ($key->type == 'unique') {
                $fields[$key->column]['unique'] = true;
            }

            if ($key->type == 'foreign') {
                if (empty($key->ref_table) || empty($key->ref_column)) {
                    throw new CribzDatabaseImportSchemaException('Foreign keys require referencetable & referencecolumn attributes.', 2);
                }
                $fk[$key->ref_table] = $key->ref_column;
            }
        }

        if (empty($pk)) {
            throw new CribzDatabaseImportSchemaException('No primary key defined for table, ' . $table->name, 3);
        }

        return CribzSqlGenerator::createTable($this->database->get_driver(), $table->name, $fields, $pk, $fk);
    }

    private function getRecordQuery($table, $record) {
        $parameters = array();
        $fields = array();
        foreach ($record as $name => $value) {
            $fields[] = $name;
            $parameters[] = $value;
        }

        $query = CribzSqlGenerator::insert($table, $fields);
        return array('query' => $query, 'parameters' => $parameters);
    }

    public function getAllowedTypes() {
        return array(
            'integer', 'smallint', 'mediumint', 'bigint',
            'float', 'double', 'decimal', 'varchar', 'char',
            'date', 'datetime', 'timestamp', 'text',
        );
    }
}

class CribzDatabaseImportSchemaException extends CribzException {}
?>
