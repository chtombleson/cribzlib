<?php
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(__FILE__)).'/cribzlib.php');

class CribzCache_Test extends PHPUnit_Framework_TestCase {
    protected $cache;
    protected $cachedir = '/tmp/cache/';

    protected function setup() {
        $cribzlib = new CribzLib();
        $cribzlib->loadModule('Cache');
        $this->cache = new CribzCache($this->cachedir);
    }

    protected function tearDown() {
        unset($this->cache);
    }

    public function test_init() {
        $this->assertFileExists($this->cachedir, 'Cache Directory wasn\'t created.');
    }

    public function test_add() {
        $name = 'test';
        $namehash = md5($name);

        $content = 'Hello World';
        $contenthash = md5($content);

        $path = $this->cachedir.$namehash.'.cache';
        $cachepath = $this->cache->add($name, $content);

        $this->assertInternalType('string', $cachepath, 'add() didn\'t return a string.');
        $this->assertEquals($path, $cachepath, 'The path to the cached file is not what is expected.');
        $this->assertFileExists($path, 'Cache file wasn\'t created.');
        $this->assertEquals($namehash, basename($cachepath,'.cache'), 'Name hashes don\'t match.');

        $cachecontent = file_get_contents($path);
        $cachehash = md5($cachecontent);
        $this->assertEquals($contenthash, $cachehash, 'Content hashes don\'t match.');
    }

    /**
    * @depends test_add
    */
    public function test_iscached() {
        $name = 'test';
        $content = 'HEllo World';

        $this->cache->add($name, $content);

        $path = $this->cachedir.md5($name).'.cache';
        $cachepath = $this->cache->is_cached($name);
        $this->assertInternalType('string', $cachepath, 'is_cached() didn\'t return a string.');
        $this->assertEquals($path, $cachepath, 'The path to the cached file is not what is expected.');
    }
}
