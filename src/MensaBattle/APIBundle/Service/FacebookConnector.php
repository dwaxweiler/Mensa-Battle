<?php

namespace MensaBattle\APIBundle\Service;


class FacebookConnector
{    
    private $facebookWrapper;
    
    public function __construct($appId, $secret)
    {
        $this->facebookWrapper = FacebookWrapper::getInstance($appId, $secret);
    }
    
    /**
     * Get the user id associated with the facebook access token.
     * 
     * @param string $fbtoken
     * @return string user id
     */
    public function getUserId($fbtoken)
    {
        return $this->facebookWrapper->getUserId($fbtoken);
    }
}