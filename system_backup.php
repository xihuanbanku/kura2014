<?php
require (dirname(__FILE__) . "/include/config_base.php");
require (dirname(__FILE__) . "/include/fix_mysql.inc.php");
require (dirname(__FILE__) . "/include/config_rglobals.php");
require (dirname(__FILE__) . "/include/page.php");
require_once (dirname(__FILE__) . "/include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $cfg_softname;?>数据库备份&还原</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style/main.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function databaseBackup() {
	var url = "system_backup.php?flag=databaseBackup&nocache="+new Date().getTime();
    //window.open('excel_kc.php?shop='+shop+'&cp_categories='+cp+'&cp_categories_down='+cp_down+'&sort='+s+'&stext='+st,'','');
	window.location.href=url;
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
			<td align="center">
				<table id="barcodes" width="30%" border="0"
					style="text-align: center;">
				</table>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="2">
					<tr>
						<td><strong>数据库备份&还原</strong></td>
					</tr>
                    <tr bgcolor="#FFEEFF" >
                         <td align="left">
                            <form id="form1" name="form1" method="post" action="">
                            	【数据库SQL文件】：<input id="sqlFile" name="sqlFile" type="file" /> 
                            	<input id="submit" name="submit" type="submit" value="还原(!!!慎用!!!)" />
                            </form>
                        </td>
					</tr>
                    <tr bgcolor="#FFEEFF" >
					   <td>
    					   <button onclick="databaseBackup()">数据库备份</button>
                        </td>
					</tr>
					</table>
			</td>
			<td></td>
		</tr>
		<tr>
			<td id="table_style" class="l_b">&nbsp;</td>
			<td><div id="Pagination" ></div><div id="totalPage"></div></td>
			<td id="table_style" class="r_b">&nbsp;</td>
		</tr>
	</table>
</body>     
</html>
<?php
// 数据库信息都存放到config.php文件中，所以加载此文件，如果你的不是存放到该文件中，注释此行即可；
require(dirname(__FILE__)."/include/config_base.php");
if ( isset ( $_POST['sqlFile'])) {
    $file_name = $_POST['sqlFile']; // 要导入的SQL文件名
    $dbhost = $cfg_dbhost; // 数据库主机名
    $dbuser = $cfg_dbuser; // 数据库用户名
    $dbpass = $cfg_dbpwd; // 数据库密码
    $dbname = $cfg_dbname; // 数据库名
    
    set_time_limit(0); // 设置超时时间为0，表示一直执行。当php在safe mode模式下无效，此时可能会导致导入超时，此时需要分段导入
    $fp = @fopen($file_name, "r") or die("不能打开SQL文件 $file_name"); // 打开文件
    mysql_connect($dbhost, $dbuser, $dbpass) or die("不能连接数据库 $dbhost"); // 连接数据库
    mysql_select_db($dbname) or die("不能打开数据库 $dbname"); // 打开数据库
    
    echo "<p>正在清空数据库,请稍等....<br>";
    $result = mysql_query("SHOW tables");
    while ($currow = mysql_fetch_array($result)) {
        mysql_query("drop TABLE IF EXISTS $currow[0]");
        echo "清空数据表【" . $currow[0] . "】成功！<br>";
    }
    echo "<br>恭喜你清理MYSQL成功<br>";
    
    echo "正在执行导入数据库操作<br>";
    // 导入数据库的MySQL命令
    exec("mysql -u$cfg_dbuser -p$cfg_dbpwd $cfg_dbname < " . $file_name);
    echo "<br>导入完成！";
    mysql_close();
} else if( isset ( $_REQUEST['flag'])) {
        date_default_timezone_set('PRC');
        // 设置SQL文件保存文件名
        $filename=date("Y-m-d_H-i-s")."-".$cfg_dbname.".sql";
        // 获取当前页面文件路径，SQL文件就导出到此文件夹内
        $tmpFile = (dirname(__FILE__))."/dump/".$filename;
        // 用MySQLDump命令导出数据库
        exec("mysqldump -h$cfg_dbhost -u$cfg_dbuser -p$cfg_dbpwd  --default-character-set=utf8 $cfg_dbname > ".$tmpFile);
        echo("备份成功!<br/><a href='dump/".$filename."'>Download ".$filename."</a>右クリックは、(として保存します)");
}
?> 