<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
</head>
<body oncontextmenu="return false">
<?php
if($action=='del'){
    $dsql=New Dedesql(false);
    $delstring="delete from #@__kc where id='$id' and tantousyaid='".GetCookie('VioomaUserID')."'";
    $dsql->executenonequery($delstring);
    $dsql->close();
//    echo "<script>alert('削除しました！');location.href='current_order.php?action=normal&did=".$rid."';</script>";
    echo "<script>location.href='current_order.php?action=normal&did=".$rid."';</script>";
}
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="2">
     <tr>
      <td><strong>&nbsp;入庫商品詳細</strong></td>
     </tr>
	 <form method="post" name="sel">
     <tr>
      <td bgcolor="#FFFFFF">
       <?php
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   if ($pid==''){
	       echo "pid ==空<br/>";
    	   if($action=='normal') {
    	       $csql->SetQuery("select * from #@__kc where rdh='$did' and tantousyaid='".GetCookie('VioomaUserID')."'");
    	   } else {
    	       $csql->SetQuery("select * from #@__kc where id<0 and tantousyaid='".GetCookie('VioomaUserID')."'");
    	   }
	   } else {
    	   if($action=='' && $did!=''){
	           echo "action ==空<br/>";
    	   
    	       if($rk_price=='' || (!is_numeric($rk_price))) {
    	           ShowMsg("仕入単価が正しくない","-1");
    	       }
                $wsql=New Dedesql(false);
                $chksql = "select * from #@__kc where productid='$pid' and rdh='$did' and labid='$lid' and labfloor='$floor' and labshelf='$shelf'"
                        . " and labzone='$zone' and labhorizontal='$horizontal' and labvertical='$vertical' and tantousyaid='".GetCookie('VioomaUserID')."'";
                $csql->Setquery($chksql);
                $csql->Execute();
                $count = $csql->GetTotalRow();
                
                if ($count > 0) {
                    $wsql->ExecuteNoneQuery("update #@__kc set number=number+'$num', dtime='".GetDateTimeMk(time())."'"
                            . " where productid='$pid' and rdh='$did' and labid='$lid' and labfloor='$floor' and labshelf='$shelf'"
                            . " and labzone='$zone' and labhorizontal='$horizontal' and labvertical='$vertical' "
                            . "and tantousyaid='".GetCookie('VioomaUserID')."'");
                } else {
                    $writesql="select * from #@__basic where cp_number='$pid'";
                    $wsql->Setquery($writesql);
                    $wsql->Execute();
                    $wrs=$wsql->GetOne();
                    $wsql->ExecuteNoneQuery("insert into #@__kc(productid,number,labid,labfloor,labshelf,labzone,labhorizontal,labvertical,rdh,rk_price,bank,bz,dtime,tantousyaid) "
                            . "values('$pid','$num','$lid','$floor','$shelf','$zone','$horizontal','$vertical','$did','$rk_price','$bank','$bz','".GetDateTimeMk(time())."','".GetCookie('VioomaUserID')."')");
                }
                $wsql->close();
    	   }
    	   $csql->SetQuery("select * from #@__kc where rdh='$did' and tantousyaid='".GetCookie('VioomaUserID')."'");
	   }
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0) {
	       echo "<tr><td>&nbsp;</td></tr>";
	   } else {
    	   echo "<tr class='row_color_head'>
    	   <td>商品コード</td>
    	   <td>メーカー・商品名</td>
    	   <td>タイトル</td>
    	   <td>倉庫</td>
    	   <td>入庫位置<br>階 - 棚 - ゾーン - 横 - 縦</td>
    	   <td>入庫数</td>
    	   <td>削除</td>
    	   </tr>";
    	   while($row=$csql->GetArray()) {
        	   $nsql=New dedesql(false);
        	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
        	   $nsql->setquery($query1);
        	   $nsql->execute();
        	   $row1=$nsql->getone();
        	   $amoney+=$row['rk_price']*$row['number'];
        	   $anum+=$row['number'];
        	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
        	   <td>".$row['productid']."</td>
        	   <td>&nbsp;".$row1['cp_name']."</td>
        	   <td width=\"55%\">".$row1['cp_title']."</td>
                   <td><center>&nbsp;".get_name($row['labid'],'lab')."</td>
        	   <td align=\"center\">".$row['labfloor']."-".$row['labshelf']."-".$row['labzone']."-".$row['labhorizontal']."-".$row['labvertical']."</td>
        	   <td>".$row['number']."</td>
        	   <td><a href='current_order.php?action=del&id=".$row['id']."&rid=".$row['rdh']."'>削除</a></td>
        	   </tr>";
        	   $nsql->close();
    	   }
	   }
	   echo "<tr>
	   <td>&nbsp;合計：</td>
	   <td></td><td></td><td></td>
	   <td>&nbsp;金額：</td>
	   <td>￥".$amoney."</td>
	   <td align='right'>数量：</td>
	   <td>".$anum."</td>
	   </tr>
	   </table>";
	   $csql->close();
	   ?>
	  </td>
     </tr></form>
    </table>
</body>
</html>
