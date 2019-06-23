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
<title>商品贩卖分析</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var cpCategoriesOptions = "<option value=\"\">大分類選択</option>";
var cpCategoriesDownOptions = "<option value=\"\">小分類選択</option>";
$(function(){
	//初始化分类
	$.ajax({
		type: "post",
		url: "../service/KcService.class.php",
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value='-1'>大分類選択</option>" +msg;
	        cpCategoriesOptions=msg;
    		$("#cp_categories").html(msg);
		}
	});
	//初始化状态下拉框
	$.each($("#writeTipDiv select"), function(index, item) {
		$.ajax({
			type: "post",
			url: "../service/AnalyzeService.class.php",
			data: {"flag":"initState", "sid": index+1},
			success: function(msg){
				$("#state"+(index+1) + "id option:gt(0)").remove();
	    		$("#state"+(index+1) + "id").append(msg);
	        	$("#writeState"+(index+1) + "Select option:gt(0)").remove();
	        	$("#writeState"+(index+1) + "Select").append(msg);
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
		url: "../service/AnalyzeService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&"+$("#searchForm").find("select").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&nocache="+new Date().getTime(),
		success: function(data){
			data = eval("("+data+")");
			if(data.totalcount == 0) {
	    		$("#contentTable tbody tr").remove();
			} else {
    			var html = "";
        		$.each(data.results, function(entryIndex, entry){
    				var trStyle = "";
    				if(entry.status == 0) {
    					trStyle = "style=\"background-color: #8b968d;\"";
    				}
            		html += ""
                		+"<tr "+trStyle+">"
                		+"    <td><input type=\"checkbox\" value=\"" + entry.cp_number + "\" name=\"strChk[]\"></td>"
                		+"    <td>"+entry.cp_number+"</td>"
                		+"    <td>"+entry.number+"</td>"
                		+"    <td>"+entry.astate1_str+"</td>"
                		+"    <td>"+entry.astate2_str+"</td>"
                		+"    <td>"+entry.astate3_str+"</td>"
                		+"    <td>"+entry.astate4_str+"</td>"
                		+"    <td>"+entry.astate5_str+"</td>"
                		+"    <td>"+entry.mindate+"</td>"
                		+"    <td>"+entry.maxdate+"</td>"
                		+"</tr>";
        		});
        		$("#contentTable tbody tr").remove();
        		$("#contentTable tbody").append(html);
			}
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
function orderBy(sortid) {
	if($("input[name=orderBy]").val() == sortid) {
		sortid=sortid+1;
	} else {
		sortid=sortid;
	}
	$("input[name=orderBy]").val(sortid);
	initPage(0);
}
//导出excel
function exportExcel() {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/AnalyzeService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
//获取子分类
function getCategoryDown(param) {
	$.ajax({
		type: "post",
		url: "../service/KcService.class.php",
		data: {"flag":"getCategoryDown", "cp_categories":param},
		success: function(msg){
			$("#cp_categories_down option:gt(0)").remove();
    		$("#cp_categories_down").append(msg);
    		$("select[name=cp_categories_down] option:gt(0)").remove();
    		$("select[name=cp_categories_down]").append(msg);
		}
	});  
}
//更新状态
function writeState() {
	var noUpdateState = true;
	var param = $("input").serialize();
	$.each($("#writeTipDiv select"), function(index, item) {
		if($(item).val()!="") {
			noUpdateState = false;
			return false;
		}
	});
	if(noUpdateState) {
		alert("没有需要更新的状态");
		return;
	}
	var checkedCount = 0;
    $.each($("#contentTable input:checkbox:gt(0)"), function(entryIndex, entry) {
    	if($(entry).prop("checked")) {
    		checkedCount ++;
    	}
    });
	if(checkedCount ==0) {
// 		if(!confirm("更新全部数据?")) {
// 			return;
// 		}
		alert("没有选中记录");
		return;
	}
	var flag = true;
	$.each($("[id^='writeState']"), function(index, item) {
		if($(item).val() =="0") {
			if(!confirm("清空状态" + (index+1) +"?")) {
				flag = false;
				return flag;
			}
		}
		if($(item).val() !="") {
			param+="&"+$(item).serialize();
		}
	});
	if(!flag) {
		return;
	}
	$.ajax({
		type: "post",
		url: "../service/AnalyzeService.class.php?" + param,
		data: {
				"flag":"writeState", 
				"user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
				initPage(0);
			} else {
				alert("更新失败");
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
						<td><strong>商品贩卖分析</strong>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderBy" value="" />
							分類:
							<select name="cp_categories" id="cp_categories" onchange="getCategoryDown(this.value)"><option value='-1'>大分類選択</option></select>->
                            <select name="cp_categories_down" id="cp_categories_down"><option value='-1'>小分類選択</option></select>
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()" >导出</button><br/>
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 55px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th class="cellcolor">商品コード</th>
										<th class="cellcolor">库存</th>
										<th class="cellcolor">状态1</th>
										<th class="cellcolor">状态2</th>
										<th class="cellcolor">状态3</th>
										<th class="cellcolor">状态4</th>
										<th class="cellcolor">状态5</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(1)">第一次售出日</a></th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(2)">最近一次售出日</a></th>
									</tr>
								</thead>
								<tbody>
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
			<td><div id="writeTipDiv">
                    <span>
                        <select id="writeState1Select" name="writeState1Select">
                            <option value=''>状態1</option>
                        </select>
                        <a target="_blank" href="../system/system_static_list.php?pType=SYSTEM_ANALYZE_STATE1">管理</a>
                        <select id="writeState2Select" name="writeState2Select">
                            <option value=''>状態2</option>
                        </select>
                        <a target="_blank" href="../system/system_static_list.php?pType=SYSTEM_ANALYZE_STATE2">管理</a>
                        <select id="writeState3Select" name="writeState3Select">
                            <option value=''>状態3</option>
                        </select>
                        <a target="_blank" href="../system/system_static_list.php?pType=SYSTEM_ANALYZE_STATE3">管理</a>
                        <select id="writeState4Select" name="writeState4Select">
                            <option value=''>状態4</option>
                        </select>
                        <a target="_blank" href="../system/system_static_list.php?pType=SYSTEM_ANALYZE_STATE4">管理</a>
                        <select id="writeState5Select" name="writeState5Select">
                            <option value=''>状態5</option>
                        </select>
                        <a target="_blank" href="../system/system_static_list.php?pType=SYSTEM_ANALYZE_STATE5">管理</a>
                        <input type="button" onclick="writeState()" value="更新状態" />
                    </span>
                </div><div id="Pagination" ></div><div id="totalPage"></div></td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
		<?php 
copyright();
?>
</body>
</html>
