<?php
namespace Waipy\WorkWechat;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

class User
{
    const API_TOKEN_GET = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
    const API_CONTACT_USER = "https://qyapi.weixin.qq.com/cgi-bin/user/get"; 

    protected $tokenJsonKey = 'access_token';

    protected $appId;
    protected $secret;
    protected $cache;
    protected $prefix = "waipy.contact.access_token.";

    protected $http;
    protected $cacheKey;

    public function __construct($appId, $secret, Cache $cache = null)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->cache = $cache;
    }
    public function getToken()
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);
        if (empty($cached)) {
            $token = $this->getTokenFromServer();
            $this->getCache()->save($cacheKey, $token[$this->tokenJsonKey], $token['expires_in'] - 1500);
            return $token[$this->tokenJsonKey];
        }
        return $cached;
    }

    public function getUserInfo($userid){
        $params = [
            'access_token' => $this->getToken(),
            'userid' => $userid
        ];
        $http = $this->getHttp();
        $result = $http->parseJSON($http->get(self::API_CONTACT_USER,$params));
        return $result;
    }

    public function getTokenFromServer()
    {
        $params = [
            'corpid' => $this->appId,
            'corpsecret' => $this->secret,
        ];
        $http = $this->getHttp();
        $token = $http->parseJSON($http->get(self::API_TOKEN_GET, $params));

        if(empty($token[$this->tokenJsonKey])){
         throw new Exception('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }
        return $token;
    }

    public function setCacheKey($cacheKey){
        $this->cacheKey = $cacheKey;

        return $this;
    }
    public function getCacheKey()
    {
        if(is_null($this->cacheKey)){
            return $this->prefix.$this->appId;
        }
        return $this->cacheKey;
    }

    public function getCache()
    {
        return $this->cache ?:$this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    public function getHttp(){
        return $this->http ?:$this->http = new Http();
    }
}
