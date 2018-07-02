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
<title><?php echo $cfg_softname;?>給料設定</title>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../js/loading.js"></script>
<script type="text/javascript">
var url = "../service/SalarySettingService.class.php";
$(function(){
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
        data: {"flag":"initPage", "user":user},
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
                    +"    <td><input disabled='disabled' type='text' size='2' name='sort' value='" + entry.sort + "'/></td>"
                    +"    <td><input disabled='disabled' type='text' name='p_name' value='" + entry.p_name + "'/></td>"
                    +"    <td><input disabled='disabled' type='text' name='p_func' value='" + entry.p_func + "'/></td>"
                    +"    <td><input disabled='disabled' type='text' size='5' name='p_value' value='" + entry.p_value + "'/></td>"
                    + radioTd
                    +"    <td><input type='hidden' name='id' value='" + entry.id + "'/>"
                    +"        <button>修改</button>"
                    +"        <button>保存</button>"
                    +"        <button value='" + entry.id + "'>删除</button>"
                    +"        <button>全局保存</button>"
                    +"        <button>切换显示</button>"
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
//更改用户类型
function updateUserType() {
    $.ajax({
        type: "post",
        url: url,
        data: "flag=updateUserType&"+$("#userType input[type='radio']:checked").serialize()+"&id="+$("#userTypeId").val(),
        success: function(data){
            if(data > 0) {
                alert("成功");
            } else {
                alert("失败");
            }
        }
    });
}
//更改时间展示方式
function updateTimeType() {
    $.ajax({
        type: "post",
        url: url,
        data: "flag=updateTimeType&"+$("#timeType input[type='radio']:checked").serialize()+"&id="+$("#timeTypeId").val(),
        success: function(data){
            if(data > 0) {
                alert("成功");
            } else {
                alert("失败");
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
                        <td><strong>&nbsp;給料設定</strong></td>
                        <td align="right"><select id="users"></select> <input type="button" value="修正" onclick="initGrant();"/></td>
                    </tr>
                    <tr>
                        <td colspan="2"><font color="red">填写规范: ①所有的数字, 计算符号都必须是英文半角<br/>
                    		②条件选择的公式, 必须是case when x1 then y1 when x2 then y2 ... else yy end 的形式 <br/>
							③注意②的填写, 必须是 case 开头, end 结尾, 其他普通公式不需要, 例如: 1#2-3#4 <br/>
							④并不是所有的excel公式, 在mysql中都通用, 例如: sum()就不支持</td>
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
                                    <td><input id="userTypeId" type="hidden" name="id"/><button>修改</button><button onclick="updateUserType()">保存</button></td>
                                </tr>
                                <tr>
                                    <td>考勤计算时间设定</td>
                                    <td id="timeType" colspan="2">
                                        <label><input type="radio" name="timeType" value="1"/>1分钟单位</label>
                                        <label><input type="radio" name="timeType" value="2"/>15分钟单位</label>
                                        <label><input type="radio" name="timeType" value="3"/>30分钟单位</label>
                                    </td>
                                    <td><input id="timeTypeId" type="hidden" name="id"/><button>修改</button><button onclick="updateTimeType()">保存</button></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#FFFFFF" colspan="2">
                            <table width="100%" cellspacing="0" border="1" id="table_border5">
                             <thead>
                                <tr bgcolor="#b9b9ff">
                                    <td colspan="6">个人设定(5#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>固定金額</th>
                                    <th>是否显示</th>
                                    <th>操作</th>
                                </tr>
                                <tr>
                                    <td><input type="text" name="sort" size="2"/></td>
                                    <td><input type="text" name="p_name"/></td>
                                    <td><input type="text" name="p_func"/></td>
                                    <td><input type="text" name="p_value" size="5"/></td>
                                    <td><label><input type='radio' name='del_flag' value='0'/>否</label>
                                          <label><input type='radio' name='del_flag' value='2'/>是</label>
                                    </td>
                                    <td><input type="hidden" name="p_type" value="5"/><button>追加项目</button></td>
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
                                        <td colspan="6">支給明細(1#)</td>
                                    </tr>
                                    <tr>
                                        <th>科目顺序</th>
                                        <th>科目名</th>
                                        <th>计算公式</th>
                                        <th>固定金額</th>
                                        <th>是否显示</th>
                                        <th>操作</th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="sort" size="2"/></td>
                                        <td><input type="text" name="p_name"/></td>
                                        <td><input type="text" name="p_func"/></td>
                                        <td><input type="text" name="p_value" size="5"/></td>
                                        <td><label><input type='radio' name='del_flag' value='0'/>否</label>
                                              <label><input type='radio' name='del_flag' value='2'/>是</label>
                                        </td>
                                        <td><input type="hidden" name="p_type" value="1"/><button>追加项目</button></td>
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
                                    <td colspan="6">控除明細(2#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>固定金額</th>
                                    <th>是否显示</th>
                                    <th>操作</th>
                                </tr>
                                <tr>
                                    <td><input type="text" name="sort" size="2"/></td>
                                    <td><input type="text" name="p_name"/></td>
                                    <td><input type="text" name="p_func"/></td>
                                    <td><input type="text" name="p_value" size="5"/></td>
                                    <td><label><input type='radio' name='del_flag' value='0'/>否</label>
                                          <label><input type='radio' name='del_flag' value='2'/>是</label>
                                    </td>
                                    <td><input type="hidden" name="p_type" value="2"/><button>追加项目</button></td>
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
                                    <td colspan="6">勤務(3#)</td>
                                </tr>
                                <tr>
                                    <th>科目顺序</th>
                                    <th>科目名</th>
                                    <th>计算公式</th>
                                    <th>固定金額</th>
                                    <th>是否显示</th>
                                    <th>操作</th>
                                </tr>
                                <tr>
                                    <td><input type="text" name="sort" size="2"/></td>
                                    <td><input type="text" name="p_name"/></td>
                                    <td><input type="text" name="p_func"/></td>
                                    <td><input type="text" name="p_value" size="5"/></td>
                                    <td><label><input type='radio' name='del_flag' value='0'/>否</label>
                                          <label><input type='radio' name='del_flag' value='2'/>是</label>
                                    </td>
                                    <td><input type="hidden" name="p_type" value="3"/><button>追加项目</button></td>
                                </tr>
                             </thead>
                             <tbody></tbody>
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
