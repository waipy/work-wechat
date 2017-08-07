<?php
namespace Waipy\WorkWechat;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;

class Wechat
{
    const API_TOKEN_GET = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
    const API_USERINFO_GET = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo";
    const API_AUTHORIZE = "https://open.weixin.qq.com/connect/oauth2/authorize";
    const API_CONNECT = "https://open.work.weixin.qq.com/wwopen/sso/qrConnect";
    const API_SEND = "https://qyapi.weixin.qq.com/cgi-bin/message/send";

    protected $tokenJsonKey = 'access_token';

    protected $appId;
    protected $secret;
    protected $cache;
    protected $prefix = "waipy.common.access_token.";

    protected $http;
    protected $cacheKey;

    public static function info()
    {
        return "this is Wechat Example";
    }

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

    public function createAuthorizeUrl($to,$agentid='',$scope='snsapi_userinfo'){
        $params = [
            'appid'=>$this->appId,
            'redirect_uri' => \urlencode($to),
            'response_type' => 'code',
            'scope' => $scope,
            'agentid'=> $agentid,
            'state'=> 'state'
        ];
        return self::API_AUTHORIZE."?".\http_build_query($params).'#wechat_redirect';
    }

    public function createConnectUrl($to,$agentid){
        $params = [
            'appid'=>$this->appId,
            'redirect_uri' => \urlencode($to),
            'agentid'=> $agentid,
            'state'=> 'state'
        ];
        return self::API_CONNECT."?".\http_build_query($params);
 
    }

    public function pushMsg($params){
        $access_token = $this->getToken();
        $http = $this->getHttp();
        $result = $http->parseJSON($http->post(self::API_SEND.'?access_token='.$access_token,json_encode($params)));
        return $result;
 
    }

    public function pushText($msg,$users,$agentid){
        if(is_array($users)){
            $usersStr = implode('|',$users);
        }else{
            $usersStr = $users;
        }
        $params = [
            'touser'=> $usersStr,
            'toparty' => "1",
            'totag' => " ",
            'msgtype' => 'text',
            'agentid' => $agentid,
            'text' => [
                'content'=>$msg
            ],
            'safe' => 0
        ];
        return $this->pushMsg($params);
    }

    public function pushArticles($article,$users,$agentid){
        if(is_array($users)){
            $usersStr = implode('|',$users);
        }else{
            $usersStr = $users;
        }
        $params = [
            'touser'=> $usersStr,
            'toparty' => "1",
            'totag' => " ",
            'msgtype' => 'news',
            'agentid' => $agentid,
            'news' => [
                "articles" => $article,
            ],
            'safe' => 0
        ];
        return $this->pushMsg($params);
    }

    public function getCode(){

    }

    public function getUserInfo($code){
        $params = [
            'access_token'=>$this->getToken(),
            'code'=>$code,
        ];
        $http = $this->getHttp();
        $user = $http->parseJSON($http->get(self::API_USERINFO_GET,$params));
        return $user;
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
