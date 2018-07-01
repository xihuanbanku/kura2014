<?php
require (dirname(__FILE__) . "/../include/config_base.php");
require (dirname(__FILE__) . "/../include/config_rglobals.php");
require (dirname(__FILE__) . "/../include/page.php");
require_once (dirname(__FILE__) . "/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg_softname;?>会社掲示版</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/loading.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css"  href="../style/jquery-ui.css"/>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script language="javascript">
var url = "../service/BulletinService.class.php";
Date.prototype.format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "H+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}
function getQueryString(name) {  
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");  
    var r = window.location.search.substr(1).match(reg);  
    if (r != null) return unescape(r[2]); return null;  
}
$(function(){
	var param = getQueryString("id");
	var is_public = getQueryString("is_public");
	var checkPwd = "";
	if(is_public > 0) {
		checkPwd = prompt("请输入密码");
	}
	refreshMethod(param, checkPwd);
	setInterval("refreshMethod("+param+","+ checkPwd+")", 10000);
});
function refreshMethod(param, checkPwd) {
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"getContentPage", "id":param, checkPwd:checkPwd},
		success: function(msg){
			if(msg.trim() == "null") {
				alert("密码错误");
				history.back();
			} else {
    			msg = eval("("+msg+")");
    			$("#content").html("<h2>"+msg.subject+"</h2>");
    			$("#content").append(msg.content.replace(/\n/g,'<br/>'));
    	        $("#is_public").val(msg.is_public);
    	        $("#receiver_id").val(msg.receiver);
    	        $("#sender_id").val(msg.sender);
    	        $("#receiver_name").val(msg.receiver_name);
    	        $("#sender_name").val(msg.sender_name);
    	        $("#bulletin_id").val(param);
    		}
		}
	});
}

function replySubmit(){
	if($("#is_public").val() == 0 && $("#receiver_id").val() != <?php echo $_COOKIE["userID"];?>) {
		alert("您无权操作该消息.");
		return;
	}
	var replyContent= $("#replyContent").val();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"replySubmit", 
			"id":$("#bulletin_id").val(), 
			"replyContent": replyContent, 
			"sender": $("#sender_id").val(), 
			"receiver": $("#receiver_id").val(), 
			"receiver_name": '<?php echo $_COOKIE["VioomaUserID"];?>', 
			"userID":<?php echo $_COOKIE["userID"];?>},
		success: function(msg){
			if(msg > 0) {
				$("#content").append("<br/><span> " + <?php echo "'".$_COOKIE["VioomaUserID"]."'";?> + "</span> 在 <span>"+ new Date().format("yyyy-MM-dd HH:mm:ss") + "</span> 时写道:<br/>"+replyContent );
				$("#replyContent").val("")
				alert("回复成功");
			} else {
				alert("您无权操作该消息.");
			}
		}
	});  
}
</script>
<style type="text/css">
#content span{
	color: red;
	font-weight: bold;
}
#bodyDiv{
	margin-left: 50px;
}
</style>
</head>
<body>
    <div id="bodyDiv">
        <div id="content">
        
        </div>
        <div id="dialog" >
          <input type="hidden" id="is_public"/>
          <input type="hidden" id="receiver_id"/>
          <input type="hidden" id="sender_id"/>
          <input type="hidden" id="bulletin_id"/>
          <input type="hidden" id="receiver_name" value="<?php echo $_COOKIE["VioomaUserID"];?>"/>
          <input type="hidden" id="sender_name"/>
          <div id="reply" >
              <textarea id="replyContent" rows="5" cols="60"></textarea><br/>
              <button onclick="replySubmit()">回复</button>
          </div>
        </div>
    </div>
</body>     
</html>

