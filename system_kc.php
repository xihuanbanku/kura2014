<?php
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/fix_mysql.inc.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
require (dirname(__FILE__) . "/include/page.php");
require_once (dirname(__FILE__) . "/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg_softname;?>在庫</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/pager.css" rel="stylesheet" type="text/css" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<link href="style/loading.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css"  href="style/jquery-ui.css"/>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript"	src="js/colResizable-1.5.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript" src="js/jquery.pagination.js"></script>
<script type="text/javascript" src="js/loading.js?r=<?php echo rand()?>"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script language="javascript">
//消息提示
var flag=false;
var inteval;
$(function(){
	var onSampleResized = function(e){
		/* var columns = $(e.currentTarget).find("th");
		var msg = "columns widths: ";
		columns.each(function(){ msg += $(this).width() + "px; "; })
		$("#sample2Txt").html(msg); */		
	};	
	$("#table_border").colResizable({
		liveDrag:true, 
		gripInnerHtml:"<div class='grip'></div>", 
		draggingClass:"dragging", 
		onResize:onSampleResized
	});
	var url = "service/KcService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initTotal"},
		success: function(msg){
    		$("#total").html(msg);
		}
	});
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initCategories"},
		success: function(msg){
	        msg = " <option value=''>大分類選択</option>" +msg;
    		$("#cp_categories").html(msg);
    		$("#cp_categories").val(getQueryString("category"));
            $("#subbtn").click();
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

	$.each($("#writeTipDiv select"), function(index, item) {
		$.ajax({
			type: "post",
			url: url,
			data: {"flag":"initState", "sid": index+1},
			success: function(msg){
				$("#state"+(index+1) + "id option:gt(0)").remove();
	    		$("#state"+(index+1) + "id").append(msg);
	        	$("#writeState"+(index+1) + "Select option:gt(0)").remove();
	        	$("#writeState"+(index+1) + "Select").append(msg);
			}
		});
	});
	
	//加载按钮
	$.ajax({
		type: "post",
		url: "service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"5", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
        		} else {
    				$("#" + entry.url).remove();
        		}
        		if(entry.url == "editA" && entry.loc > 0) {
        		    $("input[name=editA]").val(1);
        		}
        		if(entry.url == "deleteA" && entry.loc > 0) {
        		    $("input[name=deleteA]").val(1);
        		}
    		});
		}
	});
	//加载表头
	$.ajax({
		type: "post",
		url: "service/SystemService.class.php",
		data: {"flag":"loadStaticParam", "user":<?php echo GetCookie('userID')?>, "p_type":"KC_TABLE_HEAD"},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
        		if(entryIndex < 12) {
            		var html = "<span><a href='javascript:void(0)' onclick='sort(1," + (entryIndex+11) + ")'>" + entry.p_value + "</a></span>" + 
            	    "<input style=\"display: none;\" name=\"p_value\" type=\"text\" value=\"" + entry.p_value + "\"/>" + 
            	    "<input style=\"display: none;\" name=\"p_name\" type=\"text\" value=\"" + entry.p_name + "\"/>";
        			$("#table_border tr:eq(0) th:eq("+(entryIndex+8) +")").html(html);
        		}
    		});
		    $("#table_border tr:eq(0) th:gt(7):lt(12) span").each(function(i, item){
			    //alert($(item));
		        $(item).dblclick(function(){
		            $(item).toggle();
		            $(item).next().toggle();
		            $(item).next().focus();
		        });
		    })
		    $("#table_border tr:eq(0) th:gt(7):lt(12) input").each(function(i, item){
		        $(item).blur(function(){
		            $(item).prev().toggle();
		            $(item).toggle();
		        	
		        	$.ajax({
		        		type: "post",
		        		url: "service/SystemService.class.php?" + $(item).serialize() + "&" + $(item).next().serialize(),
		        		data: {"flag":"updateStaticParam", "user":<?php echo GetCookie('userID')?>, "p_type":"KC_TABLE_HEAD"},
		        		success: function(data){
	                		if(data > 0) {
	                			$(item).prev().html($(item).val());
	            				alert("修改成功")
	                		} else {
	            				alert("修改失败")
	                		}
                		}
            		});
	        	});
	        });
	    }
	});
    $("#subbtn").click(function() {
    	initPage(0);
    	initMyBulletin();
    });
    $("#searchDiv").bind("keydown",function(e){
        // 兼容FF和IE和Opera    
        var theEvent = e || window.event;    
        var code = theEvent.keyCode || theEvent.which || theEvent.charCode;    
        if (code == 13) {    
            //回??行??
                $("#subbtn").click();
            }    
    });
    $( "#dialog" ).dialog({
        autoOpen: false,
        width:800,
        height:350,
        show: {
          effect: "clip"
        },
        hide: {
          effect: "explode",
          duration: 2000
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
    $( "#dialog2" ).dialog({
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
    initMyBulletin();
    if(getQueryString("stext") != null) {
        $("input[name=stext]").val(getQueryString("stext"));
    }
})

function chkAllColumn(obj) {
	if($(obj).prop("checked")) {
    	$( "#dialog td input" ).prop("checked",true);
	} else {
    	$( "#dialog td input" ).prop("checked",false);
	}
}
function writeNote() {
	if($("input[name=isWriteNoteText]:checked").length==0 && $("input[name=isWriteASINText]:checked").length==0) {
		alert("请勾选更新内容");
		return;
	}
	var checkedCount = 0;
        $.each($("#table_border input:checkbox:gt(0)"), function(entryIndex, entry) {
        	if($(entry).prop("checked")) {
        		checkedCount ++;
        	}
        });
	if($("input[name=writeNoteText]").val() =="" && $("input[name=isWriteNoteText]:checked").val()==1) {
		if(!confirm("更新笔记为空?")) {
			return;
		}
	}
	if($("input[name=writeASINText]").val() =="" && $("input[name=isWriteASINText]:checked").val()==1) {
		if(!confirm("更新ASIN为空?")) {
			return;
		}
	}
	if(checkedCount ==0) {
// 		if(!confirm("更新全部数据?")) {
// 			return;
// 		}
		alert("没有选中记录");
		return;
	}
	var param = $("input").serialize();
	$.ajax({
		type: "post",
		url: "service/KcService.class.php?" + param,
		data: {"flag":"writeNote",
				"user":<?php echo GetCookie('userID')?>, 
				"writeNoteText":$("input[name=writeNoteText]").val(), 
				"writeASINText":$("input[name=writeASINText]").val(), 
				"isWriteASINText":$("input[name=isWriteASINText]:checked").val(), 
				"isWriteNoteText":$("input[name=isWriteNoteText]:checked").val()},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
			} else {
				alert("更新失败");
    		}
		}
	});
}
//更新状态
function writeState() {
	var noUpdateState = true;
	var param = $("input").serialize();
	$.each($("#writeTipDiv select"), function(index, item) {
		if($(item).val()!="") {
			noUpdateState = false;
			return false;
		}
	});
	if(noUpdateState) {
		alert("没有需要更新的状态");
		return;
	}
	var checkedCount = 0;
    $.each($("#table_border input:checkbox:gt(0)"), function(entryIndex, entry) {
    	if($(entry).prop("checked")) {
    		checkedCount ++;
    	}
    });
	if(checkedCount ==0) {
// 		if(!confirm("更新全部数据?")) {
// 			return;
// 		}
		alert("没有选中记录");
		return;
	}
	var flag = true;
	$.each($("[id^='writeState']"), function(index, item) {
		if($(item).val() =="0") {
			if(!confirm("清空状态" + (index+1) +"?")) {
				flag = false;
				return flag;
			}
		}
		if($(item).val() !="") {
    		param+="&"+$(item).serialize();
		}
	});
	if(!flag) {
		return;
	}
	$.ajax({
		type: "post",
		url: "service/KcService.class.php?" + param,
		data: {
				"flag":"writeState", 
				"user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
			} else {
				alert("更新失败");
    		}
		}
	});
}

function selectColumns() {
    $("#dialog").dialog("open");
}
//手动上货窗口
function show_buying() {
	var html="";
	$("#table_border tr:gt(0) input[type=checkbox]:checked").each(function(i, item){
		if($(item).parent().nextAll(":eq(9)").text() == "販売中止") {
			alert("["+$(item).parent().next().text() + "]贩卖终止 不能上货");
			return false;
		}
		if($(item).parent().nextAll(":eq(6)").text() == "6-6-6-6-6") {
			alert("["+$(item).parent().next().text() + "]请用主号上货");
			return false;
		}
		html+=""
			+"<tr>"
			+"    <td>" + $(item).parent().next().text() + "</td>"
			+"    <td>" + $(item).parent().next().next().text() + "</td>"
			+"    <td>" + $(item).parent().next().next().next().text() + "</td>"
			+"    <td><input type=\"hidden\" name=\"kid[]\" value=\"" + $(item).val().split("#")[1] + "\"/><input type=\"text\" name=\"col3[]\" value=\"" + Math.abs(Math.round($(item).parent().nextAll(":eq(20)").text())) + "\"/> </td>"
			+"</tr>";

	})
    $("#buyingTable tr:gt(0)").remove();
    $("#buyingTable").append(html);
    $("#dialog1").dialog("open");
	//恢复radio选中状态
	$("#buyingTable input:radio:eq(0)").prop("checked","true");
}
//手动上货提交
function update_buying() {
	var param = $("#buyingTable input").serialize();
	$.ajax({
		type: "post",
		url: "service/BuyingService.php?" + param,
		data: {
				"flag":"update_buying", 
				"VioomaUserID":"<?php echo GetCookie('VioomaUserID')?>",
				"sstate12":"<?php echo $_REQUEST["sstate12"]?>", 
				"user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data) +"条记录");
				$("#dialog1").dialog('close');
			} else {
				alert("更新失败");
    		}
		}
	});
}
//修改在库信息窗口
function show_dialog2() {
	var html="";
	$("#table_border tr:gt(0) input[type=checkbox]:checked").each(function(i, item){
		html+=""
			+"<tr>"
			+"    <td>" + $(item).parent().parent().children("td:eq(1)").text() + "</td>"
			+"    <td><input type=\"hidden\" name=\"kid[]\" value=\"" + $(item).val().split("#")[1] + "\"/><input type=\"text\" size=\"3\" value=\"" + $(item).parent().parent().children("td:eq(6)").find("input").val() + "\" name=\"lab_id[]\"/> </td>"
			+"    <td><input type=\"hidden\" name=\"cp_number[]\" value=\"" + $(item).parent().parent().children("td:eq(1)").text() + "\"/><input type=\"text\" value=\"" + $(item).parent().parent().children("td:eq(7)").text() + "\" name=\"position[]\"/> </td>"
			+"    <td><input type=\"text\" size=\"4\" value=\"" + $(item).parent().parent().children("td:eq(8)").text().split("￥")[1] + "\" name=\"cp_sale[]\"/> </td>"
			+"</tr>";

	})
    $("#editTable tr:gt(0)").remove();
    $("#editTable").append(html);
    $("#dialog2").dialog("open");
}
//提交在库信息
function update_lab() {
	var param = $("#editTable input").serialize();
	$.ajax({
		type: "post",
		url: "service/BuyingService.php?" + param,
		data: {
				"flag":"update_lab", 
				"user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功更新" + eval(data)/2 +"条记录");
				$("#dialog2").dialog('close');
			} else {
				alert("更新失败");
    		}
		}
	});
}
function initPage(pageIndex) {
	showLoading();
	//分页参数
	var pageCount = 20;
	var recordCount = 0;
	var url = "service/KcService.class.php?"+$("select").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&nocache="+new Date().getTime()+"&sstate12=<?php echo $_REQUEST["sstate12"]?>";
	var param = $("input").serialize();
	$.ajax({
		type: "post",
		url: url+"&"+param,
		data: {"flag":"init"},
		success: function(data){
//     		alert(data);
			data = eval("("+data+")");
    		var html="";
    		$.each(data.results, function(entryIndex, entry){
        		//         			alert(entry.id+"|"+entry.name);
    			html+="<tr>"
    			+"	<td><input type=\"checkbox\" value=\"" + entry.id + "#" + entry.kid + "\" name=\"strChk[]\">";
    			<?php if(!isset($_REQUEST["sstate12"])) {?>
    			if($("input[name=editA]").val() == "1") {
    				html+="	<a target=\"_blank\" href=\"system_kc_edit.php?id=" + entry.kid + "&lid=" + entry.l_id + "&n=" + entry.number + "&pid=" + entry.cp_number
    			    + "&floor=" + entry.l_floor + "&shelf=" + entry.l_shelf + "&zone=" + entry.l_zone + "&horizontal=" + entry.l_horizontal + "&vertical=" + entry.l_vertical + "\">修正</a>";
    			}
    			if($("input[name=deleteA]").val() == "1") {
    				html+="|<a onclick=\"submitChk(" + entry.kid + ",'" + entry.cp_number + "')\" href=\"#\">削除</a>";
    			}
    			<?php } ?>
    			html+="	</td><td><a target=\"_blank\" href=\"system_basic_refer.php?id=" + entry.cp_number + "\" style=\"text-decoration:underline\">" + entry.cp_number + "</a></td>"
    			+"  <td style=\"display:none\">" + entry.buying_number + "</td>"
    			+"  <td style=\"display:none\">" + entry.page_name_id + "</td>"
    			+"  <td title=\"" + entry.cp_name + "\">" + entry.cp_name + "</td>"
    			+"	<td>" + entry.cp_title + "</td>"
    			+"  <td><input type=\"hidden\" value=\"" + entry.l_id + "\"/><b>" + entry.l_name + "</b></td>"
    			+"  <td>" + entry.l_floor + "-" + entry.l_shelf + "-" + entry.l_zone + "-" + entry.l_horizontal + "-" + entry.l_vertical + "</td>"
    			+"	<td align=\"right\"><font color=\"blue\">￥" + entry.cp_sale1 + "</font></td>"
    			+"  <td align=\"right\"><font color=\"red\">" + entry.number + "</font></td>"
    			+"  <td>" + entry.s_name1 + "</td>"
    			+"  <td>" + entry.s_name2 + "</td>"
    			+"  <td>" + entry.s_name3 + "</td>"
    			+"  <td>" + entry.s_name4 + "</td>"
    			+"  <td>" + entry.s_name5 + "</td>"
    			+"  <td>" + entry.s_name6 + "</td>"
    			+"  <td>" + entry.s_name7 + "</td>"
    			+"  <td>" + entry.s_name8 + "</td>"
    			+"  <td>" + entry.s_name9 + "</td>"
    			+"  <td>" + entry.s_name10 + "</td>"
    			+"  <td>" + entry.s_name11 + "</td>"
    			+"  <td>" + entry.s_name12 + "</td>"
    			+"  <td title=\"" + entry.l_asin + "\">" + entry.l_asin + "</td>"
    			+"  <td title=\"" + entry.l_note + "\">" + entry.l_note + "</td>"
    			+"  <td>" + entry.dtime + "</td>"
    			+"  <td>" + entry.cp_dtime + "</td>"
    			+"  <td>" + entry.kid + "</td>"
    			+"</tr>";
    		});

    		$("#table_border tr:gt(0)").remove();
    		$("#table_border").append(html);
    	    //如果是从"チェック"跳转过来的, 需要将状态1-10隐藏, 只显示状态11-12
    	    if(getQueryString("target") == "check") {
    	    	$("#table_border th:gt(7):lt(10)").hide();
    	    	$("#table_border tr").find("td:gt(9):lt(10)").hide();
    	    } else {
    	    	$("#table_border th:gt(17):lt(2)").hide();
    	    	$("#table_border tr").find("td:gt(19):lt(2)").hide();
    	    }
    		
    		//如果是从"快速入库"跳转过来的,为tr绑定双击关闭事件
    	    if(getQueryString("target") == "blank") {
    	    	$("#table_border tr:gt(0)").dblclick(function(){
					window.opener.document.all.model.value = $(this).children("td:eq(4)").text();
					window.opener.document.all.modelDetail.value = $(this).children("td:eq(5)").text();
					window.close();
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
  	  	  	//隐藏遮照
    		hideLoading();
		}
	});  
}
function initPageCallback(page_id, jq) {
	initPage(page_id);
}
function initMyBulletin() {
	$.ajax({
		type: "post",
		url: "service/BulletinService.class.php",
		data: {"flag":"initMine", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			if(data.length > 8 && inteval == undefined) {
				data = $.parseJSON(data);
				for(var key in data) {
					if(data[key].is_public ==2) {
						alert("紧急提示:【"+data[key].subject+"】");
    				}
				}
				inteval = setInterval("newMsgCount()",500); //0.5秒之后调用一次
    		} else if(data.length <= 8) {
    			$("#news").hide();
        		clearInterval(inteval);
        		inteval = undefined;
    		}
		}
	});  
}
//获取子分类
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
function newMsgCount() {
	if(flag){
		flag=false;
		$("#news").hide();
	}else{
		flag=true;
		$("#news").show();
	}
}
function sort(param, sortid) {
	if(sortid <= 10 || sortid>=36) {
    	if($("#sort").val() == sortid) {
    		sortid=sortid+1;
    	} else {
    		sortid=sortid;
    	}
	} else {
    	if($("#sort").val() == (sortid-11)*2+12) {
    		sortid=(sortid-11)*2+11;
    // 		$(param).next().toggle();
    // 		$(param).toggle();
    	} else {
    		sortid=(sortid-11)*2+12;
    // 		$(param).prev().toggle();
    // 		$(param).toggle();
    	}
	}
	$("#sort").val(sortid);
	pagenow = $(".current").html()-1;
	if($(".current").length > 1) {
    	pagenow = $($(".current")[1]).html()-1;
	}
	initPage(pagenow);
}
//导出excel
function out_excel(shop, btnflag){
	var param = $("input").serialize();
	var url = "service/KcService.class.php?"+$("select").serialize()+"&nocache="+new Date().getTime()+"&"+param+"&flag=out_excel&shop="+shop+"&btnflag="+btnflag;
    //window.open('excel_kc.php?shop='+shop+'&cp_categories='+cp+'&cp_categories_down='+cp_down+'&sort='+s+'&stext='+st,'','');
    window.open(url);
}

function submitChk(kid,pid) {
    var flag = confirm ( "削除したら復元できないので、本当に削除しますか。");
    if (flag) {
        location.href = "system_kc_del.php?action=del&id="+kid+"&pid="+pid;
    }
    return flag;
}

function chkAll(param) {
    if ($(param).prop("checked")) {
        $.each($("#table_border input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", true);
        });
    } else {
        $.each($("#table_border input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", false);
        });
    }
}
</script>
<style type="text/css">
#news{
	font-size: 16px;
	color: red;
    font-weight: bold;
}
#table_border{
	table-layout: fixed;
}
#table_border tr:hover{
	background-color: #EBF1F6;
}
#table_border tr:hover td{
	white-space: normal;
	overflow: unset;
	text-overflow: unset;
	overflow-wrap: break-word;
}
#table_border th{
	white-space: normal;
	overflow: unset;
	text-overflow: unset;
}
#table_border tr td{
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
}
</style>
</head>
<body>
<!-- 定义部分常量 -->
<input type="hidden" name="editA"/>
<input type="hidden" name="deleteA"/>
    <table width="100%" border="0" cellspacing="0">
        <tr style="height:30px;" bgcolor="#FFFFFF">
            <td style="width:120px;"><a id="news" style="display: none;" href="system/system_bulletin.php">✉【新訊息】</a> </td>
	        <td style="text-align:right;font-weight:bold;" id="total">
	 		</td>
	    </tr>
        <tr style="height:30px;">
    		<td>
    	        <strong>&nbsp;<?php if(!isset($_REQUEST["sstate12"])) {?>在庫一覧 <?php } else {?> 自動仕入れ表 <?php }?></strong>
    	    </td>
	        <td align="right">
    	        <div id="searchDiv">
                                                    出庫日:
                    <input type="text" name="sdate_out" id="sdate_out" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                    <input type="text" name="edate_out" id="edate_out" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
                    &nbsp;
    	                                登錄日:
                    <input type="text" name="sdate" id="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                    <input type="text" name="edate" id="edate" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
                    &nbsp;
        		    <input name="sort" id="sort" type="hidden" value="1"/>
        		          倉庫：
                    <select name="labid" id="labid">
                        <option value=''>全部倉庫</option>
                    </select>
                                               分類：
                    <select name="cp_categories" id="cp_categories" onchange="getCategoryDown(this.value)">
                    </select>->
                    <select name="cp_categories_down" id="cp_categories_down">
                        <option value=''>小分類選択</option>
                    </select>
                   关键字：
                    <select name="searchType">
                        <option value="">全部</option>
                        <option value="cp_number">商品コード</option>
                        <option value="cp_tm">バーコード</option>
                        <option value="cp_parent">親子関連</option>
                        <option value="cp_name">メーカ</option>
                        <option value="cp_title">タイトル</option>
                        <option value="cp_detail">仕様</option>
                        <option value="cp_gg">商品説明</option>
                        <option value="cp_bullet_1">箇条書き１</option>
                        <option value="cp_bullet_2">箇条書き２</option>
                        <option value="cp_bullet_3">箇条書き３</option>
                        <option value="cp_bullet_4">箇条書き４</option>
                        <option value="cp_bullet_5">箇条書き５</option>
                        <option value="cp_bullet_6">箇条書き６</option>
                        <option value="cp_categories">商品分類</option>
                        <option value="cp_categories_down">単位(個)</option>
                        <option value="cp_dwname">商品タイプ</option>
                        <option value="cp_jj">仕入単価</option>
                        <option value="cp_sale"></option>
                        <option value="cp_saleall">メーカー希望卸売価格</option>
                        <option value="cp_sale1">販売価格</option>
                        <option value="cp_sdate">生産日付</option>
                        <option value="cp_edate">廃棄日付</option>
                        <option value="cp_gys">仕入先</option>
                        <option value="cp_url">メインURL</option>
                        <option value="cp_url_1">サブURL1</option>
                        <option value="cp_url_2">サブURL2</option>
                        <option value="cp_url_3">サブURL3</option>
                        <option value="cp_url_4">サブURL4</option>
                        <option value="cp_browse_node_1">推奨ブラウズノード1</option>
                        <option value="cp_browse_node_2">推奨ブラウズノード2</option>
                        <option value="cp_helpword">キーワード1</option>
                        <option value="cp_helpword_1">キーワード2</option>
                        <option value="cp_helpword_2">キーワード3</option>
                        <option value="cp_helpword_3">キーワード4</option>
                        <option value="cp_helpword_4">キーワード5</option>
                        <option value="cp_helpword_5">キーワード6</option>
                        <option value="cp_helpword_6">キーワード7</option>
                        <option value="cp_helpword_7">キーワード8</option>
                        <option value="cp_helpword_8">キーワード9</option>
                        <option value="cp_helpword_9">キーワード10</option>
                        <option value="cp_bz">備考</option>
            		</select>
                    <input type="text" name="stext" size="15"/>
                    <select name="textRelate">
                        <option value="or">或</option>
                        <option value="and">且</option>
                    </select><br/>
                                               状態1：
                    <select name="sstate1" id="state1id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態2：
                    <select name="sstate2" id="state2id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態3：
                    <select name="sstate3" id="state3id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態4：
                    <select name="sstate4" id="state4id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態5：
                    <select name="sstate5" id="state5id">
                        <option value=''>全部状態</option>
                    </select>
                    <!--                            状態6：
                    <select name="sstate6" id="state1id">
                        <option value=''>全部状態</option>
                    </select>-->
                                               状態7：
                    <select name="sstate7" id="state7id">
                        <option value=''>全部状態</option>
                    </select>
           <!--         状態8：
                    <select name="sstate8" id="state3id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態9：
                    <select name="sstate9" id="state4id">
                        <option value=''>全部状態</option>
                    </select>
                                               状態10：
                    <select name="sstate10" id="state5id">
                        <option value=''>全部状態</option>
                    </select> --><br/>
                                              在庫数
                    <select name="compare">
                        <option value="gt">&gt;</option>
        		        <option value="eq">=</option>
        		        <option value="lt">&lt;</option>
            		</select>
                    <input type="text" name="num" size="3"/>
                    <input type="button" id="subbtn" value="検索"/>
                </div>
            </td>
        </tr>
        <tr>
            <td><button style="display: none;" id="showBuyingButton" onclick="show_buying();">手打ち仕入れ</button></td>
            <td><button style="display: none;" id="showDialog2Button" onclick="show_dialog2();">状态修正</button></td>
        </tr>
        <tr>
			<td bgcolor="#FFFFFF" colspan="2">
                <table width="100%" cellspacing="0" cellpadding="0" border="1" id="table_border">
                    <tr>
                        <th width="3%">
                            <input type="checkbox" onclick="chkAll(this)"/>選択&nbsp;&nbsp;
                        </th>
                        <th width="3%">
                            <a href="javascript:void(0);" onclick="sort(this, 1);">商品コード</a>
                        </th>
	                    <th width="7%">メーカ・商品名</th>
                	    <th width="20%">タイトル</th>
                	    <th width="2%">倉庫</th>
                	    <th width="7%">
                            <a href="javascript:void(0);" onclick="sort(this, 5);">在庫位置</a>
                            <br/>階-棚-ゾーン-横-縦</th>
                        <th width="3%">
                            <a href="javascript:void(0);" onclick="sort(this, 7);">販売価格</a>
                        </th>
                        <th width="3%">
                            <a href="javascript:void(0);" onclick="sort(this, 3);">在庫数</a>
                        </th>
                        <th width="3%">状1</th>
                        <th width="3%">状2</th>
                        <th width="3%">状3</th>
                        <th width="3%">状4</th>
                        <th width="3%">状5</th>
                        <th width="3%">状6</th>
                        <th width="3%">状7</th>
                        <th width="3%">状8</th>
                        <th width="3%">状9</th>
                        <th width="3%">状10</th>
                        <th width="3%">状11</th>
                        <th width="3%">状12</th>
                        <th width="3%">
                            <a href="javascript:void(0);" onclick="sort(this, 9);">ASIN</a>
                        </th>
                        <th width="3%">笔记</th>
                        <th width="3%">商品出庫日</th>
                        <th width="3%">商品登錄日</th>
	                    <th width="3%">KID</th>
	               </tr>
               </table>
           </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <div id="writeTipDiv" style="text-align: left; position: absolute;">
                    <span>
                        <input style="display: none;" type="button" id="rakuten" onclick="out_excel('rakuten')" value="楽天データ出力" />
                        <input style="display: none;" type="button" id="amazon" onclick="out_excel('amazon')" value="Amazonデータ出力" />
                     <!--   <input type="button" onclick="out_excel('b2c')" value="B2Cデータ出力" />-->
                        <input style="display: none;" type="button" id="zone" onclick="out_excel('zone')" value="在庫位置データ出力" />
                        <input style="display: none;" type="button" id="export" onclick="selectColumns()" value="Templateデータ出力" />
                        <span id="writeNoteText" style="display: none;">
                            <input type="checkbox" name="isWriteNoteText" value="1"/>
                            <input type="text" name="writeNoteText" placeholder="笔记"/>
                        </span>
                        <span id="writeASINText" style="display: none;">
                            <input type="checkbox" name="isWriteASINText" value="1"/>
                            <input type="text" name="writeASINText" placeholder="ASIN"/>
                        </span>
                        <input style="display: none;" type="button" id="writeNote" onclick="writeNote()" value="書き込む" />
                        <br/>
                        <select id="writeState1Select" name="writeState1Select" style="display: none;">
                            <option value=''>状態1</option>
                        </select>
                        <select id="writeState2Select" name="writeState2Select" style="display: none;">
                            <option value=''>状態2</option>
                        </select>
                        <select id="writeState3Select" name="writeState3Select" style="display: none;">
                            <option value=''>状態3</option>
                        </select>
                        <select id="writeState4Select" name="writeState4Select" style="display: none;">
                            <option value=''>状態4</option>
                        </select>
                        <select id="writeState5Select" name="writeState5Select" style="display: none;">
                            <option value=''>状態5</option>
                        </select>
                        <select id="writeState6Select" name="writeState6Select" style="display: none;">
                            <option value=''>状態6</option>
                        </select>
                        <select id="writeState7Select" name="writeState7Select" style="display: none;">
                            <option value=''>状態7</option>
                        </select>
                        <select id="writeState8Select" name="writeState8Select" style="display: none;">
                            <option value=''>状態8</option>
                        </select>
                        <select id="writeState9Select" name="writeState9Select" style="display: none;">
                            <option value=''>状態9</option>
                        </select>
                        <select id="writeState10Select" name="writeState10Select" style="display: none;">
                            <option value=''>状態10</option>
                        </select>
                        <select id="writeState11Select" name="writeState11Select" style="display: none;">
                            <option value=''>状態11</option>
                        </select>
                        <input style="display: none;" id="writeState" type="button" onclick="writeState()" value="更新状態" />
                    </span><br />
                    <span>
                        <input style="display: none;" type="button" id="rakuten1" onclick="out_excel('rakuten', 1)" value="楽天データ出力>0" />
                        <input style="display: none;" type="button" id="amazon1" onclick="out_excel('amazon', 1)" value="Amazonデータ出力>0" />
                     <!--   <input type="button" onclick="out_excel('b2c')" value="B2Cデータ出力" />-->
                        <input style="display: none;" type="button" id="zone1" onclick="out_excel('zone', 1)" value="在庫位置データ出力>0" />
                    </span>
                </div>
                <div id="Pagination" ></div><div id="totalPage"></div>
            </td>
        </tr>
    </table>
    <div id="dialog" title="选择要导出的列...">
        <table >
            <tr>
                <td colspan="5"><label><input type="checkbox" value="0" onchange="chkAllColumn(this);"/>全选</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="0"/>コントロール</label></td>
                <td><label><input type="checkbox" name="columns[]" value="1"/>商品コード</label></td>
                <td><label><input type="checkbox" name="columns[]" value="2"/>バーコード</label></td>
                <td><label><input type="checkbox" name="columns[]" value="3"/>親子関連</label></td>
                <td><label><input type="checkbox" name="columns[]" value="4"/>メーカ</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="5"/>タイトル</label></td>
                <td><label><input type="checkbox" name="columns[]" value="6"/>仕様</label></td>
                <td><label><input type="checkbox" name="columns[]" value="7"/>商品説明</label></td>
                <td><label><input type="checkbox" name="columns[]" value="8"/>箇条書き1</label></td>
                <td><label><input type="checkbox" name="columns[]" value="9"/>箇条書き2</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="10"/>箇条書き3</label></td>
                <td><label><input type="checkbox" name="columns[]" value="11"/>箇条書き4</label></td>
                <td><label><input type="checkbox" name="columns[]" value="12"/>箇条書き5</label></td>
                <td><label><input type="checkbox" name="columns[]" value="13"/>箇条書き6</label></td>
                <td><label><input type="checkbox" name="columns[]" value="14"/>商品分類</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="15"/>単位</label></td>
                <td><label><input type="checkbox" name="columns[]" value="16"/>商品タイプ</label></td>
                <td id="price_in" style="display:none;"><label><input type="checkbox" name="columns[]" value="17"/>仕入単価</label></td>
                <td><label><input type="checkbox" name="columns[]" value="18"/>メーカー希望小売価格</label></td>
                <td><label><input type="checkbox" name="columns[]" value="19"/>メーカー希望卸売価格</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="20"/>販売価格</label></td>
                <td><label><input type="checkbox" name="columns[]" value="21"/>生産日付</label></td>
                <td><label><input type="checkbox" name="columns[]" value="22"/>廃棄日付</label></td>
                <td><label><input type="checkbox" name="columns[]" value="23"/>仕入先</label></td>
                <td><label><input type="checkbox" name="columns[]" value="24"/>メインURL</label></td>
            </tr> 
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="25"/>サブURL1</label></td>
                <td><label><input type="checkbox" name="columns[]" value="26"/>サブURL2</label></td>
                <td><label><input type="checkbox" name="columns[]" value="27"/>サブURL3</label></td>
                <td><label><input type="checkbox" name="columns[]" value="28"/>サブURL4</label></td>
                <td><label><input type="checkbox" name="columns[]" value="29"/>推奨ブラウズノード1</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="30"/>推奨ブラウズノード2</label></td>
                <td><label><input type="checkbox" name="columns[]" value="31"/>キーワード1</label></td>
                <td><label><input type="checkbox" name="columns[]" value="32"/>キーワード2</label></td>
                <td><label><input type="checkbox" name="columns[]" value="33"/>キーワード3</label></td>
                <td><label><input type="checkbox" name="columns[]" value="34"/>キーワード4</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="35"/>キーワード5</label></td>
                <td><label><input type="checkbox" name="columns[]" value="36"/>キーワード6</label></td>
                <td><label><input type="checkbox" name="columns[]" value="37"/>キーワード7</label></td>
                <td><label><input type="checkbox" name="columns[]" value="38"/>キーワード8</label></td>
                <td><label><input type="checkbox" name="columns[]" value="39"/>キーワード9</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="40"/>キーワード10</label></td>
                <td><label><input type="checkbox" name="columns[]" value="41"/>mainkc ID</label></td>
                <td><label><input type="checkbox" name="columns[]" value="42"/>倉庫号</label></td>
                <td><label><input type="checkbox" name="columns[]" value="43"/>在庫位置</label></td>
                <td><label><input type="checkbox" name="columns[]" value="44"/>在庫数</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="45"/>状態1</label></td>
                <td><label><input type="checkbox" name="columns[]" value="46"/>状態2</label></td>
                <td><label><input type="checkbox" name="columns[]" value="47"/>状態3</label></td>
                <td><label><input type="checkbox" name="columns[]" value="48"/>状態4</label></td>
                <td><label><input type="checkbox" name="columns[]" value="49"/>状態5</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="50"/>状態6</label></td>
                <td><label><input type="checkbox" name="columns[]" value="51"/>状態7</label></td>
                <td><label><input type="checkbox" name="columns[]" value="52"/>状態8</label></td>
                <td><label><input type="checkbox" name="columns[]" value="53"/>状態9</label></td>
                <td><label><input type="checkbox" name="columns[]" value="54"/>状態10</label></td>
            </tr>
            <tr>
                <td><label><input type="checkbox" name="columns[]" value="55"/>笔记</label></td>
                <td><label><input type="checkbox" name="columns[]" value="56"/>備考</label></td>
                <td><label><input type="checkbox" name="columns[]" value="57"/>ASIN</label></td>
            </tr>
        </table>
      <button onclick="out_excel('export')">导出</button>
    </div>
    <div id="dialog1" title="仕入れ">
        <table border="1" id="buyingTable">
            <tr>
                <td>商品コード</td>
                <td>仕入れ表合计数</td>
                <td>仕入れ表序号</td>
                <td>仕入れ表(<label><input type="radio" name="pageNameId" value="0" checked />0</label>
						<label><input type="radio" name="pageNameId" value="1" />1</label>
						<label><input type="radio" name="pageNameId" value="2" />2</label>
						<label><input type="radio" name="pageNameId" value="3" />3</label>
				)</td>
            </tr>
        </table>
      <button onclick="update_buying()">提交</button>
    </div>
    <div id="dialog2" title="状态修正">
        <table border="1" id="editTable">
            <tr>
                <td>商品コード</td>
                <td>倉庫</td>
                <td>在庫位置</td>
                <td>販売価額</td>
            </tr>
        </table>
      <button onclick="update_lab()">提交</button>
    </div>
</body>     
</html>

