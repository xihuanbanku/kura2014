<?php
/*
 * File: mailControl.php
 * Description: Received Mail Example
 * Created: 03-06-2015
 * Author: Sara Zhou
 */
@header('Content-type: text/html;charset=UTF-8');
// error_reporting(0);
ignore_user_abort(); // run script in background
set_time_limit(0); // run script forever
date_default_timezone_set('Asia/Shanghai');
include("receivemail.class.php");

class mailControl {
    //定义系统常量
	//用户名
	public $mailAccount = "test@p-mon.jp";
	public $mailPasswd = "tes001001";
	public $mailAddress = "test@p-mon.jp";
	public $mailServer = "p-mon.xsrv.jp";
	public $serverType = "pop3";
	public $port = "995";
	public $ssl = true;
	public $now       = 0;
	public $savePath  = 'upload/';
	public $webPath   = "../upload/";
	
	public function __construct()
	{
		$this->now = date("Y-m-d H:i:s",time());
		$this->setSavePath();
	}
	
	/**
	 * mail Received()读取收件箱邮件
	 *
	 * @param
	 * @access public
	 * @return result
	 */
	public function mailReceived()
	{
		// Creating a object of reciveMail Class
		$obj= new receivemail($this->mailAccount,$this->mailPasswd,$this->mailAddress,$this->mailServer,$this->serverType,$this->port,$this->ssl);
		 
		//Connect to the Mail Box
		$res=$obj->connect();         //If connection fails give error message and exit
		if (!$res)
		{
			return array("msg"=>"Error: Connecting to mail server");
		}
		// Get Total Number of Unread Email in mail box
		$tot=$obj->getTotalMails(); //Total Mails in Inbox Return integer value
		if($tot < 1) { //如果信件数为0,显示信息
			return array("msg"=>"No Message for ".$this->mailAccount);
		}
		else
		{
			$res=array("msg"=>"Total Mails:: $tot<br>");
	
			for($i=$tot;$i>0;$i--)
			{
				$head=$obj->getHeaders($i);  // Get Header Info Return Array Of Headers **Array Keys are (subject,to,toOth,toNameOth,from,fromName)
		
				//处理邮件附件
				$files=$obj->GetAttach($i,$this->savePath); // 获取邮件附件，返回的邮件附件信息数组
				
				$imageList=array();
				foreach($files as $k => $file)
				{			
					//type=1为附件,0为邮件内容图片
					if($file['type'] == 0)
					{
						$imageList[$file['title']]=$file['pathname'];
					}
				}
				$body = $obj->getBody($i,$this->webPath,$imageList);
				
				$res['mail'][]=array('head'=>$head,'body'=>$body,"attachList"=>$files);			 
// 				$obj->deleteMails($i); // Delete Mail from Mail box
        		$obj->move_mails($i,"testtest");
			}
			$obj->close_mailbox();   //Close Mail Box
			return $res;
		}
	}
	 
	/**
	* creatBox
	*
	* @access public
	* @return void
	*/
	public function creatBox($boxName)
	{
		// Creating a object of reciveMail Class
		$obj= new receivemail($this->mailAccount,$this->mailPasswd,$this->mailAddress,$this->mailServer,$this->serverType,$this->port, $this->ssl);
		$obj->creat_mailbox($boxName);
	}
	
	/**
	 * Set save path.
	 *
	 * @access public
	 * @return void
	 */
	public function setSavePath()
	{
		$savePath = "../upload/" ;
		if(!file_exists($savePath))
		{
			@mkdir($savePath, 0777, true);
			touch($savePath . 'index.html');
		}
		$this->savePath = $savePath;
	}
}
 	$obj=new mailControl();
 	//收取邮件
	$res=$obj->mailReceived();
    echo "<pre>";
	print_r($res);
	
	//创建邮箱
//  $obj->creatBox("readyBox");
?>