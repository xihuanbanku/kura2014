<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title>文件上传</title>
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">

function validate(){
    var filePathName = $("input[name=inputFile]").val();
	if(filePathName.length == 0){
		alert("请选择文件.")
		return false;
	}
	var reg=/.*\.(jpg|jpeg|png|bmp)$/;
	if(!reg.test(filePathName)) {
		alert("只支持图片类型的文件");
		return false;
	}
	return confirm("确定上传?");
}
</script>
</head>
<body style="font-size:16px;">

<?php
date_default_timezone_set("PRC");
//上传文件的标记,判断是打开页面,还是提交表单
$leadExcel="";
if(isset($_REQUEST["leadExcel"])){
    $leadExcel=$_REQUEST["leadExcel"];
}
if(isset($_REQUEST["form"])){
    $form=$_REQUEST["form"];
}
if(isset($_REQUEST["field"])){
    $field=$_REQUEST["field"];
}
if(isset($_REQUEST["dir_name"])){
    $dir_name=$_REQUEST["dir_name"];
}
if ($leadExcel == "true") {
    // 获取上传的文件名
    $filename = $_FILES['inputFile']['name'];
    
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputFile']['tmp_name'];
    
    // 自己设置的上传文件存放路径
    $filePath = '/home/p-mon/p-mon.jp/public_html/picture/'.$dir_name.'/';
    
    $uploadfile = $filePath . $filename; // 上传后的文件名地址

    error_log(date("Ymd-H:i:s")."----[".$_COOKIE["VioomaUserID"]."]上传文件".$uploadfile."\n", 3, dirname(__FILE__)."/../logs/sql".date("Ymd").".log");
    // move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    $result = move_uploaded_file($tmp_name, $uploadfile);
    $visitUrl="http://www.p-mon.jp/picture/".$dir_name."/".$filename;
    if ($result) {// 如果上传文件成功，就执行后续操作
        echo "<script language=\"javascript\">alert('文件上传成功');window.close();window.opener.document.{$form}.{$field}.value='{$visitUrl}'; </script>";
    } else {
        echo "<script language=\"javascript\">alert('上传失败')</script>";
    }
}

?>
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
                            <strong>上传文件</strong>
                        </td>
                    </tr>
                     <tr bgcolor="#FFFFFF">
                        <td id="row_color_gray" align="center">分类
                            <select name="dir_name">
                            <option value="ac">ac</option>
                            <option value="accessory">accessory</option>
                            <option value="adapter">adapter</option>
                            <option value="all">all</option>
                            <option value="battery">battery</option>
                            <option value="batterycrt">batterycrt</option>
                            <option value="bluetooth">bluetooth</option>
                            <option value="board">board</option>
                            <option value="cable">cable</option>
                            <option value="codr">codr</option>
                            <option value="cpu">cpu</option>
                            <option value="cpufancrt">cpufancrt</option>
                            <option value="dg">dg</option>
                            <option value="drive">drive</option>
                            <option value="ednall">ednall</option>
                            <option value="fan">fan</option>
                            <option value="hdd">hdd</option>
                            <option value="invter">invter</option>
                            <option value="keyboard">keyboard</option>
                            <option value="mb">mb</option>
                            <option value="memory">memory</option>
                            <option value="mogura">mogura</option>
                            <option value="mouse">mouse</option>
                            <option value="new">new</option>
                            <option value="ntyrh">ntyrh</option>
                            <option value="orico">orico</option>
                            <option value="otoiawase">otoiawase</option>
                            <option value="panel">panel</option>
                            <option value="panel/all">panel/all</option>
                            <option value="panel/allcrt">panel/allcrt</option>
                            <option value="panel/auo">panel/auo</option>
                            <option value="panel/chimei">panel/chimei</option>
                            <option value="panel/ibm">panel/ibm</option>
                            <option value="panel/lg">panel/lg</option>
                            <option value="panel/samsung">panel/samsung</option>
                            <option value="panel/sharp">panel/sharp</option>
                            <option value="panel/toshiba">panel/toshiba</option>
                            <option value="phone">phone</option>
                            <option value="soft">soft</option>
                            <option value="tyuukou">tyuukou</option>
                            <option value="ucomputer">ucomputer</option>
                            <option value="wifi">wifi</option>
                            <option value="zakka">zakka</option>
                            </select>
                        </td>
                     </tr>
                     <tr bgcolor="#FFFFFF" >
                         <td id="row_color_gray" align="center">
                             ファイル選択：<input type="file" name="inputFile"/>
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