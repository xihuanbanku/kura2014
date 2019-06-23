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
<title>写备忘录</title>
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
	$.each($("input[name='type']"), function(entryIndex, entry) {
		if($(entry).prop("checked")) {
			checkCount++;
		}
	});
	if(checkCount<=0){
		alert("请选择备忘类型");
		return;
	}
	checkCount =0;
	$.each($("input[name='strChk[]']"), function(entryIndex, entry) {
		if($(entry).prop("checked")) {
			checkCount++;
		}
	});
	if(checkCount<=0){
		alert("请选择收件人");
		return;
	}
	if($("input[name='subject']").val() == ""){
		alert("标题未填写");
		return;
	}
	var url = "../service/Calender.class.php?"+$("form").serialize();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"writeCalender", "userID":<?php echo $_COOKIE["userID"];?>, "sender_name":"<?php echo $_COOKIE["VioomaUserID"];?>"},
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
					<td><strong>书写备忘</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                	        <tr>
            				    <td id="success" class="cellcolor" style="text-align: center; display: none;" colspan="2">书写成功</td>
            				</tr>
                			<tr>
                				<td class="cellcolor" width="30%">类型：</td>
                                 <td>
                                    <label><input type="radio" name="type" value="3"/>当天</label>
                                    <select name="thisMonth">
                                        <?php for ($i=1; $i<=12; $i++){
                                            echo "<option value='".$i."'>".$i."</option>";
                                        }?>
                                    </select>
                                    <select name="thisDay">
                                        <?php for ($i=1; $i<=31; $i++){
                                            echo "<option value='".$i."'>".$i."</option>";
                                        }?>
                                    </select>
                                    <label><input type="radio" name="type" value="2"/>每月</label>
                                    <select name="monthDay">
                                        <?php for ($i=1; $i<=31; $i++){
                                            echo "<option value='".$i."'>".$i."</option>";
                                        }?>
                                    </select>
                                    <label><input type="radio" name="type" value="1"/>每周</label>
                                    <select name="weekDay">
                                        <?php for ($i=1; $i<=7; $i++){
                                            echo "<option value='".$i."'>".$i."</option>";
                                        }?>
                                    </select>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">收件人：</td>
                                 <td id="receiver">
                                  <label><input type="checkbox" onclick="chkAll(this)"/>全選</label><br/>
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
                				<td><input type="button" value="书写" onclick="checkForm()"/>
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