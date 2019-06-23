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
<link href="../style/jquery.searchableSelect.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.searchableSelect.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script type="text/javascript" src="../js/loading.js?r=<?php echo rand()?>"></script>
<title>借贷流水</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var ownerOptions="";
var financeType1Options="";
var financeType2Options="";
var financeType3Options="";
var goingTypeOptions="";

$(function(){
	$("#subButton").click(function(){
    	$.ajax({
    		type: "post",
    		url: "../service/FinanceDrCrFlowService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr textarea").serialize()+"&"+$("#formTr select").serialize()+"&flag=insert"+"&owner=<?php echo GetCookie('userID')?>",
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
		url: "../service/FinanceCashFlowService.class.php",
		data: {"flag":"initFinanceType", "p_id":1},
		success: function(msg){
			financeType1Options = msg;
    		$("#finance_type_1").html(financeType1Options);
		}
	});
	$.ajax({
		type: "post",
		url: "../service/FinanceCashFlowService.class.php",
		data: {"flag":"initFinance3Type"},
		success: function(msg){
			financeType3Options = msg;
    		$("#finance_type_3").html(financeType3Options);
    		$("#finance_type_3").searchableSelect(); 
		}
	});
	$("#finance_type_1").change(function(){
		$.ajax({
			type: "post",
			url: "../service/FinanceCashFlowService.class.php",
			data: {"flag":"initFinanceType", "p_id":$("#finance_type_1").val()},
			success: function(msg){
				financeType2Options = msg;
	    		$("#finance_type_2").html(financeType2Options);
			}
		});
	})
	$("#finance_type_2").change(function(){
		$.ajax({
			type: "post",
			url: "../service/FinanceCashFlowService.class.php",
			data: {"flag":"initFinanceType", "p_id":$("#finance_type_2").val()},
			success: function(msg){
				financeType3Options = msg;
	    		$("#finance_type_3").html(financeType3Options);
	    		$(".searchable-select").remove();
	    		$("#finance_type_3").searchableSelect(); 
			}
		});
	})
	$("#finance_type_3").click(function(){
		$.ajax({
			type: "post",
			url: "../service/FinanceCashFlowService.class.php",
			data: {"flag":"findFinanceType", "p_id":$("#finance_type_3").val()},
			success: function(msg){
				var data = eval("("+msg+")");
	    		$("#finance_type_2").html("<option value='"+data.id+"'>"+data.name+"</option>");
	    		$("#finance_type_1").val(data.p_id);
			}
		});
	})
	$.ajax({
		type: "post",
		url: "../service/FinanceDrCrFlowService.class.php",
		data: {"flag":"initStatic", "p_type":"GOING_TYPE"},
		success: function(msg){
			goingTypeOptions = msg;
    		$("#going_type").html(goingTypeOptions);
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
        		if(entry.id == <?php echo GetCookie('userID')?>) {
    				html+="<option selected=\"selected\" value=\"" + entry.id + "\">" + entry.s_name + "</option>";
        		} else {
    				html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
        		}
    		});
    		ownerOptions = html;
    		$("#owner").html(html);
			initPage(0);
		}
	});
	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"161", "user":<?php echo GetCookie('userID')?>},
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
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/FinanceDrCrFlowService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
        		html += ""
            		+"<tr >"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
 	           		+"    <td>"+entry.id+"</td>"
            		+"    <td title=\""+entry.atime+"\">"+entry.atime+"</td>"
            		+"    <td><a href=\"finance_img.php?p="+entry.picture_name+"\" target=\"_blank\">"+entry.picture_name+"</a></td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.income)+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.outgoing)+"</td>"
            		+"    <td title=\""+entry.finance_type_1_str+"\">"+entry.finance_type_1_str+"</td>"
            		+"    <td title=\""+entry.finance_type_2_str+"\">"+entry.finance_type_2_str+"</td>"
            		+"    <td title=\""+entry.finance_type_3_str+"\">"+entry.finance_type_3_str+"</td>"
            		+"    <td title=\""+entry.dr_cr+"\">"+entry.dr_cr+"</td>"
            		+"    <td title=\""+entry.remark2+"\">"+entry.remark2+"</td>"
        			+"    <td><button >修正</button><button value="+entry.id+" >削除</button></td>";
        		html +="</tr>"
            		+"<tr class=\"hideTr\" >"
            		+"    <td></td>"
             		+"    <td>"+entry.id+"</td>"
            		+"    <td>"+entry.atime+"</td>"
            		+"    <td><input value=\""+entry.picture_name+"\" name=\"picture_name\" type=\"hidden\" id=\"picture_name"+entry.id+"\"/><img src=\"../images/up.gif\" style=\"cursor:hand;\" onclick=\"window.open('finance_file_upload.php?field=picture_name"+entry.id+"','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')\" /></td>"
            		+"    <td><input value=\""+entry.income+"\" size=\"10\" name=\"income\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.outgoing+"\" size=\"10\" name=\"outgoing\" type=\"text\"/></td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.finance_type_1+"\"/><select style=\"width:70px;\" name=\"finance_type_1\">" + financeType1Options +"<option value=\"0\">请选择</option></select> </td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.finance_type_2+"\"/><select style=\"width:70px;\" name=\"finance_type_2\">" + financeType2Options +"<option value=\"0\">请选择</option></select> </td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.finance_type_3+"\"/><select style=\"width:70px;\" name=\"finance_type_3\">" + financeType3Options +"<option value=\"0\">请选择</option></select> </td>"
            		+"    <td><input value=\""+entry.dr_cr+"\" size=\"10\" name=\"dr_cr\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark2+"\" size=\"10\" name=\"remark2\" type=\"text\"/></td>"
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
        				var ownerSel = $(item).parent().parent().next().find("select[name=owner]");
        				ownerSel.val(ownerSel.prev().val());
        				var finance_type1Sel = $(item).parent().parent().next().find("select[name=finance_type_1]");
        				finance_type1Sel.val(finance_type1Sel.prev().val());
        				var finance_type2Sel = $(item).parent().parent().next().find("select[name=finance_type_2]");
        				finance_type2Sel.val(finance_type2Sel.prev().val());
        				var finance_type3Sel = $(item).parent().parent().next().find("select[name=finance_type_3]");
        				finance_type3Sel.val(finance_type3Sel.prev().val());
        				var going_typeSel = $(item).parent().parent().next().find("select[name=going_type]");
        				going_typeSel.val(going_typeSel.prev().val());
        			} else if(i%4 ==1) {//删除
//						if($(item).parent().parent().next().find("select[name=owner]").prev().val() != ) {
// 							alert("没有权限");
// 							return;
// 						}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/FinanceDrCrFlowService.class.php",
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
        	        		url: "../service/FinanceDrCrFlowService.class.php",
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
  	   	    $("#totalPage").html("<span style='font-size: 100%;color:#A17B20'>总共："+recordCount+"条, 实际收入：["
  	    	   	    +number_format(data.totalcount.s_income)+"] 实际支出：["+number_format(data.totalcount.s_outgoing)+"] 时间区间:["+s_atime+"至"+e_atime+"]</span>");
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
	var url = "../service/FinanceDrCrFlowService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
	if($("#contentTable tr:gt(0) input[type=checkbox]:checked").length <= 0) {
		alert("没有选中记录");
		return;
	}
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("select[name=statusOptions]").serialize();
    $.ajax({
		type: "post",
		url: "../service/FinanceDrCrFlowService.class.php?" + param,
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
		url: "../service/FinanceDrCrFlowService.class.php?" + param,
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
		url: "../service/FinanceDrCrFlowService.class.php?" + param,
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
						<td><strong>借贷流水</strong></td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderBy" value="" />
                        	日期:
                            <input type="text" name="s_atime" size="15" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                            <input type="text" name="e_atime" size="15" class="Wdate" onclick="WdatePicker()"/>
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br/>
							<!-- <select name="statusOptions">
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
										<th class="cellcolor">管理番号</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(3)">日期</a></th>
										<th class="cellcolor">凭证</th>
										<th class="cellcolor">实际收入</th>
										<th class="cellcolor">实际支出</th>
										<th class="cellcolor">会計要素</th>
										<th class="cellcolor">科目</th>
										<th class="cellcolor">科目明細</th>
										<th class="cellcolor">借贷关系</th>
										<th class="cellcolor">款项用途说明</th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td></td>
                                        <td><?php echo date("Y-m-d")?></td>
                                        <td><input type="hidden" name="picture_name" id="picture_name"/><img src="../images/up.gif" style="cursor:hand;" onclick="window.open('finance_file_upload.php?field=picture_name','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" /></td>
                                        <td><input type="number" min="-999999" max="999999" name="income" ></input></td>
                                        <td><input type="number" min="-999999" max="999999" name="outgoing" ></input></td>
                                        <td><select style="width:70px;" name="finance_type_1" id="finance_type_1"><option value="1">请选择</option></select></td>
                                        <td><select style="width:70px;" name="finance_type_2" id="finance_type_2"><option value="1">请选择</option></select></td>
                                        <td><select style="width:70px;" name="finance_type_3" id="finance_type_3"><option value="1">请选择</option></select></td>
                                        <td><textarea name="dr_cr" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="remark2" rows="6" cols="15"></textarea></td>
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
