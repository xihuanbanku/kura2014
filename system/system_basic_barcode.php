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
<title>商品バーコード管理</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var cpCategoriesOptions = "<option value=\"\">大分類選択</option>";
var cpCategoriesDownOptions = "<option value=\"\">小分類選択</option>";
$(function(){
	$("#subButton").click(function(){
		if($("#formTr input[name='productid']").val() == "" || $("#formTr input[name='barcode']").val() == "" || $("#cp_categories").val() <0 ||$("#cp_categories_down").val() <0) {
			alert("有项目未填写");
			return;
		}
    	$.ajax({
    		type: "post",
    		url: "../service/BarcodeService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr textarea").serialize()+"&"+$("#formTr select").serialize()+"&flag=insert",
    		success: function(data){
        		if(data > 0) {
    				alert("成功");
    				initPage(0);
    				$("#formTr textarea:not([name$=date]):not([name=status]):not([type=button])").each(function(i, item){
						$(item).val("");
    				})
        		} else {
    				alert("失败,可能是商品コード或バーコード重复");
        		}
    		}
    	});
	});

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
	initPage(0);
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/BarcodeService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&"+$("#searchForm").find("select").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
				var trStyle = "";
				if(entry.status == 0) {
					trStyle = "style=\"background-color: #8b968d;\"";
				}
        		html += ""
            		+"<tr "+trStyle+">"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
            		+"    <td>"+entry.id+"</td>"
            		+"    <td>"+entry.productid+"</td>"
            		+"    <td>"+entry.barcode+"</td>"
            		+"    <td>"+entry.cp_categories_str+"</td>"
            		+"    <td>"+entry.cp_categories_down_str+"</td>"
            		+"    <td>"+entry.status_str+"</td>"
            		+"    <td><button >修正</button><button value="+entry.id+" >削除</button></td>"
            		+"</tr>"
            		+"<tr class=\"hideTr\" >"
            		+"    <td></td>"
            		+"    <td>"+entry.id+"</td>"
            		+"    <td><input value=\""+entry.productid+"\" size=\"10\" name=\"productid\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.barcode+"\" size=\"10\" name=\"barcode\" type=\"text\"/></td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.cp_categories+"\"/><select style=\"width:70px;\" name=\"cp_categories\" onchange=\"getCategoryDown(this.value)\">" + cpCategoriesOptions +"</select> </td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.cp_categories_down+"\"/><select style=\"width:70px;\" name=\"cp_categories_down\">" + cpCategoriesDownOptions +"</select> </td>"
            		+"    <td>"+entry.status_str+"</td>"
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
        				var methodsel = $(item).parent().parent().next().find("select[name=cp_categories]");
        				methodsel.val(methodsel.prev().val());
        				var typesel = $(item).parent().parent().next().find("select[name=cp_categories_down]");
        				typesel.val(typesel.prev().val());
        			} else if(i%4 ==1) {//删除
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/BarcodeService.class.php",
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
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/BarcodeService.class.php",
        	        		data: "flag=update&userID=<?php echo GetCookie('userID')?>&id="+$(this).attr("value")+"&"+$(item).parent().parent().find("input").serialize()+"&"+$(item).parent().parent().find("select").serialize(),
        	        		success: function(data){
        	            		if(data > 0) {
        	        				alert("成功");
        	        				initPage(0);
        	            		} else {
        	        				alert("失败,可能是商品コード或バーコード重复");
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
//排序
function orderBy(obj) {
	$("#searchForm input:not([name=\""+obj +"\"])").val("");
	if($("#searchForm input[name=\""+obj +"\"]").val() == "asc") {
		$("#searchForm input[name=\""+obj +"\"]").val("desc");
	} else {
		$("#searchForm input[name=\""+obj +"\"]").val("asc");
	}
	initPage(0);
}
//导出excel
function exportExcel() {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/BarcodeService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
//批量更新备注3
function updateRemark3() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("input[name=remark3Input]").serialize();
    $.ajax({
		type: "post",
		url: "../service/BarcodeService.class.php?" + param,
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
		url: "../service/BarcodeService.class.php?" + param,
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
<?php
require_once '../service/BarcodeService.class.php';

if ($_FILES['inputExcelBuy']['size'] >0) {
    // 获取上传的文件名
    $filename = $_FILES['inputExcelBuy']['name'];
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcelBuy']['tmp_name'] ;
    // 上传顺序,区分当前页面的id
    $pageId = $_REQUEST["pageId"] ;
    $msg = uploadFile($pageId, $filename, $tmp_name);
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
    echo "删除{$msg["d"]}件<br/>";
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
						<td><strong>商品バーコード管理</strong>
    						<form name="form2"  method="post" enctype="multipart/form-data" >
    							上传内容:<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
        						<a href="../upload/Barcode_Template.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>
    						</form>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							ステータス：
							<select name="statusOptions">
								<option value="-1">全部</option>
								<option value="0">未使用</option>
								<option value="1">使用中</option>
							</select>
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br/>
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 20px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th class="cellcolor">ID</th>
										<th class="cellcolor">商品コード</th>
										<th class="cellcolor">バーコード</th>
										<th class="cellcolor">大分類</th>
										<th class="cellcolor">小分類</th>
										<th class="cellcolor">ステータス</th>
										<th class="cellcolor">操作</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td><!-- <textarea name="l_id" rows="6" cols="5"></textarea> --></td>
                                        <td><input name="productid" type="text"></input></td>
                                        <td><input name="barcode" type="text"></input></td>
                                        <td>
                                            <select name="cp_categories" id="cp_categories" onchange="getCategoryDown(this.value)">
                                            </select>
                                        </td>
                                        <td>
                                            <select name="cp_categories_down" id="cp_categories_down">
                                            <option value='-1'>小分類選択</option>
                                            </select>
                                        </td>
                                        <td></td>
                                        <td><input id="subButton" type="button" value="追加"></input></td>
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
