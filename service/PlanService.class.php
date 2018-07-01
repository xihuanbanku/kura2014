<?php
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set("PRC");
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "sendPlan":
            $result = sendPlan();
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
        case "init":
            $result = initPage();
        break;
        case "replySubmit":
            $result = replySubmit();
        break;
        case "chgRemindMe":
            $result = chgRemindMe();
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
    
    function initPage($pageCount=100000, $pageIndex=1) {
        $stext = trim($_REQUEST["stext"]);
        if($_REQUEST["pageCount"]) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if($_REQUEST["pageIndex"]) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $ksql = new ezSQL_mysql();
        
        $query = "select a.`id`, `subject`, `flag`, `dtime`, b.s_name
            from jxc_plan a left join jxc_staff b on a.receiver = b.id where a.flag != 2 ";
        if(!empty($stext)){
            $query .= " and (a.subject like '%{$stext}%' or a.content like '%{$stext}%' )";
        }
        if(!empty($sdate)){
            $query .= " and a.dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.dtime <= '{$edate}'";
        }
        if(isset($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            $query .= " and a.receiver in (" . implode(",", $strChk) . ")";
        }
        if(!isset($_REQUEST["is_admin"])) {
            $query .= " and a.receiver = {$_COOKIE["userID"]}";
        }
        if (!empty($_REQUEST["orderBy"])) {
            switch ($_REQUEST["orderBy"]) {
                case 1:
                    $query .= "   order by a.flag ";
                break;
                case 2:
                    $query .= "   order by a.flag desc ";
                break;
                case 3:
                    $query .= "   order by a.dtime ";
                break;
                case 4:
                    $query .= "   order by a.dtime desc ";
                break;
            }
        } else {
            $query .= " order by a.dtime desc";
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $results = $ksql->get_results($query);

        $total =initPageCount();
        return "{\"totalcount\":".$total.",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }
    
    function initPageCount() {
        $stext = trim($_REQUEST["stext"]);
        $sdate = $_REQUEST["sdate"];
        $edate = $_REQUEST["edate"];
        $ksql = new ezSQL_mysql();
        $query = "select count(0)
            from jxc_plan a where a.flag != 2";
        if(!empty($stext)){
            $query .= " and (a.subject like '%{$stext}%' or a.content like '%{$stext}%')";
        }
        if(!empty($sdate)){
            $query .= " and a.dtime >= '{$sdate}'";
        }
        if(!empty($edate)){
            $query .= " and a.dtime <= '{$edate}'";
        }
        if(!isset($_REQUEST["is_admin"])) {
            $query .= " and a.receiver = {$_COOKIE["userID"]}";
        }
        if(isset($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            $query .= " and a.receiver in (" . implode(",", $strChk) . ")";
        }
        return $ksql->get_var($query);
    }
    
    function sendPlan() {
        $userID = $_COOKIE["userID"];
        $subject = $_REQUEST["subject"];
        $content = $_REQUEST["content"];
        
        $newsql = new ezSQL_mysql();
        
        $result=0;
        //批量发送
        if(isset($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            foreach ($strChk as $chk) {
                $result += $newsql->query("insert into jxc_plan(`receiver`, `subject`, `content`)
                    values('$chk','$subject', concat('<span>',  '{$_COOKIE["VioomaUserID"]}', '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$content}'))");
            }
        } else {
        //单独发送
            $result += $newsql->query("insert into jxc_plan(`receiver`, `subject`, `content`)
                values('$userID','$subject', concat('<span>',  '{$_COOKIE["VioomaUserID"]}', '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$content}'))");
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
        $sql = "select a.dtime, a.subject, a.content, a.receiver, b.s_name receiver_name
             from jxc_plan a left join jxc_staff b on a.receiver = b.id where a.id = ".$id;
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
        $sql = "update jxc_plan set flag = ".$state." where id = ".$id;
        $updateCount += $newsql->query($sql);
        return $updateCount;
    }
    function replySubmit() {
        $resultCount = 0;
        $id = $_REQUEST["id"];
        $replyContent = $_REQUEST["replyContent"];
        $userID = $_REQUEST["userID"];
        $receiver_name = $_REQUEST["receiver_name"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_plan set flag = 0, content = concat(content, '<br/><span>', '{$receiver_name}', '</span> 在 <span>', now(), '</span> 时写道:<br/>', '{$replyContent}') where  id = ".$id;
        $resultCount += $newsql->query($sql);
        return $resultCount;
    }
    function chgRemindMe() {
        $id = $_REQUEST["id"];
        $remind_me = $_REQUEST["remind_me"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_plan set remind_me='{$remind_me}' where id = ".$id;
        return $newsql->query($sql);
    }

    function msgSubmit() {
        $replyContent = $_REQUEST["replyContent"];
        $msgType = $_REQUEST["msgType"];
        $receiver = $_REQUEST["receiver"];
        $newsql = new ezSQL_mysql();
        $sql = "insert into jxc_msg_collect(receiver, content, msg_type) values('{$receiver}', '{$replyContent}', '{$msgType}') ";
        return $newsql->query($sql);
    }
    function cleanMsgCollect() {
        $msgType = $_REQUEST["msgType"];
        $newsql = new ezSQL_mysql();
        $sql = "update jxc_msg_collect set flag=1 where flag = 0 and msg_type='{$msgType}'";
        return $newsql->query($sql);
    }
?>  