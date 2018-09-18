<?php
require("../include/config_rglobals.php");
require("../include/config_base.php");
require("../include/page.php");
require_once("../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../style/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>商品庫存报告</title>
<style type="text/css">
.rtext {
	background: transparent;
	border: 0px;
	color: red;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
</style>

<script type="text/javascript" src="http://www.w3school.com.cn/jquery/jquery.js"></script>
<script src="http://echarts.baidu.com/build/dist/echarts.js"></script>
<script type="text/javascript">
function trimStr(str){return str.replace(/(^\s*)|(\s*$)/g,"");}
    // 路径配置
    require.config({
        paths: {
            echarts: 'http://echarts.baidu.com/build/dist'
        }
    });
    
    // 使用
    require(
        [
            'echarts',
            'echarts/chart/line', // 使用柱状图就加载bar模块，按需加载
            'echarts/chart/bar' // 使用柱状图就加载bar模块，按需加载
        ],
        function (ec) {
            // 基于准备好的dom，初始化echarts图表
            myChart = ec.init(document.getElementById('main')); 
            
            var option = {
			title:{
			text:"庫存报告"},
                tooltip: {
                    show: true,
					showContent: true,
					trigger: 'axis'
                },
                toolbox: {
                    show : true,
                    feature : {
                        mark : {show: true},
                        dataView : {show: true, readOnly: false},
                        magicType : {show: true, type: ['line', 'bar']},
                        restore : {show: true},
                        saveAsImage : {show: true}
                    }
                },
                legend: {
                    data:['在库数', '入库数', '出库数']
                },
                xAxis : [
                    {
                        type : 'category',
                        boundaryGap : false,
                        data : [0]
                    }
                ],
                yAxis : [
                    {
                        type : 'value'
                    }
                ],
                series :  [
			        {
			            name:'在库数',
			            type:'line',
			            data:[0]
//				            markPoint : {
//				                data : [
//				                    {type : 'max', name: '最大值'},
//				                    {type : 'min', name: '最小值'}
//				                ]
//				            },
//				            markLine : {
//				                data : [
//				                    {type : 'average', name: '平均'}
//				                ]
//				            }
			        },
			        {
			            name:'入库数',
			            type:'line',
			            data:[0]
//				            markPoint : {
//				                data : [
//				                    {name : '最小', type : 'min'}
//				                ]
//				            },
//				            markLine : {
//				                data : [
//				                    {type : 'average', name : '平均'}
//				                ]
//				            }
			        },
			        {
			            name:'出库数',
			            type:'line',
			            data:[0]
//				            markPoint : {
//				                data : [
//				                    {name : '最小', type : 'min'}
//				                ]
//				            },
//				            markLine : {
//				                data : [
//				                    {type : 'average', name : '平均'}
//				                ]
//				            }
			        }
			    ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
        }
    );
function load() {
	if(trimStr($("input[name=stext]").val()) == "") {
		alert("关键字不能为空");
		return;
	}
	var param = $("input").serialize();
	$.ajax({
		type: "post",
		url: "../service/ReportService.class.php?" + param,
		data: {"flag":"init"},
		success: function(data){
    		var myOpt = myChart.getOption();
			myChart.clear();
			if(trimStr(data) != "null") {
    			data = eval("("+data+")");
    			var s_count = new Array();
    			var in_count = new Array();
    			var out_count = new Array();
    			var dates = Array();
    			$.each(data, function(entryIndex, entry){
    	//     		alert(entry.url+"|"+entry.loc);
    				s_count.push(entry.s_count);
    				in_count.push(entry.in_count);
    				out_count.push(entry.out_count);
    				dates.push(entry.dtime);
    			});
    			myOpt.series[0].data = s_count;
    			myOpt.series[1].data = in_count;
    			myOpt.series[2].data = out_count;
    			myOpt.xAxis[0].data = dates;
    			
    	        // 为echarts对象加载数据 
    	        myChart.setOption(myOpt); 
    		}
		}
	});
}
function out_excel(){
	var param = $("input").serialize();
	var url = "../service/ReportService.class.php?&nocache="+new Date().getTime()+"&"+param+"&flag=out_excel";
    //window.open('excel_kc.php?shop='+shop+'&cp_categories='+cp+'&cp_categories_down='+cp_down+'&sort='+s+'&stext='+st,'','');
    window.open(url);
}
</script>
</head>
<body>
<table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
	<tr>
		<td></td>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td><strong>&nbsp;商品庫存报告(在库数从2018.09.15开始统计)</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                           <tr>
                				<td class="cellcolor">
                				            日期:
                                    <input type="text" name="sdate" id="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                                    <input type="text" name="edate" id="edate" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
                                                                                                关键字：
                                    <input type="text" name="stext" size="15"/>
                                    <input type="button" value="提交" onclick="load()"/>
                                    <input type="button" value="导出" onclick="out_excel()"/>
                				</td>
                			</tr>
                			<tr id="simple_rk_priv_out">
                				<td>
                                    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
                                    <div id="main" style="height:400px"></div>
                                </td>
                			</tr>
            			 </table>
            		</td>
            	</tr>
    		</table>
	   </td>
	</tr>
	<tr>
		<td id="table_style" class="l_b">&nbsp;</td>
		<td id="table_style" class="r_b">&nbsp;</td>
	</tr>
</table>
<?php 
copyright();
?>
</body>
</html>
