<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzDatastore_Test extends PHPUnit_Framework_TestCase {
    protected $datastore;

    protected function setup() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('DataStore');
        $this->datastore = new CribzDatastore(1);
    }

    protected function tearDown() {
        unset($this->datastore);
    }

    public function test_add() {
        $name = 'test';
        $value = 'Hello World';
        $add = $this->datastore->add($name, $value);
        $this->assertInternalType('int', $add, 'add() didn\'t return an int.');
    }

    /**
    * @depends test_add
    */
    public function test_convert_id() {
        $this->datastore->add('test', 'Hello world');
        $id = 1;
        $name = $this->datastore->id_to_name($id);
        $hash = $this->datastore->id_to_hash($id);
        $this->assertEquals('test', $name, 'Id to name conversion returned an unexpected name.');
        $this->assertEquals(md5($name), $hash, 'Id to hash conversion returned an unexpected hash.');
    }

    /**
    * @depends test_convert_id
    */
    public function test_convert_name() {
        $this->datastore->add('test', 'Hello world');
        $name = 'test';
        $id = $this->datastore->name_to_id($name);
        $hash = $this->datastore->name_to_hash($name);
        $this->assertEquals(1, $id, 'Name to id coversion returned an unexpected id.');
        $this->assertEquals(md5($name), $hash, 'Name to hash conversion returned an unexpected hash.');
    }

    /**
    * @depends test_convert_name
    */
    public function test_convert_hash() {
        $this->datastore->add('test', 'Hello world');
        $hash = md5('test');
        $id = $this->datastore->hash_to_id($hash);
        $name = $this->datastore->hash_to_name($hash);
        $this->assertEquals(1, $id, 'Hash to id conversion returned an unexcepted id.');
        $this->assertEquals('test', $name, 'Hash to name conversion returned an unexpected name.');
    }

    /**
    * @depends test_convert_hash
    */
    public function test_update() {
        $id = $this->datastore->add('test', 'Hello world');
        $name = $this->datastore->id_to_name($id);
        $hash = $this->datastore->id_to_hash($id);
        $upid = $this->datastore->update_by_id($id, 'hello world');
        $upname = $this->datastore->update_by_name($name, 'hi world');
        $uphash = $this->datastore->update_by_hash($hash, 'Hello world');
        $this->assertTrue($upid, 'Update by id returned an unexpected value.');
        $this->assertTrue($upname, 'Update by name returned an unexpected value.');
        $this->assertTrue($uphash, 'Update by hash returned an unexpected value.');
    }


    /**
    * @depends test_update
    */
    public function test_get() {
        $id = $this->datastore->add('test', 'Hello World');
        $name = $this->datastore->id_to_name($id);
        $hash = $this->datastore->name_to_hash($name);
        $getid = $this->datastore->get_by_id($id);
        $getname = $this->datastore->get_by_name($name);
        $gethash = $this->datastore->get_by_hash($hash);
        $this->assertEquals($getname, $getid, 'Returned objects don\'t match.');
        $this->assertEquals($gethash, $getid, 'Returned objects don\'t match.');
    }
}
?>
