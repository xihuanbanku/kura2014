<?php
require(dirname(__FILE__)."/include/config_base.php");
$message=GetCookie('VioomaUserID').'さんがログアウトしました。';
$outtime=GetDatetimeMk(time());
$theip=getip();
WriteNote($message,$outtime,$theip,GetCookie('VioomaUserID'));
DropCookie('VioomaUserID');
?>