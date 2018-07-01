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
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/pager.css" rel="stylesheet" type="text/css" />
<link href="../style/colResizable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script	src="../js/colResizable-1.5.min.js"></script>
<script type="text/javascript" src="../js/jquery.pagination.js"></script>
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>空白excel表<?php echo $_REQUEST["pageId"] ?></title>
<script language="javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
var labOptions = "";
var staffOptions = "";
$(function(){
	var onSampleResized = function(e){
		/* var columns = $(e.currentTarget).find("th");
		var msg = "columns widths: ";
		columns.each(function(){ msg += $(this).width() + "px; "; })
		$("#sample2Txt").html(msg); */		
	};	
	$("#contentTable").colResizable({
		liveDrag:true, 
		gripInnerHtml:"<div class='grip'></div>", 
		draggingClass:"dragging", 
		onResize:onSampleResized
	}); 
	initPage(0);

	$.ajax({
		type: "post",
		url: "../service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"129", "user":<?php echo GetCookie('userID')?>},
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
})
function initPage(pageIndex) {
	//分页参数
	var pageCount = 50;
	var recordCount = 0;
	$.ajax({
		type: "post",
		url: "../service/ExcelTableService.php",
		//data: "flag=initPage&user=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageCount="+pageCount+"&pageIndex="+(pageIndex+1)+"&pageNameId=0&nocache="+new Date().getTime(),
		data: "flag=initPage&user=<?php echo GetCookie('userID')?>&"+$("#searchForm").find("input").serialize()+"&pageId=<?php echo $_REQUEST["pageId"] ?>&pageIndex=1&nocache="+new Date().getTime(),
		success: function(data){
    		$("#contentTable tr:gt(0)").remove();
			data = eval("("+data+")");
			var html = "";
    		$.each(data.results, function(entryIndex, entry){
        		html += ""
                    +"<tr " + (entry.finish_flag == 1 ? "class=\"pending1\"" : "") + (entry.finish_flag == 2 ? "class=\"pending2\"" : "") + (entry.finish_flag == 3 ? "class=\"finished\"" : "")+ ">"
            		+"    <td style=\"display: none;\"><input type=\"hidden\" value=\"" + entry.id + "\" name=\"strChk\"></td>"
                    +"    <td><button " + (entry.finish_flag == 0 ? "" : "style=\"display: none;\"")+ " onclick='updateFinish(this, 1)'>出庫確認</button>"
                    +"        <button " + (entry.finish_flag == 1 ? "" : "style=\"display: none;\"")+ " onclick='updateFinish(this, 2)'>領収書確認</button>"
                    +"        <button " + (entry.finish_flag == 2 ? "" : "style=\"display: none;\"")+ " onclick='updateFinish(this, 3)'>出荷済確認</button>"
                    +"        <button " + (entry.finish_flag == 3 ? "" : "style=\"display: none;\"")+ " onclick='updateFinish(this, 0)''>完了</button></td>"
                    +"    <td><span>" + entry.remark + "</span><input style=\"display: none;\" name=\"remark\" type=\"text\" value=\""+entry.remark+"\"/></td>"
                    +"    <td><span>" + entry.a + "</span><input style=\"display: none;\" name=\"a\" type=\"text\" value=\""+entry.a+"\"/></td>"
                    +"    <td><span>" + entry.b + "</span><input style=\"display: none;\" name=\"b\" type=\"text\" value=\""+entry.b+"\"/></td>"
                    +"    <td><span>" + entry.c + "</span><input style=\"display: none;\" name=\"c\" type=\"text\" value=\""+entry.c+"\"/></td>"
                    +"    <td><span>" + entry.d + "</span><input style=\"display: none;\" name=\"d\" type=\"text\" value=\""+entry.d+"\"/></td>"
                    +"    <td><span>" + entry.e + "</span><input style=\"display: none;\" name=\"e\" type=\"text\" value=\""+entry.e+"\"/></td>"
                    +"    <td><span>" + entry.f + "</span><input style=\"display: none;\" name=\"f\" type=\"text\" value=\""+entry.f+"\"/></td>"
                    +"    <td><span>" + entry.g + "</span><input style=\"display: none;\" name=\"g\" type=\"text\" value=\""+entry.g+"\"/></td>"
                    +"    <td><span>" + entry.h + "</span><input style=\"display: none;\" name=\"h\" type=\"text\" value=\""+entry.h+"\"/></td>"
                    +"    <td><span>" + entry.i + "</span><input style=\"display: none;\" name=\"i\" type=\"text\" value=\""+entry.i+"\"/></td>"
                    +"    <td><span>" + entry.j + "</span><input style=\"display: none;\" name=\"j\" type=\"text\" value=\""+entry.j+"\"/></td>"
                    +"    <td><span>" + entry.k + "</span><input style=\"display: none;\" name=\"k\" type=\"text\" value=\""+entry.k+"\"/></td>"
                    +"    <td><span>" + entry.l + "</span><input style=\"display: none;\" name=\"l\" type=\"text\" value=\""+entry.l+"\"/></td>"
                    +"    <td><span>" + entry.m + "</span><input style=\"display: none;\" name=\"m\" type=\"text\" value=\""+entry.m+"\"/></td>"
                    +"    <td><span>" + entry.n + "</span><input style=\"display: none;\" name=\"n\" type=\"text\" value=\""+entry.n+"\"/></td>"
                    +"    <td><span>" + entry.o + "</span><input style=\"display: none;\" name=\"o\" type=\"text\" value=\""+entry.o+"\"/></td>"
                    +"    <td><span>" + entry.p + "</span><input style=\"display: none;\" name=\"p\" type=\"text\" value=\""+entry.p+"\"/></td>"
                    +"    <td><span>" + entry.q + "</span><input style=\"display: none;\" name=\"q\" type=\"text\" value=\""+entry.q+"\"/></td>"
                    +"    <td><span>" + entry.r + "</span><input style=\"display: none;\" name=\"r\" type=\"text\" value=\""+entry.r+"\"/></td>"
                    +"    <td><span>" + entry.s + "</span><input style=\"display: none;\" name=\"s\" type=\"text\" value=\""+entry.s+"\"/></td>"
                    +"    <td><span>" + entry.t + "</span><input style=\"display: none;\" name=\"t\" type=\"text\" value=\""+entry.t+"\"/></td>"
                    +"    <td><span>" + entry.u + "</span><input style=\"display: none;\" name=\"u\" type=\"text\" value=\""+entry.u+"\"/></td>"
                    +"    <td><span>" + entry.v + "</span><input style=\"display: none;\" name=\"v\" type=\"text\" value=\""+entry.v+"\"/></td>"
                    +"    <td><span>" + entry.w + "</span><input style=\"display: none;\" name=\"w\" type=\"text\" value=\""+entry.w+"\"/></td>"
                    +"    <td><span>" + entry.x + "</span><input style=\"display: none;\" name=\"x\" type=\"text\" value=\""+entry.x+"\"/></td>"
                    +"    <td><span>" + entry.y + "</span><input style=\"display: none;\" name=\"y\" type=\"text\" value=\""+entry.y+"\"/></td>"
                    +"    <td><span>" + entry.z + "</span><input style=\"display: none;\" name=\"z\" type=\"text\" value=\""+entry.z+"\"/></td>"
            		+"</tr>";

    		});
    		$("#contentTable").append(html);

    		//为table中的span 绑定双击修改事件, 同时为相邻的input 绑定blur提交事件
    		$("#contentTable tr:gt(0) span").each(function(i, item){
			    //alert($(item));
		        $(item).click(function(){
		            $(item).toggle();
		            $(item).next().toggle();
		            $(item).next().focus();
		        });
		        
		        $(item).next().blur(function(){
			        _input = $(this);
			        _input.prev().toggle();
			        _input.toggle();
			        //如果没有编辑内容，就不发送更新请求
			        if(_input.val() == _input.prev().html() || (_input.prev().html()=="&nbsp;&nbsp;&nbsp;" && _input.val().trim()=="")) {
			            return;
				    }
		        	$.ajax({
		        		type: "post",
		        		url: "../service/ExcelTableService.php?strChk=" + _input.parent().parent().find("input[name=strChk]").val()+ "&" + _input.serialize(),
		        		data: {"flag":"updateCol", "userID":<?php echo GetCookie('userID')?>},
		        		success: function(data){
	                		if(data > 0) {
	                			_input.prev().html(_input.val() == "" ? "&nbsp;&nbsp;&nbsp;": _input.val());
// 	            				alert("修改成功")
	                		} else {
	            				alert("修改失败")
	                		}
                		}
            		});
	        	});
		    })

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
		}
	});
}
function initPageCallback(page_id, jq) {
	initPage(page_id);
}
function orderByField(obj) {
	switch (obj) {
    	case 1:
        	if($("input[name=orderByField").val() == "order by col1 asc") {
        		$("input[name=orderByField").val("order by col1 desc");
        	} else {
        		$("input[name=orderByField").val("order by col1 asc");
        	}
        	break;
    	case 2:
        	if($("input[name=orderByField").val() == "order by col2 asc") {
        		$("input[name=orderByField").val("order by col2 desc");
        	} else {
        		$("input[name=orderByField").val("order by col2 asc");
        	}
        	break;
    	case 3:
        	if($("input[name=orderByField").val() == "order by col3 asc") {
        		$("input[name=orderByField").val("order by col3 desc");
        	} else {
        		$("input[name=orderByField").val("order by col3 asc");
        	}
        	break;
    	case 4:
        	if($("input[name=orderByField").val() == "order by col4 asc") {
        		$("input[name=orderByField").val("order by col4 desc");
        	} else {
        		$("input[name=orderByField").val("order by col4 asc");
        	}
        	break;
    	case 5:
        	if($("input[name=orderByField").val() == "order by col5 asc") {
        		$("input[name=orderByField").val("order by col5 desc");
        	} else {
        		$("input[name=orderByField").val("order by col5 asc");
        	}
        	break;
    	case 6:
        	if($("input[name=orderByField").val() == "order by col6 asc") {
        		$("input[name=orderByField").val("order by col6 desc");
        	} else {
        		$("input[name=orderByField").val("order by col6 asc");
        	}
        	break;
		
	}
	initPage(0);
}
function chkAll(param) {
    if ($(param).prop("checked")) {
        $.each($("#contentTable input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", true);
        });
    } else {
        $.each($("#contentTable input:checkbox"), function(entryIndex, entry) {
        	$(entry).prop("checked", false);
        });
    }
}
function exportExcel() {
	var url = "../service/ExcelTableService.php?nocache="+new Date().getTime()+"&pageId=<?php echo $_REQUEST["pageId"] ?>&flag=exportExcel";
    window.open(url);
}
function passChecked(obj) {
	var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&passCheckedSelect="+obj;
	$.ajax({
		type: "post",
		url: "../service/ExcelTableService.php?" + param,
		data: {
				"flag":"passChecked", 
				"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功审核" + eval(data) +"条记录");
				initPage(0);
			} else {
				alert("审核失败");
    		}
		}
	});
}
function deleteChecked() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize();
    $.ajax({
            type: "post",
            url: "../service/ExcelTableService.php?" + param,
            data: {
                            "flag":"deleteChecked",
                            "userID":<?php echo GetCookie('userID')?>},
            success: function(data){
                    if(data >0) {
                            alert("成功删除" + eval(data) +"条记录");
                            initPage(0); 
                    } else {
                            alert("删除失败");
            }
            }
    });
} 
function transfer2Other() {
    var param = $("#contentTable tr:gt(0) input[type=checkbox]:checked").serialize()+"&"+$("select[name=transferSelect]").serialize();
    $.ajax({
            type: "post",
            url: "../service/ExcelTableService.php?" + param,
            data: {
                            "flag":"transfer2Other",
                            "userID":<?php echo GetCookie('userID')?>},
            success: function(data){
                    if(data >0) {
                            alert("成功切换" + eval(data) +"条记录");
                            initPage(0); 
                    } else {
                            alert("切换失败");
            }
            }
    });
} 
function updateFinish(obj, f) {
	var _button = $(obj);
	$.ajax({
		type: "post",
		url: "../service/ExcelTableService.php?strChk=" + _button.parent().parent().find("input[name=strChk]").val() + "&finishFlag=" +f,
		data: {
				"flag":"updateFinish", 
				"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
// 				alert("成功更新" + eval(data) +"条记录");
// 				initPage(0);
				_button.toggle();
				if(f == 0){
					_button.parent().parent().attr("class", "");
					_button.prev().prev().prev().toggle();
				} else if(f == 1){
					_button.parent().parent().attr("class", "pending1");
					_button.next().toggle();
				} else if(f == 2){
					_button.parent().parent().attr("class", "pending2");
					_button.next().toggle();
				} else {
					_button.parent().parent().attr("class", "finished");
					_button.next().toggle();
				}
			} else {
				alert("更新失败");
    		}
		}
	});
}
function cleanAll() {
	if(!confirm("确定?")) {
	    return;
	}
	$("#cleanAllButton").attr('disabled',"true");
	$.ajax({
		type: "post",
		url: "../service/ExcelTableService.php?nocache="+new Date().getTime()+"&pageId=<?php echo $_REQUEST["pageId"] ?>",
		data: {
				"flag":"cleanAll", 
				"userID":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data >0) {
				alert("成功");
				initPage(0);
			} else {
				alert("失败");
    		}
			$('#cleanAllButton').removeAttr("disabled"); 
		}
	});
}
</script>
</head>
<body>

<?php
require_once '../service/ExcelTableService.php';

if ($_FILES['inputExcelBuy']['size'] >0) {
    $excelTableService = new ExcelTableService();
    // 获取上传的文件名
    $filename = $_FILES['inputExcelBuy']['name'];
    // 上传到服务器上的临时文件名
    $tmp_name = $_FILES['inputExcelBuy']['tmp_name'] ;
    // 上传顺序,区分当前页面的id
    $pageId = $_REQUEST["pageId"] ;
    $msg = $excelTableService->uploadFile($pageId, $filename, $tmp_name);
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
//    echo "更新{$msg["u"]}件<br/>";
//    echo "删除{$msg["d"]}件<br/>";
}

?>
<div>
<form name="form2"  method="post" enctype="multipart/form-data" >
上传内容:<input type="file" name="inputExcelBuy" id="inputExcelBuy"/><input type="submit" value="取込 "/>
</form>
<button onclick="exportExcel()">导出</button>
<button style="display: none;" id="cleanAllButton" type="button" onclick="cleanAll()">清空</button>
</div>
<div class="bodyDiv" >
    <table id="contentTable" width="3000px" border="0" cellpadding="0" cellspacing="0">
    	<tr>
            <th>操作</th><th>备注</th><th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th><th>I</th><th>J</th><th>K</th><th>L</th><th>M</th><th>N</th><th>O</th><th>P</th><th>Q</th><th>R</th><th>S</th><th>T</th><th>U</th><th>V</th><th>W</th><th>X</th><th>Y</th><th>Z</th>
    	</tr>
    </table>
</div>	
<?php 
copyright();
?>
</body>
</html>
