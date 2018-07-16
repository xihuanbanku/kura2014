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
<title>給与明細書</title>
<script language="javascript">
$(function(){
	var myDate = new Date();
	//获取完整的年份(4位,1970-????)
	$("select[name='dutyYear']").val(myDate.getFullYear());
	//获取当前月份(0-11,0代表1月)
	if(myDate.getMonth() < 10) {
		$("select[name='dutyMonth']").val("0"+(myDate.getMonth()+1));
	} else {
		$("select[name='dutyMonth']").val(myDate.getMonth()+1);
	}
})
function initPage(pageIndex) {
	var url = "../service/SalaryService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: "flag=initPage&"+$("#contentTable").find("select").serialize()+"&nocache="+new Date().getTime(),
		success: function(data){
			$("#tbody_html1").html("");
			$("#tbody_html2").html("");
			$("#tbody_html3").html("");
			data = eval("("+data+")");
			var tbody_html1 ="<tr>";
			var tbody_html2 ="<tr>";
			var tbody_html3 ="<tr>";
			var tmp_html ="";
			var td_count1=0;
			var td_count2=0;
			var td_count3=0;
			$.each(data, function(entryIndex, entry){
				tmp_html ="    <th>" + entry.p_name + "</th><td>" + entry.mod_value + "</td>";
    			switch(entry.p_type) {
        			case "1":
        				tbody_html1+=tmp_html;
        				td_count1++;
        				if(td_count1>0 && td_count1%4 ==0) {
        					tbody_html1+="</tr><tr>";
        				}
        			break;
        			case "2":
        				tbody_html2+=tmp_html;
        				td_count2++;
        				if(td_count2>0 && td_count2%4 ==0) {
        					tbody_html2+="</tr><tr>";
        				}
        			break;
        			case "3":
        				tbody_html3+=tmp_html;
        				td_count3++;
        				if(td_count3>0 && td_count3%4 ==0) {
        					tbody_html3+="</tr><tr>";
        				}
        			break;
    			}
			});
			$("#tbody_html1").append(tbody_html1+completeBlank(4-td_count1%4)+"</tr>");
			$("#tbody_html2").append(tbody_html2+completeBlank(4-td_count2%4)+"</tr>");
			$("#tbody_html3").append(tbody_html3+completeBlank(4-td_count3%4)+"</tr>");
		}
	});
}
function completeBlank(count) {
	if(count%4 ==0) {
		return;
	}
	var html="";
	for(var i=0; i<count; i++) {
		html += "<th></th><td></td>";
	}
	return html;
}
</script>
<style type="text/css">
#contentTable th{
	width: 20%;
}
#contentTable td{
	width: 5%;
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
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="2" id="contentTable">
					<tr>
						<td><strong>給与明細書</strong><input type="button" value="打印" onclick="javascript:window.print();"/></td>
						<td align="right">
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
							<button onclick="initPage(0)">搜索</button>
							社員No.:<?php echo $_COOKIE["VioomaUserID"]?></td>
					</tr>
					<tr>
						<td bgcolor="#9AFF9A" colspan="2">
							<table width="100%" cellspacing="0" border="1">
							 <thead>
								<tr>
									<td colspan="8">支給明細</td>
								</tr>
							 </thead>
							 <tbody id="tbody_html1">
							 </tbody>
							</table>
                        </td>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr></tr>
					<tr>
						<td bgcolor="#40E0D0" colspan="2">
							<table width="100%" cellspacing="0" border="1">
							 <thead>
								<tr>
									<td colspan="8">控除明細</td>
								</tr>
							 </thead>
							 <tbody id="tbody_html2">
						     </tbody>
							</table>
                        </td>
					</tr>
					<tr></tr>
					<tr></tr>
					<tr></tr>
					<tr>
						<td bgcolor="#32CD32" colspan="2">
							<table width="100%" cellspacing="0" border="1">
							 <thead>
								<tr>
									<td colspan="8">勤務</td>
								</tr>
							 </thead>
							 <tbody id="tbody_html3">
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
			<td>&nbsp;</td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
<?php
copyright();
?>
</body>
</html>