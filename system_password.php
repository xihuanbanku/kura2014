<?php
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
if ($action == 'save') {
    if ($id == '') {
        ShowMsg('引数不正', '-1');
        exit();
    }
    if ($password == '') {
        showmsg('現在のパスワードを入力してください。', '-1');
        exit();
    }
    if ($password1 != $password2) {
        showmsg('入力されたパスワードが一致しません。', '-1');
        exit();
    }
    $equery = "select * from #@__boss where password='" . md5($password) . "' and id='$id'";
    $esql = new dedesql(false);
    $esql->setquery($equery);
    $esql->execute();
    $allrow = $esql->gettotalrow();
    if ($allrow == 0) {
        showmsg('現在のパスワードが正しくありません。', '-1');
        exit();
    }
    $row = $esql->getone();
    $addsql = "update #@__boss set password='" . md5($password1) . "' where id='$id'";
    $message = "担当者" . $row['boss'] . "さんのパスワードが変更されました。";
    $loginip = getip();
    $logindate = getdatetimemk(time());
    $username = GetCookie('VioomaUserID');
    $esql->ExecuteNoneQuery($addsql);
    WriteNote($message, $logindate, $loginip, $username);
    $esql->close();
    showmsg('パスワードを変更しました。', 'main.php');
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $cfg_softname;?>パスワード変更管理</title>
</head>
<body>
<?php
$esql = new Dedesql(false);
$queryboss = "select * from #@__boss where boss='" . GetCookie('VioomaUserID') . "'";
$esql->SetQuery($queryboss);
$esql->Execute();
if ($esql->GetTotalRow() == 0) {
    ShowMsg('引数エラー、もう一度実行してください。', '-1');
    exit();
}
$rs = $esql->GetOne($query);
$esql->close();
?>
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
						<td><strong>&nbsp;担当者パスワード変更</strong></td>
					</tr>
					<form action="system_password.php?action=save" method="post">
						<tr>
							<td bgcolor="#FFFFFF">
								<table width="100%" cellspacing="0" cellpadding="0" border="0"
									id="table_border">
									<tr>
										<td id="row_style">&nbsp;ユーザー名：</td>
										<td>&nbsp;<font color='red'><?php echo $rs['boss'];?></font> <input
											type="hidden" name="id" value="<?php echo $rs['id']; ?>"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;現在のパスワード：</td>
										<td>&nbsp;<input type="password" name="password"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;新しいパスワード：</td>
										<td>&nbsp;<input type="password" name="password1"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;新しいパスワードの確認：</td>
										<td>&nbsp;<input type="password" name="password2"></td>
									</tr>
									<tr>
										<td id="row_style">&nbsp;</td>
										<td>&nbsp;<input type="submit" name="submit" value=" 修正 "></td>
									</tr>
									</form>
								</table>
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
