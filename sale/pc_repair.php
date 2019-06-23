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
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>パソコン修理管理</title>
<style type="text/css">
.hideTr{
	display:none;
}
#contentTable{
	table-layout: fixed;
}
#contentTable tr:hover{
	background-color: #EBF1F6;
}
#contentTable tr:hover td{
	white-space: normal;
	overflow: unset;
	text-overflow: unset;
	overflow-wrap: break-word;
}
#contentTable th{
	white-space: normal;
	overflow: unset;
	text-overflow: unset;
}
#contentTable tr td{
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
}
</style>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var labOptions = "";
var staffOptions = "";
$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/PcRepairService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr select").serialize()+"&flag=insert",
    		success: function(data){
        		if(data > 0) {
    				alert("成功");
    				initPage(0);
    				$("#formTr input").each(function(i, item){
						$(item).val("");
    				})
        		} else {
    				alert("失败");
        		}
    		}
    	});
	});
	$.ajax({
		type: "post",
		url: "../service/KcService.class.php",
		data: {"flag":"initLab"},
		success: function(msg){
			labOptions = msg;
			$("#labid option:gt(0)").remove();
    		$("#labid").append(msg);
			initPage(0);
		}
	});

	$.ajax({
		type: "post",
		url: "../service/BulletinService.class.php",
		data: {"flag":"initReceiver"},
		success: function(data){
			data = eval("("+data+")");
    		var html="";
    		$.each(data, function(entryIndex, entry){
    			html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
    		});
    		staffOptions = html;
    		$("#owner").html(html);
		}
	});
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/PcRepairService.class.php",
		data: "flag=initPage&user=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
        		html += ""
            		+"<tr>"
            		+"    <td>"+entry.pc_number+"</td>"
            		+"    <td>"+entry.cust_name+"</td>"
            		+"    <td>"+entry.problem+"</td>"
            		+"    <td>"+entry.pc_type+"</td>"
            		+"    <td>"+entry.accessories+"</td>"
            		+"    <td>"+entry.remark1+"</td>"
            		+"    <td>"+entry.want_date+"</td>"
            		+"    <td>"+entry.progress+"</td>"
            		+"    <td>"+entry.eta+"</td>"
            		+"    <td>"+entry.phone+"</td>"
            		+"    <td>"+(entry.status==0?"未完成" : "完了")+"</td>"
            		+"    <td>"+entry.l_name+"</td>"
            		+"    <td>"+entry.remark2+"</td>"
            		+"    <td>"+entry.s_name+"</td>"
            		+"    <td>"+entry.remark3+"</td>"
            		+"    <td><button >编辑</button><button value="+entry.pc_id+" >删除</button></td>"
            		+"</tr>"
            		+"<tr class=\"hideTr\">"
            		+"    <td><input value=\""+entry.pc_number+"\" size=\"10\" name=\"pc_number\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.cust_name+"\" size=\"10\" name=\"cust_name\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.problem+"\" size=\"10\" name=\"problem\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.pc_type+"\" size=\"10\" name=\"pc_type\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.accessories+"\" size=\"10\" name=\"accessories\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark1+"\" size=\"10\" name=\"remark1\" type=\"text\"/></td>"
            		+"    <td>"+entry.want_date+"</td>"
            		+"    <td><input value=\""+entry.progress+"\" size=\"10\" name=\"progress\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.eta+"\" size=\"10\" name=\"eta\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.phone+"\" size=\"10\" name=\"phone\" type=\"text\"/></td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.status+"\"/><input type=\"radio\" name=\"status\" value=\"0\"/>未完成<input type=\"radio\" name=\"status\" value=\"1\"/>完了</td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.l_id+"\"/><select style=\"width:70px;\" name=\"pc_store\">" + labOptions +"</select> </td>"
            		+"    <td><input value=\""+entry.remark2+"\" size=\"10\" name=\"remark2\" type=\"text\"/></td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.owner+"\"/><select style=\"width:70px;\" name=\"owner\">" + staffOptions +"</select></td>"
            		+"    <td><input value=\""+entry.remark3+"\" size=\"10\" name=\"remark3\" type=\"text\"/></td>"
            		+"    <td><button value=\""+entry.pc_id+"\">保存</button><button >取消</button></td>"
            		+"</tr>";

    		});
    		$("#contentTable tbody tr:gt(0)").remove();
    		$("#contentTable tbody").append(html);


    		$("#contentTable button").each(function(i, item){
    			$(this).click(function(){
        			if(i%4 ==0) { //编辑
        				$(item).parent().parent().hide();
        				$(item).parent().parent().next().show();
        				var sel = $(item).parent().parent().next().find("select[name=pc_store]");
        				sel.val(sel.prev().val());
        				var selstff = $(item).parent().parent().next().find("select[name=owner]");
        				selstff.val(selstff.prev().val());
        				var radiostatus = $(item).parent().parent().next().find("input[name=status]");
        				radiostatus.eq(radiostatus.prev().val()).attr("checked",true);
        			} else if(i%4 ==1) {//删除
						if($(item).parent().parent().next().find("select[name=owner]").prev().val() != <?php echo GetCookie('userID')?>) {
							alert("没有权限");
							return;
						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/PcRepairService.class.php",
        	        		data: "flag=delete&id="+$(this).attr("value"),
        	        		success: function(data){
        	            		if(data > 0) {
        	        				alert("成功");
        	        				initPage(0);
        	            		} else {
        	        				alert("失败");
        	            		}
        	        		}
        	        	});
					} else if(i%4 ==2) {//保存
//						if($(item).parent().parent().find("select[name=owner]").prev().val() != <?php echo GetCookie('userID')?>) {
// 							alert("没有权限");
// 							return;
// 						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/PcRepairService.class.php",
        	        		data: "flag=update&user=<?php echo GetCookie('userID')?>&id="+$(this).attr("value")+"&"+$(item).parent().parent().find("input").serialize()+"&"+$(item).parent().parent().find("select").serialize(),
        	        		success: function(data){
        	            		if(data > 0) {
        	        				alert("成功");
        	        				initPage(0);
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


    		$.each(data.totalproperty,function(entryIndex,entry){
    			recordCount = entry.totalcount;	
    		});
    		//调用用分页函数，将分页插件绑定到id为Pagination的div上
    	    $("#Pagination").pagination(recordCount, { //recordCount在后台定义的一个公有变量，通过从数据库获取行数，返回全部的行数
        	    callback: initPageCallback,  //点击分页，调用的毁掉函数
        	    prev_text: '上一页',  //展示上一页按钮的文本
        	    next_text: '下一页',  //展示下一页按钮的文本
        	    items_per_page:pageCount,  //展示的页数
        	    num_display_entries:10,  //分页插件中显示的按钮数目
        	    current_page:pageIndex,  //当前页索引
        	    num_edge_entries:1  //分页插件左右两边表示的按页数目
    	    });
  	   	    $("#totalPage").html("<span style='font-size: 100%;color:#A17B20'>总共："+recordCount+"条</span>");
		}
	});
}
function initPageCallback(page_id, jq) {
	initPage(page_id);
}
function orderBy(sortid) {
	if($("input[name=orderBy]").val() == sortid) {
		sortid=sortid+1;
	} else {
		sortid=sortid;
	}
	$("input[name=orderBy]").val(sortid);
	initPage(0);
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
						<td><strong>パソコン修理管理</strong></td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm"><input type="hidden" name="orderBy" value="" />关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(5)">管理番号</a></th>
										<th class="cellcolor">お客様名前</th>
										<th class="cellcolor">故障内容</th>
										<th class="cellcolor">機種</th>
										<th class="cellcolor">付属品</th>
										<th class="cellcolor">備考1</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(1)">受付日</a></th>
										<th class="cellcolor">進行状況</th>
										<th class="cellcolor">渡す予定日</th>
										<th class="cellcolor">連絡</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(3)">状态</a></th>
										<th class="cellcolor">仓库 </th>
										<th class="cellcolor">備考2</th>
										<th class="cellcolor">担当者</th>
										<th class="cellcolor">備考3</th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td><input size="10" name="pc_number" type="text"/></td>
                                        <td><input size="10" name="cust_name" type="text"/></td>
                                        <td><input size="10" name="problem" type="text"/></td>
                                        <td><input size="10" name="pc_type" type="text"/></td>
                                        <td><input size="10" name="accessories" type="text"/></td>
                                        <td><input size="10" name="remark1" type="text"/></td>
                                        <td><input size="10" name="want_date" type="text" class="Wdate" onclick="WdatePicker()"/></td>
                                        <td><input size="10" name="progress" type="text"/></td>
                                        <td><input size="10" name="eta" type="text"/></td>
                                        <td><input size="10" name="phone" type="text"/></td>
                                        <td><input type="radio" name="status" value="0"/>未完成<input type="radio" name="status" value="1"/>完了</td>
                                        <td><select style="width:70px;" name="pc_store" id="labid">
                                        </select> </td>
                                        <td><input size="10" name="remark2" type="text"/></td>
                                        <td><select style="width:70px;" name="owner" id="owner"></select></td>
                                        <td><input size="10" name="remark3" type="text"/></td>
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
			<td><div id="Pagination" ></div><div id="totalPage"></div></td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
		<?php 
copyright();
?>
</body>
</html>
