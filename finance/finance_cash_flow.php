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
<link href="../style/jquery-ui.css" type="text/css" rel="stylesheet" />
<link href="../style/jquery.searchableSelect.css" rel="stylesheet" type="text/css"/>
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<link href="../style/main.css?r=<?php echo rand()?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/jquery.searchableSelect.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script type="text/javascript" src="../js/loading.js?r=<?php echo rand()?>"></script>
<title>实时流水</title>
<script language="javascript">
var financeType1Options="";
var financeType2Options="";
var financeType3Options="";
var goingTypeOptions="";

$(function(){
	$("#subButton").click(function(){

        var count=0;
        $.each($("#formTr input:gt(1):lt(4)"), function(i, item) {
            if($(item).val() != "" && $(item).val()!=0) {
                count++;
            }
        })
        if(count > 1) {
            alert("不能同时填写 実際收入,実際支出,売掛金,買掛金");
            return;
        } else if(count != 1) {
            alert("有内容未填写 実際收入,実際支出,売掛金,買掛金");
            return;
        }
        count=0;
        $.each($("#formTr select"), function(i, item) {
            if($(item).val()==0) {
                count++;
            }
        })
        if(count > 0) {
            alert("请选择会計要素,科目,科目明細,资金动向");
            return;
        }
		if(($("#formTr input[name='real_income']").val() != "" || $("#formTr input[name='real_outgoing']").val() != "")&& $("#formTr input[name='limit_date']").val() == "") {
			alert("请选择掛金日期");
			return;
		}
    	$.ajax({
    		type: "post",
    		url: "../service/FinanceCashFlowService.class.php",
    		data: $("#formTr input").serialize()+"&"+$("#formTr textarea").serialize()+"&"+$("#formTr select").serialize()+"&flag=checkExists",
    		success: function(data){
        		if(data > 0) {
    				if(!confirm("["+data+"]记录已存在,是否继续?")) {
        				return;
    				}
        		}
		    	$.ajax({
		    		type: "post",
		    		url: "../service/FinanceCashFlowService.class.php",
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
    		}
    	});
	});
	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"154", "user":<?php echo GetCookie('userID')?>},
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
		url: "../service/FinanceCashFlowService.class.php",
		data: {"flag":"initStatic", "p_type":"GOING_TYPE"},
		success: function(msg){
			goingTypeOptions = msg;
    		$("#going_type").html(goingTypeOptions);
    		$("#searchForm select").append(goingTypeOptions);
			initPage(0);
		}
	});

    $( "#dialog1" ).dialog({
        autoOpen: false,
        width:500,
        height:350,
        show: {
          effect: "clip"
        },
        hide: {
          effect: "clip",
        }
      });
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/FinanceCashFlowService.class.php",
		data: "flag=initPage&userID=<?php echo GetCookie('userID')?>&"+$("#searchForm input").serialize()+"&"+$("#searchForm select").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		success: function(data){
			data = eval("("+data+")");
			if(data.results == null) {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
        		var statusStr = "未审核";
        		var trStyle = "";
        		switch(entry.status) {
        		case "1":
        			statusStr = "自动导入";
        			trStyle = "style=\"background-color: #fff8bc;\"";
        			break;
        		case "2":
        			statusStr = "审核済";
        			trStyle = "style=\"background-color: #FA8072;\"";
        			break;
        		case "3":
        			statusStr = "";
        			trStyle = "style=\"background-color: #AA8072;\"";
        			break;
        		}
        		html += ""
            		+"<tr "+trStyle+">"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
 	           		+"    <td>"+entry.id+"</td>"
            		+"    <td title=\""+entry.atime+"\">"+entry.atime.substring(0,10)+"</td>"
            		+"    <td title=\""+entry.deal_time+"\">"+entry.deal_time+"</td>"
            		+"    <td><a href=\"finance_img.php?p="+entry.picture_name+"\" target=\"_blank\">"+entry.picture_name+"</a></td>"
            		+"    <td title=\""+entry.type+"\">"+entry.type+"</td>"
            		+"    <td title=\""+entry.remark1+"\">"+entry.remark1+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.income)+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.outgoing)+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.real_income)+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.real_outgoing)+"</td>"
            		+"    <td title=\""+entry.limit_date+"\">"+entry.limit_date+"</td>"
            		+"    <td title=\""+entry.owner_str+"\">"+entry.owner_str+"</td>"
            		+"    <td title=\""+entry.remark2+"\">"+entry.remark2+"</td>"
            		+"    <td title=\""+entry.going_type_str+"\">"+entry.going_type_str+"</td>"
            		+"    <td title=\""+entry.finance_type_1_str+"\">"+entry.finance_type_1_str+"</td>"
            		+"    <td title=\""+entry.finance_type_2_str+"\">"+entry.finance_type_2_str+"</td>"
            		+"    <td title=\""+entry.finance_type_3_str+"\">"+entry.finance_type_3_str+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.real_rest)+"</td>"
            		+"    <td style='text-align: right;'>"+number_format(entry.rest_not_back)+"</td>"
            		+"    <td title=\""+entry.status+"\">"+statusStr +"</td>";
        		if(new Date(entry.atime_1) >= new Date()) {
        			html +="    <td><button >修正</button><button value="+entry.id+" >削除</button></td>";
        		} else {
        			html +="    <td></td>";
        		}
        		html +="</tr>"
            		+"<tr class=\"hideTr\" >"
            		+"    <td></td>"
             		+"    <td>"+entry.id+"</td>"
            		+"    <td>"+entry.atime+"</td>"
            		+"    <td><input value=\""+entry.deal_time+"\" size=\"10\" name=\"deal_time\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.picture_name+"\" name=\"picture_name\" type=\"hidden\" id=\"picture_name"+entry.id+"\"/><img src=\"../images/up.gif\" style=\"cursor:hand;\" onclick=\"window.open('finance_file_upload.php?field=picture_name"+entry.id+"','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')\" /></td>"
            		+"    <td><input value=\""+entry.type+"\" size=\"10\" name=\"type\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.remark1+"\" size=\"10\" name=\"remark1\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.income+"\" size=\"10\" name=\"income\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.outgoing+"\" size=\"10\" name=\"outgoing\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.real_income+"\" size=\"10\" name=\"real_income\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.real_outgoing+"\" size=\"10\" name=\"real_outgoing\" type=\"text\"/></td>"
            		+"    <td><input value=\""+entry.limit_date+"\" size=\"10\" name=\"limit_date\" type=\"text\"/></td>"
            		+"    <td>"+entry.owner_str+"</td>"
            		+"    <td><input value=\""+entry.remark2+"\" size=\"10\" name=\"remark2\" type=\"text\"/></td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.going_type+"\"/><select style=\"width:70px;\" name=\"going_type\">" + goingTypeOptions +"</select> </td>"
            		+"    <td>"+entry.finance_type_1_str+"</td>"
            		+"    <td>"+entry.finance_type_2_str+"</td>"
            		+"    <td><input type=\"hidden\" value=\""+entry.finance_type_3+"\"/><select style=\"width:70px;\" name=\"finance_type_3\">" + financeType3Options +"</select> </td>"
            		+"    <td></td>"
            		+"    <td></td>"
            		+"    <td></td>"
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
        				var finance_type3Sel = $(item).parent().parent().next().find("select[name=finance_type_3]");
        				finance_type3Sel.val(finance_type3Sel.prev().val());
        				var going_typeSel = $(item).parent().parent().next().find("select[name=going_type]");
        				going_typeSel.val(going_typeSel.prev().val());
        			} else if(i%4 ==1) {//删除
        				if(!confirm("确定删除?")) {
        					return;
        				}
        				$.ajax({
        	        		type: "post",
        	        		url: "../service/FinanceCashFlowService.class.php",
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
        	        		url: "../service/FinanceCashFlowService.class.php",
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
  	   	    $("#totalPage").html("<span style='font-size: 100%;color:#A17B20'>总共："+recordCount+"条, 实际收入：["
  	    	   	    +number_format(data.totalcount.s_income)+"] 实际支出：["+number_format(data.totalcount.s_outgoing)+"] 应收：["+number_format(data.totalcount.s_real_income)
  	    	   	    +"] 应付：["+number_format(data.totalcount.s_real_outgoing)+"] 实际结余：["+number_format(data.totalcount.s_real_rest)+"] 实际挂账之和：["+number_format(data.totalcount.s_rest_not_back)+"] 时间区间:["+s_atime+"至"+e_atime+"]</span>");
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
	var url = "../service/FinanceCashFlowService.class.php?"+$("#searchForm input").serialize()+"&"+$("#searchForm select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
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
		url: "../service/FinanceCashFlowService.class.php?" + param,
		data: {
			"flag":"updateStatus"},
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
		url: "../service/FinanceCashFlowService.class.php?" + param,
		data: {
			"flag":"updateRemark3"},
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
		url: "../service/FinanceCashFlowService.class.php?" + param,
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
//审核窗口
function show_window() {
	var html="";
	$("#contentTable tr:gt(0) input[type=checkbox]:checked").each(function(i, item){
		html+=""
			+"<tr>"
			+"    <td>" + $(item).parent().next().text() + "</td>"
			+"    <td><input type=\"text\" size=\"10\" name=\"deal_time[]\" value=\"" + $(item).parent().nextAll(":eq(2)").text() + "\"/></td>"
			+"    <td><input type=\"text\" size=\"5\" name=\"real_income[]\" value=\"" + $(item).parent().nextAll(":eq(5)").text() + "\"/></td>"
			+"    <td><input type=\"hidden\" name=\"strChk[]\" value=\"" + $(item).val() + "\"/><input type=\"text\" size=\"5\" name=\"real_outgoing[]\" value=\"" + $(item).parent().nextAll(":eq(6)").text() + "\"/> </td>"
			+"</tr>";

	})
    $("#buyingTable tr:gt(0)").remove();
    $("#buyingTable").append(html);
    $("#dialog1").dialog("open");
}
//手动上货提交
function update_buying() {
	var param = $("#buyingTable input").serialize();
	$.ajax({
		type: "post",
		url: "../service/FinanceCashFlowService.class.php?" + param,
		data: {
				"flag":"updateStatus"
				},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
				$("#dialog1").dialog('close');
				initPage(0);
			} else {
				alert("更新失败");
    		}
		}
	});
}
</script>
</head>
<?php
require_once '../service/FinanceCashFlowService.class.php';

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
						<td><strong>实时流水</strong>
    						<form name="form2"  method="post" enctype="multipart/form-data" >
    							上传内容:<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
        						<a href="../upload/CashFlow_Template.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>
    						</form>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<input type="hidden" name="orderBy" value="" />
                        	掛金日期:
                            <input type="text" name="s_atime" size="15" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                            <input type="text" name="e_atime" size="15" class="Wdate" onclick="WdatePicker()"/><br />
                			资金动向:<select name="going_type"><option value="-1">全部</option></select>
							关键字<input name="s_text" type="text"/><button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()" id="exportButton" style="display:none;">导出</button><br/>
							<!-- <select name="statusOptions">
								<option value="0">未着</option>
								<option value="1">到着</option>
								<option value="2">済</option>
								<option value="3">未完成</option>
							</select>
							<input type="text" name="remark3Input" />
							<button onclick="updateRemark3()">更新返品対策</button>
							-->
							<button onclick="show_window()">更新状态</button>
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
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(1)">日期</a></th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(5)">发生日期</a></th>
										<th class="cellcolor">領収書</th>
										<th class="cellcolor">科目</th>
										<th class="cellcolor">摘要</th>
										<th class="cellcolor">実際收入</th>
										<th class="cellcolor">実際支出</th>
										<th class="cellcolor">売掛金</th>
										<th class="cellcolor">買掛金</th>
										<th class="cellcolor">掛金日期</th>
										<th class="cellcolor">記入者</th>
										<th class="cellcolor">金額用途説明</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(7)">口座選択</a></th>
										<th class="cellcolor">会計要素</th>
										<th class="cellcolor">科目</th>
										<th class="cellcolor">科目明細</th>
										<th class="cellcolor">実際対照</th>
										<th class="cellcolor">掛金対照</th>
										<th class="cellcolor"><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(3)">状态</a></th>
										<th class="cellcolor">修正</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td></td>
                                        <td><?php echo date("Y-m-d")?></td>
                                        <td><input value="<?php echo date("Y-m-d")?>" size="10" name="deal_time" type="text" class="Wdate" onclick="WdatePicker()"/></td>
                                        <td><input type="hidden" name="picture_name" id="picture_name"/><img src="../images/up.gif" style="cursor:hand;" onclick="window.open('finance_file_upload.php?field=picture_name','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=600,height=300,top=200,left=300')" /></td>
                                        <td><textarea name="type" rows="6" cols="15"></textarea></td>
                                        <td><textarea name="remark1" rows="6" cols="15"></textarea></td>
                                        <td><input type="number" min="-999999" max="999999" name="income" ></input></td>
                                        <td><input type="number" min="-999999" max="999999" name="outgoing" ></input></td>
                                        <td><input type="number" min="-999999" max="999999" name="real_income" ></input></td>
                                        <td><input type="number" min="-999999" max="999999" name="real_outgoing" ></input></td>
                                        <td><input value="<?php echo date("Y-m-d")?>" size="10" name="limit_date" type="text" class="Wdate" onclick="WdatePicker()"/></td>
                                        <td><!-- <select style="width:70px;" name="owner" id="owner"></select> --></td>
                                        <td><textarea name="remark2" rows="6" cols="15"></textarea></td>
                                        <td><select style="width:70px;" name="going_type" id="going_type"></select></td>
                                        <td><select style="width:100px;" name="finance_type_1" id="finance_type_1"><option value="0">请选择</option></select></td>
                                        <td><select style="width:100px;" name="finance_type_2" id="finance_type_2"><option value="0">请选择</option></select></td>
                                        <td><select style="width:100px;" name="finance_type_3" id="finance_type_3"><option value="0">请选择</option></select></td>
                                        <td><!-- <input size="8" name="real_rest" readonly="readonly"></input> --></td>
                                        <td><!-- <input size="8" name="rest_not_back" readonly="readonly"></input> --></td>
                                        <td><!-- <input size="8" name="rest_not_back" readonly="readonly"></input> --></td>
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
	<div id="dialog1" title="审核">
        <table border="1" id="buyingTable">
            <tr>
                <td>管理番号</td>
                <td>发生日期</td>
                <td>実際收入</td>
                <td>実際支出</td>
            </tr>
        </table>
      <button onclick="update_buying()">提交</button>
    </div>
		<?php 
copyright();
?>
</body>
</html>
