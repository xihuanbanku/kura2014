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
<title>荷物・返品管理</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var methodOptions = "<option value=\"1\">ヤマト運輸</option>"
                +"<option value=\"2\">佐川急便</option>"
                +"<option value=\"3\">日本郵便</option>"
                +"<option value=\"4\">そのほか</option>";
var typeOptions = "<option value=\"1\">発払い</option>"
                   +"<option value=\"2\">着払い</option>"
                   +"<option value=\"3\">代金引換</option>";
var statusOptions = "<option value=\"0\">未着</option>"
                   +"<option value=\"1\">到着</option>"
                   +"<option value=\"2\">済</option>"
                   +"<option value=\"3\">未完成</option>";
var staffOptions ="";
$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/LuggageService.class.php",
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

	$.ajax({
		type: "post",
		url: "../service/BulletinService.class.php",
		data: {"flag":"initReceiver"},
		success: function(data){
			data = eval("("+data+")");
    		var html="";
    		$.each(data, function(entryIndex, entry){
        		if(entry.id == <?php echo GetCookie('userID')?>) {
    				html+="<option selected=\"selected\" value=\"" + entry.id + "\">" + entry.s_name + "</option>";
        		} else {
    				html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
        		}
    		});
    		staffOptions = html;
    		$("#owner").html(html);
    		$("#remark2").html(html);
    		$("#method").html(methodOptions);
    		$("#type").html(typeOptions);
    		$("#status").html(statusOptions);
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
		url: "../service/LuggageService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			data = eval("("+data+")");
			if(data.totalproperty[0].totalcount == 0) {
	    		$("#contentTable tbody tr:gt(0)").remove();
			} else {
				var html = "";
				$.each(data.results, function(entryIndex, entry){
					var trStyle = "";
					if(entry.status == 2) {
						trStyle = "style=\"background-color: #8b968d;\"";
					}
					html += ""
						+"<tr "+trStyle+">"
						+"    <td><input type=\"checkbox\" value=\"" + entry.l_id + "\" name=\"strChk[]\"></td>"
						+"    <td title=\""+entry.l_id+"\">"+entry.l_id+"</td>"
						+"    <td title=\""+entry.cust_name+"\">"+entry.cust_name+"</td>"
						+"    <td title=\""+entry.destination+"\">"+entry.destination+"</td>"
						+"    <td title=\""+entry.content+"\">"+entry.content+"</td>"
						+"    <td title=\""+entry.cp_number+"\">"+entry.cp_number+"</td>"
						+"    <td>"+entry.methodstr+"</td>"
						+"    <td>"+entry.typestr+"</td>"
						+"    <td title=\""+entry.remark1+"\">"+entry.remark1+"</td>"
						+"    <td title=\""+entry.want_date+"\">"+entry.want_date+"</td>"
						+"    <td title=\""+entry.limit_date+"\">"+entry.limit_date+"</td>"
						+"    <td title=\""+entry.query_num+"\">"+entry.query_num+"</td>"
						+"    <td>"+entry.statusstr+"</td>"
						+"    <td>"+entry.is_arrival+"</td>"
						+"    <td title=\""+entry.remark2Name+"\">"+entry.remark2Name+"</td>"
						+"    <td>"+entry.s_name+"</td>"
						+"    <td title=\""+entry.remark3+"\">"+entry.remark3+"</td>"
						+"    <td><button >修正</button><button value="+entry.luggage_id+" >削除</button></td>"
						+"</tr>"
						+"<tr class=\"hideTr\" >"
						+"    <td></td>"
						+"    <td>"+entry.l_id+"</td>"
						+"    <td><input value=\""+entry.cust_name+"\" size=\"10\" name=\"cust_name\" type=\"text\"/></td>"
						+"    <td><input value=\""+entry.destination+"\" size=\"10\" name=\"destination\" type=\"text\"/></td>"
						+"    <td><input value=\""+entry.content+"\" size=\"10\" name=\"content\" type=\"text\"/></td>"
						+"    <td><input value=\""+entry.cp_number+"\" size=\"10\" name=\"cp_number\" type=\"text\"/></td>"
						+"    <td><input type=\"hidden\" value=\""+entry.method+"\"/><select style=\"width:70px;\" name=\"method\">" + methodOptions +"</select> </td>"
						+"    <td><input type=\"hidden\" value=\""+entry.type+"\"/><select style=\"width:70px;\" name=\"type\">" + typeOptions +"</select> </td>"
						+"    <td><input value=\""+entry.remark1+"\" size=\"10\" name=\"remark1\" type=\"text\"/></td>"
						+"    <td>"+entry.want_date+"</td>"
						+"    <td>"+entry.limit_date+"</td>"
						+"    <td><input value=\""+entry.query_num+"\" size=\"10\" name=\"query_num\" type=\"text\"/></td>"
						+"    <td><input type=\"hidden\" value=\""+entry.status+"\"/><select style=\"width:70px;\" name=\"status\">" + statusOptions +"</select> </td>"
						+"    <td><input value=\""+entry.is_arrival+"\" size=\"10\" name=\"is_arrival\" type=\"text\"/></td>"
						+"    <td><input type=\"hidden\" value=\""+entry.remark2+"\"/><select style=\"width:70px;\" name=\"remark2\">" + staffOptions +"</select></td>"
						+"    <td><input type=\"hidden\" value=\""+entry.owner+"\"/><select style=\"width:70px;\" name=\"owner\">" + staffOptions +"</select></td>"
						+"    <td><input value=\""+entry.remark3+"\" size=\"10\" name=\"remark3\" type=\"text\"/></td>"
						+"    <td><button value=\""+entry.luggage_id+"\">保存</button><button >取消</button></td>"
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
							var typesel = $(item).parent().parent().next().find("select[name=type]");
							typesel.val(typesel.prev().val());
							var selRemark2 = $(item).parent().parent().next().find("select[name=remark2]");
							selRemark2.val(selRemark2.prev().val());
							var selstff = $(item).parent().parent().next().find("select[name=owner]");
							selstff.val(selstff.prev().val());
							var statussel = $(item).parent().parent().next().find("select[name=status]");
							statussel.val(statussel.prev().val());
						} else if(i%4 ==1) {//删除
							if($(item).parent().parent().next().find("select[name=owner]").prev().val() != <?php echo GetCookie('userID')?>) {
								alert("没有权限");
								return;
							}
							if(!confirm("确定删除?")) {
								return;
							}
							$.ajax({
								type: "post",
								url: "../service/LuggageService.class.php",
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
								url: "../service/LuggageService.class.php",
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
			}
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
	var url = "../service/LuggageService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
		url: "../service/LuggageService.class.php?" + param,
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
		url: "../service/LuggageService.class.php?" + param,
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
		url: "../service/LuggageService.class.php?" + param,
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
						<td><strong>荷物・返品管理</strong></td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderWantDate" value="" />
							<input type="hidden" name="orderLimitDate" value="" />
							<input type="hidden" name="orderStatus" value="" />
							<input type="hidden" name="orderOwner" value="" />
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br/>
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
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 20px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th class="cellcolor">管理番号</th>
										<th class="cellcolor">注文番号or名前</th>
										<th class="cellcolor">発送先</th>
										<th class="cellcolor">内容原因</th>
										<th class="cellcolor">商品番号</th>
										<th class="cellcolor">運送方法</th>
										<th class="cellcolor">荷物分類</th>
										<th class="cellcolor">備考1</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy('orderWantDate')">受付日</a></th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy('orderLimitDate')">到期日</a></th>
										<th class="cellcolor">問い合わせ番号</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy('orderStatus')">状态</a></th>
										<th class="cellcolor">ヤマト佐川まとめ請求分金額</th>
										<th class="cellcolor">返品処理</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy('orderOwner')">記入者</a></th>
										<th class="cellcolor">返品対策</th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td><!-- <textarea name="l_id" rows="6" cols="5"></textarea> --></td>
                                        <td><textarea name="cust_name" rows="6" cols="5"></textarea></td>
                                        <td><textarea name="destination" rows="6" cols="5"></textarea></td>
                                        <td><textarea name="content" rows="6" cols="5"></textarea></td>
                                        <td><textarea name="cp_number" rows="6" cols="5"></textarea></td>
                                        <td>
                                            <select name="method" id="method">
                                            </select>
                                        </td>
                                        <td>
                                            <select name="type" id="type">
                                            </select>
                                        </td>
                                        <td><textarea name="remark1" rows="6" cols="5"></textarea></td>
                                        <td colspan="2"><?php echo date("Y-m-d")?></td>
                                        <td><textarea name="query_num" rows="6" cols="5"></textarea></td>
                                        <td><select style="width:70px;" name="status" id="status"></select></td>
                                        <td><textarea name="is_arrival" rows="6" cols="5"></textarea></td>
                                        <td><select style="width:70px;" name="remark2" id="remark2"></select></td>
                                        <td><select style="width:70px;" name="owner" id="owner"></select></td>
                                        <td><textarea name="remark3" rows="6" cols="5"></textarea></td>
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
