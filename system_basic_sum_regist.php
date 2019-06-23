<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title>商品情報一括登録</title>
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

<?php require("ZGJX_UpLoad_Excel.php"); ?>

<?php
//上传文件的标记,判断是打开页面,还是提交表单
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
        
        default:
           echo "system error" ;
        break;
    }
    echo "上传{$msg["n"]}件<br/>";
    echo "u更新{$msg["u"]}件<br/>";
    echo "t更新{$msg["t"]}件<br/>";
    echo "a更新{$msg["a"]}件<br/>";
    echo "更新位置{$msg["r"]}件<br/>";
    echo "删除{$msg["d"]}件<br/>";
}

?>
                     使用说明:<br/>
	  	1.本系统只支持Excel文件上传(扩展名xls/xlsx)<br/>
	  	2.上传内容务必按照<a href="upload/Template.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>格式<br/>
	  	3.模板文件<font color="red">请勿删除列</font>，更新(u)操作时,可以删除无关列。<br/>
	  	4.模板中表头为<font color="red">红色</font>的列为必填项，更新(u)操作时，请注意填写"<font color="red">在库位置</font>"列
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
                            <strong>&nbsp;商品情報一括登録</strong>
                        </td>
                    </tr>
                     <tr bgcolor="#FFFFFF" >
                         <td id="row_color_gray" align="center">
                                                                                        ファイル選択：<input type="file" name="inputExcel" id="inputExcel"/>
                            <!-- 上传文件的标记,判断是打开页面,还是提交表单 -->
                                  <input type="hidden" name="leadExcel" value="true"/>
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