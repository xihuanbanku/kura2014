<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<title>楽天データ登録</title>
<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
<script language="javascript" type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
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

function out_excel() {
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
	window.location.href="../service/OrderService.php?flag=out_excel&"+$("form").serialize();
}
function filenames() {
	if($("#f_sday").val() == "") {
		alert("开始时间未选择");
		return;
	}
	if($("#f_eday").val() == "") {
		alert("结束时间未选择");
		return;
	}
	if($("#f_eday").val() < $("#f_sday").val()) {
		alert("结束时间不能小于开始时间");
		return;
	}

	$.ajax({
		type: "post",
		url: "../service/OrderService.php?"+$("form").serialize(),
		data: {"flag":"filenames"},
		success: function(msg){
			msg = eval("("+msg+")");
			var html = "";
			$.each(msg, function(entryIndex, entry) {
				html+="<a href='../service/OrderService.php?flag=out_excel1&filename=" +entry.filename +"'>" +entry.filename +"</a><br/>";
			})
			$("#filename_td").html(html);
		}
	});
}
function showNext(o) {
	var obj = $(o)[0];
	if(obj.checked) {
		$("#exportTr").show();
	} else {
		$("#exportTr").hide();
	}
}
</script>
</head>
<body style="font-size:16px;">

<?php
require_once '../service/OrderService.php';

if (isset($_FILES['inputExcel'])) {
    $orderService = new OrderService();
    // 获取上传的文件名
    $filename = $_FILES['inputExcel']['name'];
    
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcel']['tmp_name'];
    $msg = $orderService->uploadFile($filename, $tmp_name);
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
    echo "上传{$msg["n"]}件<br/>导出<a href='../service/OrderService.php?flag=out_excel1&filename={$msg["filename"]}'>{$msg["filename"]}</a><br/>";
//    echo "更新{$msg["u"]}件<br/>";
//    echo "删除{$msg["d"]}件<br/>";
}

?>
                     使用说明:<br/>
	  	1.本系统只支持Excel文件上传(扩展名xls/xlsx)<br/>
	  	2.上传内容务必按照<a href="../upload/LETTO.xlsx" style="font-size: 16px; color:#0000FF">标准模板</a>格式<br/>
	  	3.模板文件<font color="red">请勿删除列</font>，如果某一列不使用，直接留空即可。<br/>
<form name="form2" onsubmit="return validate();" method="post" enctype="multipart/form-data" >
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
                            <strong>楽天データ登録</strong>
                        </td>
                    </tr>
                     <tr bgcolor="#FFFFFF" >
                         <td id="row_color_gray" align="center">
                                                                                        ファイル選択：<input type="file" name="inputExcel" id="inputExcel"/>
                          </td>
                     </tr>
                     <tr bgcolor="#FFFFFF">
                          <td id="row_color_gray" align="center"><input type="submit" value="取込 "/></td>
                     </tr>
            
                    <tr id="simple_rk_priv_out">
            			<td class="cellcolor">
            			         导出<input type="checkbox" onclick="showNext(this)"/>
                			<span id="exportTr" style="display: none;">
                			     按照时间导出
                				<input type="text" name="sday" id="sday"class="Wdate" onclick="WdatePicker()" />-
                				<input type="text" name="eday" id="eday" class="Wdate" onclick="WdatePicker()" />
                				<input type="button" value="Excel出力" onclick="out_excel()"/>
            				</span>
            			</td>
            		</tr>
                    <tr>
            			<td class="cellcolor">
                			<span>
                			     最近上传文件导出
                				<input type="text" name="f_sday" id="f_sday"class="Wdate" onclick="WdatePicker()" />-
                				<input type="text" name="f_eday" id="f_eday" class="Wdate" onclick="WdatePicker()" />
                				<input type="button" value="查询文件" onclick="filenames()"/>
            				</span><br />
            				<span id="filename_td"></span>
            			</td>
            		</tr>
               </table>
            </td>
        </tr>
    </table>
</form>
    
</body>
</html>