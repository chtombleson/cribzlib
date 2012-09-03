<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzDatabase_Mysql_Test extends PHPUnit_Framework_TestCase {
    protected $database;
    protected $dbhost = 'HOST';
    protected $dbuser = 'USER';
    protected $dbpass = 'PASS';
    protected $dbname = 'NAME';
    protected $dbport = null;
    protected $record = array(
        'name' => 'test',
        'value' => 'testing insert.',
        'created' => 20120903,
    );

    protected function setup() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Database');
        $this->database = new CribzDatabase('mysql', $this->dbhost, $this->dbname, $this->dbuser, $this->dbpass, $this->dbport);
    }

    protected function tearDown() {
        unset($this->database);
    }

    public function test_connect() {
        $conn = $this->database->connect();
        $this->assertNull($conn, 'Unable to connect to mysql database.');
    }

    /**
    * @depends test_connect
    */
    public function test_restore() {
        $this->database->connect();
        $restore = $this->database->restore_sql_file(dirname(__FILE__).'/files/test_db.mysql.sql');
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
        $id = $this->database->lastInsertId('test', 'id');
        $this->assertInternalType('int', $id, 'Last insert id is not an int, it should though.');
    }

    /**
    * @depends test_lastid
    */
    public function test_update() {
        $this->database->connect();
        $id = $this->database->lastInsertId('test', 'id');
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
}
?>
