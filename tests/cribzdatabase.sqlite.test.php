<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzDatabase_Sqlite_Test extends PHPUnit_Framework_TestCase {
    protected $database;
    protected $db = 'files/sqlite.db';
    protected $record = array(
        'name' => 'test',
        'value' => 'testing insert.',
        'created' => 20120903,
    );

    protected function setup() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Database');
        $this->database = new CribzDatabase('sqlite', '', dirname(__FILE__).'/'.$this->db, '', '');
    }

    protected function tearDown() {
        unset($this->database);
    }

    public function test_connect() {
        $conn = $this->database->connect();
        $this->assertNull($conn, 'Unable to connect to sqlite database.');
    }

    /**
    * @depends test_connect
    */
    public function test_restore() {
        $this->database->connect();
        $restore = $this->database->restore_sql_file(dirname(__FILE__).'/files/test_db.sqlite.sql');
        $this->assertTrue($restore, 'Unable to restore sql file.');
    }

    /**
    * @depends test_restore
    */
    public function test_insert() {
        $this->database->connect();
        $insert = $this->database->insert('test', (object) $this->record);
        $this->assertTrue($insert, 'Unable to insert record.');
    }

    /**
    * @depends test_insert
    */
    public function test_lastid() {
        $this->database->connect();
        $id = $this->database->lastInsertId();
        $this->assertEmpty($id, 'Could not get last insert id.');
    }

    /**
    * @depends test_lastid
    */
    public function test_update() {
        $this->database->connect();
        $id = $this->database->lastInsertId();
        $record = (object) $this->record;
        $record->id = $id;
        $record->modified = time();

        $update = $this->database->update('test', $record);
        $this->assertTrue($update, 'Unable to update record.');
    }

    /**
    * @depends test_update
    */
    public function test_select() {
        $this->database->connect();
        $select = $this->database->select('test');
        $this->assertTrue($select, 'Unable to select record.');
    }

    /**
    * @depends test_select
    */
    public function test_fetch() {
        $this->database->connect();
        $select = $this->database->select('test');
        $fetch = $this->database->fetch();
        $this->assertNotEmpty($fetch, 'Fetch returned empty record.');
        $this->assertArrayHasKey('name', (array) $fetch, 'Record doesn\'t contain the name field.');
    }

    /**
    * @depends test_fetch
    */
    public function test_delete() {
        $this->database->connect();
        $delete = $this->database->delete('test', array('id' => 1));
        $this->assertTrue($delete, 'Unable to delete record.');
    }

    /**
    * @depends test_delete
    */
    public function test_table_exists() {
        $this->database->connect();
        $exists = $this->database->check_table_exists('test');
        $this->assertTrue($exists, 'Test table doesn\'t exist when it should.');
    }
}
?>
