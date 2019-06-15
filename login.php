<HTML>
<HEAD>
<TITLE>在庫管理システム</TITLE>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<LINK href="/css/webcss.css" type=text/css rel=stylesheet>
<STYLE type=text/css>
body,td {font-size:12px;}
.STYLE2 {color: #FFFFFF}
</STYLE>
<SCRIPT language=JavaScript type=text/JavaScript>
				nereidFadeObjects = new Object();
				nereidFadeTimers = new Object();
				function nereidFade(object, destOp, rate, delta){
				if (!document.all)
				return
					if (object != "[object]"){ 
						setTimeout("nereidFade("+object+","+destOp+","+rate+","+delta+")",0);
						return;
					}
					clearTimeout(nereidFadeTimers[object.sourceIndex]);
					diff = destOp-object.filters.alpha.opacity;
					direction = 1;
					if (object.filters.alpha.opacity > destOp){
						direction = -1;
					}
					delta=Math.min(direction*diff,delta);
					object.filters.alpha.opacity+=direction*delta;
					if (object.filters.alpha.opacity != destOp){
						nereidFadeObjects[object.sourceIndex]=object;
						nereidFadeTimers[object.sourceIndex]=setTimeout("nereidFade(nereidFadeObjects["+object.sourceIndex+"],"+destOp+","+rate+","+delta+")",rate);
					}
				}
				</SCRIPT>
<SCRIPT language=javascript>   
function login(){
thisname=document.form1.username.value;
thispwd=document.form1.password.value;
thiscode=document.form1.code.value;
if (thisname=='')
{
alert('ユーザーIDを入力してください。');
return false;
}
else if (thispwd=='')
{
alert('パスワードを入力してください。');
return false;
}
//else if (thiscode=='')
//{
//alert('検証コードを入力してください。');
//return false;
//}
else
return true;
}
</SCRIPT>
<META content="MSHTML 6.00.2900.5583" name=GENERATOR></HEAD>
<BODY leftMargin="0" topMargin="0" onload="document.form1.username.focus()" MARGINHEIGHT="0" MARGINWIDTH="0">
<?php
require_once(dirname(__FILE__)."/include/config_rglobals.php");
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
if ($action=='login')
{
 if (GetCkVdValue()!=$code)
  {//登録処理
  $username = preg_replace("/['\"\$ \r\n\t;<>\*%\?]/", '', $username);
  $loginip=getip();
  $logindate=getdatetimemk(time());
  $lsql=new Dedesql(false);
  $sql="select * from #@__staff b where b.s_no ='$username' and password='".md5($password)."'";
//   $sql="select * from #@__boss where boss='takamatsu' and password='dba634fa0ce5b357b87dc37d7e7963d7'";
  $lsql->SetQuery($sql);
  $lsql->Execute();
  $rowcount=$lsql->GetTotalRow();
  $row=$lsql->getone();
  if ($rowcount==0){
  $message='ユーザID・パスワードに誤りがあるか、登録されていません。';
  WriteNote($message,$logindate,$loginip,$username);
  showmsg($message,-1);
  }
  else
  {
      echo "----";
      echo $row;
      echo "----";
    //正常登録、データ読み込み可
    $message="正常登録しました。";
    $_SESSION['VioomaUserID']=$username;
    $_SESSION['mac']=$mac_addr;
    $_SESSION["userID"]=$row["id"];
    setcookie('VioomaUserID',$username, -1);
    setcookie('rank',$row['s_utype'], -1);
    setcookie('userID',$row['id'], -1);
    WriteNote($message,$logindate,$loginip,$username);
    $loginsql="update #@__staff set logindate='$logindate',loginip='$loginip',session_id='{$_COOKIE["PHPSESSID"]}' where s_no='$username'";
    $lsql->executenonequery($loginsql);
    echo "<script language='javascript'>window.location.href='index.php';</script>";
    //header("Location:index.php");
  }
	$lsql->close();
  }
  else
  {
//  $errmessage="検証コードに誤りがある！";
//  showmsg($errmessage,-1);
  }
  }
else
{
?>
<FORM name="form1" onSubmit="return login()" action="login.php" method="post">
<TABLE height="86%" cellSpacing=0 cellPadding=0 width="100%" border=0 align="center">
  <TBODY>
  <TR>
    <TD align=middle height=439>
      <TABLE width=720 border=0>
        <TBODY>
        <TR>
            <TD align=center>
            <TABLE height=337 cellSpacing=0 cellPadding=0 width=491 
            background=images/bsdt.jpg border=0>
              <TBODY>
              <TR>
                <TD colSpan=3 height=130 align="center"><span class="STYLE2"><font size="+5">在庫管理システム</font></span></TD>
			   </TR>
              <TR>
                <TD width=140 height=120>&nbsp;</TD>
                <TD align=middle width=312>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                    <TBODY>
                    <TR>
                      <TD align=middle width="22%" height=30><SPAN 
                        class=STYLE4>ユーザーID：</SPAN><BR></TD>
                      <TD width="42%"><INPUT id="username" size="18" name="username"></TD>
                      <TD vAlign=center align=middle width="36%" rowspan="3">
<INPUT type=image height=23 width=71 src="images/login1.gif" name=Submit onMouseOver=nereidFade(this,100,10,5) style="FILTER:alpha(opacity=50)" onMouseOut=nereidFade(this,50,10,5)> 
</TD>
                    <TR>
                      <TD align=middle height=30><SPAN 
                      class=STYLE4>パスワード：</SPAN></TD>
                      <TD><INPUT id="password" type="password" size="18"
                        name="password"></TD>
</TR>
<TR style="display:none;">
                      <TD align=middle height=30><SPAN 
                      class=STYLE4>検証コード：</SPAN></TD>
                      <TD><INPUT id=code type=text size=5 name=code>&nbsp;&nbsp;
					  <img src="include/getcode.php">
					   </TD>
</TR>
</TBODY>
</TABLE>
</TD>
                <TD width=39>&nbsp;</TD></TR>
              <TR align=middle>
                <TD colSpan=3 style="text-align:center;line-height:150%;padding:10px;">
            CopyRights &copy; 2013</TD>
              </TR></TBODY></TABLE></TD></TR>
        <TR>
          <TD>&nbsp;</TD>
          <TD>
		  </TD>
		  </TR></TBODY></TABLE></TD></TR></TBODY></TABLE>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TBODY>
  <TR>
   <TD height=4></TD>
  </TR>
</TBODY>
</TABLE>
<input type="hidden" name="action" value="login">
</FORM>
<?php
}
?>
</BODY>
</HTML>
