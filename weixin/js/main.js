/**
 * 获取URL中参数值
 * @param name
 * @returns
 */
function getPara(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
	var r = window.location.search.substr(1).match(reg);
	if (r != null)
		return decodeURIComponent(r[2]);
	return "";
}

function goCommonInfo(str){
	window.location.href='commonInfo.html?name='+encodeURIComponent(str);
}

$(function(){
	$("#search").val("请输入关键字");
	$("#search").focus(function(){
		if($(this).val() == "请输入关键字"){
			$(this).val("");
		}
	});
	$("#search").blur(function(){
		if($(this).val() == ""){
			$(this).val("请输入关键字");
		}
	});
});

Date.prototype.Format = function (fmt) { //author: meizz 
  var o = {
    "M+": this.getMonth() + 1, //月份 
    "d+": this.getDate(), //日 
    "h+": this.getHours(), //小时 
    "m+": this.getMinutes(), //分 
    "s+": this.getSeconds(), //秒 
    "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
    "S": this.getMilliseconds() //毫秒 
  };
  if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
  for (var k in o)
  if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
  return fmt;
};

if (!Array.prototype.forEach) {  
    Array.prototype.forEach = function(callback, thisArg) {  
        var T, k;  
        if (this == null) {  
            throw new TypeError(" this is null or not defined");  
        }  
        var O = Object(this);  
        var len = O.length >>> 0; // Hack to convert O.length to a UInt32  
        if ({}.toString.call(callback) != "[object Function]") {  
            throw new TypeError(callback + " is not a function");  
        }  
        if (thisArg) {  
            T = thisArg;  
        }  
        k = 0;  
        while (k < len) {  
            var kValue;  
            if (k in O) {  
                kValue = O[k];  
                callback.call(T, kValue, k, O);  
            }  
            k++;  
        }  
    };  
}

Server={
    appID: "wxc1434c835f9b9013",
    appSecret: "0547711fc5a2b521ea6feadc380e270d"
};
Server.addr = "";
if(window.location.href.indexOf("selisse") <= -1){
    Server.addr="http://www.selisse.com.cn/Selisse/";//http://localhost:8080/Selisse/";
}
else{
    Server.addr="http://www.selisse.com.cn/Selisse/";
}

// 公用方法
Common = {
        //更新余额
        updateBalance: function(type,balance,agentID,successCallBack,failCallBack){
            $.ajax({
                url: Server.addr + "updateAgentInfo",
                type: "post",
                data: "agentID=" + agentID + "&value=" + balance + "&type=" + type,
                success: function(json){
                    if(json.result == "000000"){
                        successCallBack();
                    }else{
                        failCallBack();
                    }
                }
            });
        },
        // 数据库时间对象转化为字符串
        timeToString: function(time){
            var strtime = new Date(time);
            return strtime.Format("yyyy-MM-dd hh:mm:ss");
        }
}