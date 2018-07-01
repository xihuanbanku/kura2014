<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");


$row=new dedesql(false);
if(empty($_REQUEST["avrg"])) {
    $query="SELECT p_value FROM `jxc_static` where p_type = 'AVRAG_PARAM' and p_name='avrg'";
    $row->setquery($query);
    $row->execute();
    while($rs=$row->getArray()){
        $avrg = $rs['p_value'];
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<link href="style/loading.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>?r=<?php echo rand()?>"></script>
<script type="text/javascript" src="js/loading.js?r=<?php echo rand()?>"></script>
<title>仕入れ表</title>
<style type="text/css">
#report_table{
	table-layout:fixed;
}
#report_table tr{
	background-color: #FFF;	
}
#report_table tr:hover{
	background-color: #EBF1F6;	
}
#report_table td{
	white-space: nowrap;
	text-overflow:ellipsis;
	overflow:hidden;
}
</style>
<script language=javascript>
$(function(){
	var url = "service/KcService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value=''>大分類選択</option>" +msg;
    		$("#cp_categories").html(msg);
		}
	});

	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: {"flag":"loadStaticParam", "user":<?php echo GetCookie('userID')?>, "p_type":"REPORT_DATE"},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
        		if(entry.p_name == "date_last_term") {
      			  $("#lastTerm").html("上期["+entry.p_value+"]");
        		} else {
      			  $("#thisTerm").html("本期["+entry.p_value+"]");
        		}
    		});
	    }
	});
});
function out_excel(sign){
    window.open('service/ReportService.class.php?flag=report_sale2_out_excel&'+$("input").serialize()+'&'+$("select").serialize(),'','');
}
function getCategoryDown(param) {
	var url = "service/KcService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"getCategoryDown", "cp_categories":param},
		success: function(msg){
			$("#cp_categories_down option:gt(0)").remove();
    		$("#cp_categories_down").append(msg);
		}
	});  
}
function initPage() {
	showLoading();
	var url = "service/ReportService.class.php?"+ $("input").serialize() +"&"+ $("select").serialize();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"report_sale2_initPage"},
		success: function(msg){
			var n1 =0;
			var last_n1 =0;
			var n2 =0;
			var money =0;
    		var html="";
    		msg = eval("("+msg+")");
    		$("#lastTerm").html("上期["+msg.last_sday+"~"+msg.sday+"]");
    		$("#thisTerm").html("本期["+msg.sday+"~"+msg.eday+"]");
			$.each(msg.results, function(entryIndex, entry){
			   n1 += entry.number*1;
			   last_n1 += entry.last_number*1;
			   n2 += Math.round(entry.remain_left)*1;
			   money+=entry.number*entry.sale;
			   html+="<tr >"
		                   +"<td>"+entry.productid+"</td>"
			               +"<td title=\""+entry.cp_title+"\">"+entry.cp_title+"</td>"
				           +"<td title=\""+entry.cp_detail+"\">"+entry.cp_detail+"</td>"
				           +"<td align='right'>￥"+number_format(entry.sale)+"</td>"
				           +"<td align='right'>"+Math.round(entry.avrg)+"</td>"
				           +"<td align='right'>"+entry.number+"</td>"
				           +"<td align='right'>"+Math.round(entry.last_avrg)+"</td>"
				           +"<td align='right'>"+entry.last_number+"</td>"
				           +"<td align='right'>"+Math.round(entry.remain_left)+"</td>"
				           +"<td align='right'>￥"+number_format(entry.number*entry.sale)+"</td></tr>";
			});
			html+="<tr><td>合　計:</td><td colspan='4'>&nbsp;</td><td align='right'>"+n1+"</td><td align='right'></td><td align='right'>"+last_n1+"</td><td align='right'>"+n2+"</td><td align='right'>￥"+number_format(money)+"</td></tr>";
    		$("#report_table tr:gt(2)").remove();
    		$("#report_table").append(html);
    		hideLoading();
		}
	});  
}
</script> 
</head>
<body>
<table width="100%" border="1" id="table_style_all" cellpadding="0" cellspacing="0">
  <tr>
    <td id="table_style" class="l_t">&nbsp;</td>
    <td>&nbsp;</td>
    <td id="table_style" class="r_t">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><span id="thisTerm"></span><span id="lastTerm"></span></td>
    <td></td>
  </tr>
     <tr>
      <td colspan="3"><strong>&nbsp;仕入れ表</strong></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF" colspan="3">
        <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
    	    <tr height="40">
    		 <td id="row_style">日付を選択してください&nbsp;&nbsp;
    		 <input type="text" name="sday" class="Wdate" onClick="WdatePicker()">
    		 -<input type="text" name="eday" class="Wdate" onClick="WdatePicker()">
            分類：
            <select name="cp_categories" id="cp_categories" onchange="getCategoryDown(this.value)">
            </select>->
            <select name="cp_categories_down" id="cp_categories_down">
                <option value=''>小分類選択</option>
            </select>
            平均数制限:
            <input type="text" name="avrg" maxlength="3" size="3" value="<?php echo $avrg;?>"/>
            类型:<select name="s_type">
            	<option value="-1">全部</option>
            	<option value="0">u更新</option>
            	<option value="1">a更新</option>
            </select>
    		 <input type="button" value="日報レビュー" onclick="initPage()">&nbsp;
    		 <input type="button" value="Excel出力" onclick="out_excel('day1')"></td>
    	    </tr>
        </table>
       <table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>
           <tr><td colspan="10">販売レポート日報</td></tr>
           <tr><td colspan="10">担当者：<?php echo GetCookie('VioomaUserID')?></td></tr>
    	   <tr class='row_report_head'>
        	   <td >商品コード</td>
        	   <td >タイトル</td>
        	   <td >仕様</td>
        	   <td >販売単価</td>
        	   <td >平均数</td>
        	   <td >合計数</td>
        	   <td >上期平均数</td>
        	   <td >上期合計数</td>
        	   <td >仕入れ数</td>
        	   <td >総合金額</td>
    	   </tr>
       </table>
      </td>
     </tr>
</table>
<?php
copyright();
?>
</body>
</html>
