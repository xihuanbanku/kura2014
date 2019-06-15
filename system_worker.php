<?php
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/fix_mysql.inc.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
require_once (dirname(__FILE__) . "/include/checklogin.php");
if ($action == 'save') {
    if ($s_no == '') {
        ShowMsg('社員IDを入力してください。', '-1');
        exit();
    }
    if ($s_name == '') {
        ShowMsg('社員名を入力してください。', '-1');
        exit();
    }
    $addsql = "insert into #@__staff(s_no,s_name,s_address,s_phone,s_part,s_way,s_money,password) values('$s_no','$s_name','$s_address','$s_phone','$s_part','$s_way','$s_money','$password1')";
    $message = "社員" . $s_name . "さんを追加しました。";
    $password1 = md5($password);
    $loginip = getip();
    $logindate = getdatetimemk(time());
    $username = GetCookie('VioomaUserID');
    $asql = new Dedesql(false);
    $rs = $asql->ExecuteNoneQuery($addsql);
    if (! $rs) {
        showmsg('エラー：' . $asql->getError(), '-1');
        exit();
    }
//     $rs1 = $asql->ExecuteNoneQuery($addsql2);
//     if (! $rs1) {
//         showmsg('エラー：' . $asql->getError(), '-1');
//         exit();
//     }
    WriteNote($message, $logindate, $loginip, $username);
    showmsg('社員を追加しました。', 'system_worker.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfs_softname;?>社員管理</title>
<script language="javascript">
function cway(value){
if(value==0)
document.forms[0].s_e.value="%";
else
document.forms[0].s_e.value="円/件";
}
function getinfo(){
window.open('part_list.php?form=form1&field=s_part','selected','directorys=no,toolbar=no,status=no,menubar=no,resizable=no,width=500,height=500,top=100,left=320');
}
</script>
</head>
<body>
	<table width="100%" border="0" id="table_style_all" cellpadding="0"
		cellspacing="0">
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
						<td><strong>&nbsp;社員管理</strong>&nbsp;&nbsp;<a
							href="system_worker.php?action=new">社員追加</a> | <a
							href="system_worker.php">社員一覧</a></td>
					</tr>
					<form action="system_worker.php?action=save" method="post"
						name="form1">
						<tr>
							<td bgcolor="#FFFFFF">
	  <?php if($action=='new'){ ?>
       <table width="100%" cellspacing="0" cellpadding="0" border="0"
									id="table_border">
									<tr>
										<td id="row_style">&nbsp;社員ID/登録ID：</td>
										<td>&nbsp;<input type="text" name="s_no" size="10" id="need"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;名前：</td>
										<td>&nbsp;<input type="text" name="s_name" size="20" id="need"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;パスワード：</td>
										<td>&nbsp;<input type="password" name="password" size="12"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;住所：</td>
										<td>&nbsp;<input type="text" name="s_address" size="30"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;電話番号：</td>
										<td>&nbsp;<input type="text" name="s_phone" size="15"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;所属：</td>
										<td>&nbsp;<input type="text" name="s_part" size="20">&nbsp;<input
												type="button" value="部門選択" onclick="getinfo()"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;職務/グループ：</td>
										<td>
		 &nbsp;<?php getusertype('',0);?></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;割戻方法：</td>
										<td>
		 <?php
    if ($cfg_way == '1') {
        ?>
		 &nbsp;<select name="s_way" onchange="cway(this.value)"><option
													value="0">売上総額の割合</option>
												<option value="1">固定(件数より)</select>
		 <?php
    } else
        echo "&nbsp;割戻機能が禁止されている。";
    ?>
		 </td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;割合(ブランクは割戻なし):</td>
										<td>
		 <?php
    if ($cfg_way == '1') {
        ?>
		 &nbsp;<input type="text" name="s_money" size="5" value="0"> <input
												type="text" name="s_e" size="5"
												style="border: 0px; background: transparent;" value="%"
												readonly>
		 <?php
    } else
        echo "&nbsp;";
    ?>
		 
										
										</td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;</td>
										<td>&nbsp;<input type="submit" name="submit" value=" 社員追加 "></td>
									</tr>
									</form>
								</table>
	   <?php
} else {
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" id=\"table_border\" style=\"text-align:center;\">";
    $csql = new Dedesql(false);
    $csql->SetQuery("select a.*, b.typename from #@__staff a, #@__usertype b where a.s_utype=b.rank ");
    $csql->Execute();
    $rowcount = $csql->GetTotalRow();
    if ($rowcount == 0)
        echo "<tr><td>&nbsp;社員が存在しません、追加してください。<a href=system_worker.php?action=new>社員追加</a>。</td></tr>";
    else {
        echo "<tr class='row_color_head'>
	   <td>社員ID/登録ID</td>
	   <td>名前</td>
	   <td>住所</td>
	   <td>電話番号</td>
	   <td>部門</td>
	   <td>職務</td>
	   <td>最后一次登录时间</td>
	   <td>最后一次登录IP</td>
	   <td>操作</td>
	   </tr>";
        while ($row = $csql->GetArray()) {
            echo "<tr>
	   <td>" . $row['s_no'] . "</td>
	   <td>" . $row['s_name'] . "</td>
	   <td>" . $row['s_address'] . "</td>
	   <td>" . $row['s_phone'] . "</td>
	   <td>" . $row['s_part'] . "</td>
	   <td>" . $row['typename'] . "</td>
	   <td>" . $row['logindate'] . "</td>
	   <td>" . $row['loginip'] . "</td>
	   <td><center><a href=system_worker_edit.php?id=" . $row['id'] . ">修正</a> | <a href=system_worker_del.php?id=" . $row['id'] . "&buser=" . $row['s_name'] . ">削除</a></td>
	   </tr>";
        }
    }
	   echo "</table>";
	  
	   $csql->close();
}
	   ?>
	  </td>
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
<?php
copyright();
?>
</body>
</html>
