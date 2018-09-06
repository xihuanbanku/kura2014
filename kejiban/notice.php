<?php
require (dirname(__FILE__)."/../include/config.php");
require (dirname(__FILE__)."/../include/config_base.php");
require (dirname(__FILE__)."/../include/config_rglobals.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/loading.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<script type="text/javascript" src="../js/jquery-ui.js"></script>
<title>NOTICE</title>
<script type="text/javascript">
var url = "../service/NoticeService.class.php";
$(function(){
	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"160", "user":<?php echo GetCookie('userID')?>},
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
	//textarea 使用ctrl+enter提交
    $("#replyContent").ctrlEnter("#replySubmit", function () {
        var replyContent= $("#replyContent").val();
    	var replyTitle= $("#replyTitle").val();
    	if($.trim(replyTitle) == "" || $.trim(replyContent) == "") {
			alert("请输入标题和内容");
			return false;
	   	}
	   	$("#form2").submit();
 	});
    initPage();
});
function initPage() {
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initPage", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			data = eval("("+data+")");
			if(data.results!= null) {
        		var html="";
        		var attachFile="";
				$.each(data.results, function(i, item){
    			    //alert($(item));
    			    if(item.filename != null && item.filename != "") {
    			    	attachFile="<a href='../upload/" + item.filename + "'>下载附件</a>";
    			    } else {
    			    	attachFile="";
    			    }
					html+="<div data-nid=" + item.id + ">"
                		+"    [<a href='javascript:void(0);'>X</a>]<span class='title'>" + item.title + "</span><input name='title' type='text' style='display: none;' value='" + item.title + "' />" + attachFile + "<br />"
                		+"    <span class='content'>" + item.content.replace(/\n/g,"<br />") + "</span><textarea rows='10' cols='100' name='content' style='display: none;' >" + item.content + "</textarea>"
                		+"</div>"
                		+"<hr />";
    		    })
            	$("#note_td").html(html);

            	$("#note_td div a").each(function(i, item){
    			    //alert($(item));
    		        $(item).click(function(){
    		        	if(confirm("删除?")) {
        		        	$.ajax({
        		        		type: "post",
        		        		url: url+"?" + $(item).serialize(),
        		        		data: {"flag":"delete", "id":$(item).parent().attr("data-nid"), "userID":<?php echo GetCookie('userID')?>},
        		        		success: function(data){
        	                		if(data > 0) {
        	                			$(item).prev().html($(item).val().replace(/\n/g,"<br />") );
        	    		            	$(item).parent().hide(1000);
        	                		} else {
        	            				alert("删除失败")
        	                		}
                        		}
                    		});
    		        	}
    		        });
    		    })
            	$("#note_td div span").each(function(i, item){
    			    //alert($(item));
    		        $(item).dblclick(function(){
    		            $(item).toggle();
    		            $(item).next().toggle();
    		            $(item).next().focus();
    		        });
    		    })
    		    $("#note_td div :not(span):not(a)").each(function(i, item){
    		        $(item).blur(function(){
    		            $(item).prev().toggle();
    		            $(item).toggle();
    		        	
    		        	$.ajax({
    		        		type: "post",
    		        		url: url+"?" + $(item).serialize(),
    		        		data: {"flag":"update", "id":$(item).parent().attr("data-nid"), "userID":<?php echo GetCookie('userID')?>},
    		        		success: function(data){
    	                		if(data > 0) {
    	                			$(item).prev().html($(item).val().replace(/\n/g,"<br />") );
    	            				alert("修改成功")
    	                		} else {
    	            				alert("修改失败")
    	                		}
                    		}
                		});
    	        	});
    	        });
    		}
		}
	});  
}
</script>
<style type="text/css">
.title {
	font-size: 20px;
	font-weight: bold;
}
.content {
	font-size: 15px;
}
</style>
</head>
<?php
if (sizeof($_REQUEST) >0) {
	require_once '../service/NoticeService.class.php';
    // 获取上传的文件名
    $filename = $_FILES['inputExcelBuy']['name'];
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcelBuy']['tmp_name'] ;
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
        case 6:
            echo "<br/><font color='red'>提交成功</font><br/>";
        break;
        
        default:
           echo "system error" ;
        break;
    }
//    echo "上传{$msg["n"]}件<br/>";
//    echo "更新{$msg["u"]}件<br/>";
//    echo "删除{$msg["d"]}件<br/>";
}

?>
<body>
<table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
  <tr>
    <td id="table_style" class="l_t">&nbsp;</td>
    <td>&nbsp;</td>
    <td id="table_style" class="r_t">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td id="note_td">
	</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td style="display: none;" id="replyTd">
        <div>
      		<form id="form2"  method="post" enctype="multipart/form-data">
      		    <input type="hidden" name="flag" value="1"/>
                <input placeholder="标题" name="replyTitle" id="replyTitle" type="text"/><br/>
                <textarea rows="10" cols="100" name="replyContent" id="replyContent"></textarea><br/>
      			上传文件:<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><br/>
                <input type="submit" id="replySubmit" value="提交 "/>
      		</form>
        </div>
<!--         <button id="replySubmit">提交</button><span>可按"Ctrl+Enter"键提交</span> -->
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
