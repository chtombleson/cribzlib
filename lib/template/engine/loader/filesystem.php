<?php
class CribzTemplate_Loader_Filesystem extends CribzTemplate_Loader {
    private $paths = array();

    function __construct($paths) {
        if (is_array($paths)) {
            $this->set_paths($paths);
        } else {
            $this->set_path($path);
        }
    }

    public function set_paths($paths) {
        foreach ($paths as $path) {
            $this->set_path($path);
        }
    }

    public function set_path($path) {
        if (!in_array($path, $this->paths)) {
            if (file_exists($path) && is_dir($path)) {
                if (!is_readable($path)) {
                    throw new CribzTemplate_LoaderException("Path must be readable, {$path}", 0);
                }

                $this->paths[] = realpath($path);
            } else {
                throw new CribzTemplate_LoaderException("Path must point to a directory not a file, {$path}", 1);
            }
        }
    }

    public function get_paths() {
        return $this->paths;
    }

    public function get_template($name) {
        $path = $this->find_template($name);
        return file_get_contents($path);
    }

    private function find_template($name) {
        $found = false;

        foreach ($this->paths as $path) {
            if (file_exists($path.'/'.$name)) {
                $found = true;
                $path = $path.'/'.$name;
            }
        }

        if ($found) {
            return $path;
        } else {
            throw new CribzTemplate_LoaderException("Couldn't find template, {$name}", 2);
        }
    }
}
class CribzTemplate_LoaderException extends CribzException {}
?>
