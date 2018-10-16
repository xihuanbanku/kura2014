<?php
require(dirname(__FILE__)."/../include/config_rglobals.php");
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/page.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<title>发送消息</title>
<style type="text/css">
#table_border {
    text-align: left;
}
#receiver label{
	width:90px;
	font-size: 13px;
	display: inline-block;
}
#receiver label p{
	display: table-cell;
}
</style>
<script language="javascript">
$(function(){
	var url = "../service/BulletinService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initReceiver"},
		success: function(data){
			data = eval("("+data+")");
    		var html="";
    		$.each(data, function(entryIndex, entry){
    			html+="<label><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\">" + entry.s_name + "<p>(" + entry.s_no + ")</p></label></input>";
    		});
    		$("#receiver").append(html);
		}
	});

	$("input[type=radio]").click(function(){
		if($(this).val() ==0 || $(this).val() ==2) {
			$("#reciverTr").show();
			$("#passwdTr").hide();
		} else {
			$("#reciverTr").hide();
			$("#passwdTr").show();
		}
	});
});
function chkAll(param) {
    if ($(param).prop("checked")) {
    	$("#receiver input:checkbox").prop("checked", true);
    } else {
    	$("#receiver input:checkbox").prop("checked", false);
    }
}
function checkForm(){
	var checkCount =0;
	$.each($("input[name='strChk[]']"), function(entryIndex, entry) {
		if($(entry).prop("checked")) {
			checkCount++;
		}
	});
	if($("input[name=is_public]:checked").length<=0){
		alert("是否公开该公告");
		return;
	}
	if(($("input[type=radio]:checked").val() == 0 || $("input[type=radio]:checked").val() == 2) && checkCount<=0){
		alert("请选择收件人");
		return;
	}
	if($("input[type=radio]:checked").val() == 1){
		if($("input[name=setPass]").val() != $("input[name=repeatPass]").val()) {
    		alert("密码不一致");
    		return;
		}
	}
	if($("input[name='subject']").val() == ""){
		alert("标题未填写");
		return;
	}
	var url = "../service/BulletinService.class.php?"+$("form").serialize();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"sendBulletin", "userID":<?php echo $_COOKIE["userID"];?>, "sender_name":"<?php echo $_COOKIE["VioomaUserID"];?>"},
		success: function(data){
			if(data >= 1) {
				$("#success").fadeIn(1500);
				$("#success").fadeOut(500);
				$("form")[0].reset()
			} else {
				alert(data);
			}
		}
	});
}
</script>
</head>
<body>
<table width="100%" border="0" id="table_style_all"
	cellpadding="0" cellspacing="0">
	<tr>
		<td id="table_style" class="l_t">&nbsp;</td>
		<td>&nbsp;</td>
		<td id="table_style" class="r_t">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
		<form>
			<table width="100%" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td><strong>发送消息</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                	        <tr>
            				    <td id="success" class="cellcolor" style="text-align: center; display: none;" colspan="2">发送成功</td>
            				</tr>
                			<tr>
                				<td class="cellcolor" width="30%">类型：</td>
                                 <td>
                                    <label><input type="radio" name="is_public" value="3"/>お問い合わせ管理</label>
                                    <label><input type="radio" name="is_public" value="1"/>公共</label>
                                    <label><input type="radio" name="is_public" value="0" checked="checked"/>私信</label>
                                    <label><input type="radio" name="is_public" value="2"/>紧急提示</label>
                        		</td>
                			</tr>
                			<tr id="reciverTr">
                				<td class="cellcolor" width="30%">收件人：</td>
                                 <td id="receiver">
                                  <label><input type="checkbox" onclick="chkAll(this)"/>全選</label><br/>
                        		</td>
                			</tr>
                			<tr style="display:none;" id="passwdTr">
                				<td class="cellcolor" width="30%">设置</td>
                                <td><input type="password" style="display:none"/>
                                                                                               密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码：<input type="password" name="setPass"/><br/>
                                                                                               确认密码：<input type="password" name="repeatPass"/><br/>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">标题：</td>
                                 <td>
                                    <input name="subject" size="100"/>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">内容：</td>
                				<td>
                                    <textarea rows="10" cols="80" name="content"></textarea>
                				</td>
                			</tr>
                			
                			<tr>
                				<td class="cellcolor">&nbsp;</td>
                				<td><input type="button" value="发送" onclick="checkForm()"/>
                				</td>
                			</tr>
            			</table>
            		</td>
            	</tr>
			</table>
	      </form>
		</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td id="table_style" class="l_b">&nbsp;</td>
			<td>&nbsp;</td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
		<?php 
copyright();
?>
</body>
</html>