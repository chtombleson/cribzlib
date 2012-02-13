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
* @subpackage   Cribz Imap
* @author       Christopher Tombleson
* @copyright    Copyright 2011 onwards
*/

class CribzImap {
    /**
    * Imap
    *
    * @var imap stream
    */
    private $imap;

    /**
    * Server
    *
    * @var string
    */
    private $server;

    /**
    * Port
    *
    * @var int
    */
    private $port;

    /**
    * Username
    *
    * @var string
    */
    private $username;

    /**
    * Password
    *
    * @var string
    */
    private $password;

    /**
    * Option
    *
    * @var int
    */
    private $option;

    /**
    * Constructor
    *
    * @param string $server         Imap Server to connect to
    * @param string $username       Name of user to connect as
    * @param string $password       Password for given user
    * @param int    $port           Port to connect to (optional)
    * @param int    $option         Imap open options (optional)
    */
    function __construct($server, $username, $password, $port = null, $option = null) {
        $this->server = $server;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->option = $option;
    }

    /**
    * Connect
    * Open a Imap stream
    *
    * @return true on success or false on failure
    */
    function connect() {

        if (empty($this->port)) {
            $conn_str = "{" . $this->server . "}";
        } else {
            $conn_str = "{" . $this->server . ":" . $this->port . "}";
        }

        if (empty($this->option)) {
            $imap = imap_open($conn_str, $this->username, $this->password);
        } else {
            $imap = imap_open($conn_str, $this->username, $this->password, $this->option);
        }

        if (!$imap) {
            return false;
        }

        $this->imap = $imap;
        return true;
    }

    /**
    * Close
    * Close an Imap stream
    */
    function close() {
        imap_close($this->imap);
        return true;
    }

    /**
    * List Mail Boxes
    * List the mail boxes on the current server.
    *
    * @return list of mailboxes on success or empty array on failure
    */
    function list_mailboxes() {
        if (empty($port)) {
            $server = "{" . $this->server . "}";
        } else {
            $server = "{" . $this->server . ":" . $this->port . "}";
        }

        $list = imap_list($this->imap, $server, "*");

        if (is_array($list)) {
            return $list;
        }

        return array();
    }

    /**
    * Search
    * Search messages
    *
    * @param string $query  Imap search query string
    *
    * @return array of message id's on success or false on error
    */
    function search($query) {
        $results = imap_search($this->imap, $query);

        if (empty($results)) {
            return false;
        }

        return $results;
    }

    /**
    * Header Info
    * Get the header info for a given message id.
    *
    * @param int $msgid     Message id
    *
    * @return header object on success or false on failure
    */
    function header_info($msgid) {
        $headers = imap_headerinfo($this->imap, $msgid);

        if (empty($headers)) {
            return false;
        }

        return $headers;
    }

    /**
    * Get Body
    * Get the body of a message
    *
    * @param int $mgsid     Message id
    *
    * @return message on success or false on failure
    */
    function get_body($msgid) {
        $body = imap_body($this->imap, $msgid);

        if (empty($body)) {
            return false;
        }

        return $body;
    }
}
?>
