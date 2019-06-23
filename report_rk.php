<?php
require(dirname(__FILE__)."/include/config_base.php");
require(dirname(__FILE__)."/include/config_rglobals.php");
require(dirname(__FILE__)."/include/page.php");
require_once(dirname(__FILE__)."/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<script language="javascript" src="include/calendar.js"></script>
<script language="javascript" type="text/javascript" src="My97DatePicker/WdatePicker.js?r=<?php echo rand()?>"></script>
<title>入庫レポート管理</title>
<script language=javascript>    
function preview(oper) {
    if (oper < 10){
        bdhtml=window.document.body.innerHTML;
        sprnstr="<!--startprint"+oper+"-->";
        eprnstr="<!--endprint"+oper+"-->";
        prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)+18);
        
        prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));
        window.document.body.innerHTML=prnhtml;
        window.print();
        window.document.body.innerHTML=bdhtml;
    } else {
    	window.print();
    }
}
function out_excel(sign){
    edate=document.forms[0].sday.value;
    window.open('excel_rk.php?type='+sign+'&sday='+edate+'&s_type=<?php echo $_REQUEST["s_type"]?>','','');
}
</script> 
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
      <td><strong>&nbsp;入庫レポート管理</strong>&nbsp;&nbsp;<a href="report_rk.php?type=day&s_type=<?php echo $_REQUEST["s_type"]?>">日報</a> | <a href="report_rk.php?type=week&s_type=<?php echo $_REQUEST["s_type"]?>">週報</a> | <a href="report_rk.php?type=month&s_type=<?php echo $_REQUEST["s_type"]?>">月報</a> | <a href="report_rk.php?type=year&s_type=<?php echo $_REQUEST["s_type"]?>">年報</a>&nbsp;&nbsp;<input type="button" onClick="preview(1);" value=" 印刷 "></td>
     </tr>
     <tr>
      <td bgcolor="#FFFFFF">
     <?php
	  if($type=='')$type='day';
	  switch($type){
	   case 'day':
	  ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_rk.php?action=save&type=day&s_type=<?php echo $_REQUEST["s_type"]?>" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 日報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('day')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'week':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_rk.php?action=save&type=week&s_type=<?php echo $_REQUEST["s_type"]?>" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">週を選択してください&nbsp;&nbsp;
		 <?php
		 echo "<select name='sday'>";
		 for($i=1;$i<=52;$i++){
		 if($action=='save' && $i==$sday)
		 echo "<option value='$i' selected>第{$i}週</option>";
		 else
		 echo "<option value='$i'>第{$i}週</option>";
		 }
		 echo "</select>";
		 ?>
		 &nbsp;&nbsp;<input type="submit" value=" 週報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('week')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'month':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_rk.php?action=save&type=month&s_type=<?php echo $_REQUEST["s_type"]?>" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 月報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('month')"></td>
	    </tr>
       </form>
	   <?php
	   break;
	   case 'year':
	   ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <form action="report_rk.php?action=save&type=year&s_type=<?php echo $_REQUEST["s_type"]?>" name="form1" method="post">
	    <tr height="40">
		 <td id="row_style" colspan="10">日付を選択してください&nbsp;&nbsp;<input type="text" name="sday" class="Wdate" onClick="WdatePicker()" value="<?php echo ($action=='save')?$sday:GetDateMk(time());?>">&nbsp;&nbsp;<input type="submit" value=" 年報レビュー ">&nbsp;<input type="button" value=" Excel出力 " onclick="out_excel('year')"></td>
	    </tr>
       </form>
		<?php
	   break;
	   case 'other':
	   ?>
	   <table width="100%" cellspacing="0" cellpadding="0" border="0" id="table_border">
	   <?php
	   break;
	   }
		if($action=='save'){
        $row=new dedesql(false);
		$plist=new datalist();
		$plist->pageSize = $cfg_record;
		
		switch($type){
    		case "day":
                $query="select group_concat(c.l_name) gc_l_name,
                    group_concat(a.number) gc_number, 
                    group_concat(concat(d.l_floor,'-', d.l_shelf,'-', d.l_zone,'-', d.l_horizontal,'-', d.l_vertical)) gc_pos,
                    a.productid, 
                    b.cp_name,
                    b.cp_title,
                    a.rdh,
                    a.rk_price,
                    sum(a.number) s_number,
                    d.number dn
                from #@__kc a,#@__basic b , #@__lab c , #@__mainkc d 
                where to_days(a.dtime)=to_days('$sday') 
                and a.productid=b.cp_number 
                and a.labid = c.id 
                and a.productid = d.p_id
                and a.s_type = $s_type
                group by a.rdh, a.productid
                order by a.rdh";
                $query1="select sum(a.number * a.rk_price) s_np, sum(a.number) s_n from #@__kc a where to_days(a.dtime)=to_days('$sday') ";
                $report_title="入庫レポート日報";
                break;
            case "week":
                $query="select * 
                from #@__kc,#@__basic 
                where week(#@__kc.dtime)='$sday' 
                and #@__kc.productid=#@__basic.cp_number
                order by #@__kc.rdh";
                $query1="select * from #@__kc,#@__basic where week(#@__kc.dtime)='$sday' and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $report_title="入庫レポート週報";
                break;
            case "month":
                $query="select * from #@__kc,#@__basic where YEAR(#@__kc.dtime)=YEAR('$sday') and month(#@__kc.dtime)=month('$sday') and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $query1="select * from #@__kc,#@__basic where YEAR(#@__kc.dtime)=YEAR('$sday') and month(#@__kc.dtime)=month('$sday') and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $report_title="入庫レポート月報";
                break;
            case "year":
                $query="select * from #@__kc,#@__basic where YEAR(#@__kc.dtime)=YEAR('$sday') and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $query1="select * from #@__kc,#@__basic where year(#@__kc.dtime)=year('$sday') and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $report_title="入庫レポート年報";
                break;
            case "other":
                $query="select * from #@__kc,#@__basic where #@__kc.rdh='$sday' and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $query1="select * from #@__kc,#@__basic where #@__kc.rdh='$sday' and #@__kc.productid=#@__basic.cp_number order by #@__kc.rdh";
                $report_title="購買入庫レポート";
                break;
        }
        $p_name=GetCookie('VioomaUserID');
        $p_date=GetDateMk(time());
        $dh=$sday;
        $rad=$row->getone("select r_people from #@__reportrk where r_dh='$sday'");
        $p_adid=$rad['r_people'];
        $row->setquery($query1);
        $row->execute();
        while($rs=$row->getArray()){
            $allmoney=$rs['s_np'];
            $alln=$rs['s_n'];
        }
        $row->close();
        $plist->SetParameter("type",$type);
        $plist->SetParameter("action",$action);
        $plist->SetParameter("sday",$sday);
        $plist->SetSource($query);
        $p_rtitle= "<tr class='row_report_head'>
                        <td>品番</td>
                        <td>名称</td>
                        <td>タイトル</td>
                        <td>入庫表番号</td>
                        <td>倉庫</td>
                        <td>在庫位置</td>
                        <td>在庫数</td>
                        <td>入庫数</td>
                        <td>金額</td>
                    </tr>";
        $mylist = $plist->GetDataList();
        while($row = $mylist->GetArray('dm')){
    	   $n+=$row['s_number'];
    	   $money+=$row['s_number']*$row['rk_price'];
    	   $p_string=$p_string."<tr onMouseMove=\"javascript:this.bgColor='#EBF1F6';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
    	   <td>".$row['productid']."</td>
    	   <td>&nbsp;".$row['cp_name']."</td>
    	   <td width=\"45%\">".$row['cp_title']."&nbsp;</td>
    	   <td width=\"15%\">".$row['rdh']."&nbsp;</td>
    	   <td>".$row['gc_l_name']."</td>
    	   <td>".$row['gc_pos']."</td>
    	   <td>".$row['dn']."</td>
    	   <td align=\"right\">".$row['gc_number']."</td>
    	   <td align=\"right\">￥".$row['s_number']*$row['rk_price']."</td>
    	   </tr>";
        }
    	   $p_string="<table width='100%' id='report_table' border='1' cellspacing='0' cellpadding='0'>". $p_rtitle .$p_string. "<tr>\r\n<td>&nbsp;&nbsp;小　計：</td><td colspan='6'>&nbsp;</td><td colspan='1' align=\"right\">数量：".$n."</td><td colspan='2' align=\"right\">金額：￥".$money."</td>\r\n</tr>\r\n
    	   <tr><td>&nbsp;&nbsp;合　計：</td><td colspan='6'>&nbsp;</td><td align=\"right\" colspan='1'>数量：".$alln."</td><td align=\"right\" colspan='1'>金額：￥".number_format($allmoney,2,'.',',')."</td></tr>
    	   </table>";	
    	   $p_pagestring=$plist->GetPageList($cfg_record);
		}
		?>
	   </table><?php if($action=='save'){?>
	   <table width="100%" cellspacing="0" cellpadding="0">
	    <tr>
		 <td>
   	  <!--startprint1-->
<?php
if($type=='other') 
require(dirname(__FILE__)."/templets/t_rk_single.html");
else
require(dirname(__FILE__)."/templets/t_rk.html");
?>
	   <!--endprint1-->
	     </td>
		</tr>
	   </table>
	   <?php } ?>
	  </td>
     </tr>
    </table>
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
