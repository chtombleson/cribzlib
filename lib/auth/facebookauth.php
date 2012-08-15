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
require_once(dirname(__FILE__).'/lib/facebook-php-sdk/src/facebook.php');
class CribzAuth_Facebook {
    /**
    * App ID
    * Facebook App ID
    *
    * @var string
    */
    protected $appid;

    /**
    * App Secret
    * Facebook App Secret
    *
    * @var string
    */
    protected $appsecret;

    /**
    * Constructor
    * Create a new instance of CribzAuth_Facebook
    *
    * @param string $appid      Facebook App ID
    * @param string $appsecret  Facebook App Secret
    */
    function __construct($appid, $appsecret) {
        if (!extension_loaded('curl')) {
            throw new CribzAuth_FacebookException("Curl Extension for PHP must be installed to use Facebook Auth", 0);
        }

        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    /**
    * Authenticate
    * Authenticate a user using Facebook
    *
    * @return array, user logged in array contains "user & logout" elements or if not logged in array contains "login" element.
    */
    function authenticate() {
        $facebook = new Facebook(array(
            'appId' => $this->appid,
            'secret' => $this->appsecret,
        ));

        $userid = $facebook->getUser();

        if ($userid) {
            try {
                $user = $facebook->api('/me');
            } catch(FacebookApiException $e) {
                $userid = 0;
            }
        }

        if (!empty($user)) {
            return array(
                'user' => $user,
                'logout' => $facebook->getLogoutUrl(),
            );
        }

        return array('login' => $facebook->getLoginUrl());
    }
}

class CribzAuth_FacebookException extends CribzException {}
