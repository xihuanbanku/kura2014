<?php
require(dirname(__FILE__)."/include/config.php");
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/fix_mysql.inc.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
require (dirname(__FILE__) . "/include/page.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="style/jquery.event.calendar.css"/>
<link rel="stylesheet" type="text/css"  href="style/jquery-ui.css"/>
<link href="style/loading.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery.SuperSlide.2.1.1.js"></script>
<script type="text/javascript" src="js/loading.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script src="js/jquery.event.calendar.js"></script>
<script src="js/jquery.event.calendar.en.js"></script>
<title>メニュー</title>
<script type="text/javascript">
$(function(){
	//确认是否已经打卡
	$.ajax({
		type: "post",
		url: "service/DutyService.class.php",
		data: {"flag":"checkTodayStatus", "user":<?php echo GetCookie('userID')?>},
		success: function(data){
			if(data>0) {
				window.location.href="personal/duty.php";
			}
		}
	});
	//初始化按钮
	$.ajax({
		type: "post",
		url: "service/MenuService.class.php",
		data: {"flag":"initButton", "reid":"1", "user":<?php echo GetCookie('userID')?>},
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
	var today = new Date();
	$('#calendar').eCalendar({
		ajaxDayLoader	: "service/Calender.class.php?flag=hbDays&userID=<?php echo $_COOKIE["userID"];?>",
		ajaxEventLoader	: "service/Calender.class.php?flag=hbEvents&userID=<?php echo $_COOKIE["userID"];?>",
		eventsContainer	: "#hb-event-list",
		currentYear		: today.getFullYear(),
		currentMonth	: today.getMonth()+1,
		startYear		: 2016,
		startMonth		: 1,
		endYear			: 2025,
		endMonth		: 12,
		firstDayOfWeek	: 0,
		onBeforeLoad	: function() {},
		onAfterLoad		: function() {
			currentMonth = this.currentMonth;
			$.ajax({
				type: "post",
				url: "service/Calender.class.php",
				data: {
					"flag":"hbThisMonth",
					"userID":<?php echo $_COOKIE["userID"];?>,
					"currentMonth": this.currentMonth,
					"currentYear": this.currentYear
				},
				success: function(data){
					$.each($(".hb-day"), function(entryIndex, entry){
						if($(entry).text() == today.getDate() && (today.getMonth()+1) == currentMonth) {
							$(entry).css("background", "#eed8ae");
						}
						if(data.indexOf(","+$(entry).text()+",") >=0) {
							$(entry).attr("class", "hb-day hb-day-active");
							$(entry).attr("data-day", $(entry).text());
						}
					})
				}
			});
		},
		onClickMonth	: function() {},
		onClickDay		: function() {}
	});
    $( "#dialog" ).dialog({
        autoOpen: false,
        width:600,
        height:300,
        show: {
          effect: "fold",
          direction : "down",
          duration: 500
        },
        hide: {
          effect: "fold",
          duration: 500
        }
      });

    var url = "service/BulletinService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initMine", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			if(data!= "null ") {
    			data = eval("("+data+")");
        		var html="";
        		var remind_me_html="";
        		$.each(data, function(entryIndex, entry){
            		if(entry.is_public >= 2) {
    			        $("#dialog").dialog("open");
    	    			$("#dialog").dialog({title:'紧急提示:'+entry.subject});
    	    			$("#dialog p").html(entry.content.replace(/\n/g,'<br/>'));
    	    	        $("#dialog").dialog("open");
    	    	        $("#dialog a").show();
    	    	        showLoading();
            			html+=""
                			+"<li>"
                			+"<a href=\"system/system_bulletin.php\">" + entry.subject + "</a>"
                			+"</li>";
                		$(".bd ul").html(html);
            		} else {
                		if(entry.content==""){
                		    remind_me_html+="<li><a href=\"sale/luggages.php\">"+entry.subject+"</a></li>";
            		    } else {
                		    remind_me_html+="<li><a href=\"system/system_bulletin.php\">"+entry.subject+"</a></li>";
            		    }
            		}
        		});
        		$("#remind_me_div ul").html(remind_me_html);
            	jQuery(".txtMarquee-left").slide({mainCell:".bd ul",autoPlay:true,effect:"leftMarquee",vis:1,interTime:7});
    		}
		}
	});  
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initMineNote", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			if(data!= "null ") {
    			data = eval("("+data+")");
        		var html="<span style=\"font-size:20px\">" + data[0].subject + "</span><input name=\"note_title\" type=\"text\" style=\"display: none;\" value=\"" + data[0].subject + "\" /><br />" +
    	    	   "<span style=\"font-weight: initial;\">" + data[0].content.replace(/\n/g,"<br />") + "</span><textarea rows=\"10\" cols=\"100\" name=\"note_content\" style=\"display: none;\" >" + data[0].content + "</textarea>";
            	$("#note_div").html(html);

            	$("#note_div span").each(function(i, item){
    			    //alert($(item));
    		        $(item).dblclick(function(){
    		            $(item).toggle();
    		            $(item).next().toggle();
    		            $(item).next().focus();
    		        });
    		    })
    		    $("#note_div :not(span)").each(function(i, item){
    		        $(item).blur(function(){
    		            $(item).prev().toggle();
    		            $(item).toggle();
    		        	
    		        	$.ajax({
    		        		type: "post",
    		        		url: url+"?" + $(item).serialize(),
    		        		data: {"flag":"updateMineNote", "userID":<?php echo GetCookie('userID')?>},
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
});
function closeMe(obj, id) {
	$.ajax({
		type: "post",
		url: "service/Calender.class.php",
		data: {"flag":"closeMe", "id":id, "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			if(data > 0) {
				$(obj).parent().parent().hide();
    		}
		}
	});  
}
</script>
<style type="text/css">
.txtMarquee-left{
    width: 100%;
    position: relative;
}
.bd {
	width:100%;
}
.txtMarquee-left .bd ul li {
	width: 100%;
}
.txtMarquee-left .bd ul li a{
	color: red;
	font-size: 20px;
}
</style>
</head>
<body>
<table width="100%" border="0" id="table_style_all" cellpadding="0" cellspacing="0">
  <tr>
    <td id="table_style" class="l_t">&nbsp;</td>
    <td>&nbsp;</td>
    <td id="table_style" class="r_t">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
	<table width="100%" border="0" cellpadding="0" cellspacing="2">
     <tr>
        <td align="left">
          <div class="txtMarquee-left">
              <div class="bd">
        		<ul>
        		</ul>
            </div> 
          </div> 
        </td>
     </tr>
     <tr>
      <td>
        <div id="calendar"></div>
    	<div id="hb-event-list" class="hb-event-list"></div>
	  </td>
     </tr>
     <tr>
      <td >予定表:
    	<div id="remind_me_div">
    	   <ul></ul>
    	</div>
	  </td>
     </tr>
     <tr>
      <td align="center">
    	<div id="note_div">
    	</div>
	  </td>
     </tr>
     <tr>
      <td><strong>&nbsp;<?php echo $cfg_au_version;?>よく使う機能</strong></td>
     </tr>
     <tr>
      <td id="row_style">
	  <a id="system_basic_cpA" style="display: none;" href="system_basic_cp.php?action=seek"><img src="images/normal_1.gif" border="0"/></a>
<!-- 	  <img src="images/arrow_to.gif" border="0"/> -->
	  <a id="system_rkA" style="display: none;" href="system_rk.php"><img src="images/normal_2.gif" border="0"/></a>
<!-- 	  <img src="images/arrow_to.gif" border="0"/> -->
	  <a id="system_kcA" style="display: none;" href="system_kc.php"><img src="images/normal_4.gif" border="0"/></a>
	  </td>
     </tr>
     <tr>
      <td id="row_style">
	  <a id="saleA" style="display: none;" href="sale.php"><img src="images/normal_3.gif" border="0"/></a>
<!-- 	  <img src="images/arrow_to.gif" border="0"/> -->
	  <a id="system_moneyA" style="display: none;" href="system_money.php"><img src="images/normal_5.gif" border="0"/></a>
<!-- 	  <img src="images/arrow_to.gif" border="0"/> -->
	  <a id="reportA" style="display: none;" href="report.php"><img src="images/normal_6.gif" border="0"/></a>	  </td>
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
    <div id="dialog" title="a">
      <p></p>
      <a style="display: none;" href="system/system_bulletin.php" >処理</a>
    </div>
<?php
copyright();
?>
</body>
</html>
