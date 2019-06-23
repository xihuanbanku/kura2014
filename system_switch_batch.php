<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title>倉庫批量調達</title>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">

function validate(){
    var filePathName = $("#inputExcel").val();
	if(filePathName.length == 0){
		alert("请选择文件.")
		return false;
	}
	var reg=/.*\.(xls|xlsx)$/;
	if(!reg.test(filePathName)) {
		alert("只支持xls或xlsx类型的文件");
		return false;
	}
	return confirm("确定上传?");
}
</script>
</head>
<body style="font-size:16px;">

<?php require("Switch_Batch_Excel.php"); ?>

<?php
$leadExcel="";
if($_REQUEST["leadExcel"]){
    $leadExcel=$_REQUEST["leadExcel"];
}
if ($leadExcel == "true") {
    
    // 获取上传的文件名
    $filename = $_FILES['inputExcel']['name'];
    
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcel']['tmp_name'];
    $msg = uploadFile($filename, $tmp_name);
    switch ($msg["msg"]) {
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
    echo "更新{$msg["succ"]}件<br/>";
}

?>
                     使用说明:<br/>
	  	1.本系统只支持Excel文件上传(扩展名xls/xlsx)<br/>
	  	2.上传内容务必按照<a href="upload/Template.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>格式<br/>
	  	
<form name="form2" onsubmit="return validate();" method="post" enctype="multipart/form-data">
    <table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
        <tr>
            <td id="table_style" class="l_t">&nbsp;</td>
            <td>&nbsp;</td>
            <td id="table_style" class="r_t">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="2" >
                    <tr>
                        <td>
                            <strong>&nbsp;商品批量調達</strong>
                        </td>
                    </tr>
                     <tr bgcolor="#FFFFFF" >
                         <td id="row_color_gray" align="center">
                                                                                        ファイル選択：<input type="hidden" name="leadExcel" value="true"/>
                                  <input type="file" name="inputExcel" id="inputExcel"/>
                          </td>
                     </tr>
                     <tr bgcolor="#FFFFFF">
                          <td id="row_color_gray" align="center"><input type="submit" value="取込 "/></td>
                     </tr>
            
               </table>
            </td>
        </tr>
    </table>
</form>
	
</body>
</html>