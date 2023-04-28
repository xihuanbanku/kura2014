<?php
//Session曐懚
$sessSavePath = dirname(__FILE__)."/../data/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }
session_start();//
function getrandom ($length,$mode)
{ 
switch ($mode)
{ 
case '1': 
$str = '1234567890'; 
break; 
case '2': 
$str = 'abcdefghijklmnopqrstuvwxyz';
break;
case '3': 
$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
break;
case '4': 
$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
break;
case '5': 
$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
break;
case '6': 
$str = 'abcdefghijklmnopqrstuvwxyz1234567890';
break;
default: 
$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890'; 
break; 
} 
$result = ''; 
$l = strlen($str);
for($i = 0;$i < $length;$i++)
{
$num = rand(0, $l-1); 
$result .= $str[$num]; 
}
return $result;
}
if(function_exists("imagecreate"))
{
mt_srand((double)microtime()*1000000);
$mode = 1;//mt_rand(1,5);
$text=getrandom(4,$mode);
$_SESSION["v_ckstr"] = strtolower($text);

Header("Content-type: image/PNG");
$im=imagecreate(50,20);
$black = ImageColorAllocate($im, 0,0,0);
$white = ImageColorAllocate($im, 255,255,255);
$gray = ImageColorAllocate($im, 200,200,200); 
imagefill($im,0,0,$white);

imagestring($im, 6, 10, 3, $text, $black);

for($i=0;$i<200;$i++)
{ 
$randcolor = ImageColorallocate($im,rand(0,255),rand(0,255),rand(0,255));
imagesetpixel($im, rand()%70 , rand()%30 , $randcolor); 
} 

imagepng($im); 
imagedestroy($im);
}
else
{
	//PutCookie("dd_ckstr","abcd",1800,"/");
	$_SESSION['v_ckstr'] = "abcd";
	header("content-type:image/jpeg\r\n");
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	$fp = fopen("./vdcode.jpg","r");
	echo fread($fp,filesize("./vdcode.jpg"));
	fclose($fp);
}
?>