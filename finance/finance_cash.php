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
<script type="text/javascript" src="../js/loading.js?r=<?php echo rand()?>"></script>
<title>资金动向</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var staffOptions ="";
$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/FinanceCashService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr textarea").serialize()+"&"+$("#formTr select").serialize()+"&flag=insert",
    		success: function(data){
        		if(data > 0) {
    				alert("成功");
    				initPage(0);
    				$("#formTr textarea:not([name$=date]):not([name=status]):not([type=button])").each(function(i, item){
						$(item).val("");
    				})
        		} else {
    				alert("失败");
        		}
    		}
    	});
	});
	initPage(0);
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/FinanceCashService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
				var trStyle = "";
        		html += ""
            		+"<tr>"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
            		+"    <td>"+entry.aid+"</td>"
            		+"    <td>"+entry.going_type_str+"</td>"
            		+"    <td style='text-align: right;' >"+number_format(entry.initial)+"</td>"
            		+"    <td style='text-align: right;' >"+number_format(entry.s_real_rest)+"</td>"
            		+"    <td style='text-align: right;' >"+number_format(entry.s_rest_not_back)+"</td>"
            		+"    <td><button >修正</button><button value="+entry.id+" >削除</button></td>"
            		+"</tr>"
            		+"<tr class=\"hideTr\" >"
            		+"    <td></td>"
            		+"    <td><input value=\""+entry.aid+"\" size=\"10\" name=\"aid\" type=\"text\"/></td>"
            		+"    <td>"+entry.going_type_str+"</td>"
            		+"    <td>"+entry.initial+"</td>"
            		+"    <td title=\""+entry.s_real_rest+"\">"+entry.s_real_rest+"</td>"
            		+"    <td title=\""+entry.s_rest_not_back+"\">"+entry.s_rest_not_back+"</td>"
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
        				var methodsel = $(item).parent().parent().next().find("select[name=method]");
        				methodsel.val(methodsel.prev().val());
        			} else if(i%4 ==1) {//删除
//						if($(item).parent().parent().next().find("select[name=owner]").prev().val() != ) {
// 							alert("没有权限");
// 							return;
// 						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/FinanceCashService.class.php",
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
        	        		url: "../service/FinanceCashService.class.php",
        	        		data: "flag=update&userID=<?php echo GetCookie('userID')?>&id="+$(this).attr("value")+"&"+$(item).parent().parent().find("input").serialize()+"&"+$(item).parent().parent().find("select").serialize(),
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

			recordCount = data.totalcount;
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
//排序
function orderBy(obj) {
	if($("#searchForm input[name=\"orderBy\"]").val() == obj) {
		$("#searchForm input[name=\"orderBy\"]").val(obj+1);
	} else {
		$("#searchForm input[name=\"orderBy\"]").val(obj);
	}
	initPage(0);
}
//导出excel
function exportExcel() {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/FinanceCashService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
    window.open(url);
}
//全选
function chkAll(param) {
    if ($(param).prop("checked")) {
        $.each($("#contentTable input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", true);
        });
    } else {
        $.each($("#contentTable input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", false);
        });
    }
}
//批量更新状态
function updateStatus() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("select[name=statusOptions]").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceCashService.class.php?" + param,
		data: {
			"flag":"updateStatus",
			"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
				initPage(0); 
			} else {
				alert("失败");
			}
		}
    });
}
//批量更新备注3
function updateRemark3() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("input[name=remark3Input]").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceCashService.class.php?" + param,
		data: {
			"flag":"updateRemark3",
			"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
				initPage(0); 
			} else {
				alert("失败");
			}
		}
    });
}
//批量删除
function deleteChked() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceCashService.class.php?" + param,
		data: {
			"flag":"deleteChked",
			"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功删除" + eval(data) +"条记录");
				initPage(0); 
			} else {
				alert("失败");
			}
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
						<td><strong>资金动向</strong></td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderBy" value="" />
                        	日期:
                            <input type="text" name="s_atime" size="15" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                            <input type="text" name="e_atime" size="15" class="Wdate" onclick="WdatePicker()"/>
							<button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br/>
							<!-- 关键字<input name="s_text" type="text"/>
							<select name="statusOptions">
								<option value="0">未着</option>
								<option value="1">到着</option>
								<option value="2">済</option>
								<option value="3">未完成</option>
							</select>
							<button onclick="updateStatus()">更新状态</button>
							<input type="text" name="remark3Input" />
							<button onclick="updateRemark3()">更新返品対策</button>
							<button onclick="deleteChked()">删除</button>
							-->
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 20px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th class="cellcolor">番号</th>
										<th class="cellcolor">资金帐号名称</th>
										<th class="cellcolor">初期金额</th>
										<th class="cellcolor">实际金额</th>
										<th class="cellcolor">全部计入挂金之和</th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <!-- <td></td>
                                        <td><textarea name="id" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="going_type" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="price" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="number" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="total" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="remark1" rows="6" cols="15"></textarea></td>
                                        <td><input size="10" name="atime" type="text" class="Wdate" onclick="WdatePicker()"/></td>
                                        <td><php echo date("Y-m-d")?></td>
                                        <td><input id="subButton" type="button" value="追加"></input></td>
                                         -->
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
