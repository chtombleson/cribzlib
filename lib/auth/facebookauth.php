<?php
require_once(dirname(__FILE__).'/lib/facebook-php-sdk/src/facebook.php');
class CribzAuth_Facebook {
    protected $appid;
    protected $appsecret;

    function __construct($appid, $appsecret) {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
    }

    function authenicate() {
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
