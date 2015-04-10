<?php
/**
  * wechat php test
  */

//define your token
define("TOKEN", "lkkj");
header("content-Type: text/html; charset=utf-8"); 
//require 'common.class.php';
$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();
//$wechatObj->valid();

class wechatCallbackapiTest
{
    // 日志记录
    public function writelog($str)
    {
        $open=fopen("log.txt","a" );
        date_default_timezone_set("PRC");
        fwrite($open,date("Y-m-d H:i:s")."   ".$str."\r\n");
        fclose($open);
    }

	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");//因为很多都设置了register_globals禁止,不能用$GLOBALS["HTTP_RAW_POST_DATA"]
      	//extract post data
        $postStr = str_replace( array( '<![CDATA[' , ']]>'), array( '' , '' ), $postStr);
        $postStr = mb_convert_encoding($postStr,"utf-8");
        $postStr = str_replace("gb2312","gbk",$postStr);
        $this->writelog("提交参数：".$postStr);
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                //libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                var_dump($postObj);
                // openID
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $MsgType = $postObj->MsgType;//消息类型  
                $msgType = "text";   

                $content="";
                if($MsgType=='event')
                {
                    $MsgEvent = $postObj->Event;//获取事件类型  
                    if ($MsgEvent=='subscribe') 
                    {
                        //订阅事件  
                    }
                    elseif ($MsgEvent=='CLICK')
                    {
                        //点击事件  
                        $EventKey = $postObj->EventKey;//菜单的自定义的key值，可以根据此值判断用户点击了什么内容，从而推送不同信息  
                        switch($EventKey)
                        {
                            case "query_true" :
                            //要返回相关内容
                            $content="欢迎使用诗轩防伪查询。\r\n查询端口1：\r\n<a href='http://www.acec315.com/'>【点击输入诗轩防伪码】</a>";
                            break;
                            case "customer_service" :
                            $content="尊敬的贵宾您好！您有任何关于诗轩的问题，专业微客服都会尽快给到您周全详尽的答复。诗轩微客服随时恭候您的咨询。\r\n\r\n请直接回复您的问题或关键词，微客服会尽快给您答复，谢谢！";
                            break;
                            case "query_balance" :
                                $openID = $fromUsername;
                                // 查看服务器上此用户的余额
                                $url = "http://www.selisse.com.cn/Selisse/getBalance?openID=".$openID;
                                $balance = file_get_contents($url);
                                $content="您的账户余额是：".$balance."元";
                            break;
                            case "user_bind" : 
                                $content="回复BD加微信号绑定，如：BDzmcnxd";
                            break;
                            default :
                            $content="正在鞭打程序员，努力开发中！";
                        }
                    }
                }
                elseif ($MsgType=='text')
                {
                    $this->writelog("用户输入文字：".$keyword);
                    switch($keyword)
                    {
                        case "防伪测试" :
                        //要返回相关内容
                        $content="欢迎使用诗轩防伪查询。\r\n<a href='http://www.china3-1-5.com/'>【点击输入诗轩防伪码】</a>";
                        break;
                        case "防伪查询" :
                        //要返回相关内容
                        $content="欢迎使用诗轩防伪查询。\r\n查询端口1：\r\n<a href='http://www.acec315.com/'>【点击输入诗轩防伪码】</a>";
                        break;
                        default :
                        $content="您好，欢迎关注裸妆护肤第一品牌——SELISSE诗轩。\r\n美丽热线：400-999-7705\r\n官方网站：www.selisse.com.cn\r\n诗轩品牌相关的疑问和需求，请按上述方式联系客服为您解答，谢谢。";
                    }
                    // 如果回复的绑定微信号
                    if(strpos($keyword,"BD") >= 0){
                        // 查询是否代理，如是，则绑起，记录下openID
                        $bindURL = "http://www.selisse.com.cn/Selisse/bindAgent?openID=".$fromUsername."&weixinNo=".substr($keyword,2);
                        $result = file_get_contents($bindURL);
                        if($result == "success"){
                            $content = "恭喜您！绑定成功，现在可以查询余额，在线订货。";
                        }
                        else{
                            $content = "对不起，绑定失败！没有查询到相关代理信息。"
                        }
                    }
                }
                echo $this->_response_text($postObj,$content);
        }else {
            echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    private function _response_text($object,$content){
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, "0");
        return $resultStr;
    }

    
}

?>