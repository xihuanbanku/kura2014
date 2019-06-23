<?php
require (dirname(__FILE__) . "/../include/config_base.php");
require (dirname(__FILE__) . "/../include/config_rglobals.php");
require (dirname(__FILE__) . "/../include/page.php");
require_once (dirname(__FILE__) . "/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg_softname;?>情报收集</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/loading.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css"  href="../style/jquery-ui.css"/>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/loading.js?r=<?php echo rand()?>"></script>
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

$(function(){
	//清空屏幕
	var msgType = <?php echo $_REQUEST["msg_type"] ?>;
    $("#msgCleanButton").click(function() {
    	$.ajax({
    		type: "post",
    		url: url,
    		data: {"flag":"cleanMsgCollect",
    				"msgType": msgType},
    		success: function(data){
//         		alert(data);
    		    if(data >= 0) {
        		    alert("共清理"+data+"条记录");
        		    initPage(0);
    		    }
    		}
    	});
    });
    //textarea 使用ctrl+enter提交
    $("#replyContent").ctrlEnter("#replySubmit", function () {
    	var replyContent= $("#replyContent").val();
    	if(replyContent.trim() == "") {
			alert("请输入内容");
			return;
	   	}
    	var msgType = <?php echo $_REQUEST["msg_type"] ?>;
    	$.ajax({
    		type: "post",
    		url: url,
    		data: {"flag":"msgSubmit", 
    			"replyContent": replyContent,
    			"msgType": msgType,
    			"sender": '<?php echo $_COOKIE["VioomaUserID"];?>'},
    		success: function(msg){
        		var html = "";
    			if(msg > 0) {
    		        if(msgType == 0) {
    		        	html = "<li><span> " + <?php echo "'".$_COOKIE["VioomaUserID"]."'";?> + "</span>" 
        		    	    	+" 在 <span>"+ new Date().format("yyyy-MM-dd HH:mm:ss") + "</span> 时写道:<br/>"+replyContent.replace(/\n/g, "<br/>");
    		        } else {
    		        	html = "<li> <span>"+ new Date().format("yyyy-MM-dd HH:mm:ss") + "</span> お知らせ:<br/>"+replyContent.replace(/\n/g, "<br/>");
    		        }
    		    	$("#content_ul").prepend(html).fadeIn('slow'); 
    				$("#replyContent").val("")
//    				alert("回复成功");
    			} else {
    				alert("回复失败.");
    			}
    		}
    	});
 	}); 
    initPage(0);
});
function initPage(pageIndex) {
	var msgType = <?php echo $_REQUEST["msg_type"] ?>;
	showLoading();
	//分页参数
	var pageCount = 20;
	var recordCount = 0;
	var url = "../service/BulletinService.class.php?nocache="+new Date().getTime();
	var param = $("input").serialize();
	$.ajax({
		type: "post",
		url: url+"&"+param,
		data: {"flag":"initMsgCollect", "msgType":msgType},
		success: function(data){
//     		alert(data);
    		var html="";
			data = eval("("+data+")");
			if(data==null) {
	    		hideLoading();
	    		$("#content_ul").html(html);
	    	    return;
			}
			if(msgType == 0) {
        		$.each(data, function(entryIndex, entry){
        			html+="<li><span>" + entry.sender + "</span>在<span>" + entry.dtime + "</span>时写道:<br/>" + entry.content + "</li>";
        		});
			} else {
        		$.each(data, function(entryIndex, entry){
        			html+="<li><span>" + entry.dtime + "</span>お知らせ:<br/>" + entry.content + "</li>";
        		});
			}
    		$("#content_ul").html(html);
    		hideLoading();
		}
	});  
}
$.ajax({
	type: "post",
	url: "../service/MenuService.class.php",
	data: {"flag":"initButton", "reid":"139", "user":<?php echo GetCookie('userID')?>},
	success: function(data){
		data = eval("("+data+")");
		$.each(data, function(entryIndex, entry){
//     		alert(entry.url+"|"+entry.loc);
    		if(entry.loc > 0) {
				$("#" + entry.url).show();
    		} else {
				$("#" + entry.url).remove();
    		}
		});
	}
});
</script>
<style type="text/css">
#content_ul li span{
	color: red;
	font-weight: bold;
}
</style>
</head>
<body>
    <table width="100%" border="0" cellspacing="0">
        <tr style="height:30px;">
    		<td>
    	        <strong><?php if($_REQUEST["msg_type"] == 0) {?>情报收集<?php }?></strong>
    	    </td>
            <td align="right"> 
                <?php if($_REQUEST["msg_type"] == 0) {?><input style="display: none;" type="button" id="msgCleanButton" value="情报クリア"/><?php }?>
            </td>
 <!--               <table width="100%" cellspacing="0" cellpadding="0" border="1" id="table_border">
                    <tr class="row_color_head">
                        <td>操作</td>
                            <input type="checkbox" onclick="chkAll(this)"/>選択
                        <td>状态</td>
                	    <td>主题</td>
	                    <td>发件人</td>
                	    <td>收件人</td>
                        <td>发送时间</td>
	               </tr>
               </table>-->
        </tr>
        <tr>
			<td bgcolor="#FFFFFF" colspan="2">
              <div id="reply">
                  <textarea id="replyContent" rows="5" cols="60"></textarea><br/>
                  <button id="replySubmit">回复</button><span>可按"Ctrl+Enter"键提交</span>
              </div>
			  <ul id="content_ul">
			  </ul>
           </td>
        </tr>
    </table>
</body>     
</html>

