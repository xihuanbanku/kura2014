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
$delstring="delete from #@__sale where id='$id' and tantousyaid='".GetCookie('VioomaUserID')."'";
$dsql->executenonequery($delstring);
$dsql->close();
//echo "<script>alert('削除しました。');location.href='current_order_sale.php?action=normal&did=".$rid."';</script>";
echo "<script>location.href='current_order_sale.php?action=normal&did=".$rid."';</script>";
}
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="2">
     <tr>
      <td><strong style="color:#0000FF">&nbsp;販売商品詳細</strong></td>
     </tr>
	 <form method="post" name="sel">
     <tr>
      <td bgcolor="#FFFFFF">
       <?php
       echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\">";
       $csql=New Dedesql(false);
	   if ($pid==''){
	   if($action=='normal')
	   $csql->SetQuery("select * from #@__sale where rdh='$did' and tantousyaid='".GetCookie('VioomaUserID')."'");
	   else
	   $csql->SetQuery("select * from #@__sale where id<0 and tantousyaid='".GetCookie('VioomaUserID')."'");
	   }
	   else{
                if($action=='' && $did!=''){
                    $wsql=New Dedesql(false);
                    $chksql = "select * from #@__sale where productid='$pid' and rdh='$did' and salelab='$labid' and labfloor='$floor' and labshelf='$shelf'"
                        . " and labzone='$zone' and labhorizontal='$horizontal' and labvertical='$vertical' and tantousyaid='".GetCookie('VioomaUserID')."'";
                    $csql->Setquery($chksql);
                    $csql->Execute();
                    $count = $csql->GetTotalRow();
                    if ($count > 0) {
                        $wsql->ExecuteNoneQuery("update #@__sale set number=number+'$num', dtime='".GetDateTimeMk(time())."'"
                            . " where productid='$pid' and rdh='$did' and salelab='$labid' and labfloor='$floor' and labshelf='$shelf'"
                            . " and labzone='$zone' and labhorizontal='$horizontal' and labvertical='$vertical' and tantousyaid='".GetCookie('VioomaUserID')."'");
                    } else {
                        $writesql="select * from #@__basic where cp_number='$pid'";
                        $wsql->Setquery($writesql);
                        $wsql->Execute();
                        $wrs=$wsql->GetOne();
                        if($sale<$wrs['cp_jj']){
                            Showmsg("販売単価は仕入単価より安くなることができません。",-1);
                            exit();
                        }
                        $del_flag = 1;
                        if($is_report == 1) {
                            $del_flag = 0;
                        }
                        $wsql->ExecuteNoneQuery("insert into #@__sale(productid,sale,salelab,number,rdh,member,dtime,labfloor,labshelf,labzone,labhorizontal,labvertical,tantousyaid,del_flag) "
                                . "values('".$pid."','".$sale."','".$labid."','".$num."','".$did."','".$member."','".GetDateTimeMk(time())."',"
                                . "'".$floor."','".$shelf."','".$zone."','".$horizontal."','".$vertical."','".GetCookie('VioomaUserID')."', '".$del_flag."')");
                    }
                    $wsql->close();
                }
	   $csql->SetQuery("select * from #@__sale where rdh='$did' and tantousyaid='".GetCookie('VioomaUserID')."'");
	   }
	   $csql->Execute();
	   $rowcount=$csql->GetTotalRow();
	   if($rowcount==0)
	   echo "<tr><td>&nbsp;</td></tr>";
	   else{
	   echo "<tr class='row_color_head'>
	   <td>商品コード</td>
	   <td>メーカー・商品名</td>
	   <td>タイトル</td>
	   <td>倉庫</td>
           <td>在庫位置</td>
	   <td>販売単価</td>
	   <td>販売数</td>
	   <td>削除</td>
	   </tr>";
	   while($row=$csql->GetArray()){
	   $amoney+=$row['sale']*$row['number'];
	   $anum+=$row['number'];
	   $nsql=New dedesql(false);
	   $query1="select * from #@__basic where cp_number='".$row['productid']."'";
	   $nsql->setquery($query1);
	   $nsql->execute();
	   $row1=$nsql->getone();
	   echo "<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
	   <td><center>".$row['productid']."</td>
	   <td><center>&nbsp;".$row1['cp_name']."</td>
	   <td width=\"50%\"><center>".$row1['cp_title']."</td>
	   <td><center>".get_name($row['salelab'],'lab')."</td>
           <td align=\"center\">".$row['labfloor']."-".$row['labshelf']."-".$row['labzone']."-".$row['labhorizontal']."-".$row['labvertical']."</td>
	   <td><center>￥".$row['sale']."</td>
	   <td><center>".$row['number']."</td>
	   <td><center><a href='current_order_sale.php?action=del&id=".$row['id']."&rid=".$row['rdh']."'>削除</a></td>
	   </tr>";
	   $nsql->close();
	   }
	   }
	   echo "<tr><td>&nbsp;合計：</td><td></td><td></td><td></td><td>&nbsp;金額：</td><td>￥".$amoney."</td><td align='right'>数量：</td><td>".$anum."</td></tr></table>";
	   $csql->close();
	   ?>
	  </td>
     </tr></form>
    </table>
</body>
</html>
