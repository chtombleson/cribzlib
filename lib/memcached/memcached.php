<?php
class CribzMemcached {
    private $memcached;

    function __construct() {
        $this->memcached = new Memcached();
    }

    function addServers($servers) {
        foreach ($servers as $server) {
            $host = $server['host'];
            $port = empty($server['port']) ? 11211 : $server['port'];
            $weight = empty($server['weight']) ? 0 : $server['weight'];
            $this->addServer($host, $port, $weight);
        }
    }

    function addServer($server, $port=11211, $weight=0) {
        $this->memcached->addServer($server, $port, $weight);
    }

    function add($key, $value, $expires=0) {
        return $this->memcached->add($key, $value, $expires);
    }

    function append($key, $value) {
        return $this->memcached->append($key, $value);
    }

    function delete($key, $value) {
        return $this->memcached->delete($key, $value);
    }

    function get($key) {
        return $this->memcached->get($key);
    }

    function set($key, $value, $expires=0) {
        return $this->memcached->set($key, $value, $expires);
    }

    function getServerList() {
        $this->memcached->getServerList();
    }
}
?>
