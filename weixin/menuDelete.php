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

header('Content-Type: text/html; charset=UTF-8');

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
$MENU_URL="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$ACC_TOKEN;

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