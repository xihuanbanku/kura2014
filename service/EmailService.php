<?php
ini_set("SMTP", "smtp.sina.com");
ini_set("smtp_port", "25");

$to = "info@tousho.co.jp";
if(isset($_POST["name"]) && !empty($_POST["name"])) {
    $name = $_POST["name"];
} else {
    echo "请输入お名前";
    exit();
}
if(isset($_POST["subject"]) && !empty($_POST["subject"])) {
    $subject = $_POST["subject"];
} else {
    echo "请输入タイトル";
    exit();
}
if(isset($_POST["message"]) && !empty($_POST["message"])) {
    $message  = nl2br($_POST["message"]);
} else {
    echo "请输入メッセージ";
    exit();
}
if(isset($_POST["from"]) && !empty($_POST["from"])) {
    $from  = $_POST["from"];
} else {
    echo "请输入ール";
    exit();
}
//$to="lianghaikun@sina.com";
//$subject = "Test mail";
//$message = "こんにちは, 桜井　直人. Hello! This is a simple email message sent from China. Please DO NOT reply. <br/>梁";
//$from = "someonelse@example.com";
error_log(date("Ymd-H:i:s")."----[EmailService][".$name."][".$from."][".$to."][".$subject."][".$message."]\n", 3, dirname(__FILE__)."/../logs/sql".date("Ymd").".log");

$content .= "下記のお問い合わせを受信いたしました<br/>
    [お名前]<br/>".
    $name."<br/>
    [ご連絡先メールアドレス]<br/>".
    $from."<br/>
    [お問合せ内容]<br/>".
    $message;

$headers = "From: {$from}\r\n";
$headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
mail($to,$subject,$content,$headers);
header("Content-Type:text/html;charset=utf-8");
echo "<script type=\"text/javascript\">alert('送信しました'); window.history.go(-1);window.location.reload();</script>";

?>