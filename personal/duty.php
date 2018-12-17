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
<title>考勤</title>
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
	if(thisMonth < 9) {
		$("select[name='dutyMonth']").val("0"+(thisMonth+1));
	} else {
		$("select[name='dutyMonth']").val(thisMonth+1);
	}
	$("#formTr button").each(function(i, item){
		$(this).click(function(){
			if(i ==0) { //出勤
				$.ajax({
	        		type: "post",
	        		url: "../service/DutyService.class.php",
	        		data: "flag=check_in",
	        		success: function(data){
	            		if(data > 0) {
	        				alert("成功");
	        				initPage(0);
	            		} else {
	        				alert("失败");
	            		}
	        		}
	        	});
			} else if(i ==1) {//休憩开始
				$.ajax({
	        		type: "post",
	        		url: "../service/DutyService.class.php",
	        		data: "flag=snooze_start",
	        		success: function(data){
	            		if(data > 0) {
	        				alert("成功");
	        				initPage(0);
	            		} else {
	        				alert("失败");
	            		}
	        		}
	        	});
			} else if(i ==2) {//休憩结束
				$.ajax({
	        		type: "post",
	        		url: "../service/DutyService.class.php",
	        		data: "flag=snooze_end",
	        		success: function(data){
	            		if(data > 0) {
	        				alert("成功");
	        				initPage(0);
	            		} else {
	        				alert("失败");
	            		}
	        		}
	        	});
			} else {//签退
				$.ajax({
	        		type: "post",
	        		url: "../service/DutyService.class.php",
	        		data: "flag=check_out",
	        		success: function(data){
	            		if(data > 0) {
	        				alert("成功");
	        				initPage(0);
	            		} else {
	        				alert("失败");
	            		}
	        		}
	        	});
			}
		});
	});
	initPage(0);
})
function initPage(pageIndex) {
	$.ajax({
		type: "post",
		url: "../service/DutyService.class.php",
		data: "flag=initPage&"+$("#searchForm").find("select").serialize()+"&nocache="+new Date().getTime(),
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
 	           		+"    <td>"+entry.atime+"("+entry.wk+")</td>"
 	           		+"    <td>"+entry.check_in_on_time.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.check_out_on_time.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.check_in.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.snooze_start.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.snooze_end.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.check_out.substr(11, 5)+"</td>"
 	           		+"    <td>"+entry.work_time+"</td>"
 	           		+"    <td>"+entry.over_time1+"</td>"
 	           		+"    <td>"+entry.over_time2+"</td>"
 	           		+"    <td>"+entry.remark+"</td>"
            		+"</tr>";

    		});
    		$("#contentTable tbody tr:gt(0)").remove();
    		$("#contentTable tbody").append(html);
    	}
	});
}
//导出excel
function exportExcel() {
	var param = $("input[name=s_text]").serialize()+"&"+$("input[type=checkbox]").serialize();
	var url = "../service/DutyService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=exportExcel";
    window.open(url);
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
    $msg = uploadFile("staff", $filename, $tmp_name);
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
						<td><strong>考勤</strong>
    						<form name="form2"  method="post" enctype="multipart/form-data" >
    							上传内容(导出文件后上传, 程序读取的是B,D,E列的内容):<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
    						</form>
						</td>
					</tr>
					<tr>
						<td style="text-align:right;" id="searchForm">
							<select name="dutyYear">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
								<option value="2019">2019</option>
								<option value="2020">2020</option>
								<option value="2021">2021</option>
								<option value="2022">2022</option>
								<option value="2023">2023</option>
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
							<button onclick="initPage(0)">搜索</button>
							<button onclick="exportExcel()">导出</button><br />
						</td>
					</tr>
					<tr>
						<td bgcolor="#FFFFFF">
							<table id="contentTable" width="100%" border="0">
								<thead>
									<tr>
										<th class="cellcolor">日付</th>
										<th class="cellcolor">规定出勤时间</th>
										<th class="cellcolor">规定退勤时间</th>
										<th class="cellcolor">出勤時刻</th>
										<th class="cellcolor">開始時間</th>
										<th class="cellcolor">終了時間</th>
										<th class="cellcolor">退勤時刻</th>
										<th class="cellcolor">勤務時間　</th>
										<th class="cellcolor">普通残業</th>
										<th class="cellcolor">深夜残業</th>
										<th class="cellcolor">備考</th>
									</tr>
								</thead>
								<tbody>
                                    <tr id="formTr">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><button>出勤</button></td>
                                        <td><button>休憩開始	</button></td>
                                        <td><button>休憩終了	</button></td>
                                        <td><button>退勤</button></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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

