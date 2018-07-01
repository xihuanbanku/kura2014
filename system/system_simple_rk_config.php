<?php
require(dirname(__FILE__)."/../include/config_base.php");
require(dirname(__FILE__)."/../include/config_rglobals.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>快速入库参数设定</title>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
var clickCount = 0;
$(function(){
    $("#cp_categories").change(function() {
    	loadItem(this);
    });
    $("button").click(function(){
    	submitColParam();
    })
	$.ajax({
		type: "post",
		url: "../service/KcService.class.php",
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value=''>大分類選択</option>" +msg;
    		$("#cp_categories").html(msg);
		}
	});
    initForm();
});
function loadItem(obj) {
	$.ajax({
		type: "post",
		url: "../service/SimpleRkService.class.php",
		data: $("#cp_categories").serialize() + "&flag=loadItem",
		success: function(msg){
			if(msg.trim() != "null") {
    			msg = eval("("+msg+")");
    			for(var i=1; i<11; i++) {
    			    $("input[name='item_"+i+"']").val(msg[0]["item_"+i]);
    			    $("input[name='item_type_"+i+"']").eq(msg[0]["item_type_"+i]).attr("checked",true)
    			    $("input[name='item_type_"+i+"']").eq(1).next().html("<a target='_blank' href='system_static_list.php?pType=CAT_" +$("#cp_categories").val()+ "_ITEM_" +i+ "'>编辑</a>");
    			}
    			for(var i=1; i<7; i++) {
    				var cols = msg[0]["bullet_cols_"+i].replace(new RegExp("' ',", "gm"), "").split(",")
    				for(var j=0; j<cols.length; j++) {
        				$("select[name='bullet_cols_"+(j+1)+"[]'").eq(i-1).val(cols[j]);
    				}
    			}
    		}
		}
	});
}
function submitColParam() {
	if(clickCount<=0) {
		clickCount++;
	} else {
		alert("请勿重复提交");
	}
	$.ajax({
		type: "post",
		url: "../service/SimpleRkService.class.php",
		data: $("#table_border input").serialize() + "&"+$("#table_border select").serialize() + "&flag=submitColParam",
		success: function(msg){
			clickCount =0;
			if(msg > 0) {
				alert("更新成功");
			} else {
				alert("更新失败");
    		}
		}
	});
}
function initForm() {
	var html ="";
	for(var i=1; i<11; i++) {
		html += ""
			+"<tr>"
			+" <td class='row_style'>"
			+"  项目" +i
			+" </td>"
			+" <td>"
			+" <input type='text' name='item_" +i + "'/> <input type='radio' name='item_type_" +i + "' value='0'/>输入框 <input type='radio' name='item_type_" +i + "' value='1'/>下拉框<span></span>"
			+" </td>"
			+"</tr>"
			+"";
	}
	$("#table_border tr:gt(0)").remove();
	$("#table_border").append(html);
	html ="";
	var option_html = "<option value='&apos;&apos;&apos;&apos;'></option>";
	var select_html = "";
	for(var i=1; i<11; i++) {
		option_html += "<option value='item_" +i + "'>项目" +i + "</option>";
	}
	for(var i=1; i<11; i++) {
		select_html += "<select name='bullet_cols_" +i + "[]'>" + option_html + "</select>";
	}
	for(var i=1; i<7; i++) {
		html += ""
			+"<tr>"
			+" <td class='row_style'>"
			+"  箇条書き" +i
			+" </td>"
			+" <td>"
			+select_html
			+" </td>"
			+"</tr>"
			+"";
	}
	$("#table_border").append(html);
}
</script>
</head>
<body>
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
      <td><strong>快速入库参数设定</strong></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	    <tr>
		 <td class="row_style">分类:</td>
		 <td>
            <select name="cp_categories" id="cp_categories">
            </select>
		 </td>
		</tr>
		<tr>
		 <td class="row_style">
		  项目1
		 </td>
		 <td>
		 <input type="text" name="item_1"/> <input type="radio" name="item_type_1"/>输入框 <input type="radio" name="item_type_1"/>下拉框
		 </td>
	    </tr>
	   </table>
	  </td>
	 </tr>
    	<tr>
    	 <td align="center"><button>提交</button></td>
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
