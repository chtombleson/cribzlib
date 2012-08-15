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
require_once(dirname(__FILE__).'/lib/lightopenid/openid.php');
class CribzAuth_OpenId {
    /**
    * Provider Url
    *
    * @var string
    */
    private $providerurl;

    /**
    * Return Url
    *
    * @var string
    */
    private $returnurl;

    /**
    * Domain
    *
    * @var string
    */
    private $domain;

    /**
    * Constructor
    * Create a new instance of CribzAuth_OpenId
    *
    * @param string $providerurl        The open id url.
    * @param string $returnurl          Url to return to when login with provider is complete.
    * @param string $domain             Domain name of site. (Optional)
    *
    * @return throws CribzAuth_OpenIdException if curl extension is not loaded.
    */
    function __construct($providerurl, $returnurl, $domain='') {
        if (!extension_loaded('curl')) {
            throw new CribzAuth_OpenIdException("The Curl Extension for PHP is needed for OpenId Authenication.", 0);
        }

        $this->providerurl = $providerurl;
        $this->returnurl = $returnurl;
        $this->domain = empty($domain) ? $_SERVER['SERVER_NAME'] : $domain;
    }

    /**
    * Login
    * Do the inital authenication with the openid provider.
    *
    * @param array $fields      Array that contains required and optional info to get from open id provider. (Optional)
    */
    function login($fields=array()) {
        $openid = new LightOpenID($this->domain);
        $openid->identity = $this->providerurl;

        if (!empty($fields)) {
            if (!empty($fields['required'])) {
                $openid->required = $fields['required'];
            }

            if (!empty($fields['optional'])) {
                $openid->optional = $fields['optional'];
            }
        } else {
            $openid->required = array('contact/email');
        }

        $openid->returnUrl = $this->returnurl;
        header('Location: ' . $openid->authUrl());
    }

    /**
    * Authenicate
    * Authenicate the open id providers reply.
    *
    * @return false on error or object with user info.
    */
    function authenicate() {
        $openid = new LightOpenID($this->domain);
        if ($openid->mode) {
            if ($openid->validate()) {
                $result = $openid->getAttributes();
                return (object) $result;
            }
            return false;
        }
    }
}
class CribzAuth_OpenIdException extends CribzException {}
?>
