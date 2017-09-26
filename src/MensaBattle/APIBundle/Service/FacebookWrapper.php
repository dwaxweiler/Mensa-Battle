<?php
namespace MensaBattle\APIBundle\Service;

class FacebookWrapper
{
    private static $facebookWrapper;
    private static $facebook;
    
    private function __construct($appId, $secret)
    {
        $config = array();
        $config['appId'] = $appId;
        $config['secret'] = $secret;
        $config['fileUpload'] = true;

        self::$facebook = new Facebook($config);
    }

    public static function getInstance($appId, $secret)
    {
        if (is_null(self::$facebook))
            self::$facebookWrapper = new FacebookWrapper($appId, $secret);
        return self::$facebookWrapper;
    }

    /**
     * Get the user id associated with the facebook access token.
     * 
     * @param string $fbtoken
     * @throws \Exception
     * @return string user id
     */
    public function getUserId($fbtoken)
    {
        self::$facebook->setAccessToken($fbtoken);
        
        $id = self::$facebook->getUser();
        if (!$id)
            throw new \Exception('Invalid facebook token.');
        
        return $id;
    }
}
