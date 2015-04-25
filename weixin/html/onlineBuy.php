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
    <div id="product_container" class="product-container">
        <h3>请选择正装产品</h3>
    </div>
    <div id="xiaoyang_container" class="xiaoyang-container" style="display: none;clear:left;">
        <h3>请选择小样物料</h3>
    </div>
    <div id="address_container" class="address-container" style="display: none;clear:left;">
        <h3>请选择收获地址</h3>
        <div class="address">

        </div>
        <div class="express">
            快递：<select id="express_choose"></select>
        </div>
    </div>
    <div class="start-buy"><a class="ui-btn" id="start_buy" href="javascript:">下一步</a></div>
<script type="text/javascript">
    $(function(){
        // 产品订购数量对象
        var proBuy = {};
        // 订购的正装金额
        var totalPrice = 0;
        // 订购的小样物料金额
        var xiaoyangPrice = 0;
        // 进行到哪一步了
        var step = 0;
        var productContainer = null;
        var agent = {
            // 代理类型
            agentType : "<?php if($agentInfo){echo $agentInfo->type;} ?>",
            // 代理ID
            agentID : "<?php if($agentInfo){echo $agentInfo->ID;} ?>",
            // 代理余额
            balance : "<?php if($agentInfo){echo $agentInfo->balance;} ?>",
            // 代理姓名
            agentName : "<?php if($agentInfo){echo $agentInfo->name;} ?>"
        };

        var exprArr = ['申通','中通','顺丰','EMS','圆通','韵达','百世汇通','宅急送','天天','德邦物流','新邦物流','天地华宇','其他'];
        for(var index=0;index<exprArr.length;index++){
            $("#express_choose").append("<option value='"+index+"'>"+exprArr[index]+"</option>");
        }

        // 获取收货地址
        $.ajax({
            url : Server.addr + "getAgentAddress",
            data : "userID=" + agent.agentID,
            dataType : "jsonp",
            jsonp:"jsonpcallback",
            success : function(data){
                if(data.length==0){
                    //alert("没有设置收货地址，不能订货，请先设置收货地址!");
                    //return;
                }
                for(var index=0;index<data.length;index++){
                    $(".address").append(
                        '<p>'+
                            '<input type="radio" data-addr-text="'+data[index].name+','+data[index].mobile+','+data[index].addr+'" data-reciver="'+data[index].name+'" data-mobile="'+data[index].mobile+'" value="'+index+'"'+(index==0?' checked':'')+
                            '  name="addr"/>'+
                            '收货人姓名：<span id="reciver">'+data[index].name+'</span>&nbsp;&nbsp;'+
                            '电话：<span id="mobile">'+data[index].mobile+'</span>&nbsp;&nbsp;收货地址：<span id="addr">'+data[index].addr+'</span>'+
                        '</p>'
                    );
                }
            }
        });
        
        
        // 获取所有正装产品
        $.ajax({
            url : Server.addr + "getProduct",
            dataType : "jsonp",
            jsonp:"jsonpcallback",
            success : function(data){
                data.forEach(function(item){
                    if(item.type==1){
                        productContainer = $("#product_container");
                    }else if(item.type==2){
                        productContainer = $("#xiaoyang_container");
                    }
                    var type = (agent.agentType == "a" ? "A" : "总");
                    var price = (type == "A" ? item.aPrice: item.zongPrice);
                    if(item.name.indexOf("套装") <=-1 && item.name.indexOf("系列") <= -1){
                        // 取消套装的下单
                        $("<div class='product'><div class='productImg'>"+
                                "<a href='productDetail.html?productID=" + item.ID +
                                "'><img src='" + Server.addr + item.imgSrc + "' /></a></div>"+
                                "<div class='productName'>" + item.name + "</div>"+
                                "<div class='amount'>库存："+item.amount+"件</div>"+
                                "<div class='price'>"+type+"代价："+price+"元</div>"+
                                "<div class='buy-amount'>订购数量：<span class='jian' onclick='jian(this)'>-</span><input oninput='OnInput (event)' onpropertychange='OnPropChanged (event)' class='orderinput' data-price='"+price+"' data-productName='"+item.name+"' data-productID='"+item.ID+"' data-amount='"+item.amount+"' type='text' value='0'/><span class='jia' onclick='jia(this,\""+price+"\",\""+item.ID+"\",\""+item.type+"\")'>+</span></div></div>").appendTo(productContainer);
                        proBuy[item.ID] = 0;
                    }
                });
            }
        });

        $("#start_buy").click(function(){
            $(".orderinput").each(function(){
                var productID = $(this).attr("data-productID");
                var productPrice = $(this).attr("data-price");
                var productAmount = $(this).attr("data-amount");
                var productName = $(this).attr("data-productName");
                proBuy[productID] = $(this).val();
                if(productAmount < $(this).val()){
                    alert("您订购的" + productName + "超过了它的库存，请重新输入！");
                    return;
                }
                if(step == 0){
                    totalPrice += parseFloat(productPrice) * parseInt($(this).val());
                }else if(step == 1){
                    xiaoyangPrice += parseFloat(productPrice) * parseInt($(this).val());
                }
            });
            if(step == 0){
                // 必须要选择产品
                if(totalPrice <= 0){
                    alert("请选择产品！");
                    return;
                }

                // 判断进货金额是否超过3000
                if(totalPrice >= 3000){
                    // 到选择小样物料页面
                    $("#xiaoyang_container").show();
                    $("#product_container").hide();
                    step = 1;
                }else{
                    // 选择收获地址
                    $("#xiaoyang_container").hide();
                    $("#product_container").hide();
                    $("#address_container").show();
                    step = 2;
                }
            }else if(step == 1){
                // 判断小样物料是否超过5%
                if(xiaoyangPrice > totalPrice * 0.05){
                    alert("小样和物料总额不能超过正装订单金额的5%");
                    return;
                }else{
                    // 选择收获地址
                    $("#xiaoyang_container").hide();
                    $("#product_container").hide();
                    $("#address_container").show();
                    step = 2;
                }
            }else if(step == 2){
                // 提交订单
                makeOrder();
            }
            
            
        });

        function makeOrder(){
            if(agent.balance >= totalPrice){
                // 生成订单
                $.ajax({
                    url: Server.addr + "addOrder",
                    type: "post",
                    dataType : "jsonp",
                    jsonp:"jsonpcallback",
                    data: {
                        agentID: agent.agentID,
                        name: agent.agentName,
                        reciver: $("input[name='addr']:checked").attr("data-reciver"),
                        address: $("input[name='addr']:checked").attr("data-addr-text"),
                        mobile: $("input[name='addr']:checked").attr("data-mobile"),
                        amount: totalPrice,
                        products: JSON.stringify(proBuy),
                        express: exprArr[$("#express_choose").val()],
                        samples: "",
                        wuliao: "",
                        charges: "0"
                    },
                    success: function(json){
                        if(json.result == "000000"){
                            alert("结算成功，可在订单查询里查询，稍后跟单会为您发货！");
                            // 更新余额
                            Common.updateBalance("minus",parseFloat(totalPrice),agent.agentID,function(){
                                alert("订购成功！");
                            },function(){
                                alert("余额更新失败，请联系管理员！");
                            });
                        }else{
                            alert("结算失败，请联系小明！");
                        }
                    }
                });
            }else{
                alert("余额不足，请联系诗轩小秘书充值！");
            }
        }
    });
</script>
</div>
</body>
</html>