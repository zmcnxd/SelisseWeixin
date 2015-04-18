<?php
/**
 * 微信公众平台-自定义菜单功能源代码
 * ================================
 * Copyright 2013-2014 David Tang
 * http://www.cnblogs.com/mchina/
 * 乐思乐享微信论坛
 * http://www.joythink.net/
 * ================================
 * Author:David|唐超
 * 个人微信：mchina_tang
 * 公众微信：zhuojinsz
 * Date:2013-10-12
 */
require 'common.class.php';

header('Content-Type: text/html; charset=UTF-8');

//更换成自己的APPID和APPSECRET
$APPID="wxc1434c835f9b9013";
$APPSECRET="0547711fc5a2b521ea6feadc380e270d";

// 测试环境
//$APPID="wx9eb7e0863de513b0";
//$APPSECRET="0c806f5c2531d0aac89a41934f4cde38";



$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;
$json=file_get_contents($TOKEN_URL);
$result=json_decode($json);

$ACC_TOKEN=$result->access_token;
echo $ACC_TOKEN;    
$data='{
    "button": [ 
        {
            "name": "常用服务", 
            "sub_button": [
                {
                    "type": "view", 
                    "name": "代理查询", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/queryAgent.html"
                },
                {
                    "type": "click", 
                    "name": "防伪查询", 
                    "key": "query_true"
                },
                {
                    "type": "view", 
                    "name": "品牌介绍", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/xuanchuan.html"
                },
                {
                    "type": "click", 
                    "name": "微客服", 
                    "key": "customer_service"
                }
                
            ]
        },
        {
            "name": "订制美丽", 
            "sub_button": [
                {
                    "type": "view", 
                    "name": "明星产品", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/stars.html"
                },
                {
                    "type": "click", 
                    "name": "产品评测", 
                    "key": "product_test"
                },
                {
                    "type": "view", 
                    "name": "美丽课堂", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/classIndex.html"
                }
            ]
        },
        {
            "name": "诗迷社区", 
            "sub_button": [
                {
                    "type": "click", 
                    "name": "会员绑定", 
                    "key": "user_bind"
                },
                {
                    "type": "click", 
                    "name": "查询余额", 
                    "key": "query_balance"
                },
                {
                    "type": "click", 
                    "name": "账户充值", 
                    "key": "charge_balance"
                },
                {
                    "type": "view", 
                    "name": "在线下单", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/onlineBuy.html"
                },
                {
                    "type": "view", 
                    "name": "肌肤类型测试", 
                    "url": "http://www.selisse.com.cn/Selisse/weixin/html/pifuTest.html"
                }
            ]
        }
    ]
}';

$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$ACC_TOKEN;
/*$ch = curl_init($MENU_URL);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
$info = curl_exec($ch);
$menu = json_decode($info);
print_r($info);		//创建成功返回：{"errcode":0,"errmsg":"ok"}

if($menu->errcode == "0"){
	echo "菜单创建成功";
}else{
	echo "菜单创建失败";
}*/


$ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $MENU_URL); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$info = curl_exec($ch);
if (curl_errno($ch)) {
	echo 'Errno'.curl_error($ch);
}

curl_close($ch);

var_dump($info);

?>