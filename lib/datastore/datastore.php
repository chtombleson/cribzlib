<?php
/*
*   This file is part of CribzLib.
*
*    CribzLib is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    CribzLib is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with CribzLib.  If not, see <http://www.gnu.org/licenses/>.
*/
/**
* @package      CribzLib
* @subpackage   Cribz Datastore
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzDatastore {
    /**
    * Store
    *
    * @var array
    */
    private $store = array();

    /**
    * Name Index
    *
    * @var array
    */
    private $nameindex = array();

    /**
    * Id Index
    *
    * @var array
    */
    private $idindex = array();

    /**
    * Hash Index
    *
    * @var array
    */
    private $hashindex = array();

    /**
    * Type Index
    *
    * @var array
    */
    private $typeindex = array();

    /**
    * ID Count
    *
    * @var int
    */
    private $idcount = 0;

    /**
    * Store Size
    *
    * @var int
    */
    private $storesize = 1;

    /**
    * Valid Fields
    *
    * @var array
    */
    private $validfields = array('id', 'name', 'hash', 'type', 'data', 'timecreated', 'timemodified');

    /**
    * Persistent
    *
    * @var bool
    */
    private $persistent;

    /**
    * Data File Path
    *
    * @var string
    */
    private $datafilepath;

    /**
    * Construct
    * Create a new instance of Cribz Datastore.
    *
    * @param int   $size         The size of the data store, how many items can be stored.
    * @param bool  $persistent   If true the current datastore will be wriiten to file when class is destroyed.
    * @param strng $datafilepath Path to file to write data to.
    * @param bool  $load_data    Load data from data file.
    */
    function __construct($size, $persistent=false, $datafilepath='', $load_data=false) {
        $this->storesize = $size;
        $this->persistent = $persistent;
        $this->datafilepath = $datafilepath;

        if ($load_data) {
            if (file_exists($this->datafilepath) && is_readable($this->datafilepath)) {
                $this->load_data($this->datafilepath);
            } else {
                throw new CribzDatastoreException("Data file must exist and be readable.", 7);
            }
        }
    }

    /**
    * Destruct
    * Destory the class.
    */
    function __destruct() {
        if ($this->persistent) {
            $this->save_data($this->datafilepath);
        }

        unset($this->store, $this->nameindex, $this->idindex, $this->hashindex);
        unset($this->idcount, $this->storesize, $this->validfields, $this->persistent);
        unset($this->datafilepath);
    }

    /**
    * Load Data
    * Load data the has been written to a file.
    *
    * @param string $path   Path to file to load.
    * @param bool   $resize If true resize the data store to allow for data to be loaded.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function load_data($path, $resize=false) {
        $data = file_get_contents($path);
        $stored_data = unserialize($data);

        if (!$resize) {
            if ($this->is_full()) {
                throw new CribzDatastoreException("Data store is full.", 0);
            }

            if ((count($stored_data) + count($this->store)) >= $this->storesize) {
                throw new CribzDatastoreException("Data store is not large enough to hold all the data, resize it and try again.", 6);
            }
        } else {
            if ($this->is_full()) {
                $newsize = count($stored_data) + $this->storesize;
                $this->resize($newsize);
            } else if ((count($stored_data) + count($this->store)) >= $this->storesize) {
                $newsize = count($stored_data) + count($this->store);
                $this->resize($newsize);
            }
        }

        $this->store = array_merge($this->store, $stored_data);

        foreach ($this->store as $hash => $value) {
            $type = strtolower($value->type);
            if (!isset($this->typeindex[$type])) {
                $this->typeindex[$type] = array();
            }

            if (!$this->id_exists($value->id) && !$this->name_exists($value->name)){
                $this->hashindex[] = $hash;
                $this->nameindex[$value->name] = $hash;
                $this->idindex[$value->id] = $hash;
                $this->typeindex[$type][] = $hash;
            } else if ($this->id_exists($value->id)) {
                $id = count($this->store) + 1;
                $this->hashindex[] = $hash;
                $this->nameindex[$value->name] = $hash;
                $this->idindex[$id] = $hash;
                $this->store[$hash]->id = $id;
                $this->typeindex[$type][] = $hash;
            }
        }

        $this->idcount = count($this->store);
        $this->persistent = true;
        $this->datafilepath = $path;
        return true;
    }

    /**
    * Save Data
    * Write data out to file.
    *
    * @param string $path   Path to file to write to.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function save_data($path) {
        $data = serialize($this->store);
        if (!file_put_contents($this->datafilepath, $data)) {
            throw new CribzDatastoreException("Could not write data to data file.", 8);
        }
        return true;
    }

    /**
    * Add
    * Adds to the data store.
    *
    * @param string $name   Name of item.
    * @param mixed  $value  Data to store.
    *
    * @return int id of stored item or throws CribzDatastoreException.
    */
    function add($name, $value) {
        if ($this->is_full()) {
            throw new CribzDatastoreException("Data store is full.", 0);
        }

        if ($this->name_exists($name)) {
            throw new CribzDatastoreException("The name, {$name} is already used.", 1);
        }

        $hash = md5($name);
        $type = strtolower(gettype($value));
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

        if (!isset($this->typeindex[$type])) {
            $this->typeindex[$type] = array();
        }

        $this->hashindex[] = $hash;
        $this->idindex[$id] = $hash;
        $this->nameindex[$name] = $hash;
        $this->typeindex[$type][] = $hash;
        $this->store[$hash] = $data;
        $this->idcount = $id;
        return $id;
    }

    /**
    * Update By Name
    * Update an item in the data store by name.
    *
    * @param string $name   Name of item to update.
    * @param mixed  $value  Updated data.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function update_by_name($name, $value) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->update_by_hash($hash, $value);
    }

    /**
    * Update By ID
    * Update an item in the data store by ID.
    *
    * @param int    $id     ID of item to update.
    * @param mixed  $value  Updated data.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function update_by_id($id, $value) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->update_by_hash($hash, $value);
    }

    /**
    * Update By Hash
    * Update an item in the data store by hash.
    *
    * @param string $hash   Hash of item to update.
    * @param mixed  $value  Updated data.
    *
    * @return true on success or throws CribzDatastoreException.
    */
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

    /**
    * Delete By Name
    * Delete an item by name.
    *
    * @param string $name   Name of item to delete.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function delete_by_name($name) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->delete_by_hash($hash);
    }

    /**
    * Delete By ID
    * Delete an item by ID.
    *
    * @param int $id ID of item to delete.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function delete_by_id($id) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->delete_by_hash($hash);
    }

    /**
    * Delete By Hash
    * Delete an item by hash.
    *
    * @param string $hash   Hash of item to delete.
    *
    * @return true on success or throws CribzDatastoreException.
    */
    function delete_by_hash($hash) {
        if (!$this->hash_exists($hash)) {
            throw new CribzDatastoreException("The hash: {$hash}, does not exists in the data store.", 4);
        }

        unset($this->store[$hash]);
        return true;
    }

    /**
    * Delete By Names
    * Delete multiple items by name.
    *
    * @param array $names   Array of names to delete.
    * @see Delete By Name
    */
    function delete_by_names(array $names) {
        foreach ($names as $name) {
            $this->delete_by_name($name);
        }
        return true;
    }

    /**
    * Delete By IDs
    * Delete multiple items by id.
    *
    * @param array $ids   Array of ids to delete.
    * @see Delete By ID
    */
    function delete_by_ids(array $ids) {
        foreach ($ids as $id) {
            $this->delete_by_id($id);
        }
        return true;
    }

    /**
    * Delete By Hashes
    * Delete multiple items by hash.
    *
    * @param array $hashes   Array of hashes to delete.
    * @see Delete By Hash
    */
    function delete_by_hashes(array $hashes) {
        foreach ($hashes as $hash) {
            $this->delete_by_hash($hash);
        }
        return true;
    }

    /**
    * Get By Name
    * Get an item by name.
    *
    * @param string $name    Name of item to get.
    * @param string $fields  Fields to get. (Optional)
    *
    * @return stdclass with the data on success or null on failure, throws CribzDatastoreException.
    */
    function get_by_name($name, $fields=null) {
        if (!$this->name_exists($name)) {
            throw new CribzDatastoreException("The name: {$name}, does not exists in the data store.", 2);
        }

        $hash = $this->name_to_hash($name);
        return $this->get_by_hash($hash, $fields);
    }

    /**
    * Get By ID
    * Get an item by ID.
    *
    * @param string $id      ID of item to get.
    * @param string $fields  Fields to get. (Optional)
    *
    * @return stdclass with the data on success or null on failure, throws CribzDatastoreException.
    */
    function get_by_id($id, $fields=null) {
        if (!$this->id_exists($id)) {
            throw new CribzDatastoreException("The id: {$id}, does not exists in the data store.", 3);
        }

        $hash = $this->id_to_hash($id);
        return $this->get_by_hash($hash, $fields);
    }

    /**
    * Get By Hash
    * Get an item by hash.
    *
    * @param string $hash    Hash of item to get.
    * @param string $fields  Fields to get. (Optional)
    *
    * @return stdclass with the data on success or null on failure, throws CribzDatastoreException.
    */
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

    /**
    * Get By Names
    * Get multiple items by name.
    *
    * @param array  $names   Array of names.
    * @param string $fields  Fields to get. (Optional)
    * @see Get By Name
    */
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

    /**
    * Get By IDs
    * Get multiple items by ID.
    *
    * @param array  $ids     Array of ids.
    * @param string $fields  Fields to get. (Optional)
    * @see Get By ID
    */
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

    /**
    * Get By Hashes
    * Get multiple items by hash.
    *
    * @param array  $hashes  Array of hashes.
    * @param string $fields  Fields to get. (Optional)
    * @see Get By Hash
    */
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

    /**
    * Is Full
    * Checks to see if the data store is full.
    *
    * @return true if the data store is full or false if there is still room.
    */
    function is_full() {
        if (count($this->store) < $this->storesize) {
            return false;
        }

        return true;
    }

    /**
    * Hash Exists
    * Checks to see if the hash exists.
    *
    * @param string $hash   Hash to check.
    *
    * @return true if hash exists or false if it does not exist.
    */
    function hash_exists($hash) {
        if (in_array($hash, $this->hashindex)) {
            return true;
        }

        return false;
    }

    /**
    * Name Exists
    * Checks to see if the name exists.
    *
    * @param string $name   Name to check.
    *
    * @return true if name exists or false if it does not exist.
    */
    function name_exists($name) {
        if (in_array($name, array_keys($this->nameindex))) {
            return true;
        }

        return false;
    }

    /**
    * ID Exists
    * Checks to see if the id exists.
    *
    * @param int $id    ID to check.
    *
    * @return true if id exists or false if it does not exist.
    */
    function id_exists($id) {
        if (in_array($id, array_keys($this->idindex))) {
            return true;
        }

        return false;
    }

    /**
    * Hash To Name
    * Get the name of the item with specified hash.
    *
    * @param string $hash   Hash of item you wat to get the name of.
    *
    * @return string name of item on success or null if name not found.
    */
    function hash_to_name($hash) {
        $name = array_search($hash, $this->nameindex);

        if ($name === false) {
            return null;
        }

        return $name;
    }

    /**
    * Hash To ID
    * Get the id of the item with specified hash.
    *
    * @param string $hash   Hash of item you wat to get the id of.
    *
    * @return int id of item on success or null if id not found.
    */
    function hash_to_id($hash) {
        $id = array_search($hash, $this->idindex);

        if ($id === false) {
            return null;
        }

        return $id;
    }

    /**
    * ID To Hash
    * Get the hash of the item with specified ID.
    *
    * @param int $id    ID of item you want to get hash of.
    *
    * @return string hash of item on success or null if ID not found.
    */
    function id_to_hash($id) {
        if ($this->id_exists($id)) {
            return $this->idindex[$id];
        }

        return null;
    }

    /**
    * Name To Hash
    * Get the hash of the item with specified name.
    *
    * @param string $name   Name of item you want to get hash of.
    *
    * @return string hash of item on success or null if name not found.
    */
    function name_to_hash($name) {
        if ($this->name_exists($name)) {
            return $this->nameindex[$name];
        }

        return null;
    }

    /**
    * ID To name
    * Get the name of the item with specified ID.
    *
    * @param int $id    ID of item you want to get the name of.
    *
    * @return string name of item on success or null if id does not exists.
    */
    function id_to_name($id) {
        $hash = $this->id_to_hash($id);

        if (!empty($hash)) {
            $name = $this->hash_to_name($hash);
            return $name;
        }

        return null;
    }

    /**
    * Name To ID
    * Get ID of item with specified name.
    *
    * @param string $name   Name of item you want to get ID of.
    *
    * @return int id of item onn success or null if name not found.
    */
    function name_to_id($name) {
        $hash = $this->name_to_hash($name);

        if (!empty($hash)) {
            $id = $this->hash_to_id($hash);
            return $id;
        }

        return null;
    }

    /**
    * Resize
    * Resize the data store.
    *
    * @param int $newsize   New size of data store.
    *
    * @return throws CribzDatastoreException on error.
    */
    function resize($newsize) {
        if ($newsize <= count($this->store)) {
            throw new CribzDatastoreException("Cannot shrink the size of the data store.", 5);
        }
        $this->storesize = $newsize;
    }

    /**
    * Get Names By Type
    * Get an array of names based on the type. 
    * eg string, object, array.
    *
    * @param string $type   PHP type.
    * 
    * @return array of names on success or null if nothing found.
    */
    function get_names_by_type($type) {
        $hashes = $this->get_hashes_by_type($type);
        $returnarr = array();
        foreach ($hashes as $hash) {
            $returnarr[] = $this->hash_to_name($hash);
        }
        return empty($returnarr) ? null : $returnarr;
    }

    /**
    * Get IDs By Type
    * Get an array of ids based on the type. 
    * eg string, object, array.
    *
    * @param string $type   PHP type.
    * 
    * @return array of ids on success or null if nothing found.
    */
    function get_ids_by_type($type) {
        $hashes = $this->get_hashes_by_type($type);
        $returnarr = array();
        foreach ($hashes as $hash) {
            $returnarr[] = $this->hash_to_id($hash);
        }
        return empty($returnarr) ? null : $returnarr;
    }

    /**
    * Get Hashes By Type
    * Get an array of hashes based on the type. 
    * eg string, object, array.
    *
    * @param string $type   PHP type.
    * 
    * @return array of hashes on success or null if nothing found.
    */
    function get_hashes_by_type($type) {
        if (!$this->type_exists($type)) {
            return null;
        }

        return $this->typeindex[$type];
    }

    /**
    * Type Exists
    * Check to see if a type exists in the type index.
    *
    * @param string $type   Type to check for.
    *
    * @return true if type exists or false if it does not exist.
    */
    function type_exists($type) {
        if (!in_array($type, array_keys($this->typeindex))) {
            return false;
        }

        return true;
    }
}
class CribzDatastoreException extends CribzException {}
?>
