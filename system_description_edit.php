<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
if($action=='save'){
 $addsql="insert into #@__description(id, name, price_1, price_2, price_3, price_4, price_5)values
 ('$cp_categories', '$categories','$price_1','$price_2','$price_3','$price_4','$price_5') 
 on duplicate key update 
 name='$categories', price_1='$price_1', price_2='$price_2', price_3='$price_3', price_4='$price_4', price_5='$price_5'";
 $message= "商品说明".$categories."成功";
 $loginip=getip();
 $logindate=getdatetimemk(time());
 $username=str_replace($cfg_cookie_encode,'',$_COOKIE["VioomaUserID"]);
 $asql=New Dedesql(false);
 $asql->ExecuteNoneQuery($addsql);
 $asql->ExecuteNoneQuery("insert into #@__recordline(message,date,ip,userid) values('{$message}','{$logindate}','{$loginip}','$username')");
 
 $asql->close();
 showmsg('商品说明を修正しました。','system_description_edit.php');
 exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>商品说明</title>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" >
$(function(){
    $("select[name=which]").change(function() {
    	loadPriceParam(this);
    });
    $("#cp_categories").change(function() {
    	loadDesContent(this);
    });
    $("#price_param_td button").click(function(){
        submitPriceParam();
    })
    $("#system_kc_buying_param_td button").click(function(){
        submitBuyingParam();
    })
    $("#system_buying_before_time_td button").click(function(){
        submitSystemBuyingBeforeTime();
    })
	$.ajax({
		type: "post",
		url: "service/KcService.class.php",
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value=''>大分類選択</option>" +msg;
    		$("#cp_categories").html(msg);
		}
	});
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: {"flag":"loadStaticParam", "p_type":"SYSTEM_KC_BUYING_PARAM"},
		success: function(msg){
			msg = eval("("+msg+")");
			$.each(msg, function(entryIndex, entry){
//	 			    alert(entry.p_name+"|"+entry.p_value );
				$("#system_kc_buying_param_td input[name="+entry.p_name +"]").val(entry.p_value);
    		});
		}
	});
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: {"flag":"loadStaticParam", "p_type":"SYSTEM_BUYING1_BEFORE_TIME"},
		success: function(msg){
			msg = eval("("+msg+")");
			$.each(msg, function(entryIndex, entry){
// 			    alert(entry.p_name+"|"+entry.p_value );
				$("#system_buying_before_time_td select[name="+entry.p_name +"]").val(entry.p_value);
    		});
		}
	});
    initSelect();
});
function loadPriceParam(obj) {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $("#price_param_td input").serialize() + "&"+$("#price_param_td select").serialize() + "&flag=loadPriceParam",
		success: function(msg){
			if(msg.trim() == "null") {
				$("#price_param_td input").each(function(i, item){
				    $(item).val("");
				})
			} else {
    			msg = eval("("+msg+")");
    			$.each(msg, function(entryIndex, entry){
//     			    alert(entry.p_name+"|"+entry.p_value );
    				$("#price_param_td input[name="+entry.p_name +"]").val(entry.p_value);
        		});
    		}
		}
	});
}
function loadBuyingParam(obj) {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: "flag=loadStaticParam",
		success: function(msg){
			if(msg.trim() == "null") {
				$("#dian_param_td input").each(function(i, item){
				    $(item).val("");
				})
			} else {
    			msg = eval("("+msg+")");
    			$.each(msg, function(entryIndex, entry){
//     			    alert(entry.p_name+"|"+entry.p_value );
    				$("#dian_param_td input[name="+entry.p_name +"]").val(entry.p_value);
        		});
    		}
		}
	});
}
function loadDesContent(obj) {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $(obj).serialize() + "&flag=loadDesContent",
		success: function(msg){
			if(msg.trim() != "null") {
    			msg = eval("("+msg+")");
				$("textarea[name=categories]").html(msg[0].name);
				$("select[name=price_1]").val(msg[0].price_1);
				$("select[name=price_2]").val(msg[0].price_2);
				$("select[name=price_3]").val(msg[0].price_3);
				$("select[name=price_4]").val(msg[0].price_4);
				$("select[name=price_5]").val(msg[0].price_5);
    		}
		}
	});
}
function initSelect() {
	var html ="";
	for(var i=1; i<51; i++) {
		html+= "<option value=\"PRICE_" + i + "\">" + i + "</option>"
	}
	$("select[name=which]").append(html);
}
function submitPriceParam() {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $("#price_param_td input").serialize() + "&"+$("#price_param_td select").serialize() + "&flag=submitPriceParam",
		success: function(msg){
			if(msg > 0) {
				alert("更新成功");
			} else {
				alert("更新失败");
    		}
		}
	});
}
function submitBuyingParam() {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $("#system_kc_buying_param_td input").serialize() + "&flag=submitBuyingParam",
		success: function(msg){
			if(msg > 0) {
				alert("更新成功");
			} else {
				alert("更新失败");
    		}
		}
	});
}
function submitSystemBuyingBeforeTime() {
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: $("#system_buying_before_time_td select").serialize() + "&flag=submitSystemBuyingBeforeTime",
		success: function(msg){
			if(msg > 0) {
				alert("更新成功");
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
$esql=New Dedesql(false);
$query="select * from #@__description where id=1";
$esql->SetQuery($query);
$esql->Execute();
if($esql->GetTotalRow()==0){
ShowMsg('引数エラー、もう一度実行してください。','-1');
exit();
}
$row=$esql->GetOne($query);
$esql->close();
?>
<table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
  <tr>
    <td class="row_style" class="l_t">&nbsp;</td>
    <td>&nbsp;</td>
    <td class="row_style" class="r_t">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<table width="100%" border="0" cellpadding="0" cellspacing="2">
     <tr>
      <td><strong>&nbsp;商品说明修正</strong></td>
     </tr>
	 <form action="system_description_edit.php?action=save" method="post">
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td class="row_style">&nbsp;商品说明：</td>
		 <td>
		 <select name="cp_categories" id="cp_categories">
        </select>
		 <textarea rows="20" cols="100" name="categories"></textarea>
		 </td>
		</tr>
		<tr>
		 <td class="row_style">
		 [タイトル]构成
		 </td>
		 <td>
		 <select name="price_1">
		  <option value="b.name">名称</option>
		  <option value="c.categories">大分類</option>
		  <option value="d.categories">小分類</option>
		  <option value="a.model">型号</option>
		  <option value="a.modelDetail">型号详细</option>
		 </select>
		 <select name="price_2">
		  <option value="b.name">名称</option>
		  <option value="c.categories">大分類</option>
		  <option value="d.categories">小分類</option>
		  <option value="a.model">型号</option>
		  <option value="a.modelDetail">型号详细</option>
	     </select>
		 <select name="price_3">
		  <option value="b.name">名称</option>
		  <option value="c.categories">大分類</option>
		  <option value="d.categories">小分類</option>
		  <option value="a.model">型号</option>
		  <option value="a.modelDetail">型号详细</option>
		 </select>
		 <select name="price_4">
		  <option value="b.name">名称</option>
		  <option value="c.categories">大分類</option>
		  <option value="d.categories">小分類</option>
		  <option value="a.model">型号</option>
		  <option value="a.modelDetail">型号详细</option>
		 </select>
		 <select name="price_5">
		  <option value="b.name">名称</option>
		  <option value="c.categories">大分類</option>
		  <option value="d.categories">小分類</option>
		  <option value="a.model">型号</option>
		  <option value="a.modelDetail">型号详细</option>
		 </select>
		 </td>
	    </tr>
		<tr>
		 <td class="row_style">&nbsp;</td>
		 <td>&nbsp;<input type="submit" name="submit" value="提交"></td>
	    </tr>
		</form>
	    <tr>
		 <td class="row_style">仕入単価系数：</td>
		 <td id="price_param_td">
		 <select name="which">
		      <option value=""></option>
		 </select>
		 价格区间
		 <input size="3" type="text" name="price_from" />~
		 <input size="3" type="text" name="price_to" />
		 系数
		 <input size="3" type="text" name="price_1" />*
		 <input size="3" type="text" name="price_2" />*
		 <input size="3" type="text" name="price_3" />*
		 <input size="3" type="text" name="price_4" />*
		 <input size="3" type="text" name="price_5" />*
		 <input size="3" type="text" name="price_6" />*
		 <input size="3" type="text" name="price_7" />*
		 <input size="3" type="text" name="price_8" />*
		 <input size="3" type="text" name="price_9" />*
		 <input size="3" type="text" name="price_10"/>
		 <button>提交</button>
		 </td>
	    </tr>
	    <tr>
		 <td class="row_style">仕入数系数：</td>
		 <td id="system_kc_buying_param_td">
		 <input size="3" type="text" name="system_kc_buying_param" />
		 <button>提交</button>
		 </td>
	    </tr>
	    <tr>
		 <td class="row_style">仕入分界时间：</td>
		 <td id="system_buying_before_time_td">
		 每周
		 <select name="system_buying1_before_date">
		      <option value="0">周一</option>
		      <option value="1">周二</option>
		      <option value="2">周三</option>
		      <option value="3">周四</option>
		      <option value="4">周五</option>
		      <option value="5">周六</option>
		      <option value="6">周日</option>
		 </select>
		 小时
		 <select name="system_buying1_before_hour">
		      <option value="1">01</option>
		      <option value="2">02</option>
		      <option value="3">03</option>
		      <option value="4">04</option>
		      <option value="5">05</option>
		      <option value="6">06</option>
		      <option value="7">07</option>
		      <option value="8">08</option>
		      <option value="9">09</option>
		      <option value="10">10</option>
		      <option value="11">11</option>
		      <option value="12">12</option>
		      <option value="13">13</option>
		      <option value="14">14</option>
		      <option value="15">15</option>
		      <option value="16">16</option>
		      <option value="17">17</option>
		      <option value="18">18</option>
		      <option value="19">19</option>
		      <option value="20">20</option>
		      <option value="21">21</option>
		      <option value="22">22</option>
		      <option value="23">23</option>
		 </select>
		 <button>提交</button>
		 </td>
	    </tr>
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
