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
<title>仕入れ表<?php echo $_REQUEST["pageNameId"] ?></title>
<style type="text/css">
#contentTable {
	table-layout: fixed;
	width:100%;
}

#contentTable td{
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
}
#contentTable tr:hover{
	background-color: #EBF1F6;
}
#contentTable tr:hover td{
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;	
}
.dcs{
	width:40;
}
</style>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var labOptions = "";
var staffOptions = "";
$(function(){
	initPage(0);

	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"36", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
        		} else {
    				$("#" + entry.url).remove();
        		}
    		});
		}
	});
	
	//搜索框绑定回车事件
	$("input[name='s_text']").bind("keydown",function(e){
	    // 兼容FF和IE和Opera
	    var theEvent = e || window.event;
	    var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
	    if (code == 13) {
	        //回车换行
	    	initPage(0);
        }
	});
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/BuyingService.php",
		data: "flag=initPage&user=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=<?php echo $_REQUEST["pageNameId"] ?>&nocache="+new Date().getTime(),
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
            		+"    <td><input type=\"checkbox\" value=\"" + entry.kid + "\" name=\"strChk[]\"></td>"
            		+"    <td><a target=\"_blank\" href=\"../system_basic_refer.php?id="+entry.cp_number+"\">"+entry.cp_number+"</a></td>"
            		+"    <td title=\""+entry.cp_title+"\">"+entry.cp_title+"</td>"
            		+"    <td title=\""+entry.cp_gg+"\">"+entry.cp_gg+"</td>"
            		+"    <td>"+entry.l_id+"</td>"
            		+"    <td>"+entry.position+"</td>"
            		+"    <td>"+entry.number+"</td>"
            		+"    <td><span>" + entry.col1 + "</span><input style=\"display: none;\" name=\"col1\" type=\"text\" value=\""+entry.col1+"\"/></td>"
            		+"    <td><span>" + entry.col2 + "</span><input style=\"display: none;\" name=\"col2\" type=\"text\" value=\""+entry.col2+"\"/></td>"
            		+"    <td><span>" + entry.col3 + "</span><input style=\"display: none;\" name=\"col3\" type=\"text\" value=\""+entry.col3+"\"/></td>"
            		+"    <td><span>" + entry.col4 + "</span><input style=\"display: none;\" name=\"col4\" type=\"text\" value=\""+entry.col4+"\"/></td>"
            		+"    <td><span>" + entry.col5 + "</span><input style=\"display: none;\" name=\"col5\" type=\"text\" value=\""+entry.col5+"\"/></td>"
            		+"    <td><span>" + entry.col6 + "</span><input style=\"display: none;\" name=\"col6\" type=\"text\" value=\""+entry.col6+"\"/></td>"
            		+"    <td>"+entry.l_state9+"</td>"
            		+"    <td>"+entry.on_board_date+"</td>"
            		+"    <td>"+entry.check_flag+"</td>"
            		+"    <td><input name=\"remark1[]\" type=\"text\" value=\""+entry.remark1+"\"/></td>"
            		+"    <td>"+entry.arrival_luggage+"</td>"
            		+"    <td><input name=\"remark2[]\" type=\"text\" value=\""+entry.remark2+"\"/></td>"
            		+"</tr>";

    		});
    		$("#contentTable tbody").html(html);

    		//为table中的span 绑定双击修改事件, 同时为相邻的input 绑定blur提交事件
    		$("#contentTable tr:gt(0) span").each(function(i, item){
			    //alert($(item));
		        $(item).dblclick(function(){
		            $(item).toggle();
		            $(item).next().toggle();
		            $(item).next().focus();
		        });
		        
		        $(item).next().blur(function(){
			        _input = $(this);
			        _input.prev().toggle();
			        _input.toggle();
		        	
		        	$.ajax({
		        		type: "post",
		        		url: "../service/BuyingService.php?strChk=" + _input.parent().parent().find(":checkbox").val()+ "&" + _input.serialize(),
		        		data: {"flag":"updateColNumber", "userID":<?php echo GetCookie('userID')?>, "VioomaUserID":"<?php echo GetCookie('VioomaUserID')?>"},
		        		success: function(data){
	                		if(data > 0) {
	                			_input.prev().html(_input.val());
	            				alert("修改成功")
	                		} else {
	            				alert("修改失败")
	                		}
                		}
            		});
	        	});
		    })

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
function orderByField(obj) {
	switch (obj) {
    	case 1:
        	if($("input[name=orderByField").val() == "order by col1 asc") {
        		$("input[name=orderByField").val("order by col1 desc");
        	} else {
        		$("input[name=orderByField").val("order by col1 asc");
        	}
        	break;
    	case 2:
        	if($("input[name=orderByField").val() == "order by col2 asc") {
        		$("input[name=orderByField").val("order by col2 desc");
        	} else {
        		$("input[name=orderByField").val("order by col2 asc");
        	}
        	break;
    	case 3:
        	if($("input[name=orderByField").val() == "order by col3 asc") {
        		$("input[name=orderByField").val("order by col3 desc");
        	} else {
        		$("input[name=orderByField").val("order by col3 asc");
        	}
        	break;
    	case 4:
        	if($("input[name=orderByField").val() == "order by col4 asc") {
        		$("input[name=orderByField").val("order by col4 desc");
        	} else {
        		$("input[name=orderByField").val("order by col4 asc");
        	}
        	break;
    	case 5:
        	if($("input[name=orderByField").val() == "order by col5 asc") {
        		$("input[name=orderByField").val("order by col5 desc");
        	} else {
        		$("input[name=orderByField").val("order by col5 asc");
        	}
        	break;
    	case 6:
        	if($("input[name=orderByField").val() == "order by col6 asc") {
        		$("input[name=orderByField").val("order by col6 desc");
        	} else {
        		$("input[name=orderByField").val("order by col6 asc");
        	}
        	break;
		
	}
	initPage(0);
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
function exportExcel(pname) {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/BuyingService.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&pageNameId="+pname+"&"+param+"&flag=exportExcel";
    //window.open('excel_kc.php?shop='+shop+'&cp_categories='+cp+'&cp_categories_down='+cp_down+'&sort='+s+'&stext='+st,'','');
    window.open(url);
}

function passChecked(obj) {
	var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&passCheckedSelect="+obj;
	$.ajax({
		type: "post",
		url: "../service/BuyingService.php?" + param,
		data: {
			"flag":"passChecked", 
			"userID":<?php echo GetCookie('userID')?>,
			"VioomaUserID":"<?php echo GetCookie('VioomaUserID')?>"},
		success: function(data){
			if(data >0) {
				alert("成功审核" + eval(data) +"条记录");
				initPage(0);
			} else {
				alert("审核失败");
    		}
		}
	});
}
function deleteChecked() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize();
    $.ajax({
		type: "post",
		url: "../service/BuyingService.php?" + param,
		data: {
			"flag":"deleteChecked",
			"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
					alert("成功删除" + eval(data) +"条记录");
					initPage(0); 
			} else {
					alert("删除失败");
			}
		}
    });
} 
function transfer2Other() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("select[name=transferSelect]").serialize();
    $.ajax({
		type: "post",
		url: "../service/BuyingService.php?" + param,
		data: {
			"flag":"transfer2Other",
			"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功切换" + eval(data) +"条记录");
				initPage(0); 
			} else {
				alert("切换失败");
			}
		}
    });
} 
function updateRemark1() {
	var param = "";
	$("#contentTable tr:gt(0) input[type=checkbox]:checked").each(function(i, item) {
		param+=$(item).parent().parent("tr").find("input").serialize()+"&";
	})
	$.ajax({
		type: "post",
		url: "../service/BuyingService.php?" + param,
		data: {
				"flag":"updateRemark1", 
				"userID":<?php echo GetCookie('userID')?>},
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

<?php
require_once '../service/BuyingService.php';

if ($_FILES['inputExcelBuy']['size'] >0||$_FILES['inputExcelOnBoard']['size'] >0) {
    $buyingService = new BuyingService();
    if($_FILES['inputExcelBuy']['size'] >0) {
    // 获取上传的文件名
        $filename = $_FILES['inputExcelBuy']['name'];
    // 上传到服务器上的临时文件名
        $tmp_name = $_FILES['inputExcelBuy']['tmp_name'] ;
    // 上传按钮顺序,区分类型
        $type = 1;
    } else {
        $filename = $_FILES['inputExcelOnBoard']['name'];
        $tmp_name = $_FILES['inputExcelOnBoard']['tmp_name'] ;
        $type = 2;
    }
    $msg = $buyingService->uploadFile($type, $filename, $tmp_name);
    switch ($msg["m"]) {
        case 0:
            echo "文件上传成功<br/>";
        break;
        case 1:
            echo "<br/><font color='red'>导入失败</font><br/>";
        break;
        case 2:
            echo "<br/><font color='red'>更新失败</font><br/>";
        break;
        case 3:
            echo "<br/><font color='red'>删除失败</font><br/>";
        break;
        case 4:
            echo "<br/><font color='red'>操作类型错误</font><br/>";
        break;
        case 5:
            echo "<br/><font color='red'>excel上传失败</font><br/>";
        break;
        
        default:
           echo "system error" ;
        break;
    }
    echo "上传{$msg["n"]}件<br/>";
//    echo "更新{$msg["u"]}件<br/>";
//    echo "删除{$msg["d"]}件<br/>";
}

?>
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
						<td colspan="2"><strong>仕入れ表<?php echo $_REQUEST["pageNameId"] ?></strong></td>
					</tr>
					<tr>
						<td colspan="2">
                            <font color="red">上传文件模板使用导出内容即可，导出的文件内容A列(商品コード)不可删除，否则无法上传成功。<br/>
						          填写G列即可更新"仕入れ数"<br/>
						          填写J列即可更新"発送済荷物数"<br/>
                            </font>
                        </td>
					</tr>
                     <tr bgcolor="#FFEEFF" style="display:none;" id="inputExcelBuyTr">
                         <td  align="left">
                <form name="form2"  method="post" enctype="multipart/form-data" >
                          一括仕入れアップロード：<input type="file" name="inputExcelBuy" id="inputExcelBuy"/>
                         </td>
                         <td align="left"><input type="submit" value="取込 "/></td>
                     </tr>
                     <tr bgcolor="#FFEEFF" style="display:none;" id="inputExcelOnBoardTr">
                         <td align="left">
                            一括発送アップロード：<input type="file" name="inputExcelOnBoard" id="inputExcelOnBoard"/>
                </form>
                         </td>
                          <td align="left"><input type="submit" value="取込 "/></td>
                     </tr>
					<tr>
					   <td>
    					   <button style="display: none;" id="exportExcel0_3Button" onclick="exportExcel(-1)">导出(0-3)</button>
    					   <button onclick="exportExcel(<?php echo $_REQUEST["pageNameId"] ?>)">导出</button>
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
    					       <option value="3">仕入れ表3</option>
    					   </select>
					   </td>
						<td style="text-align: right;" id="searchForm"><input type="hidden" name="orderByField" value="" />关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button></td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF" colspan="2">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th class="dcs"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
                                        <th class="cellcolor">商品コード</th>
                                        <th class="cellcolor">商品名</th>
                                        <th class="cellcolor">仕様</th>
                                        <th class="dcs">倉庫番号</th>
                                        <th class="cellcolor">在庫位置<br/>階-棚-ゾーン-横-縦</th>
                                        <th class="dcs">現在在庫数</th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(1)">周3前未审核数</a></th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(2)">周3前当日仕入れ数</a></th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(3)">周3前仕入れ総合</a></th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(4)">周3后未审核数</a></th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(5)">周3后当日仕入れ数</a></th>
                                        <th class="dcs"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderByField(6)">周3后仕入れ総合</a></th>
                                        <th class="dcs">販売平均数</th>
                                        <th class="cellcolor">発送日付</th>
                                        <th class="dcs">状態</th>
                                        <th class="cellcolor">備考1</th>
                                        <th class="dcs">到着荷物</th>
                                        <th class="cellcolor">備考2</th>
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
