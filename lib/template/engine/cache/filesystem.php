<?php
class CribzTemplate_Cache_Filesystem extends CribzTemplate_Cache {
    private $cachepath;
    private $cache = array();

    function __construct($cachepath) {
        $this->set_cachepath($path);
    }

    public function set_cachepath($path) {
        if (file_exists($path) && is_dir($path) && is_writable($path)) {
            $this->cachepath = realpath($path);
        } else {
            throw new CribzTemplate_CacheException("Cache path is invalid: {$path}, make sure the path exists, is a directory and is writable.", 0);
        }
    }

    public function set_cache($name, $value) {
        $namehash = md5($name);
        $contenthash = md5($value);

        if (!isset($this->cache[$namehash])) {
            $this->cache[$namehash] = (object) array(
                'name' => $name,
                'path' => $this->cachepath.'/'.$namehash.'.php',
                'contenthash' => $contenthash,
            );
            return file_put_contents($this->cache[$namehash]->path, $value);
        } else {
            if ($this->cache[$namehash]->contenthash !== $contenthash) {
                return file_put_contents($this->cache[$namehash]->path, $value);
            }
            return true;
        }
    }

    public function get_cache($name) {
        return $this->cache[md5($name)]->path;
    }

    public function in_cache($name) {
        return in_array(md5($name), array_keys($this->cache));
    }
}
?>
