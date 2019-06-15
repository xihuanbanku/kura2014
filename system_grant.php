<?php
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/fix_mysql.inc.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
require_once (dirname(__FILE__) . "/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<link href="style/loading.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#parent_row_style td {
	text-align:left;
	background-color:#b9b9ff;
}
#child_row_style td {
	text-align:left;
}

</style>
<title><?php echo $cfg_softname;?>担当者権限管理</title>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/loading.js"></script>
<script type="text/javascript">
var url = "service/MenuService.class.php";
$(function(){
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initUser"},
		success: function(data){
			data = eval("("+data+")");
			var html="";
			$.each(data, function(entryIndex, entry){
				html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
			});
			$("#users").html(html);
		}
	});
    $("#subbtn").click(function() {
    	showLoading();
    	var param = $("input").serialize();
    	var user = $("#users").val();
    	$.ajax({
    		type: "post",
    		url: url+"?"+param,
    		data: {"flag":"saveGrant", "user":user},
    		success: function(data){
        		if(data>0) {
            		alert("修改成功");
        		} else {
            		alert("修改失败");
        		}
        		hideLoading();
    		}
    	});
    });
});
function initGrant() {
	var user = $("#users").val();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initGrant", "user":user},
		success: function(data){
			data = eval("("+data+")");
			var html="";
			$.each(data, function(entryIndex, entry){
				var checked = "";
				var pageType = "";
				var parent = entryIndex.split("#");
				if(parent[2] > 0) {
					checked = "checked=\"checked\"";
				}
				if(parent[3] == 1) {
					pageType = "页面";
				} else if(parent[3] == 2) {
					pageType = "按钮";
				}
				html+=""
					+"<tr id='parent_row_style'>"
					+"    <td>&nbsp;&nbsp;" + parent[1] + " <font color='#999999'>("+pageType+":" + parent[1] + ")</font></td>"
					+"    <td><input type='checkbox' name='r[]' value='" + parent[0] + "'" + checked + "/></td>"
					+"</tr>"
					+"";
				$.each(entry, function(entryIndex1, entry1){
					checked = "";
					if(entry1.bloc > 0) {
						checked = "checked=\"checked\"";
					}
					pageType = "";
					if(entry1.btype == 1) {
						pageType = "页面";
					} else if(entry1.btype == 2) {
						pageType = "按钮";
					}
					html+=""
						+"<tr id='child_row_style' onmouseout=\"javascript:this.bgColor='#FFFFFF';\" onmouseover=\"javascript:this.bgColor='#EBF1F6';\">"
						+"    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--" + entry1.bname + " <font color='#999999'>("+pageType+":" + entry1.url + ")</font></td>"
						+"    <td><input type='checkbox' name='r[]' value='" + entry1.bid + "'" + checked + "/></td>"
						+"</tr>"
						+"";
				});
			});
			$("#table_border tr:gt(0)").remove();
			$("#table_border").append(html);
			$("#subbtn").show();
// 			$.each($("#table_border #child_row_style"), function(entryIndex1, entry1){
// 				$(entry1).css("text-align", "left");
// 				if(entryIndex1%2==1) {
// 					$(entry1).css("background-color", "#e1ffff");
// 				} else {
// 					$(entry1).css("background-color", "#ffffe0");
// 				}
// // 				alert($(entry1).attr("id"));
// 			});
		}
	});
}
</script>
</head>
<body>
	<table width="100%" border="0" id="table_style_all" cellpadding="0"
		cellspacing="0">
		<tr>
			<td id="table_style" class="l_t">&nbsp;</td>
			<td>&nbsp;</td>
			<td id="table_style" class="r_t">&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="2">
					<tr>
						<td><strong>&nbsp;担当者権限管理</strong></td>
						<td align="right"><select id="users"></select> <input type="button" value="修正" onclick="initGrant();"/></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF" colspan="2">
							<table width="100%" cellspacing="0" border="0" id="table_border">
								<tr>
									<td>
										<center>システム操作</center>
									</td>
									<td>
										<center>ユーザーグループ</center>
									</td>
								</tr>
							</table>
                        </td>
					</tr>
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		
		<tr>
    		<td>&nbsp;</td>
    		<td align="center"><input style="display: none;" type="button" id="subbtn" value="保存"/></td>
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
