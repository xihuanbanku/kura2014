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
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>お取り寄せ管理</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/CustomerWantService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr select").serialize() +"&flag=insert",
    		success: function(data){
        		if(data > 0) {
    				alert("成功");
    				initPage();
        		} else {
    				alert("失败");
        		}
    		}
    	});
	});
// 	$.ajax({
// 		type: "post",
// 		url: "../service/BulletinService.class.php",
// 		data: {"flag":"initReceiver"},
// 		success: function(data){
// 			data = eval("("+data+")");
//     		var html="";
//     		$.each(data, function(entryIndex, entry){
//     			html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
//     		});
//     		staffOptions = html;
//     		$("#transporter").html(html);
//     		$("#client").html(html);
// 		}
// 	});
	initPage();
})
function initPage() {
	$.ajax({
		type: "post",
		url: "../service/CustomerWantService.class.php",
		data: {"flag":"initPage", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(trimStr(data) == "null") {
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		$.each(data, function(entryIndex, entry){

        		html += ""
            		+"<tr onmouseout=\"javascript:this.bgColor='#FFFFFF';\" onmousemove=\"javascript:this.bgColor='#EBF1F6';\">"
            		+"    <td>"+entry.cp_number+"</td>"
            		+"    <td>"+entry.want_content+"</td>"
            		+"    <td>"+entry.want_date+"</td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_bought == 1 ? "checked=\"checked\"" : "")+" name=\"is_bought\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_transfor == 1 ? "checked=\"checked\"" : "")+" name=\"is_transfor\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_arrived == 1 ? "checked=\"checked\"" : "")+" name=\"is_arrived\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_passed == 1 ? "checked=\"checked\"" : "")+" name=\"is_passed\"/></td>"
            		+"    <td>"+entry.remark1+"</td>"
            		+"    <td>"+entry.remark2+"</td>"
            		+"    <td>"+entry.remark3+"</td>"
            		+"    <td>"+entry.remark4+"</td>"
            		+"    <td>"+entry.remark5+"</td>"
            		+"    <td><button >编辑</button><button value="+entry.id+" >删除</button></td>"
            		+"</tr>"
            		+"<tr class=\"hideTr\" onmouseout=\"javascript:this.bgColor='#FFFFFF';\" onmousemove=\"javascript:this.bgColor='#EBF1F6';\">"
            		+"    <td>"+entry.cp_number+"</td>"
            		+"    <td><input value=\""+entry.want_content+"\" size=\"10\" name=\"want_content\" type=\"text\"/></td>"
            		+"    <td>"+entry.want_date+"</td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_bought == 1 ? "checked=\"checked\"" : "")+" name=\"is_bought\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_transfor == 1 ? "checked=\"checked\"" : "")+" name=\"is_transfor\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_arrived == 1 ? "checked=\"checked\"" : "")+" name=\"is_arrived\"/></td>"
            		+"    <td><input value=\"1\" type=\"checkbox\""+(entry.is_passed == 1 ? "checked=\"checked\"" : "")+" name=\"is_passed\"/></td>"
            		+"    <td><input value=\""+entry.remark1+"\" size=\"10\" name=\"remark1\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark2+"\" size=\"10\" name=\"remark2\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark3+"\" size=\"10\" name=\"remark3\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark4+"\" size=\"10\" name=\"remark4\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark5+"\" size=\"10\" name=\"remark5\" type=\"text\"/></td>"
            		+"    <td><button value=\""+entry.id+"\">保存</button><button >取消</button></td>"
            		+"</tr>";




        		
    		});
    		$("#contentTable tbody tr:gt(0)").remove();
    		$("#contentTable tbody").append(html);


    		$("#contentTable button").each(function(i, item){
    			$(this).click(function(){
        			if(i%4 ==0) { //编辑
        				$(item).parent().parent().hide();
        				$(item).parent().parent().next().show();
//         				var sel = $(item).parent().parent().next().find("select[name=transporter]");
//         				sel.val(sel.prev().val());
//         				var selstff = $(item).parent().parent().next().find("select[name=client]");
//         				selstff.val(selstff.prev().val());
        			} else if(i%4 ==1) {//删除
//						if($(item).parent().parent().next().find("select[name=client]").prev().val() != ) {
// 							alert("没有权限");
// 							return;
// 						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/CustomerWantService.class.php",
        	        		data: "flag=delete&id="+$(this).attr("value"),
        	        		success: function(data){
        	            		if(data > 0) {
        	        				alert("成功");
        	        				initPage();
        	            		} else {
        	        				alert("失败");
        	            		}
        	        		}
        	        	});
					} else if(i%4 ==2) {//保存
//						if($(item).parent().parent().find("select[name=owner]").prev().val() != ) {
// 							alert("没有权限");
// 							return;
// 						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/CustomerWantService.class.php",
        	        		data: "flag=update&userID=<?php echo GetCookie('userID')?>&id="+$(this).attr("value")+"&"+$(item).parent().parent().find("input").serialize()+"&"+$(item).parent().parent().find("select").serialize(),
        	        		success: function(data){
        	            		if(data > 0) {
        	        				alert("成功");
        	        				initPage();
        	            		} else {
        	        				alert("失败");
        	            		}
        	        		}
        	        	});
        			} else {//取消
        				$(item).parent().parent().hide();
        				$(item).parent().parent().prev().show();
        			}
    			});
    		});
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
			<td align="center">
				<table id="barcodes" width="30%" border="0"
					style="text-align: center;">
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="2">
					<tr>
						<td><strong>お取り寄せ管理</strong></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0" id="table_border">
								<thead>
									<tr>
										<th class="cellcolor">管理番号</th>
										<th class="cellcolor">メール内容</th>
										<th class="cellcolor">メール日付</th>
										<th class="cellcolor">返信済</th>
										<th class="cellcolor">見積もり済</th>
										<th class="cellcolor">客注文済</th>
										<th class="cellcolor">出荷済</th>
										<th class="cellcolor">備考1</th>
										<th class="cellcolor">備考2</th>
										<th class="cellcolor">備考3</th>
										<th class="cellcolor">備考4</th>
										<th class="cellcolor">備考5</th>
										<th class="cellcolor">操作</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td><input size="10" name="cp_number" type="text"/></td>
                                        <td><input size="10" name="want_content" type="text"/></td>
                                        <td><input size="10" name="want_date" type="text" class="Wdate" onclick="WdatePicker()"/></td>
                                        <td><input size="10" name="is_bought" value="1" type="checkbox"/></td>
                                        <td><input size="10" name="is_transfor" value="1" type="checkbox"/></td>
                                        <td><input size="10" name="is_arrived" value="1" type="checkbox"/></td>
                                        <td><input size="10" name="is_passed" value="1" type="checkbox"/></td>
                                        <td><input size="10" name="remark1" type="text"/></td>
                                        <td><input size="10" name="remark2" type="text"/></td>
                                        <td><input size="10" name="remark3" type="text"/></td>
                                        <td><input size="10" name="remark4" type="text"/></td>
                                        <td><input size="10" name="remark5" type="text"/></td>
                                        <td><input type="button" id="subButton" value="提交"/></td>
                                    </tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
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
