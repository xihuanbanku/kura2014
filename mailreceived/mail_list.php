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
<link href="../style/main.css?r=<?php echo rand()?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script type="text/javascript" src="../js/loading.js?r=<?php echo rand()?>"></script>
<title>邮件列表</title>
<script language="javascript">
var sendStatusOptions = "<option value=\"0\">未発注</option>"
    +"<option value=\"1\">発注済</option>"
    +"<option value=\"2\">到着済</option>";
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/FinanceBookingService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr textarea").serialize()+"&"+$("#formTr select").serialize()+"&flag=insert",
    		success: function(data){
        		if(data > 0) {
    				alert("成功");
    				initPage(0);
    				$("#formTr input:not([type=button])").each(function(i, item){
						$(item).val("");
    				})
    				$("#formTr textarea").each(function(i, item){
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
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"164", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
        		} else {
    				$("#" + entry.url).remove();
        		}
        		if(entry.url == "editBtn" && entry.loc > 0) {
        		    $("input[name=editBtn]").val(1);
        		}
        		if(entry.url == "deleteBtn" && entry.loc > 0) {
        		    $("input[name=deleteBtn]").val(1);
        		}
    		});
        	initPage(0);
		}
	});
	$("#sent_status").html(sendStatusOptions);
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/MailService.class.php",
		data: "flag=initPage&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
			var trStyle = "";
			var statusStr = "";
			var sendStatusStr = "";
    		$.each(data.results, function(entryIndex, entry){
				switch(entry.send_status) {
				case "0":
					sendStatusStr = "未発注";
					trStyle = "";
					break;
				case "1":
					sendStatusStr = "発注済";
					trStyle = "style=\"background-color: #fff8bc;\"";
					break;
				case "2":
					sendStatusStr = "到着済";
					trStyle = "style=\"background-color: #8b968d;\"";
					break;
				}
				switch(entry.status) {
				case "0":
					statusStr = "未審査";
					break;
				case "1":
					statusStr = "同意";
					break;
				case "2":
					statusStr = "不同意";
					break;
				}
        		html += ""
            		+"<tr "+trStyle+">"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
 	           		+"    <td>"+entry.id+"</td>"
            		+"    <td title=\""+entry.fromBy+"\">"+entry.fromName+"</td>"
            		+"    <td title=\""+entry.toList+"\">"+entry.toNameOth+"</td>"
            		+"    <td title=\""+entry.subject+"\">"+entry.subject+"</td>"
            		+"    <td title=\""+entry.mailDate+"\">"+entry.mailDate+"</td>"
            		+"    <td title=\""+entry.attachList+"\">"+entry.attachList+"</td>"
            		+"    <td title=\""+sendStatusStr+"\">"+sendStatusStr+"</td>"
            		+"    <td title=\""+statusStr+"\">"+statusStr+"</td>"
            		+"    <td title=\""+entry.atime+"\">"+entry.atime+"</td><td>";
        			if($("input[name=editBtn]").val() == "1") {
        				html+="    <button >修正</button>";
        			}
        			if($("input[name=deleteBtn]").val() == "1") {
        				html+=" <button value="+entry.id+" >削除</button>";
        			}
//         		html +="</td></tr>"
//             		+"<tr class=\"hideTr\" >"
//             		+"    <td></td>"
//              		+"    <td>"+entry.id+"</td>"
//             		+"    <td><input value=\""+entry.picture_name+"\" name=\"picture_name\" type=\"hidden\" id=\"picture_name"+entry.id+"\"/><img src=\"../images/up.gif\" style=\"cursor:hand;\" onclick=\"window.open('finance_file_upload.php?field=picture_name"+entry.id+"','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')\" /></td>"
//             		+"    <td><input value=\""+entry.cp_number+"\" size=\"10\" name=\"cp_number\" type=\"text\"/></td>"
//             		+"    <td><input value=\""+entry.cp_name+"\" size=\"10\" name=\"cp_name\" type=\"text\"/></td>"
//             		+"    <td><input value=\""+entry.cp_type+"\" size=\"10\" name=\"cp_type\" type=\"text\"/></td>"
//             		+"    <td><input value=\""+entry.cp_price+"\" size=\"10\" name=\"cp_price\" type=\"text\"/></td>"
//             		+"    <td><input value=\""+entry.cp_count+"\" size=\"10\" name=\"cp_count\" type=\"text\"/></td>"
//             		+"    <td>"+entry.total+"</td>"
//             		+"    <td><input value=\""+entry.track_number+"\" size=\"10\" name=\"track_number\" type=\"text\"/></td>"
//             		+"    <td><input value=\""+entry.remark1+"\" size=\"10\" name=\"remark1\" type=\"text\"/></td>"
//             		+"    <td><input type=\"hidden\" value=\""+entry.send_status+"\"/><select style=\"width:70px;\" name=\"send_status\">" + sendStatusOptions +"</select> </td>"
//             		+"    <td>"+statusStr+"</td>"
//             		+"    <td>"+entry.atime+"</td>"
//             		+"    <td><button value=\""+entry.id+"\">保存</button><button >取消</button></td>"
            		+"</tr>";

    		});
    		$("#contentTable tbody tr:gt(0)").remove();
    		$("#contentTable tbody").append(html);


    		$("#contentTable button").each(function(i, item){
    			$(this).click(function(){
        			if($(this).html() =="修正") { //编辑
        				$(item).parent().parent().hide();
        				$(item).parent().parent().next().show();
        				var going_typeSel = $(item).parent().parent().next().find("select[name=send_status]");
        				going_typeSel.val(going_typeSel.prev().val());
        			} else if($(this).html() =="削除") {//删除
        				if(!confirm("确定删除?")) {
        					return;
        				}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/FinanceBookingService.class.php",
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
					} else if($(this).html() =="保存") {//保存
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/FinanceBookingService.class.php",
        	        		data: "flag=update&id="+$(this).attr("value")+"&"+$(item).parent().parent().find("input").serialize()+"&"+$(item).parent().parent().find("select").serialize(),
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

			recordCount = data.totalcount.c;	
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
    	    //判断时间区间显示
    	    var s_atime = $("input[name=s_atime]").val();
    	    var e_atime = $("input[name=e_atime]").val();
    	    if(e_atime == "") {
    	    	e_atime = "<?php echo date("Y-m-d")?>";
    	    }
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
	var url = "../service/FinanceBookingService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
//批量删除
function deleteChked() {
	if($("#contentTable tr:gt(0) input[type=checkbox]:checked").length <= 0) {
		alert("没有选中记录");
		return;
	}
	if(!confirm("确定删除?")) {
		return;
	}
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceBookingService.class.php?" + param,
		data: {
			"flag":"deleteChked"},
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
//批量更新状态
function updateStatus() {
	if($("#contentTable tr:gt(0) input[type=checkbox]:checked").length <= 0) {
		alert("没有选中记录");
		return;
	}
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("select[name=statusOptions]").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceBookingService.class.php?" + param,
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
</script>
</head>
<?php
require_once '../service/FinanceBookingService.class.php';

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
    echo "更新{$msg["u"]}件<br/>";
    echo "删除{$msg["d"]}件<br/>";
}

?>
<body>
<!-- 定义部分常量 -->
<input type="hidden" name="editBtn"/>
<input type="hidden" name="deleteBtn"/>
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
						<td><strong>購入予定</strong>
    						<form name="form2"  method="post" enctype="multipart/form-data" >
    							上传内容:<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
        						<a href="../upload/Booking_Template.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>
    						</form>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderBy" value="" />
                        	日期:
                            <input type="text" name="s_atime" size="15" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                            <input type="text" name="e_atime" size="15" class="Wdate" onclick="WdatePicker()"/>
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button  onclick="exportExcel()">导出</button><br />
							<span style="display: none;" id="updateStatusButton" >
							<select name="statusOptions">
								<option value="0">未審査</option>
								<option value="1">同意</option>
								<option value="2">不同意</option>
							</select>
							<button onclick="updateStatus()">審査</button>
							</span>
							<button  onclick="deleteChked()">删除</button>
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 20px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th class="cellcolor">管理番号</th>
										<th class="cellcolor">发件人</th>
										<th class="cellcolor">收件人</th>
										<th class="cellcolor">主题</th>
										<th class="cellcolor">时间</th>
										<th class="cellcolor">附件</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(5)">发货状態</a></th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(3)">審査</a></th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(1)">登记日期</a></th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <!-- <tr id="formTr">
                                        <td></td>
                                        <td></td>
                                        <td><input type="text" name="cp_number" ></input></td>
                                        <td><input type="text" name="cp_name" ></input></td>
                                        <td><input type="text" name="cp_type" ></input></td>
                                        <td><input type="text" name="cp_price" ></input></td>
                                        <td><input type="text" name="cp_count" ></input></td>
                                        <td></td>
                                        <td><input type="text" name="track_number" ></input></td>
                                        <td><input type="text" name="remark1" ></input></td>
                                        <td><select style="width:70px;" name="sent_status" id="sent_status"></select></td>
                                        <td></td>
                                        <td><?php echo date("Y-m-d")?></td>
                                        <td><input id="subButton" type="button" value="追加"></input></td>
                                    </tr> -->
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

