<?php
//require_once(dirname(__FILE__)."/include/cryption.php");
//require_once(dirname(__FILE__)."/include/a_code.php");
//require_once(dirname(__FILE__)."/include/config_base.php");
//require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
?>
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CopyRights</title>
<link href="style/main.css" rel="stylesheet" type="text/css" /> 
<style type="text/css">
 body,td{
 font:normal 12px Verdana, Arial, Helvetica, sans-serif;
 color:#FFF;
 }
</style>
    <script type="application/javascript">

        today=new Date();
        function initArray(){
            this.length=initArray.arguments.length;
            for(var i=0; i<this.length; i++) {
                this[i+1]=initArray.arguments[i];
            }
        }
        var d=new initArray(" 日曜日"," 月曜日"," 火曜日"," 水曜日"," 木曜日"," 金曜日"," 土曜日"); document.write(today.getFullYear(),"年","",today.getMonth()+1,"月",today.getDate(),"日",d[today.getDay()+1]);
    </script>
</head>
<body style="background:url(images/f_bg.png) repeat-x" topmargin="3">
<table width="80%" align="right" cellpadding="0" cellspacing="2">
 <tr>
  <td width="70%">
  </td>
  <td align="right" width="30%">

  </td>
  <td style="font:bold 12px Verdana, Arial, Helvetica, sans-serif;color:#FF0;text-align:right">
  <?php
   //check_key(true); 
  ?>
  </td>
 </tr>
</table>
</body>
</html>
