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
* @subpackage   Cribz Auth
* @author       Christopher Tombleson
* @copyright    Copyright 2012 onwards
*/

require_once(dirname(dirname(__FILE__)).'/mvc/model.php');
class CribzAuth_DB extends CribzAuth {
    /**
    * Table
    *
    * @var string
    */
    private $table;

    /**
    * Database
    *
    * @var CribzDatabase
    */
    private $database;

    /**
    * Constructor
    * Create a new instance of CribzAuth_DB
    *
    * @param CribzDatabase $database    CribzDatabase Object
    * @param string        $table       Name of table in the database to store user info
    */
    function __construct($database, $table='users') {
        $this->table = $table;
        $this->database = $database;
    }

    /**
    * Create User
    * Create a new user
    *
    * @param string $username   Username
    * @param string $email      Email Address
    * @param string $password   Password
    * @param string $salt       Salt to hash with password. (Optional)
    *
    * @return true on success or throw CribzAuth_DBException
    */
    function create_user($username, $email, $password, $salt='') {
        $model = new CribzAuth_DBModel($this->database, $this->table);

        if ($model->user_exists($username)) {
            throw new CribzAuth_DBException("The username: {$username}, already exists.", 0);
        }

        if ($model->email_exists($email)) {
            throw new CribzAuth_DBException("The email address: {$email}, already exists.", 1);
        }

        if (!$this->validate_email($email)) {
            throw new CribzAuth_DBException("Email address is not valid.", 2);
        }

        if (strlen($password) < 6) {
            throw new CribzAuth_DBException("Password is too short. Must be more than 6 characters.", 3);
        }

        if (empty($salt) || strlen($salt) < 9) {
            $chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
            $salt = '';

            for ($i=0; $i < 12; $i++) {
                $salt .= $chars[array_rand($chars, 1)];
            }

            $hashedsalt = md5($salt);
        } else {
            $hashedsalt = md5($salt);
        }

        $model->username = $username;
        $model->email = $email;
        $model->password = md5($password.$hashedsalt);
        $model->salt = $hashedsalt;
        $model->timecreated = time();
        $model->commit();
        return true;
    }

    /**
    * Update User
    * Update a users record
    *
    * @param int    $id         The users id from the database
    * @param string $username   Username
    * @param string $email      Email Address
    * @param string $password   Password
    *
    * @return true on success or throws CribzAuth_DBException
    */
    function update_user($id, $username, $email, $password) {
        $model = new CribzAuth_DBModel($this->database, $this->table);

        if (!$model->userid_exists($id)) {
            throw new CribzAuth_DBException("The user id: {$id}, does not exist.", 4);
        }

        $model->load_data($id);

        if ($username != $model->username) {
            if ($model->user_exists($username)) {
                throw new CribzAuth_DBException("The username: {$username}, already exists.", 0);
            }

            $model->username = $username;
        }

        if ($email != $model->email) {
            if ($model->email_exists($email)) {
                throw new CribzAuth_DBException("The email address: {$email}, already exists.", 1);
            }

            if (!$this->validate_email($email)) {
                throw new CribzAuth_DBException("Email address is not valid.", 2);
            }

            $model->email = $email;
        }

        if (md5($password.$model->salt) != $model->password) {
            $model->password = md5($password.$model->salt);
        }

        $model->timemodified = time();
        $model->commit();
        return true;
    }

    /**
    * Authenticate
    * Authenticate a user
    *
    * @param string $username   Username
    * @param string $password   Password
    *
    * @return true on success or throws CribzAuth_DBException
    */
    function authenticate($username, $password) {
        $model = new CribzAuth_DBModel($this->database, $this->table);
        $userid = $model->username_to_id($username);
        if (!$userid) {
            throw new CribzAuth_DBException("The username: {$username}, does not exist.", 5);
        }

        $model->load_data($userid);

        if ($username != $model->username) {
            throw new CribzAuth_DBException("Usernames do not match.", 6);
        }

        if (md5($password.$model->salt) != $model->password) {
            throw new CribzAuth_DBException("Incorrect password.", 7);
        }

        return true;
    }

    /**
    * Validate Email
    * Validate an email address
    *
    * @param string $email  Email Address
    *
    * @return true if valid or false if invalid
    */
    private function validate_email($email) {
        $regex = '#([a-zA-Z0-9\.\-_]+)@([a-zA-Z0-9]+)\.([a-zA-z]+)(\.([a-zA-Z]+))?#';
        if (preg_match($regex, $email)) {
            return true;
        }
        return false;
    }
}

class CribzAuth_DBModel extends CribzModel {
    public $Tabledefinition = array(
        'id' => 'int not null primary key',
        'username' => 'varchar(32) not null unique',
        'email' => 'varchar(150) not null unique',
        'password' => 'varchar(32) not null',
        'salt' => 'varchar(32) not null',
        'timecreated' => 'int not null default 0',
        'timemodified' => 'int not null default 0',
    );

    public $Pk = 'id';

    function __construct($database, $table) {
        $this->Table = $table;
        parent::__construct($database);
    }

    function user_exists($username) {
        $result = $this->Database->select($this->Table, array('username' => $username));
        return empty($result) ? false : true;
    }

    function email_exists($email) {
        $result = $this->Database->select($this->Table, array('email' => $email));
        return empty($result) ? false : true;
    }

    function userid_exists($id) {
        $result = $this->Database->select($this->Table, array('id' => $id));
        return empty($result) ? false : true;
    }

    function username_to_id($username) {
        $result = $this->Database->select($this->Table, array('username' => $username));
        return empty($result->id) ? false : $result->id;
    }
}
class CribzAuth_DBException extends CribzException {}
?>
