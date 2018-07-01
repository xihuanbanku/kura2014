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
<title>考勤管理</title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
$(function(){
	var myDate = new Date();
	//获取完整的年份(4位,1970-????)
	$("select[name='dutyYear']").val(myDate.getFullYear());
	//当前月
	thisMonth = myDate.getMonth();
	if(myDate.getDate() > 15) {
		thisMonth++;
	}
	//获取当前月份(0-11,0代表1月)
	if(thisMonth < 10) {
		$("select[name='dutyMonth']").val("0"+(thisMonth+1));
	} else {
		$("select[name='dutyMonth']").val(thisMonth+1);
	}
	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initUser"},
		success: function(data){
			data = eval("("+data+")");
			var html="<option value=\"-1\">请选择</option>";
			$.each(data, function(entryIndex, entry){
				html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
			});
			$("#users").html(html);
		}
	});
	$.ajax({
		type: "post",
		url: "../service/SystemService.class.php",
		data: {"flag":"loadStaticParam", "p_type":"DUTY_HOLIDAY_TYPE"},
		success: function(data){
			data = eval("("+data+")");
			var html="";
			$.each(data, function(entryIndex, entry){
				html+="<option value=\"" + entry.p_value + "\">" + entry.p_name + "</option>";
			});
			$("#holiday_type").html(html);
		}
	});
})
function initPage(pageIndex) {
	$.ajax({
		type: "post",
		url: "../service/DutyService.class.php",
		data: "flag=initPage&"+$("#searchForm").find("select").serialize()+"&user="+$("#users").val()+"&fromPage=admin&nocache="+new Date().getTime(),
		success: function(data){
			if(trimStr(data) == "null") {
	    		$("#contentTable tbody tr:gt(0)").remove();
				return;
			}
			data = eval("("+data+")");
			var html = "";
    		var trStyle="";
    		$.each(data.results, function(entryIndex, entry){
				trStyle="";
        		if(entry.wk == "Sat" || entry.wk == "Sun") {
	        		trStyle="style='background-color: #c1efb1;'";
	        		entry.check_in_on_time = "";
	        		entry.check_out_on_time = "";
        		}
        		html += ""
            		+"<tr "+trStyle+">"
            		+"    <td><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\"></td>"
 	           		+"    <td>"+entry.atime+"("+entry.wk+")</td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='check_in_on_time' value='"+entry.check_in_on_time+"'/></td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='check_out_on_time' value='"+entry.check_out_on_time+"'/></td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='check_in' value='"+entry.check_in+"'/></td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='snooze_start' value='"+entry.snooze_start+"'/></td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='snooze_end' value='"+entry.snooze_end+"'/></td>"
 	           		+"    <td><input class='Wdate' onclick='WdatePicker()' size='14' type='text' name='check_out' value='"+entry.check_out+"'/></td>"
 	           		+"    <td>"+entry.work_time+"</td>"
 	           		+"    <td>"+entry.over_time1+"</td>"
 	           		+"    <td>"+entry.over_time2+"</td>"
            		+"    <td>"+entry.is_holiday+"</td>"
 	           		+"    <td><input size='10' type='text' name='remark' value='"+entry.remark+"'/><input type='hidden' name='id' value='"+entry.id+"'/><button>保存</button></td>"
            		+"</tr>";
//     			work_time_sum += work_time_temp;
//         		work_time_15_sum += work_time_15_temp;
//         		work_time_30_sum += work_time_30_temp;
//         		over_time1_sum += over_time1_temp;
//         		over_time2_sum += over_time2_temp;

    		});
    		$("#contentTable tbody tr").remove();
    		$("#contentTable tbody").append(html);
    		$("#contentTable tbody tr button").each(function(i, item){
        		$(item).click(function(){
        			$.ajax({
    	        		type: "post",
    	        		url: "../service/DutyService.class.php",
    	        		data: "flag=remark&"+$(item).parents("tr:eq(0)").find("input").serialize(),
    	        		success: function(data){
    	            		if(data > 0) {
    	        				alert("成功");
    	        				initPage(0);
    	            		} else {
    	        				alert("失败");
    	            		}
    	        		}
    	        	});
        		});
    		});
    		var results_sum = data.results_sum[0];
    		$("#contentTable tbody").append("<tr>"
                    		+"    <td></td>"
                    		+"    <td>合计</td>"
                    		+"    <td></td>"
                    		+"    <td></td>"
                       		+"    <td></td>"
                       		+"    <td></td>"
                       		+"    <td></td>"
                       		+"    <td></td>"
                       		+"    <td>"+parseInt(results_sum.work_time_sum/60)+":"+parseInt(results_sum.work_time_sum%60)+"</td>"
                       		+"    <td>"+parseInt(results_sum.over_time1_sum/60)+":"+parseInt(results_sum.over_time1_sum%60)+"</td>"
                       		+"    <td>"+parseInt(results_sum.over_time2_sum/60)+":"+parseInt(results_sum.over_time2_sum%60)+"</td>"
                       		+"    <td></td>"
                			+"</tr>");
			switch(data.timeType) {
			case 1:
				$("#timeType").html("(分)");
				break;
			case 2:
				$("#timeType").html("(15分)");
				break;
			case 3:
				$("#timeType").html("(30分)");
				break;
			}
    	}
	});
}
//导出excel
function exportExcel() {
	var param = $("#searchForm").find("select").serialize();
	var url = "../service/DutyService.class.php?nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel&fromPage=admin";
    window.open(url);
}
//批量更新状态
function updateHoliday() {
	if($("#contentTable tr input[type=checkbox]:checked").length <= 0) {
		alert("没有选中记录");
		return;
	}
	var is_all =0;
	if($("input[name=is_all]:checked").length>0) {
		if(confirm("确认全员标记?")) {
			is_all =1;
		} else {
			return;
		}
	}
    var param = $("#contentTable tr input[type=checkbox]:checked").serialize()+"&"+$("select[name=statusOptions]").serialize()+"&isAll="+is_all;
    $.ajax({
		type: "post",
		url: "../service/DutyService.class.php?" + param,
		data: {
			"flag":"updateHoliday",
			"holiday_type":$("select[name=holiday_type]").val()},
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
//审核
function updateState() {
    var param = $("#updateTable input").serialize()+"&"+$("#updateTable select").serialize()+"&"+$("#searchForm select").serialize();
    $.ajax({
		type: "post",
		url: "../service/DutyService.class.php?" + param,
		data: {
			"flag":"updateState"},
		success: function(data){
			if(data >0) {
				alert("成功");
			} else {
				alert("失败, 可能是重复审核或尚未审核");
			}
		}
    });
}
//选择用户的时候, 同时要设置上传文件的input 隐藏域
function setUploadInput() {
	$("#formHiddenInput").val($("#users").val());
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
</script>
</head>
<?php
require_once '../service/DutyService.class.php';

if ($_FILES['inputExcelBuy']['size'] >0) {
    // 获取上传的文件名
    $filename = $_FILES['inputExcelBuy']['name'];
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcelBuy']['tmp_name'] ;
    // 上传顺序,区分当前页面的id
    $pageId = $_REQUEST["pageId"] ;
    $msg = uploadFile("admin", $filename, $tmp_name);
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
//    echo "上传{$msg["n"]}件<br/>";
    echo "更新{$msg["u"]}件<br/>";
//    echo "删除{$msg["d"]}件<br/>";
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
						<td><strong>考勤管理</strong>
    						<form name="form2"  method="post" enctype="multipart/form-data" >
    						  <input type="hidden" id="formHiddenInput" value="-1" name="user"/>
    							上传内容(导出文件后上传, 程序读取的是B,D,E列的内容, 注意确认当前员工账号):<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
    						</form>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<select name="dutyYear">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
							</select>年
							<select name="dutyMonth">
								<option value="01">01</option>
								<option value="02">02</option>
								<option value="03">03</option>
								<option value="04">04</option>
								<option value="05">05</option>
								<option value="06">06</option>
								<option value="07">07</option>
								<option value="08">08</option>
								<option value="09">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>月
							<select id="users" name="users" onchange="setUploadInput()"></select> 
							<button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br />
							<a href="../system/system_static_list.php?pType=DUTY_HOLIDAY_TYPE">休假类型</a>
							<select id="holiday_type" name="holiday_type">
							</select>
							<label><input type="checkbox" name="is_all" />全员标记</label>
							<button onclick="updateHoliday()">标记休假</button>
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
                                        <th style="width: 20px;"><input type="checkbox" onclick="chkAll(this)"/>選択</th>
										<th style="width:9%;" class="cellcolor">日付</th>
										<th style="width:11%;" class="cellcolor">规定出勤时间</th>
										<th style="width:11%;" class="cellcolor">规定退勤时间</th>
										<th style="width:11%;" class="cellcolor">出勤時刻</th>
										<th style="width:11%;" class="cellcolor">開始時間</th>
										<th style="width:11%;" class="cellcolor">終了時間</th>
										<th style="width:11%;" class="cellcolor">退勤時刻</th>
										<th id="thId1" class="cellcolor">勤務時間<span id="timeType"></span></th>
 										<!--<th id="thId15" class="cellcolor">勤務時間<br />(15分)</th>
                                        <th id="thId30" class="cellcolor">勤務時間<br />(半小时)</th> -->
										<th class="cellcolor">普通残業</th>
										<th class="cellcolor">深夜残業</th>
										<th class="cellcolor">休假</th>
										<th class="cellcolor">備考</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
						  <table width="100%" id="updateTable">
						      <thead>
						          <tr><td>审核人</td><td>审核时间</td><td>审核状态</td><td>操作</td></tr>
					          </thead>
						      <tbody>
						          <tr bgcolor="#FFFFFF">
						              <td><?php echo $_COOKIE["VioomaUserID"]?></td>
						              <td><input class="Wdate" onclick="WdatePicker()" name="passDate" type="text" value="<?php echo date("Y-m-d H:i:s")?>"/></td>
						              <td><select name="salaryState"><option value="0">未审核</option><option value="1">已审核</option></select></td>
						              <td><button onclick="updateState()">提交</button></td>
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
			<td><div id="totalPage"></div></td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
		<?php 
copyright();
?>
</body>
</html>

