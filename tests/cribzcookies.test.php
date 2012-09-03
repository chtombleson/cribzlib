<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzCookies_Test extends PHPUnit_Framework_TestCase {
    protected $cookie;

    protected function setup() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Cookie');
        $this->cookie = new CribzCookies();
    }

    protected function tearDown() {
        unset($this->cookie);
    }

    public function test_set() {
        $set = $this->cookie->set('test', 'hello world', 0, '/', 'localhost');
        $this->assertTrue($set, 'Unable to set cookie.');
    }

    /**
    * @depends test_set
    */
    public function test_get() {
        $this->cookie->set('test', 'hello world', 0, '/', 'localhost');
        $get = $this->cookie->get('test');
        $this->assertInternalType('string', $get, 'get() didn\'t return what is expected.');
        $this->assertEquals('hello world', $get, 'Cookie data doesn\'t match.');
    }
}
?>
