<?php
class CribzDatastore {
    private $store = array();
    private $nameindex = array();
    private $idindex = array();
    private $hashindex = array();
    private $idcount = 0;
    private $storesize = 1;
    private $validfields = array('id', 'name', 'hash', 'type', 'data', 'timecreated', 'timemodified');
    private $persistent;
    private $datafilepath;


    function __construct($size, $persistent=false, $datafilepath='') {
        $this->storesize = $size;
        $this->persistent = $persistent;
        $this->datafilepath = $datafilepath;

        if (file_exists($this->datafilepath)) {
            $this->load_json($this->datafilepath);
        }
    }

    function __destruct() {
        if ($this->persistent) {
            $json = serialize($this->store);
            file_put_contents($this->datafilepath, $json);
        }

        unset($this->store, $this->nameindex, $this->idindex, $this->hashindex);
        unset($this->idcount, $this->storesize, $this->validfields, $this->persistent);
        unset($this->datafilepath);
    }

    function load_json($path) {
        $json = file_get_contents($path);
        $data = unserialize($json);

        if ($this->is_full()) {
            throw new CribzDatastoreException("Data store is full.", 0);
        }

        if ((count($data) + (count($this->store) - 1)) >= $this->storesize) {
            throw new CribzDatastoreException("Data store is not large enough to hold all the data, resize it and try again.", 6);
        }

        $this->store = array_merge($this->store, $data);

        foreach ($this->store as $hash => $value) {
            $this->hashindex[] = $hash;
            $this->nameindex[$value->name] = $hash;
            $this->idindex[$value->id] = $hash;
        }

        $this->idcount = count($this->store);
        $this->persistent = true;
        $this->datafilepath = $path;
        return true;
    }

    function add($name, $value) {
        if ($this->is_full()) {
            throw new CribzDatastoreException("Data store is full.", 0);
        }

        if ($this->name_exists($name)) {
            throw new CribzDatastoreException("The name, {$name} is already used.", 1);
        }

        $hash = md5($name);
        $type = gettype($value);
        $id = $this->idcount + 1;

        $data = (object) array(
            'id' => $id,
            'hash' => $hash,
            'name' => $name,
            'type' => $type,
            'data' => $value,
            'timecreated' => time(),
            'timemodified' => 0,
        );

        $this->hashindex[] = $hash;
        $this->idindex[$id] = $hash;
        $this->nameindex[$name] = $hash;
        $this->store[$hash] = $data;
        $this->idcount = $id;
        return $id;
    }

    function update_by_name($name, $value) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->update_by_hash($hash, $value);
    }

    function update_by_id($id, $value) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->update_by_hash($hash, $value);
    }

    function update_by_hash($hash, $value) {
        if (!$this->hash_exists($hash)) {
            throw new CribzDatastoreException("The hash: {$hash}, does not exists in the data store.", 4);
        }

        $data = $this->store[$hash];
        $data->data = $value;
        $data->timemodified = time();
        $this->store[$hash] = $data;
        return true;
    }

    function delete_by_name($name) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->delete_by_hash($hash);
    }

    function delete_by_id($id) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->delete_by_hash($hash);
    }

    function delete_by_hash($hash) {
        if (!$this->hash_exists($hash)) {
            throw new CribzDatastoreException("The hash: {$hash}, does not exists in the data store.", 4);
        }

        unset($this->store[$hash]);
        return true;
    }

    function delete_by_names(array $names) {
        foreach ($names as $name) {
            $this->delete_by_name($name);
        }
        return true;
    }

    function delete_by_ids(array $ids) {
        foreach ($ids as $id) {
            $this->delete_by_id($id);
        }
        return true;
    }

    function delete_by_hashes(array $hashes) {
        foreach ($hashes as $hash) {
            $this->delete_by_hash($hash);
        }
        return true;
    }

    function get_by_name($name, $fields=null) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->get_by_hash($hash, $fields);
    }

    function get_by_id($id, $fields=null) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->get_by_hash($hash, $fields);
    }

    function get_by_hash($hash, $fields=null) {
        if (!$this->hash_exists($hash)) {
            throw new CribzDatastoreException("The hash: {$hash}, does not exists in the data store.", 4);
        }

        if (!empty($fields)) {
            $wantedfields = explode(',', $fields);
            $returnobj = array();

            foreach ($wantedfields as $field) {
                if (in_array($field, $this->validfields)) {
                    $returnobj[$field] = $this->store[$hash]->{$field};
                }
            }

            return empty($returnobj) ? null : (object) $returnobj;
        }

        return $this->store[$hash];
    }

    function get_by_names(array $names, $fields=null) {
        $returnarr = array();
        foreach ($names as $name) {
            $data = $this->get_by_name($name, $fields);
            if (!empty($data)) {
                $returnarr[] = $data;
            }
        }
        return empty($returnarr) ? null : $returnarr;
    }

    function get_by_ids(array $ids, $fields=null) {
        $returnarr = array();
        foreach ($ids as $id) {
            $data = $this->get_by_id($id, $fields);
            if (!empty($data)) {
                $returnarr[] = $data;
            }
        }
        return empty($returnarr) ? null : $returnarr;
    }

    function get_by_hashes(array $hashes, $fields=null) {
        $returnarr = array();
        foreach ($hashes as $hash) {
            $data = $this->get_by_hash($hash, $fields);
            if (!empty($data)) {
                $returnarr[] = $data;
            }
        }
        return empty($returnarr) ? null : $returnarr;
    }

    function is_full() {
        if (count($this->store) < $this->storesize) {
            return false;
        }

        return true;
    }

    function hash_exists($hash) {
        if (in_array($hash, $this->hashindex)) {
            return true;
        }

        return false;
    }

    function name_exists($name) {
        if (in_array($name, array_keys($this->nameindex))) {
            return true;
        }

        return false;
    }

    function id_exists($id) {
        if (in_array($id, array_keys($this->idindex))) {
            return true;
        }

        return false;
    }

    function hash_to_name($hash) {
        $name = array_search($hash, $this->nameindex);

        if ($name === false) {
            return null;
        }

        return $name;
    }

    function hash_to_id($hash) {
        $id = array_search($hash, $this->idindex);

        if ($id === false) {
            return null;
        }

        return $id;
    }

    function id_to_hash($id) {
        if ($this->id_exists($id)) {
            return $this->idindex[$id];
        }

        return null;
    }

    function name_to_hash($name) {
        if ($this->name_exists($name)) {
            return $this->nameindex[$name];
        }

        return null;
    }

    function id_to_name($id) {
        $hash = $this->id_to_hash($id);

        if (!empty($hash)) {
            $name = hash_to_name($hash);
            return $name;
        }

        return null;
    }

    function name_to_id($name) {
        $hash = $this->name_to_hash($name);

        if (!empty($hash)) {
            $id = $this->hash_to_id($hash);
            return $id;
        }

        return null;
    }

    function resize($newsize) {
        if ($newsize <= count($this->store)) {
            throw new CribzDatastoreException("Cannot shrink the size of the data store.", 5);
        }
        $this->storesize = $newsize;
    }
}
class CribzDatastoreException extends CribzException {}
?>
