<?php
/**
  * wechat php test
  */

//define your token
header('Content-Type: text/html; charset=UTF-8');

//更换成自己的APPID和APPSECRET
$APPID="wxc1434c835f9b9013";
$APPSECRET="0547711fc5a2b521ea6feadc380e270d";

//定义处理函数
function get($str){
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}

// 获取网页授权code，用code换取openid
// 获取完整URL
$str = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$data = get($str);
// 获取code
$code = $data['code'];

$TOKEN_URL="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$APPID."&secret=".$APPSECRET."&code=".$code."&grant_type=authorization_code";

$ch = curl_init(); 

curl_setopt($ch, CURLOPT_URL, $TOKEN_URL); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

$userInfo = json_decode(curl_exec($ch));
if (curl_errno($ch)) {
    echo 'Errno'.curl_error($ch);
}

curl_close($ch);

// 通过openID获取代理信息
$openid = $userInfo->openid;
$GETAGENT_URL = "http://www.selisse.com.cn/Selisse/getAgentDetail?type=openid&openid=".$openid;
$json = file_get_contents($GETAGENT_URL);
$agentInfo=json_decode($json);
var_dump($agentInfo);

?>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="../css/main.css">
<link rel="stylesheet" type="text/css" href="../css/buy.css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/main.js"></script>
<title>诗轩（SELISSE）在线下单</title>
<meta name="description" content="SÉLISSE诗轩隶属法国SÉLISSE护肤中心，由法国著名的SILIA化妆品公司负责研发与监制。诗轩品牌在全球率先提出裸妆护肤理念（nude make-up skin care），倡导“清新、自然、时尚”的护肤体验，旨在为爱美女性提供更亲肤、更高效、更持久的裸妆护肤产品，充分感受“裸妆护肤、完美出镜”的非凡理念。">
<meta name="keywords" content="诗轩、SELISSE、诗轩面膜、裸妆护肤、裸妆面膜、诗轩新闻、诗轩公告">
</head>
<body>
<div class="mainContainer">
    <div id="product-container" class="product-container">
        <h3>请选择正装产品</h3>
    </div>
    <div class="start-buy"><a class="ui-btn" id="start_buy" href="javascript:">结&nbsp;算</a></div>
<script type="text/javascript">
    $(function(){
        // 产品订购数量对象
        var proBuy = {};
        var productContainer = null;
        var agentType = "<?php if($agentInfo){echo $agentInfo->type;} ?>";
        
        // 获取所有正装产品
        $.ajax({
            url : Server.addr + "getProduct",
            dataType : "jsonp",
            jsonp:"jsonpcallback",
            success : function(data){
                data.forEach(function(item){
                    if(item.type==1){
                        productContainer = $("#product-container");
                    }else if(item.type==2){
                        productContainer = $("#xiaoyang-container");
                    }else if(item.type==3){
                        productContainer = $("#wuliao-container");
                    }
                    var type = (agentType == "a" ? "A" : "总");
                    var price = (type == "A" ? item.aPrice: item.zongPrice);
                    if(item.name.indexOf("套装") <=-1 && item.name.indexOf("系列") <= -1){
                        // 取消套装的下单
                        $("<div class='product'><div class='productImg'>"+
                                "<a href='productDetail.html?productID=" + item.ID +
                                "'><img src='" + Server.addr + item.imgSrc + "' /></a></div>"+
                                "<div class='productName'>" + item.name + "</div>"+
                                "<div class='price'>"+type+"代价："+price+"元</div>"+
                                "<div class='buy-amount'>订购数量：<span class='jian' onclick='jian(this)'>-</span><input oninput='OnInput (event)' onpropertychange='OnPropChanged (event)' data-price='"+price+"' data-productID='"+item.ID+"' type='text' value='0'/><span class='jia' onclick='jia(this,\""+price+"\",\""+item.ID+"\",\""+item.type+"\")'>+</span></div></div>").appendTo(productContainer);
                        proBuy[item.ID] = 0;
                    }
                });
            }
        });
    });
</script>
</div>
</body>
</html>