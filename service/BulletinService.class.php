<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set("PRC");
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "sendBulletin":
            $result = sendBulletin();
        break;
        case "initReceiver":
            $result = initReceiver();
        break;
        case "getContent":
            $result = getContent(0);
        break;
        case "getContentPage":
            $result = getContent(1);
        break;
        case "chgState":
            $result = chgState();
        break;
        case "initMine":
            $result = initMine();
        break;
        case "init":
            $result = initPage();
        break;
        case "replySubmit":
            $result = replySubmit();
        break;
        case "chgRemindMe":
            $result = chgRemindMe();
        break;
        case "initMineNote":
            $result = initMineNote();
        break;
        
        case "updateMineNote":
            $result = updateMineNote();
        break;
        case "initMsgCollect":
            $result = initMsgCollect();
        break;
        case "msgSubmit":
            $result = msgSubmit();
        break;
        case "cleanMsgCollect":
            $result = cleanMsgCollect();
        break;
        
        default:
            return "error";
        break;
    }
    echo $result;  

    

    function initMineNote() {
        $userID = trim($_REQUEST["userID"]);
    
        $newsql = new ezSQL_mysql();
        $sql = "select subject, content from jxc_bulletin where id='".$userID."'";
        $result = $newsql->get_results($sql);
        return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    function updateMineNote() {
        $userID = trim($_REQUEST["userID"]);
        $p_subject = trim($_REQUEST["note_title"]);
        $p_content = trim($_REQUEST["note_content"]);
    
        $newsql = new ezSQL_mysql();
        if(!empty($p_subject)) {
            $sql = "update jxc_bulletin set subject = '".$p_subject."' where id='".$userID."'";
            $result = $newsql->query($sql);
        }
        if(!empty($p_content)) {
            $sql = "update jxc_bulletin set  content = '".$p_content."' where id='".$userID."'";
            $result = $newsql->query($sql);
        }
        return $result;
    }
    
    function initMine() {
        $userID = trim($_REQUEST["userID"]);
        $ksql = new ezSQL_mysql();
        $query = "select `subject`, CASE
                    WHEN now() > ifnull(a.remind_me, ADDDATE(dtime,INTERVAL 4 HOUR)) THEN
                    	3
                    WHEN is_public = 1 THEN
                    	1
                    WHEN is_public = 2 THEN
                    	2
                    ELSE
                    	1
                    END is_public, content, ifnull(a.remind_me, ADDDATE(dtime, INTERVAL 4 hour)) ord 
					from jxc_bulletin a where a.flag = 0 and  a.receiver = ".$userID."
                     union 
                    select l_id, 1, '', ifnull(limit_date, want_date) from jxc_luggage where status<>2 and del_flag=0
                        and now() >= ifnull(limit_date, want_date) and owner =".$userID."
                     union 
                    select l_id, 1, '', ifnull(want_date, now()) from jxc_luggage where status<>2 and del_flag=0
                        and now() >= ifnull(want_date, now()) and remark2=".$userID."
                 order by ord";
        $results = $ksql->get_results($query);
        return json_encode($results, JSON_FORCE_OBJECT);
    }
    
    function initPage($pageCount=100000, $pageIndex=1) {
        $stext = trim($_REQUEST["stext"]);
        if(isset($_REQUEST["receiver"])) {
            $receiver = $_REQUEST["receiver"];
        }
        if($_REQUEST["pageCount"]) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if($_REQUEST["pageIndex"]) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $is_public = $_REQUEST["is_public"];
        $ksql = new ezSQL_mysql();
        
        $query = "select a.`id`, b.s_name `sender`, ifnull(c.s_name, 'all') `receiver`, `subject`, `flag`, `dtime`, c.id receiver_id, case when a.passwd is not null then 1 else 0 end passwd
            from jxc_bulletin a left join jxc_staff b on a.sender = b.id left join jxc_staff c on a.receiver = c.id where a.flag != 2 ";
        if($is_public >= 0){
            $query .= " and a.is_public = ( ".$is_public.")";
        }
        if(!empty($stext)){
            $query .= " and (a.subject like '%{$stext}%' or a.content like '%{$stext}%' or b.s_name like '%{$stext}%' or c.s_name like '%{$stext}%')";
        }
        if(!empty($receiver)){
            $query .= " and a.receiver = '{$receiver}'";
        }
        if(!empty($sdate)){
            $query .= " and a.dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.dtime <= '{$edate}'";
        }
        $query .= " order by a.dtime desc";
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $results = $ksql->get_results($query);

        $total =initPageCount();
        return "{\"totalcount\":".$total.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }
    
    function initPageCount() {
        $stext = trim($_REQUEST["stext"]);
        if(isset($_REQUEST["receiver"])) {
            $receiver = $_REQUEST["receiver"];
        }
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $is_public = $_REQUEST["is_public"];
        $ksql = new ezSQL_mysql();
        $query = "select count(0)
            from jxc_bulletin a where a.flag != 2";
        if($is_public >= 0){
            $query .= " and a.is_public = ( ".$is_public.")";
        }
        if(!empty($stext)){
            $query .= " and (a.subject like '%{$stext}%' or a.content like '%{$stext}%')";
        }
        if(!empty($receiver)){
            $query .= " and a.receiver = '{$receiver}'";
        }
        if(!empty($sdate)){
            $query .= " and a.dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.dtime <= '{$edate}'";
        }
        return $ksql->get_var($query);
    }
    
    function sendBulletin() {
        $userID = $_REQUEST["userID"];
        $sender_name = $_REQUEST["sender_name"];
        $subject = $_REQUEST["subject"];
        $content = $_REQUEST["content"];
        $is_public = $_REQUEST["is_public"];
        $setPass = $_REQUEST["setPass"];
        
        if($setPass) {
            $setPass = md5($setPass);
        }
        $result=0;
        $newsql = new ezSQL_mysql();
        if($is_public==1) {
            $result += $newsql->query("insert into jxc_bulletin(`sender`, `receiver`, `subject`, `content`, is_public, passwd)
                values('$userID','-1','$subject', concat('<span>', (select s_name from jxc_staff where id = {$userID}), '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$content}'), '{$is_public}', '{$setPass}')") or mysql_error();
        } else {
            $strChk = $_REQUEST["strChk"];
            foreach ($strChk as $chk) {
                $result += $newsql->query("insert into jxc_bulletin(`sender`, `receiver`, `subject`, `content`, is_public)
                    values('$userID','$chk','$subject', concat('<span>', (select s_name from jxc_staff where id = {$userID}), '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$content}'), '{$is_public}')") or mysql_error();
            }
        }
        return $result;
         
    }
    
    function initReceiver() {
        $newsql = new ezSQL_mysql();
        $sql = "select id, s_no, s_name from jxc_staff order by s_no";
        $results = $newsql->get_results($sql);
        return json_encode($results, JSON_FORCE_OBJECT);
    }
    function getContent($flag) {
        $id = $_REQUEST["id"];
        $newsql = new ezSQL_mysql();
        $checkPwd = "";
        $sql = "select a.dtime, a.subject, a.content, a.sender, a.receiver, b.s_name sender_name, c.s_name receiver_name, a.is_public, ifnull(a.remind_me, a.dtime) remind_me
             from jxc_bulletin a left join jxc_staff b on a.sender = b.id left join jxc_staff c on a.receiver = c.id where a.id = ".$id;
        if($flag==1) {
            if(isset($_REQUEST["checkPwd"])) {
                $checkPwd = $_REQUEST["checkPwd"];
            }
            $sql .= " and a.passwd = md5('{$checkPwd}')";
        }
        return json_encode($newsql->get_row($sql), JSON_FORCE_OBJECT);
    }
    function chgState() {
        $id = $_REQUEST["id"];
        $state = $_REQUEST["state"];
        $userID = $_REQUEST["userID"];
        $newsql = new ezSQL_mysql();
        switch ($state) {
            case 0:
                $state = 1;
            break;
            case 1:
                $state = 0;
            break;
            default:
                $state = 2;
            break;
        }
        $updateCount = 0;
        $sql = "update jxc_bulletin set flag = ".$state." where id = ".$id." and is_public = 1 and (sender = ".$userID." or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $updateCount += $newsql->query($sql);
        $sql = "update jxc_bulletin set flag = ".$state." where id = ".$id." and (receiver = ".$userID." or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
        $updateCount += $newsql->query($sql);
        return $updateCount;
    }
    function replySubmit() {
        $resultCount = 0;
        $id = $_REQUEST["id"];
        $replyContent = $_REQUEST["replyContent"];
        $userID = $_REQUEST["userID"];
        $sender = $_REQUEST["sender"];
        $receiver = $_REQUEST["receiver"];
        $receiver_name = $_REQUEST["receiver_name"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_bulletin set flag = 0, sender = ".$receiver.", receiver = ".$sender.", content = concat(content, '<br/><span>', '{$receiver_name}', '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$replyContent}') where is_public <> 1 and receiver={$userID} and id = ".$id;
        $resultCount += $newsql->query($sql);
        $sql = "update jxc_bulletin set flag = 0, content = concat(content, '<br/><span>', '{$receiver_name}', '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$replyContent}') where is_public = 1 and id = ".$id;
        $resultCount += $newsql->query($sql);
        return $resultCount;
    }
    function chgRemindMe() {
        $id = $_REQUEST["id"];
        $remind_me = $_REQUEST["remind_me"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_bulletin set remind_me='{$remind_me}' where id = ".$id;
        return $newsql->query($sql);
    }

    function initMsgCollect() {
        $msgType = $_REQUEST["msgType"];
        $ksql = new ezSQL_mysql();
        $query = "select sender,replace(content, '\n', '<br/>') content, dtime from jxc_msg_collect a where a.flag = 0 and a.msg_type='{$msgType}' order by dtime desc";
        $results = $ksql->get_results($query);
        return json_encode($results);
    }
    function msgSubmit() {
        $replyContent = $_REQUEST["replyContent"];
        $msgType = $_REQUEST["msgType"];
        $sender = $_REQUEST["sender"];
        $newsql = new ezSQL_mysql();
        $sql = "insert into jxc_msg_collect(sender, content, msg_type) values('{$sender}', '{$replyContent}', '{$msgType}') ";
        return $newsql->query($sql);
    }
    function cleanMsgCollect() {
        $msgType = $_REQUEST["msgType"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_msg_collect set flag=1 where flag = 0 and msg_type='{$msgType}'";
        return $newsql->query($sql);
    }
?>  