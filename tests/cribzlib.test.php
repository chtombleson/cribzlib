<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzLib_Test extends PHPUnit_Framework_TestCase {
    protected $cribzlib;

    protected function setup() {
        $this->cribzlib = new CribzLib();
    }

    protected function tearDown() {
        unset($this->cribzlib);
    }

    public function test_getModules() {
        $modules = $this->cribzlib->getModules();
        $this->assertInternalType('array', $modules, 'getModule didn\'t return an array.');
    }

    /**
    * @depends test_getModules
    */
    public function test_loadModule() {
        $modules = $this->cribzlib->getModules();

        foreach ($modules as $module) {
            $load = $this->cribzlib->loadModule($module);
            $this->assertTrue($load, 'Unable to load the '.$module.' module.');
        }
    }

    public function test_getVersion() {
        $version = $this->cribzlib->getVersion();
        $this->assertInternalType('object', $version, 'getVersion didn\'t return an object.');
    }

    public function test_moduleExists() {
        $module = 'Database';
        $exists = $this->cribzlib->moduleExists($module);
        $this->assertTrue($exists, 'moduleExists is returning false instead of true.');
    }
}
?>
