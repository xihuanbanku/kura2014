<?php
require (dirname(__FILE__) . "/../include/config_base.php");
require (dirname(__FILE__) . "/../include/config_rglobals.php");
require (dirname(__FILE__) . "/../include/page.php");
require_once (dirname(__FILE__) . "/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg_softname;?>会社掲示版</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/loading.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css"  href="../style/jquery-ui.css"/>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<script type="text/javascript" src="../js/md5.min.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<script language="javascript">
var url = "../service/BulletinService.class.php";
Date.prototype.format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "H+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}

$(function(){
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
function initPage(pageIndex) {
	showLoading();
	//分页参数
	var pageCount = 20;
	var recordCount = 0;
	var url = "../service/BulletinService.class.php?pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&is_public=1&nocache="+new Date().getTime();;
	var param = $("input").serialize();
	$.ajax({
		type: "post",
		url: url+"&"+param,
		data: {"flag":"init"},
		success: function(data){
//     		alert(data);
			data = eval("("+data+")");
			if(data.totalcount == 0) {
	    		hideLoading();
				return;
			}
    		var html="";
    		$.each(data.results, function(entryIndex, entry){
        		//         			alert(entry.id+"|"+entry.name);
    			html+="<tr bgcolor=\"#FFFFFF\" onmouseout=\"javascript:this.bgColor='#FFFFFF';\" onmousemove=\"javascript:this.bgColor='#EBF1F6';\">"
    			+"	<td><a href=\"javascript:void(0);\" onclick=\"chgState(" + entry.id + ", "+entry.flag+", "+entry.receiver_id+", this)\">修改状态</a>"
    			+"|<a href=\"javascript:void(0);\" onclick=\"chgState(" + entry.id + ", 2, "+entry.receiver_id+", this)\">削除</a></td>";
    			if(entry.flag == 0) {
    				html+="  <td align=\"center\"><font color=\"red\">未完成</font></td>"
    			} else {
    				html+="  <td align=\"center\">已完成</font></td>"
    			}
    			html+="  <td><a href=\"bulletin_content.php?id=" + entry.id + "&is_public=" + entry.passwd + "\"><b>" + entry.subject + "</b></a></td>"
    			
    			+"  <td>" + entry.sender + "</td>"
//     			+"	<td>" + entry.receiver + "</td>"
    			+"  <td>" + entry.dtime + "</td>"
    			+"</tr>";
    		});
    		$("#table_border tr:gt(0)").remove();
    		$("#table_border").append(html);

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
function openContent(param, passwd) {
	if(passwd >0) {
		$("#dialog").dialog({title:"请输入密码"});
		$("#dialog p").html("<input type='password' id='checkPwd'/> <input type='button' onclick='validatePwd("+param + ")' value='确认'/>");
        $("#dialog").dialog("open");
        $("#dialog a").hide();
        $("#reply").hide();
	} else {
		validatePwd(param);
	}
}
function validatePwd(param) {
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"getContent", "id":param, checkPwd:$('#checkPwd').val()},
		success: function(msg){
			if(msg.trim() == "null") {
				alert("密码错误");
			} else {
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
    		}
		}
	});
}
function chgState(param, state, receiver_id, obj) {
	if(confirm("确定?")) {
    	if($("#is_public").val() == 0 && receiver_id != <?php echo $_COOKIE["userID"];?>) {
    		alert("您无权操作该消息.");
    		return;
    	}
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
function showReply(){
	if($("#reply").css("display") == "none") {
		$("#reply").show(500);
	} else {
		$("#reply").hide(500);
	}
}
function replySubmit(){
	if($("#is_public").val() == 0 && $("#receiver_id").val() != <?php echo $_COOKIE["userID"];?>) {
		alert("您无权操作该消息.");
		return;
	}
	var replyContent= $("#replyContent").val();
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"replySubmit", 
			"id":$("#bulletin_id").val(), 
			"replyContent": replyContent, 
			"sender": $("#sender_id").val(), 
			"receiver": $("#receiver_id").val(), 
			"receiver_name": '<?php echo $_COOKIE["VioomaUserID"];?>', 
			"userID":<?php echo $_COOKIE["userID"];?>},
		success: function(msg){
			if(msg > 0) {
				$("#dialog p").append("<br/><span> " + <?php echo "'".$_COOKIE["VioomaUserID"]."'";?> + "</span> 在 <span>"+ new Date().format("yyyy-MM-dd HH:mm:ss") + "</span> 时写道:<br/>"+replyContent );
				$("#replyContent").val("")
				alert("回复成功");
			} else {
				alert("您无权操作该消息.");
			}
		}
	});  
}
</script>
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
</head>
<body>
    <table width="100%" border="0" cellspacing="0">
        <tr style="height:30px;">
    		<td>
    	        <strong>公告一覧</strong>
    	    </td>
	        <td align="right">
    	        <div id="searchDiv">
    	                                创建时间:
                    <input type="text" name="sdate" id="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                    <input type="text" name="edate" id="edate" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
                                               关键字：
                    <input type="text" name="stext" size="15"/>
                    <input type="button" id="subbtn" value="検索"/>
                </div>
            </td>
        </tr>
        <tr>
			<td bgcolor="#FFFFFF" colspan="2">
                <table width="100%" cellspacing="0" cellpadding="0" border="1" id="table_border">
                    <tr class="row_color_head">
                        <td>操作</td>
                            <!-- <input type="checkbox" onclick="chkAll(this)"/>選択 -->
                        <td>状态</td>
                	    <td>主题</td>
	                    <td>创建者</td>
<!--                 	    <td>收件人</td> -->
                        <td>创建时间</td>
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
</body>     
</html>

