<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>在庫管理システム</title>
<style type="text/css">
body {font:normal 12px Arial;background:#3179bd url(images/right_bg.gif) repeat-x top;margin:0px;}
td,p{font-size:12px;}
#main_table {background:url(images/top_bg.gif) no-repeat left top;}
#top_menu {background:url(images/menu02.gif) no-repeat bottom center;text-align:center;width:70px;float:left;margin-left:5px;height:27px;padding-top:8px;}
#top_menu a{padding-top:10px;}
#menu_big{font:bold 13px Arial;color:#FFF;padding-left:10px;}
a.main:link, a.main:visited {
font: normal 120% Verdana, Arial, Helvetica, sans-serif;
color: black;
text-decoration: none;
}
a:link, a:visited, a:hover {
font: normal 12px Verdana, Arial, Helvetica, sans-serif;
color: black;
text-decoration: none;
}
a:-webkit-any-link {
color: -webkit-link;
text-decoration: underline;
}
.newDiv{
    left: -215px;
    position: relative;
    top: -11px;
	display: none;
}
</style>
<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(function(){
	var url = "service/MenuService.class.php";
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initTop", "userID":<?php echo $_COOKIE["userID"];?>},
		success: function(data){
			data = eval("("+data+")");
			var html="";
			$.each(data, function(entryIndex, entry){
				html+="<div id=\"top_menu\"><a href=\"" + entry.url + "\" target=\"main\" onclick=\"OpenMenu(" + entry.id + ")\" class=\"main\">" + entry.name + "</a></div>";
			});
			$("#topTD").append(html);
		}
	});
	//加载按钮
	$.ajax({
		type: "post",
		url: url,
		data: {"flag":"initButton", "reid":"193", "user":<?php echo $_COOKIE["userID"]?>},
		success: function(data){
			data = eval("("+data+")");
    		$.each(data, function(entryIndex, entry){
//         		alert(entry.url+"|"+entry.loc);
        		if(entry.loc > 0) {
    				$("#" + entry.url).show();
    				//获取商品数量
    				$.ajax({
    					type: "post",
    					url: url,
    					data: {"flag":"initAlarmCount"},
    					success: function(datar){
    						datar = eval("("+datar+")");
		    				$("#alarm_b").html(datar[0].c);
    					}
    				});
        		} else {
    				$("#" + entry.url).remove();
        		}
    		});
		}
	});

});

function $Nav(){
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
  else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
  else return "OT";
}

var preID = 0;

function OpenMenu(cid,lurl,bid){
   if($Nav()=='IE'){
     if(rurl!='') top.document.frames.main.location = rurl;
     if(cid > -1) top.document.frames.menu.location = 'menu.php?c='+cid;
     else if(lurl!='') top.document.frames.menu.location = lurl;
     //if(bid>0) document.getElementById("d"+bid).className = 'thisclass';
     if(preID>0 && preID!=bid) document.getElementById("d"+preID).className = '';
     preID = bid;
   }else{
     if(cid > -1) top.document.getElementById("menu").src = 'menu.php?c='+cid;
     //if(bid>0) document.getElementById("d"+bid).className = 'thisclass';
     preID = bid;
   }
}

var preFrameW = '160,*';
var FrameHide = 0;
function ChangeMenu(way){
	var addwidth = 10;
	var fcol = top.document.all.bodyFrame.cols;
	if(way==1) addwidth = 10;
	else if(way==-1) addwidth = -10;
	else if(way==0){
		if(FrameHide == 0){
			preFrameW = top.document.all.bodyFrame.cols;
			top.document.all.bodyFrame.cols = '0,*';
			FrameHide = 1;
			return;
		}else{
			top.document.all.bodyFrame.cols = preFrameW;
			FrameHide = 0;
			return;
		}
	}
	fcols = fcol.split(',');
	fcols[0] = parseInt(fcols[0]) + addwidth;
	top.document.all.bodyFrame.cols = fcols[0]+',*';
}

function resetBT(){
	if(preID>0) document.getElementById("d"+preID).className = 'bdd';
	preID = 0;
}

</script>
</head>
<body leftmargin="0" topmargin="0">
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="90" id="main_table">
  <tr height="41">
    <td width="180" align="center"><font size="+2" color="#FFFFFF">在庫管理</font></td>
    <td valign="bottom" align="left">
	 <table width="100%" height="27" cellspacing="0" cellpadding="0" border="0">
	  <tr>
	   <td id="topTD">
	 <!--   <div id="top_menu"><a href="javascript:OpenMenu(102,'','system_basic.php',101)" class="main">システム</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(103,'','system_rk.php',102)" class="main">入庫管理</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(104,'','sale.php',101)" class="main">販売管理</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(105,'','system_kc.php',101)" class="main">在庫管理</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(106,'','system_guest.php',101)" class="main">顧客管理</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(107,'','report.php',101)" class="main">レポート</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(108,'','system_money.php',101)" class="main">会計管理</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(109,'','system_ys.php',101)" class="main">出入り</a></div>
	   <div id="top_menu"><a href="system_note.php" target="main" class="main">ログ一覧</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(101,'','main.php',101)" class="main">ﾃﾞｽｸﾄｯﾌﾟ</a></div>
	   <div id="top_menu"><a href="javascript:OpenMenu(110,'','about.html',110)" class="main">About</a></div> 
		<div id="top_menu"><a href="system_note.php" target="main" class="main">総合一覧</a></div>-->
	   </td>
	   <td width="10%">&nbsp;</td>
	  </tr>
	 </table>
	</td>
  </tr>
  <tr height="17">
    <td colspan="2"></td>
  </tr>
  <tr height="32">
   <td style="background:url(images/left_menu_bg.gif) no-repeat center bottom;"><div id="menu_big">クイックナビゲーション</div></td>
   <td>
   <table width="100%" border="0" cellspacing="0" cellpadding="0" height="32">
    <tr>
     <td width="10" style="background:url(images/bg_bottom.gif) repeat-x bottom;vertical-align:top"></td>
     <td style="background:url(images/bg_bottom.gif) repeat-x bottom">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
       <tr>
        <td width="15"><img src="images/arrow.gif"></td>
		<td width="420" style="color:#FF0000;">
		<div id="alarm_div" style="display:none;">アラーム:<b id="alarm_b">0</b>件商品がまもなく在庫切れです。<a href='system_kc.php?target=check&sstate12=1' target='main'>チェック</a>
		</div></td>
		<?php require_once(dirname(__FILE__)."/include/config_base.php");
              require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");?>
        <td align="right">&nbsp;<?php echo $_COOKIE['VioomaUserID']."(".getusertype($_COOKIE['rank'],0).")";?>&nbsp;|&nbsp;<a href="system_password.php" target="main">パスワード変更</a>&nbsp;|&nbsp;<a href="system_out.php" target="_top">ログアウト</a></td>
       </tr>
      </table>
	 </td>
     <td style="background:url(images/bg_bottom.gif) repeat-x bottom right;vertical-align:top;width:8px;"></td>
	 <td width="15"></td>
    </tr>
   </table>
   </td>
  </tr>
</table>

</body>
</html>
