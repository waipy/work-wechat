<?php
namespace Waipy\WorkWechat\Tests;

use PHPUnit\Framework\TestCase;
use Waipy\WorkWechat\Wechat;
use Waipy\WorkWechat\MsgCrypt;

class WechatTest extends TestCase
{
  const app_id = 'ww5d63863c568c1b31'; // 必填
  const corpId = 'ww5d63863c568c1b31';
  const secret = 's669eEInJ-jHTMWQ0p_JeS5xNTxRfHapiEFD1RbeKOs'; // 必填
  const token = 'Rlvm7B2eKw4UArqOLhe4XzEnrpeE6'; // 必填
  const aes_key = '3XooA46i3e6xomma1bPyGnytFfhdVFSOBXtmUCwk5kV'; // 加密模式需要，其它模式不需要
    
    public function testInfo()
    {
        $wechat = new Wechat(self::corpId,self::secret);
        $this->assertEquals("this is Wechat Example", $wechat->info());
   }

    public function testGetToken(){
        $wechat = new Wechat(self::corpId,self::secret);
        $token = $wechat->getToken();
        print($token);
        // $this->assertEquals('abc',$wechat->getUserInfo('YsPkU32us8yWUCbXr2RudinycVhP46lq2uqFYrv3TyA'));
        // $this->assertEquals("9Sw8rpTTbT7ez0Hu75vOJB0Z2koy1EIakhllq-xpL0uL1yZSsKUzFhC2GJiZHQ0n8JJ0ij_p78Sq_HVleGqz2aIwIL9n6mXbQe180ytv5HCNy68b63foWEpnX821ZEfG6z8wGfOb1aKsIXu9bJ73YBJwTMUCUFYD7GELCL0kLWRjw2WNVwMYpQW9-oPMWMx5RsT7-fQSjuPAzdC_TCn48Cw5CthkVJGO6lxS3tuoBAAGlwa4knx0z6_rfnK41H40bcRvYQrTy4rP3Cu4VEgFK-6Cppngcejhds2UadpkoVo",$token);
        $this->assertEquals("hello",$wechat->createConnectUrl("http://sqmt.ipucao.com",'1000002'));
 
    }
    
    public function testVerifyRUL()
    {
        $encodingAesKey = "jWmYm7qr5nMoAUwZRjGtBxmz3KA1tkAj3ykkR6q2B2C";
        $token = "QDG6eK";
        $corpId = "wx5823bf96d3bd56c7";

/*
------------使用示例一：验证回调URL---------------
*企业开启回调模式时，企业号会向验证url发送一个get请求 
假设点击验证时，企业收到类似请求：
* GET /cgi-bin/wxpush?msg_signature=5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3&timestamp=1409659589&nonce=263014780&echostr=P9nAzCzyDtyTWESHep1vC5X9xho%2FqYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp%2B4RPcs8TgAE7OaBO%2BFZXvnaqQ%3D%3D 
* HTTP/1.1 Host: qy.weixin.qq.com

接收到该请求时，企业应
1.解析出Get请求的参数，包括消息体签名(msg_signature)，时间戳(timestamp)，随机数字串(nonce)以及企业微信推送过来的随机加密字符串(echostr),
这一步注意作URL解码。
2.验证消息体签名的正确性 
3. 解密出echostr原文，将原文当作Get请求的response，返回给企业微信
第2，3步可以用企业微信提供的库函数VerifyURL来实现。

*/

// $sVerifyMsgSig = HttpUtils.ParseUrl("msg_signature");
        $sVerifyMsgSig = "5c45ff5e21c57e6ad56bac8758b79b1d9ac89fd3";
// $sVerifyTimeStamp = HttpUtils.ParseUrl("timestamp");
        $sVerifyTimeStamp = "1409659589";
// $sVerifyNonce = HttpUtils.ParseUrl("nonce");
        $sVerifyNonce = "263014780";
// $sVerifyEchoStr = HttpUtils.ParseUrl("echostr");
        $sVerifyEchoStr = "P9nAzCzyDtyTWESHep1vC5X9xho/qYX3Zpb4yKa9SKld1DsH3Iyt3tP3zNdtp+4RPcs8TgAE7OaBO+FZXvnaqQ==";

// 需要返回的明文
        $sEchoStr = "";

        $wxcpt = new MsgCrypt($token, $encodingAesKey, $corpId);
        $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
        if ($errCode == 0) {
            //
            // 验证URL成功，将sEchoStr返回
            // HttpUtils.SetResponce($sEchoStr);
        } else {
            print("ERR: " . $errCode . "\n\n");
        }


        $sReqMsgSig = "477715d11cdb4164915debcba66cb864d751f3e6";
// $sReqTimeStamp = HttpUtils.ParseUrl("timestamp");
        $sReqTimeStamp = "1409659813";
// $sReqNonce = HttpUtils.ParseUrl("nonce");
        $sReqNonce = "1372623149";
// post请求的密文数据
// $sReqData = HttpUtils.PostData();
        $sReqData = "<xml><ToUserName><![CDATA[wx5823bf96d3bd56c7]]></ToUserName><Encrypt><![CDATA[RypEvHKD8QQKFhvQ6QleEB4J58tiPdvo+rtK1I9qca6aM/wvqnLSV5zEPeusUiX5L5X/0lWfrf0QADHHhGd3QczcdCUpj911L3vg3W/sYYvuJTs3TUUkSUXxaccAS0qhxchrRYt66wiSpGLYL42aM6A8dTT+6k4aSknmPj48kzJs8qLjvd4Xgpue06DOdnLxAUHzM6+kDZ+HMZfJYuR+LtwGc2hgf5gsijff0ekUNXZiqATP7PF5mZxZ3Izoun1s4zG4LUMnvw2r+KqCKIw+3IQH03v+BCA9nMELNqbSf6tiWSrXJB3LAVGUcallcrw8V2t9EL4EhzJWrQUax5wLVMNS0+rUPA3k22Ncx4XXZS9o0MBH27Bo6BpNelZpS+/uh9KsNlY6bHCmJU9p8g7m3fVKn28H3KDYA5Pl/T8Z1ptDAVe0lXdQ2YoyyH2uyPIGHBZZIs2pDBS8R07+qN+E7Q==]]></Encrypt><AgentID><![CDATA[218]]></AgentID></xml>";
        $sMsg = "";  // 解析之后的明文
        $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
        if ($errCode == 0) {
            // 解密成功，sMsg即为xml格式的明文
            // TODO: 对明文的处理
            // For example:
            $xml = new \DOMDocument();
            $xml->loadXML($sMsg);
            $content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;
            print("content: " . $content . "\n\n");
            // ...
            // ...
        } else {
            print("ERR: " . $errCode . "\n\n");
            //exit(-1);
        }
    }
}
