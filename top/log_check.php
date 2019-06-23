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
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title><?php echo $cfg_softname;?>查看日志</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var labOptions = "";
var staffOptions = "";
$(function(){
	initPage(0);
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/LogService.php",
		data: "flag=initPage&user=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&"+$("#searchForm").find("select").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&nocache="+new Date().getTime(),
		success: function(data){
			data = eval("("+data+")");
			var html = "";
			if(data.results != null) {
        		$.each(data.results, function(entryIndex, entry){
            		html += ""
                		+"<tr>"
    //             		+"    <td><input type=\"checkbox\" value=\"" + entry.kid + "\" name=\"strChk[]\"></td>"
                		+"    <td >"+entry.productid+"</td>"
                		+"    <td>"+entry.pos+"</td>"
                		+"    <td>" + entry.member + "</td>"
                		+"    <td>" + entry.op_type_text + "</td>"
                		+"    <td>" + entry.col1 + "</td>"
                		+"    <td>" + entry.col2 + "</td>"
    //             		+"    <td>" + entry.col3 + "</td>"
                		+"    <td>" + entry.col4 + "</td>"
                		+"    <td>" + entry.col5 + "</td>"
    //             		+"    <td>" + entry.col6 + "</td>"
                		+"    <td>"+entry.dtime+"</td>"
                		+"</tr>";
    
        		});
			}
    		$("#contentTable tbody").html(html);
//     		//为table中的span 绑定双击修改事件, 同时为相邻的input 绑定blur提交事件
//     		$("#contentTable tr:gt(0) span").each(function(i, item){
// 			    //alert($(item));
// 		        $(item).dblclick(function(){
// 		            $(item).toggle();
// 		            $(item).next().toggle();
// 		            $(item).next().focus();
// 		        });
		        
// 		        $(item).next().blur(function(){
// 			        _input = $(this);
// 			        _input.prev().toggle();
// 			        _input.toggle();
		        	
// 		        	$.ajax({
// 		        		type: "post",
// 		        		url: "../service/BuyingService.php?strChk=" + _input.parent().parent().find(":checkbox").val()+ "&" + _input.serialize(),
//		        		data: {"flag":"updateColNumber", "userID":,
// 		        		success: function(data){
// 	                		if(data > 0) {
// 	                			_input.prev().html(_input.val());
// 	            				alert("修改成功")
// 	                		} else {
// 	            				alert("修改失败")
// 	                		}
//                 		}
//             		});
// 	        	});
// 		    })

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
function exportExcel() {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/BuyingService.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&pageNameId=<?php echo $_REQUEST["pageNameId"] ?>&"+param+"&flag=exportExcel";
    //window.open('excel_kc.php?shop='+shop+'&cp_categories='+cp+'&cp_categories_down='+cp_down+'&sort='+s+'&stext='+st,'','');
    window.open(url);
}
</script>
<style type="text/css">
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
						<td colspan="2"><strong>查看日志</strong></td>
					</tr>
					<tr>
					   <!--<td>
    					   <button onclick="exportExcel()">导出</button>
    					   <button onclick="updateRemark1()">保存</button>
    					   <button style="display: none;" id="passChecked0Button" type="button" onclick="passChecked(0)">审核周3前未审核数</button>
    					   <button style="display: none;" id="passChecked1Button" type="button" onclick="passChecked(1)">审核周3前当日仕入れ数</button>
    					   <button style="display: none;" id="passChecked2Button" type="button" onclick="passChecked(2)">审核周3后未审核数</button>
    					   <button style="display: none;" id="passChecked3Button" type="button" onclick="passChecked(3)">审核周3后当日上货数</button>
    					   <button style="display: none;" id="deleteCheckedButton" type="button" onclick="deleteChecked()">删除</button>
    					   <button style="display: none;" id="transfer2OtherButton" type="button" onclick="transfer2Other()">切换至</button>
    					   <select name="transferSelect">
    					       <option value="0">仕入れ表0</option>
    					       <option value="1">仕入れ表1</option>
    					       <option value="2">仕入れ表2</option>
    					   </select>
					   </td>-->
						<td colspan="2" style="text-align: right;" id="searchForm">
						日期<input type="text" name="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash;
                           <input type="text" name="edate"size="15" value="" class="Wdate" onclick="WdatePicker()"/>
					       操作类型<select name="opTypeSelect">
    					       <option value="-1">全部</option>
    					       <option value="0">审核周3前未审核数</option>
    					       <option value="1">审核周3前当日仕入れ数</option>
    					       <option value="2">审核周3后未审核数</option>
    					       <option value="3">审核周3后当日上货数</option>
    					       <option value="4">修改第1列</option>
    					       <option value="5">修改第2列</option>
    					       <option value="6">修改第3列</option>
    					       <option value="7">修改第4列</option>
    					       <option value="8">修改第5列</option>
    					       <option value="9">修改第6列</option>
    					       <option value="10">手打ち仕入れ表0</option>
    					       <option value="11">手打ち仕入れ表1</option>
    					       <option value="12">手打ち仕入れ表2</option>
    					       <option value="13">手打ち仕入れ表3</option>
					       </select>
						关键字<input name="s_text" type="text"/>
						<button onclick="initPage(0)">搜索</button></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF" colspan="2">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <!-- <th class="dcs"><input type="checkbox" onclick="chkAll(this)"/>選択</th> -->
                                        <th class="cellcolor">商品コード</th>
                                        <th class="cellcolor">在庫位置<br/>階-棚-ゾーン-横-縦</th>
                                        <th class="cellcolor">操作人</th>
                                        <th class="cellcolor">操作类型</th>
                                        <th class="cellcolor">周3前未审核数</th>
                                        <th class="cellcolor">周3前当日仕入れ数</th>
<!--                                         <th class="cellcolor">周3前仕入れ総合</th> -->
                                        <th class="cellcolor">周3后未审核数</th>
                                        <th class="cellcolor">周3后当日仕入れ数</th>
<!--                                         <th class="cellcolor">周3后仕入れ総合</th> -->
                                        <th class="cellcolor">操作日期</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td></td>
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

