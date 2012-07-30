<?php
require_once(dirname(__FILE__).'/autoloader.php');
require_once(dirname(__FILE__).'/parser.php');

class CribzTemplate_Engine {
    private $loaders = array();
    private $cache;
    private $parser;

    function __construct(array $loaders, array $cache, array $options=array()) {
        $this->set_loaders($loaders);
        $this->set_cache($cache);
        $this->parser = new CribzTemplate_Parser();
    }

    function set_loaders($loaders) {
        foreach ($loaders as $type => $options)  {
            $this->set_loader($type, $options);
        }
    }

    function set_loader($type, $options) {
        if (!isset($this->loaders[$type])) {
            $this->loaders[$type] = $options;
        } else {
            $this->loaders[$type] = array_merge($this->loader[$type], $options);
        }
    }

    function set_cache($cache) {
        $classname = 'CribzTemplate_Cache_'.key($cache);
        $this->cache = new $classname($cache[key($cache)]);
    }
}
class CribzTemplate_EngineException extends CribzException {}
?>
