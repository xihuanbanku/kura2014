<?php
require (dirname(__FILE__) . "/../include/config_base.php");
require (dirname(__FILE__) . "/../include/config_rglobals.php");
require_once (dirname(__FILE__) . "/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<link href="../style/loading.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#parent_row_style td {
    text-align:left;
    background-color:#b9b9ff;
}
#child_row_style td {
    text-align:left;
}

</style>
<title><?php echo $cfg_softname;?>給料备考</title>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<script type="text/javascript">
var url = "../service/SalaryService.class.php";
$(function(){
	// 初始化默认日期
	var myDate = new Date();
	//获取完整的年份(4位,1970-????)
	$("select[name='dutyYear']").val(myDate.getFullYear());
	//获取当前月份(0-11,0代表1月)
	if(myDate.getMonth() < 10) {
		$("select[name='dutyMonth']").val("0"+(myDate.getMonth()+1));
	} else {
		$("select[name='dutyMonth']").val(myDate.getMonth()+1);
	}
    $.ajax({
        type: "post",
        url: "../service/MenuService.class.php",
        data: {"flag":"initUser"},
        success: function(data){
            data = eval("("+data+")");
            var html="";
            $.each(data, function(entryIndex, entry){
                html+="<option value=\"" + entry.id + "\">" + entry.s_name + "</option>";
            });
            $("#users").html(html);
        }
    });
    $("[id^='table_border'] thead button").each(function(i, item){
        $(this).click(function(){
            if($(item).parent().parent().find("input[name=p_name]").val() == "") {
                alert("请输入名称");
                return;
            }
            //追加项目
            $.ajax({
                type: "post",
                url: url,
                data: "flag=insert&"+$(item).parent().parent().find("input").serialize()+"&user="+$("#users").val(),
                success: function(data){
                    if(data > 0) {
                        alert("成功");
                        $(item).parent().parent().find("input[name!=p_type]").each(function(i, entry){
                            $(entry).val("");
                        });
                        initGrant();
                    } else {
                        alert("失败");
                    }
                }
            });
        });
    });
});
function initGrant() {
    var user = $("#users").val();
    $.ajax({
        type: "post",
        url: url,
        data: {"flag":"initPage4Admin", "user":user, "dutyYear":$("select[name=dutyYear]").val(), "dutyMonth":$("select[name=dutyMonth]").val()},
        success: function(data){
            data = eval("("+data+")");
            var html1="";
            var html2="";
            var html3="";
            var html5="";
            var html6="";
            var row ="";
            var radioTd = "";
            $.each(data, function(entryIndex, entry){
                if(entry.del_flag == "0") {
                    radioTd = "<td>是</td>";
                } else {
                    radioTd = "<td>否</td>";
                }
                row ="<tr>"
                    +"    <td>" + entry.sort + "</td>"
                    +"    <td>" + entry.p_name + "</td>"
                    +"    <td title='"+entry.p_func +"'>" + (entry.p_func.length > 20 ? (entry.p_func.substr(0, 20) + "...") : entry.p_func) +"</td>"
                    +"    <td>" + entry.p_value + "</td>"
                    +"    <td><input disabled='disabled' type='text' size='5' name='mod_value' value='" + entry.mod_value + "'/></td>"
                    +"    <td><input type='hidden' name='id' value='" + entry.id + "'/>"
                    +"        <button>修改</button>"
                    +"        <button>保存</button>"
                    +"        <span style='display:none'><button value='" + entry.id + "'>删除</button>"
                    +"        <button>全局保存</button>"
                    +"        <button>切换显示</button></span>"
                    +"        <button>取消</button></td>"
                    +"</tr>"
                    +"";
                switch(entry.p_type) {
                    case "1":
                        html1+=row;
                    break;
                    case "2":
                        html2+=row;
                    break;
                    case "3":
                        html3+=row;
                    case "4":
                        if(entry.p_name == "職位") {
                            $("#userType input[type='radio']").prop("checked", false);
                            $("#userType input[type='radio'][value='" + entry.p_value + "']").prop("checked", true);
                            $("#userTypeId").val(entry.id);
                        } else if(entry.p_name == "时间展示方式") {
                            $("#timeType input[type='radio']").prop("checked", false);
                            $("#timeType input[type='radio'][value='" + entry.p_value + "']").prop("checked", true);
                            $("#timeTypeId").val(entry.id);
                        }
                    break;
                    case "5":
                        html5+=row;
                        break;
                    case "6":
                        html6+=row;
                        break;
                }
            });
            $("#table_border1 tbody tr").remove();
            $("#table_border1 tbody").append(html1);
            $("#table_border2 tbody tr").remove();
            $("#table_border2 tbody").append(html2);
            $("#table_border3 tbody tr").remove();
            $("#table_border3 tbody").append(html3);
            $("#table_border5 tbody tr").remove();
            $("#table_border5 tbody").append(html5);
            $("#table_border6 tbody tr").remove();
            $("#table_border6 tbody").append(html6);

            $("[id^='table_border'] tbody button").each(function(i, item){
                $(this).click(function(){
                    if(i%6 ==0) { //修改
                        $(item).parent().parent().find("input").each(function(i, entry){
                            $(entry).removeAttr("disabled");
                            if($(entry).val() == "出勤时间" 
                                || $(entry).val() == "退勤时间"
                                || $(entry).val() == "欠勤日数"
                                || $(entry).val() == "当月出勤見込み"
                                || $(entry).val() == "勤務日数"
                                || $(entry).val() == "強制退勤時間") {
                                $(entry).attr("readonly", "readonly");
                            }
                        });
                    } else if(i%6 ==1) {//保存
                        var click = true;
                        $(item).parent().parent().find("input").each(function(i, entry){
                            if($(entry).attr("disabled") == "disabled") {
                                alert("请先点击修改");
                                click = false;
                                return false;
                            }
                        });
                        if(click) {
                            $.ajax({
                                type: "post",
                                url: url,
                                data: "flag=update&all=0&"+$(item).parent().parent().find("input").serialize()+"&user="+$("#users").val(),
                                success: function(data){
                                    if(data > 0) {
                                        alert("成功");
                                        initGrant();
                                    } else {
                                        alert("失败");
                                    }
                                }
                            });
                        }
                    } else if(i%6 ==2) {//删除
                        if($(item).parent().parent().find("input[name=p_name]").val() == "出勤时间" 
                            || $(item).parent().parent().find("input[name=p_name]").val() == "退勤时间"
                            || $(item).parent().parent().find("input[name=p_name]").val() == "欠勤日数"
                            || $(item).parent().parent().find("input[name=p_name]").val() == "当月出勤見込み"
                            || $(item).parent().parent().find("input[name=p_name]").val() == "勤務日数"
                            || $(item).parent().parent().find("input[name=p_name]").val() == "強制退勤時間" ) {
                            alert("出勤时间,強制退勤時間和退勤时间不能删除");
                            return;
                        }
                        if(!confirm("确定删除?")) {
                            return;
                        }
                        $.ajax({
                            type: "post",
                            url: url,
                            data: "flag=delete&id="+$(this).attr("value"),
                            success: function(data){
                                if(data > 0) {
                                    alert("成功");
                                    $(item).parent().parent().remove();
                                } else {
                                    alert("失败");
                                }
                            }
                        });
                    } else if(i%6 ==3) {//全局保存
                        $.ajax({
                            type: "post",
                            url: url,
                            data: "flag=update&all=1&"+$(item).parent().parent().find("input").serialize()+"&user="+$("#users").val(),
                            success: function(data){
                                if(data > 0) {
                                    alert("成功");
                                    $(item).parent().parent().find("input").each(function(i, entry){
                                        $(entry).attr("disabled", "disabled");
                                    });
                                } else {
                                    alert("失败");
                                }
                            }
                        });
                    } else if(i%6 ==4) {//切换显示状态
                        $.ajax({
                            type: "post",
                            url: url,
                            data: "flag=updateDelFlag&"+$(item).parent().parent().find("input").serialize()+"&user="+$("#users").val(),
                            success: function(data){
                                if(data > 0) {
                                    alert("成功");
                                    $(item).parent().parent().find("input").each(function(i, entry){
                                        $(entry).attr("disabled", "disabled");
                                    });
                                    if($(item).parent().prev().html() == "是") {
                                        $(item).parent().prev().html("否");
                                    } else {
                                        $(item).parent().prev().html("是");
                                    }
                                } else {
                                    alert("失败");
                                }
                            }
                        });
                    } else {//取消
                        $(item).parent().parent().find("input").each(function(i, entry){
                            $(entry).attr("disabled", "disabled");
                        });
                    }
                });
            });
        }
    });
}
//审核发布薪酬
function updateState() {
    var param = $("#updateTable input").serialize()+"&"+$("#updateTable select").serialize();
    $.ajax({
		type: "post",
		url: "../service/SalaryService.class.php?" + param,
		data: {"flag":"updateState", "users": $("#users").val(), "dutyYear":$("select[name=dutyYear]").val(), "dutyMonth":$("select[name=dutyMonth]").val()},
		success: function(data){
			if(data >0) {
				alert("成功");
			} else {
				alert("失败, 可能是重复审核或尚未审核");
			}
		}
    });
}
</script>
</head>
<body>
    <table width="100%" border="0" id="table_style_all" cellpadding="0"
        cellspacing="0">
        <tr>
            <td id="table_style" class="l_t">&nbsp;</td>
            <td>&nbsp;</td>
            <td id="table_style" class="r_t">&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="2" id="contentTable">
                    <tr>
                        <td><strong>&nbsp;給料备考</strong></td>
                        <td align="right">
                        	<select name="dutyYear">
								<option value="2017">2017</option>
								<option value="2018">2018</option>
							</select>年
							<select name="dutyMonth">
								<option value="01">01</option>
								<option value="02">02</option>
								<option value="03">03</option>
								<option value="04">04</option>
								<option value="05">05</option>
								<option value="06">06</option>
								<option value="07">07</option>
								<option value="08">08</option>
								<option value="09">09</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
							</select>月
							<select id="users"></select> <input type="button" value="修正" onclick="initGrant();"/></td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1">
                                <tr bgcolor="#b9b9ff">
                                    <td colspan="4">人员类型</td>
                                </tr>
                                <tr>
                                    <td>職位</td>
                                    <td id="userType" colspan="2">
                                        <label><input type="radio" name="userType" value="1"/>正社員</label>
                                        <label><input type="radio" name="userType" value="2"/>アルバイト</label>
                                    </td>
                                    <td></td>
                                </tr>
                                <!-- <tr>
                                    <td>考勤计算时间设定</td>
                                    <td id="timeType" colspan="2">
                                        <label><input type="radio" name="timeType" value="1"/>1分钟单位</label>
                                        <label><input type="radio" name="timeType" value="2"/>15分钟单位</label>
                                        <label><input type="radio" name="timeType" value="3"/>30分钟单位</label>
                                    </td>
                                    <td></td>
                                </tr> -->
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1" id="table_border5">
                             <thead>
                                <tr bgcolor="#b9b9ff">
                                    <td colspan="7">个人设定(5#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>参考值</th>
                                    <th>修改值</th>
                                    <th>操作</th>
                                </tr>
                             </thead>
                             <tbody></tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1" id="table_border1">
                                <thead>
                                    <tr bgcolor="#b9b9ff">
                                        <td colspan="7">支給明細(1#)</td>
                                    </tr>
                                    <tr>
                                        <th>科目顺序</th>
                                        <th>科目名</th>
                                        <th>计算公式</th>
                                        <th>参考值</th>
                                        <th>修改值</th>
                                        <th>操作</th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1" id="table_border2">
                             <thead>
                                <tr bgcolor="#b9b9ff">
                                    <td colspan="7">控除明細(2#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>参考值</th>
                                    <th>修改值</th>
                                    <th>操作</th>
                                </tr>
                             </thead>
                             <tbody></tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1" id="table_border3">
                             <thead>
                                <tr bgcolor="#b9b9ff">
                                    <td colspan="7">勤務(3#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>参考值</th>
                                    <th>修改值</th>
                                    <th>操作</th>
                                </tr>
                             </thead>
                             <tbody></tbody>
                            </table>
                        </td>
                    </tr>
					<tr>
						<td>
						  <table width="100%" id="updateTable">
						      <thead>
						          <tr><td>审核人</td><td>审核时间</td><td>审核状态</td><td>操作</td></tr>
					          </thead>
						      <tbody>
						          <tr bgcolor="#FFFFFF">
						              <td><?php echo $_COOKIE["VioomaUserID"]?></td>
						              <td><input class="Wdate" onclick="WdatePicker()" name="passDate" type="text" value="<?php echo date("Y-m-d H:i:s")?>"/></td>
						              <td><select name="salaryState"><option value="0">取消审核</option><option value="1">确认审核</option></select></td>
						              <td><button onclick="updateState()">提交</button></td>
					              </tr>
				              </tbody>
						  </table>
					   </td>
					</tr>
                </table>
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
