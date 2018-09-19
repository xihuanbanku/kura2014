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
<title>贩卖渠道汇总</title>
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
            myChart2 = ec.init(document.getElementById('main2'));
            
            var option = {
			title:{
			text:"贩卖渠道汇总"},
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
                    data:['全部', '検済', 'サイト検済', '工場検済', 'amazon', 'rakuten', '', 'Vendor']
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
			            name:'全部',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'サイト検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'工場検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'amazon',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'rakuten',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'yahooshopping',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'Vendor',
			            type:'line',
			            data:[0]
			        }
			    ]
            };
            var option2 = {
			title:{
			text:"贩卖渠道金额汇总"},
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
                    data:['全部', '検済', 'サイト検済', '工場検済', 'amazon', 'rakuten', '', 'Vendor']
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
			            name:'全部',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'サイト検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'工場検済',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'amazon',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'rakuten',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'yahooshopping',
			            type:'line',
			            data:[0]
			        },
			        {
			            name:'Vendor',
			            type:'line',
			            data:[0]
			        }
			    ]
            };
    
            // 为echarts对象加载数据 
            myChart.setOption(option); 
            myChart2.setOption(option2); 
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
		data: {"flag":"jxc_report_sum"},
		success: function(data){
    		var myOpt = myChart.getOption();
			myChart.clear();
    		var myOpt2 = myChart2.getOption();
			myChart2.clear();
			if(trimStr(data) != "null") {
    			data = eval("("+data+")");
    			var c_67 = new Array();
    			var c_68 = new Array();
    			var c_69 = new Array();
    			var c_74 = new Array();
    			var c_75 = new Array();
    			var c_76 = new Array();
    			var c_77 = new Array();
    			var count_sum = new Array();
    			var s_67 = new Array();
    			var s_68 = new Array();
    			var s_69 = new Array();
    			var s_74 = new Array();
    			var s_75 = new Array();
    			var s_76 = new Array();
    			var s_77 = new Array();
    			var sale_sum = new Array();
    			var dates = Array();
    			$.each(data, function(entryIndex, entry){
    	//     		alert(entry.url+"|"+entry.loc);
    		    	switch(entry.state_id) {
    		    	case 67:
    		    	    c_67.push(entry.count_sum);
    		    	    s_67.push(entry.sale_sum);
    		    	    break;
    		    	case 68:
    		    	    c_68.push(entry.count_sum);
    		    	    s_68.push(entry.sale_sum);
    		    	    break;
    		    	case 69:
    		    	    c_69.push(entry.count_sum);
    		    	    s_69.push(entry.sale_sum);
    		    	    break;
    		    	case 74:
    		    	    c_74.push(entry.count_sum);
    		    	    s_74.push(entry.sale_sum);
    		    	    break;
    		    	case 75:
    		    	    c_75.push(entry.count_sum);
    		    	    s_75.push(entry.sale_sum);
    		    	    break;
    		    	case 76:
    		    	    c_76.push(entry.count_sum);
    		    	    s_76.push(entry.sale_sum);
    		    	    break;
    		    	case 77:
    		    	    c_77.push(entry.count_sum);
    		    	    s_77.push(entry.sale_sum);
    		    	    break;

    		    	}
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
					<td><strong>贩卖渠道汇总</strong>
    				</td>
               </tr>
                <tr>
                	<td bgcolor="#FFFFFF">
                		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="table_border">
                           <tr>
                				<td class="cellcolor">
									渠道:<select name="state_8"><option value="-1">全部</option>
                				            日期:
                                    <input type="text" name="sdate" id="sdate" size="15" value="" class="Wdate" onclick="WdatePicker()"/> &ndash; 
                                    <input type="text" name="edate" id="edate" size="15" value="" class="Wdate" onclick="WdatePicker()"/>
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
                			<tr id="simple_rk_priv_out2">
                				<td>
                                    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
                                    <div id="main2" style="height:400px"></div>
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
