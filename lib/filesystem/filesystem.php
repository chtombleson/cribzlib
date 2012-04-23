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
* @subpackage   Cribz Filesystem
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/
class CribzFilesystem {
    /**
    * Copy File
    * copy a file
    *
    * @param string $file       Path to File to copy.
    * @param string $newfile    New file path.
    *
    * @return true on success or false on failure.
    */
    static function copyFile($file, $newfile) {
        if (!file_exists($file)) {
            return false;
        }

        return copy($file, $newfile);
    }

    /**
    * Remove File
    * Remove a file
    *
    * @param string $file   Path to file to remove.
    *
    * @return true on success or false on failure.
    */
    static function removeFile($file) {
        if (!file_exists($file)) {
            return false;
        }

        return unlink($file);
    }

    /**
    * Create Dir
    * Create a directory.
    *
    * @param string $dir        Path to new directory
    * @param int    $chmod      Mode for the new directory(Optional)
    * @param bool   $recursive  Create directories recursively(Optional)
    *
    * @return true on success or false on failure.
    */
    static function createDir($dir, $chmod = 0777, $recursive = false) {
        if (file_exists($dir)) {
            return false;
        }

        return @mkdir($dir, $chmod, $recursive);
    }

    /**
    * Remove Dir
    * Remove a directory
    *
    * @param string $dir    Directory to remove
    *
    * @return true on success or false on failure.
    */
    static function removeDir($dir) {
        if (!file_exists($dir)) {
            return false;
        }

        return rmdir($dir);
    }

    /**
    * Search Dir By Ext
    * Search a directory for files with a certain extension.
    *
    * @param string $dir        Directory to search
    * @param string $search     Extension to search for.
    *
    * @return array of files on success or an empty array if nothing found.
    */
    static function searchDirByExt($dir, $search) {
        $ext = array(
            'txt' => '*.txt',
            'php' => '*.php',
            'html' => '*.html',
            'htm' => '*.htm',
            'js' => '*.js',
            'ico' => '*.ico',
            'bmp' => '*.bmp',
            'jpg' => '*.jpg',
            'jpeg' => '*.jpeg',
            'gif' => '*.gif',
            'tiff' => '*.tiff',
            'png' => '*.png',
            'avi' => '*.avi',
            'mov' => '*.mov',
            'mkv' => '*.mkv',
            'flv' => '*.flv',
            'ogg' => '*.ogg',
            'swf' => '*.swf',
        );

        if (in_array(array_keys($ext), $search)) {
            $dir = rtrim($dir, "/");
            return glob($dir."/".$ext[$search]);
        }

        return array();
    }

    /**
    * Search Dir By Filename
    * Search a directory for a certian file.
    *
    * @param string $dir        Directory to search
    * @param string $filename   Filename to search for.
    *
    * @return array with path to file or an empty array.
    */
    static function searchDirByFilename($dir, $filename) {
        $dir = rtrim($dir, "/");
        return glob($dir."/".$filename);
    }

    /**
    * Change Mode
    * Change the mode of a file or directory (chmod)
    *
    * @param string $item   File or Directory to change mode on
    * @param int    $mode   Mode to set
    *
    * @return true on success or false on failure.
    */
    static function changeMode($item, $mode) {
        if (!file_exists($item)) {
            return false;
        }

        return @chmod($item, $mode);
    }

    /**
    * Change Owner
    * Change the owner of a file or directory (chown)
    *
    * @param string $item   File or Directory to change owner on
    * @param int    $owner  New owner
    *
    * @return true on success or false on failure.
    */
    static function changeOwner($item, $owner) {
        if (!file_exists($item)) {
            return false;
        }

        return @chown($item, $owner);
    }

    /**
    * Change Group
    * Change the group of a file or directory (chgrp)
    *
    * @param string $item   File or Directory to change group on
    * @param int    $group  New group
    *
    * @return true on success or false on failure.
    */
    static function changeGroup($item, $group) {
        if (!file_exists($item)) {
            return false;
        }

        return @chgrp($item, $group);
    }
}
?>
