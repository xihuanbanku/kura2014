<?php
require(dirname(__FILE__)."/../include/config_rglobals.php");
require(dirname(__FILE__)."/../include/config_base.php");
require_once(dirname(__FILE__)."/../include/checklogin.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>凭证</title>
</head>
<body>
<?php 
$ref=$_SERVER['HTTP_REFERER'];
if($ref==''){
    echo '403 Fobbiden,无法访问';
    exit();
}
if(!strpos($ref,"tousho.co.jp")){
    echo '403 Fobbiden,无法访问';
    exit();
}
$picture_name = $_REQUEST["p"];
$data = file_get_contents('/home/p-mon/tousho.co.jp/upload/finance/'.$picture_name);
// header('content-type:image/jpeg');
// echo $data;
?>
<img src="data:image/bmp;base64,<?php echo base64_encode($data)?>"  />
</body>
</html>
