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
<link rel="stylesheet" type="text/css" href="../style/pager.css" />
<link rel="stylesheet" type="text/css" href="../style/main.css" />
<link rel="stylesheet" type="text/css" href="../style/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="../style/loading.css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<style type="text/css">
#dialog p span{
	color: red;
	font-weight: bold;
}
#dialog a{
	color: blue;
	font-weight: bold;
}
</style>
<title>统筹进度管理一览</title>
<script language="javascript">
var url = "../service/PlanService.class.php";
$(function(){
	$.ajax({
		type: "post",
		url: "../service/BulletinService.class.php",
		data: {"flag":"initReceiver"},
		success: function(data){
			data = eval("("+data+")");
    		var html="";
    		$.each(data, function(entryIndex, entry){
    			html+="<label><input type=\"checkbox\" value=\"" + entry.id + "\" name=\"strChk[]\">" + entry.s_name + "</label></input>";
    		});
    		$("#receiver").append(html);
    		$("#receiver_filter").append(html);
		}
	});
    $("#subbtn").click(function() {
    	initPage(0);
    });
    $("#searchDiv").bind("keydown",function(e){
        // 兼容FF和IE和Opera    
        var theEvent = e || window.event;    
        var code = theEvent.keyCode || theEvent.which || theEvent.charCode;    
        if (code == 13) {    
            //回车提交
            $("#subbtn").click();
        }
    });
    $( "#dialog" ).dialog({
        autoOpen: false,
        width:600,
        height:300,
        show: {
          effect: "fold",
          direction : "down",
          duration: 500
        },
        hide: {
          effect: "fold",
          duration: 500
        }
      });
    initPage(0);
});
//人员名称全选
function chkAll(param) {
    if ($(param).prop("checked")) {
    	$(param).parents("td").find("input:checkbox").prop("checked", true);
    } else {
    	$(param).parents("td").find("input:checkbox").prop("checked", false);
    }
}
//提交表单
function checkForm(){
	if($("input[name='subject']").val() == ""){
		alert("标题未填写");
		return;
	}
	$.ajax({
		type: "post",
		url: url+"?"+$("form").serialize(),
		data: {"flag":"sendPlan"},
		success: function(data){
			if(data >= 1) {
				$("#success").fadeIn(1500);
				$("#success").fadeOut(500);
				$("form")[0].reset()
				initPage(0);
			} else {
				alert(data);
			}
		}
	});
}
function initPage(pageIndex) {
	showLoading();
	//分页参数
	var pageCount = 20;
	var recordCount = 0;
	var param = $("#searchDiv input").serialize();
	$.ajax({
		type: "post",
		url: url+"?pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&nocache="+new Date().getTime()+"&"+param+"&"+$("select").serialize(),
		data: {"flag":"init"},
		success: function(data){
//     		alert(data);
			data = eval("("+data+")");
    		var html="";
			if(data.totalcount != 0) {
        		$.each(data.results, function(entryIndex, entry){
            		//         			alert(entry.id+"|"+entry.name);
        			html+="<tr bgcolor=\"#FFFFFF\" onmouseout=\"javascript:this.bgColor='#FFFFFF';\" onmousemove=\"javascript:this.bgColor='#EBF1F6';\">"
        			+"	<td><a href=\"javascript:void(0);\" onclick=\"chgState(" + entry.id + ", "+entry.flag+", this)\">修改状态</a>"
        			+"|<a href=\"javascript:void(0);\" onclick=\"chgState(" + entry.id + ", 2, this)\">削除</a></td>";
        			if(entry.flag == 0) {
        				html+="  <td align=\"center\"><font color=\"red\">未完成</font></td>"
        			} else {
        				html+="  <td align=\"center\">已完成</font></td>"
        			}
        			html+="  <td><a href=\"javascript:void(0);\" onclick=\"openContent(" + entry.id + ")\"><b>" + entry.subject + "</b></a></td>"
        			
        			+"  <td>" + entry.s_name + "</td>"
        			+"  <td>" + entry.dtime + "</td>"
        			+"</tr>";
        		});
			}
    		$("#contentTable tr:gt(0)").remove();
    		$("#contentTable").append(html);

    		recordCount = data.totalcount;
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
    		hideLoading();
		}
	});  
}
function initPageCallback(page_id, jq) {
	initPage(page_id);
}
//显示回复
function showReply(){
	if($("#reply").css("display") == "none") {
		$("#reply").show(500);
	} else {
		$("#reply").hide(500);
	}
}
//提交回复
function replySubmit(){
	var replyContent= $("#replyContent").val();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"replySubmit", 
			"id":$("#bulletin_id").val(), 
			"replyContent": replyContent, 
			"receiver_name": '<?php echo $_COOKIE["VioomaUserID"];?>', 
			"userID":<?php echo $_COOKIE["userID"];?>},
		success: function(msg){
			if(msg > 0) {
				$("#dialog p").append("<br/><span> " + <?php echo "'".$_COOKIE["VioomaUserID"]."'";?> + "</span> 在 <span>"+ new Date().format("yyyy-MM-dd HH:mm:ss") + "</span> 时写道:<br/>"+replyContent );
				$("#replyContent").val("")
				alert("回复成功");
			} else {
				alert("失败.");
			}
		}
	});  
}
//更改状态
function chgState(param, state, obj) {
	if(confirm("确定?")) {
    	$.ajax({
    		type: "post",
    		url: url,
    		data: {"flag":"chgState", "id":param, "state": state, "userID":<?php echo $_COOKIE["userID"];?>},
    		success: function(msg){
    			if(msg > 0) {
    				switch(state) {
    				case 0:
    				case 1:
    					 if($(obj).parent().next().text() == "未完成") {
    						 $(obj).parent().next().html("已完成");
    					 } else {
    						 $(obj).parent().next().html("<font color=\"red\">未完成</font>");
    					 }
    					 break;
    				case 2:
    					 $(obj).parent().parent().hide(500);
    					 break;
    				}
    			} else {
    				alert("您无权操作该消息.");
    			}
    		}
    	});  
	}
}
//打开详情
function openContent(param, passwd) {
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"getContent", "id":param, checkPwd:$('#checkPwd').val()},
		success: function(msg){
			msg = eval("("+msg+")");
			$("#dialog").dialog({title:msg.subject});
			$("#dialog p").html(msg.content.replace(/\n/g,'<br/>'));
	        $("#dialog").dialog("open");
	        $("#dialog a").show();
	        $("#is_public").val(msg.is_public);
	        $("#receiver_id").val(msg.receiver);
	        $("#sender_id").val(msg.sender);
	        $("#receiver_name").val(msg.receiver_name);
	        $("#sender_name").val(msg.sender_name);
	        $("#bulletin_id").val(param);
// 	        $("#remind_me").val(msg.remind_me);
		}
	});
}
//排序
function orderBy(obj) {
	if($("#searchDiv input[name=\"orderBy\"]").val() == obj) {
		$("#searchDiv input[name=\"orderBy\"]").val(obj+1);
	} else {
		$("#searchDiv input[name=\"orderBy\"]").val(obj);
	}
	initPage(0);
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
		<td>
		<form>
			<table width="100%" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td><strong>统筹进度管理一览</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                	        <tr>
            				    <td id="success" class="cellcolor" style="text-align: center; display: none;" colspan="2">提交成功</td>
            				</tr>
                			<tr id="reciverTr">
                				<td class="cellcolor" width="30%">收件人：</td>
                                 <td id="receiver">
                                  <label><input type="checkbox" onclick="chkAll(this)"/>全選</label><br/>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">标题：</td>
                                 <td>
                                    <input name="subject" size="100"/>
                        		</td>
                			</tr>
                			<tr>
                				<td class="cellcolor" width="30%">内容：</td>
                				<td>
                                    <textarea rows="10" cols="80" name="content"></textarea>
                				</td>
                			</tr>
                			
                			<tr>
                				<td class="cellcolor">&nbsp;</td>
                				<td><input type="button" value="提交" onclick="checkForm()"/>
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
	    <table width="100%" border="0" cellspacing="0">
        <tr style="height:30px;">
	        <td align="right">
    	        <div id="searchDiv">
					<input type="hidden" name="orderBy" value="" />
					<input type="hidden" name="is_admin" value="1" />
        	        <div id="receiver_filter">
        	               提交者：<label><input type="checkbox" onclick="chkAll(this)"/>全選</label>
        	        </div>
    	                                发送时间:
                    <input type="text" name="sdate" id="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                    <input type="text" name="edate" id="edate" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
                                               关键字：
                    <input type="text" name="stext" size="15"/>
                    <input type="button" id="subbtn" value="検索"/>
                </div>
            </td>
        </tr>
        <tr>
			<td bgcolor="#FFFFFF">
                <table width="100%" cellspacing="0" cellpadding="0" border="1" id="contentTable">
                    <tr class="row_color_head">
                        <td>操作</td>
                            <!-- <input type="checkbox" onclick="chkAll(this)"/>選択 -->
                        <td><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(1)">状态</a></td>
                	    <td>主题</td>
                	    <td>收件人</td>
                        <td><a style="font-size: 15px;" href="javascript:void(0);" onclick="orderBy(3)">发送时间</a></td>
	               </tr>
               </table>
           </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right;">
                <div id="Pagination" ></div><div id="totalPage"></div>
            </td>
        </tr>
    </table>
    <div id="dialog" title="a">
      <!-- アラーム:<input type="text" id="remind_me"  class="Wdate" onclick="WdatePicker()"/><button onclick="chgRemindMe()">确定</button> -->
      <p></p>
      <input type="hidden" id="is_public"/>
      <input type="hidden" id="receiver_id"/>
      <input type="hidden" id="sender_id"/>
      <input type="hidden" id="bulletin_id"/>
      <input type="hidden" id="receiver_name" value="<?php echo $_COOKIE["VioomaUserID"];?>"/>
      <input type="hidden" id="sender_name"/>
      <a href="javascript:void(0);" onclick="showReply();">reply</a>
      <div id="reply" style="display: none;">
          <textarea id="replyContent" rows="5" cols="60"></textarea><br/>
          <button onclick="replySubmit()">回复</button> 
      </div>
    </div>
		<?php 
copyright();
?>
</body>
</html>