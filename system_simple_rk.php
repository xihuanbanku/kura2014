<?php
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>商品快速入庫</title>
<style type="text/css">
.rtext {
	background: transparent;
	border: 0px;
	color: red;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
</style>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var url = "service/KcService.class.php";
var clickCount = 0;
$(function(){
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = "<option value=''>大分類選択</option>" +msg;
    		$("#cp_categories").html(msg);
		}
	});
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initNew"},
		success: function(msg){
    		$("#new").append(msg);
    		$("#new").change(function(){
        		switch($(this).val()) {
        		case "1":
        		case "2":
            		$("#state2id").val(73);
            		break;
        		default:
            		$("#state2id").val(72);
            		break;
        		}
    		});
		}
	});
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initYear"},
		success: function(msg){
    		$("#year").html(msg);
		}
	});
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initLab"},
		success: function(msg){
			$("#labid option:gt(0)").remove();
    		$("#labid").append(msg);
		}
	});

	$.each($(".stateTr select"), function(index, item) {
		$.ajax({
			type: "post",
			url: url,
			data: {"flag":"initState", "sid": index+1},
			success: function(msg){
				$("#state"+(index+1) + "id option:gt(0)").remove();
	    		$("#state"+(index+1) + "id").append(msg);
			}
		});
	});

	$.ajax({
		type: "post",
		url: "service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"64", "user":<?php echo GetCookie('userID')?>},
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
	//在型号和型号详细上绑定查询事件
	$("input[name=model], input[name=modelDetail]").keyup(function(e){
		var _input = $(this);
		if(_input.val().trim()=="") {
			_input.next().html("相似产品0");
			return;
		}
		if (e.which == 13) {
    		$.ajax({
    			type: "post",
    			url: "service/SystemService.class.php?"+ $(this).serialize()+"&"+$("#cp_categories").serialize(),
    			data: {"flag":"query4Count", "user":<?php echo GetCookie('userID')?>},
    			success: function(data){
    				_input.next().html("<a href='system_kc.php?target=blank&stext="+_input.val().trim()+"&"+$("#cp_categories").serialize()+"' target='_blank'>相似产品"+data+"</a>");
    			}
    		});
		}
	});
		

})
function checkForm(){
	if(clickCount<=0) {
		clickCount++;
	} else {
		alert("请勿重复提交");
	}
	var stateFlag = true;
	$.each($(".stateTr select"), function(index, item) {
		if($(item).val()=="") {
			stateFlag = false;
			return stateFlag;
		}
	});
// 	if(!stateFlag) {
// 		alert("状态未选择完成");
// 		return;
// 	}
	if($("#cp_categories").val() == ""){
		alert("分類未选择");
		return;
	}
	if($("#cp_categories_down").val() == ""){
		alert("小分類未选择");
		return;
	}
	if($("input[name='serial']").val() == ""){
		alert("系列未填写");
		return;
	}
	if($("input[name='serial']").val().match(/\d+/g)) {
		alert("系列中不能包含数字");
		return;
	}
	if($("input[name='model']").val() == ""){
		alert("型号未填写");
		return;
	}
	if($("input[name='modelDetail']").val() == ""){
		alert("型号详细未填写");
		return;
	}
	if($("input[name='remark']").val() == ""){
		alert("備考未填写");
		return;
	}
	var url = "service/SystemService.class.php?"+$("form").serialize();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"insert", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			clickCount =0;
			if(trimStr(data) == "error1") {
				var html = "<tr>"
    				+"	<td><font color='red'>商品code已存在</font></td>"
    				+"	<td><font color='red'>请重新提交</font></td>"
    				+" </tr>";
				$("#barcodes").html(html);
			} else {
				data = eval(data);
    			var html = "<tr>"
            				+"	<td>" + trimStr($("input[name='serial']").val()) + data[0].next_id + "</td>"
            				+"	<td>"+(100000000000 +parseInt(data[0].jxc_basic_copy_id)) + "</td>"
            				+" </tr>";
    			$("#barcodes").html(html);
    			$("#success").fadeIn(500);
    			$("#success").fadeOut(500);
    			$("form")[0].reset();
			}
		},
		error: function(e) {
			alert(e);
		}
	});
}
function out_excel(obj){
	if($("#sday").val() == "") {
		alert("开始时间未选择");
		return;
	}
	if($("#eday").val() == "") {
		alert("结束时间未选择");
		return;
	}
	if($("#eday").val() < $("#sday").val()) {
		alert("结束时间不能小于开始时间");
		return;
	}
	if(obj == "mine") {
    	window.location.href="service/SystemService.class.php?flag=out_excel&obj="+<?php echo $_COOKIE["userID"];?>+"&"+$("form").serialize();
	} else {
    	window.location.href="service/SystemService.class.php?flag=out_excel&obj=all&"+$("form").serialize();
	}
// 	$.ajax({
// 		type: "post",
// 		url: url,
//		data: {"flag":"out_excel", "userID":},
// 		success: function(data){
// 			if(data == 1) {
// 				$("#success").fadeIn(1000);
// 				$("#success").fadeOut(1000);
// 			} else {
// 				alert(data);
// 			}
// 		}
// 	});
}
function showNext(o) {
	var obj = $(o);
	if(obj.prop("checked")) {
		$("#exportTr").show(500);
	} else {
		$("#exportTr").hide(500);
	}
}
function getCategoryDown(param) {
	var array=new Array();
	 switch(param) {
 	case "23":
		array=new Array("サイズ：15.6インチ 15.4インチ 14.0インチ 13.3インチ 10.1インチ","解像度：1366*768 解像度：1600*900 解像度：1920*1080","表面処理：光沢 表面処理：非光沢", "バックライト：LED バックライト：LCD", "ピン数：40PIN ピン数：30PIN", "外観状況特記のみ：");
		break;
		
	case "13":
		array=new Array("電圧：10.8V 電圧：11V 電圧：14.4V","容量：4400mah 容量：5200mah","色：ブラック 色：シルバー 色：ホワイト", "消耗程度：", "外観状況特記のみ：", "記入必要なし");
		break;
		
	case "15":
		array=new Array("輸入電圧：100V～240V","出力電圧：19V","出力電流：","コネクタ形：Ｌ字 コネクタ形：ストレート","外観状況特記のみ：", "記入必要なし");
		break;
	
	case "20":
		array=new Array("電圧：5V","電流：","PIN数：4PIN PIN数：3PIN PIN数：2PIN", "記入必要なし", "記入必要なし", "記入必要なし");
		break;
	
	case "22":
		array=new Array("状態：動作確認済み","外観状況：使用感ありますが、全体的に綺麗状態です。：","記入必要なし", "記入必要なし", "記入必要なし", "記入必要なし");
		break;
		
	case "25":
		array=new Array("記入必要なし","記入必要なし","記入必要なし", "記入必要なし", "記入必要なし", "記入必要なし");
		break;
		
	case "198":
		array=new Array("タイプ： ない場合跳ばす","動作確認：","コネクター：SATA コネクター：IDE", "外観状況特記のみ：", "記入必要なし", "記入必要なし");
		break;
	case "251":
		array=new Array("記入必要なし","記入必要なし","記入必要なし", "記入必要なし", "記入必要なし", "記入必要なし");
		break;
	
	case "366":
		array=new Array("外した液晶パネルの解像度：1366*768 ","記入必要なし","記入必要なし", "記入必要なし", "記入必要なし", "記入必要なし");
		break;
		
	case "967":
		array=new Array("中身：マザーボード　CPUなど","スペック：CPU　メモリー　HDD","状態：OSまで動作確認済み　BIOSのみ確認済", "確認済項目：キーボード反応　光学ドライブ読み込み、USBの反応　LAN", "状態説明：動作確認用のキーボード、光学ドライブなど付属致しません。", "記入必要なし");
		break;

     default:
             array=new Array("箇条書き1", "箇条書き2", "箇条書き3", "箇条書き4", "箇条書き5", "箇条書き6");
             break;
     }
	$.each($(".cellcolor:gt(10):lt(17)"), function(entityIndex, entity) {
		$(entity).html(array[entityIndex]);
	});
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
</script>
</head>
<body>
<table width="100%" border="0" id="table_style_all"
	cellpadding="0" cellspacing="0">
	<tr>
		<td id="table_style" class="l_t">&nbsp;</td>
		<td>&nbsp;</td>
		<td id="table_style" class="r_t">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	    <td align="center">
		  <table id="barcodes" width="30%" border="0" style="text-align: center;">
		  </table>
		</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<form>
			<table width="100%" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td><strong>&nbsp;商品快速入庫</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                	         <tr id="success" style="display: none;">
            				    <td colspan="2">提交成功</td>
            				</tr>
                			<tr>
                				<td class="cellcolor">入庫担当：</td>
                				<td class="cellcolor"><input
                						type="text" name="operator"
                						value="<?php echo Getcookie('VioomaUserID'); ?>"
                						readonly="readonly" class="rtext" size="10"/> 
        						</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">新旧类型：</td>
                                 <td>
                                    <select name="new" id="new">
                                        <option value='1'>请选择</option>
                                    </select>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">年式：</td>
                                 <td>
                                    <select name="year" id="year">
                                    </select>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">倉庫：</td>
                                 <td>
                                    <select name="labid" id="labid">
                                    </select>
                        		</td>
                			</tr>
                			<tr class="stateTr">
                				<td class="cellcolor" width="30%">状态1-5：</td>
                                 <td>
                                    <select name="l_state1" id="state1id">
                                        <option value='0'>状態1</option>
                                    </select>
                                    <select name="l_state2" id="state2id">
                                        <option value='0'>状態2</option>
                                    </select>
                                    <select name="l_state3" id="state3id">
                                        <option value='0'>状態3</option>
                                    </select>
                                    <select name="l_state4" id="state4id">
                                        <option value='0'>状態4</option>
                                    </select>
                                    <select name="l_state5" id="state5id">
                                        <option value='0'>状態5</option>
                                    </select>
                        		</td>
                			</tr>
                			<tr class="stateTr">
                				<td class="cellcolor" width="30%">状态6-10：</td>
                                 <td>
                                    <select name="l_state6" id="state6id">
                                        <option value='0'>状態6</option>
                                    </select>
                                    <select name="l_state7" id="state7id">
                                        <option value='0'>状態7</option>
                                    </select>
                                    <select name="l_state8" id="state8id">
                                        <option value='0'>状態8</option>
                                    </select>
                                    <select name="l_state9" id="state9id">
                                        <option value='0'>状態9</option>
                                    </select>
                                    <select name="l_state10" id="state10id">
                                        <option value='0'>状態10</option>
                                    </select>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">分類：</td>
                				<td>
                                    <select name="cp_categories" id="cp_categories" onchange="getCategoryDown(this.value)">
                                    </select>->
                                    <select name="cp_categories_down" id="cp_categories_down">
                                        <option value=''>小分類選択</option>
                                    </select>
                				</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">系列(U-TB)：</td>
                				<td><input type="text" name="serial" size="50"></input>
                				</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">パソコン番号(シリーズ番号)：</td>
                				<td><input type="text" name="model" size="50"></input><span>相似产品0</span>
                				</td>
                			</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">パーツ番号(IBL50 LA 3611P Rev/1.0)：</td>
                    			<td><input type="text" name="modelDetail" size="50"></input><span>相似产品0</span></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き1：</td>
                    			<td><input type="text" name="cp_bullet_1" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き2：</td>
                    			<td><input type="text" name="cp_bullet_2" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き3：</td>
                    			<td><input type="text" name="cp_bullet_3" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き4：</td>
                    			<td><input type="text" name="cp_bullet_4" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き5：</td>
                    			<td><input type="text" name="cp_bullet_5" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">箇条書き6：</td>
                    			<td><input type="text" name="cp_bullet_6" size="50"></input></td>
                    		</tr>
                			<tr>
                    			<td class="cellcolor" width="30%">備考：</td>
                    			<td><input type="text" name="remark" size="50"></input></td>
                    		</tr>
                			
                			<tr>
                				<td class="cellcolor">&nbsp;</td>
                				<td><input type="button" value="保存" onclick="checkForm()"/>
                				</td>
                			</tr>
                			<tr id="simple_rk_priv_out">
                				<td class="cellcolor">导出<input type="checkbox" onclick="showNext(this)"/></td>
                				<td style="display: none;" id="exportTr">
                    				<input type="text" name="sday" id="sday"class="Wdate" onclick="WdatePicker()" />-
                    				<input type="text" name="eday" id="eday" class="Wdate" onclick="WdatePicker()" />
                    				<input type="button" value="个人Excel" onclick="out_excel('mine')"/>
                    				<input style="display: none;" id="simple_rk_out" type="button" value="Excel出力" onclick="out_excel('all')"/>
                				</td>
                			</tr>
                									
                			</table>
                		</td>
                	</tr>
				</table>
		      </form>
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

