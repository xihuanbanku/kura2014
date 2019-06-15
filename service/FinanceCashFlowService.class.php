<?php
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set('PRC');
session_start();
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "insert":
            $result = insert();
            break;
        case "checkExists":
            $result = checkExists();
            break;
        case "delete":
            $result = delete();
            break;
        case "update":
            $result = update();
            break;
        case "exportExcel":
            exportExcel();
            break;
        case "updateStatus":
            $result = updateStatus();
            break;
        case "deleteChked":
            $result = deleteChked();
            break;
        case "updateRemark3":
            $result = updateRemark3();
            break;
        case "initFinanceType":
            $result = initFinanceType();
            break;
        case "initFinance3Type":
            $result = initFinance3Type();
            break;
        case "findFinanceType":
            $result = findFinanceType();
            break;
        case "initStatic":
            $result = initStatic();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function initPage($pageCount=100000, $pageIndex=1) {
        updateOverTimeRecords();
        if(isset($_REQUEST["pageCount"])) {
            $pageCount = $_REQUEST["pageCount"];
        }
        if(isset($_REQUEST["pageIndex"])) {
            $pageIndex = $_REQUEST["pageIndex"];
        }
        $userID = $_COOKIE['userID'];

        //実際対照= 实际收入 -实际支出  5 = 1 - 2
        //掛金対照= 売掛金+買掛金 6 = 3 + 4
        $query = "select a.id, atime, SUBSTR(ADDDATE(a.atime,1),1,10) atime_1, a.type, a.remark1, a.income, a.outgoing, a.real_income, 
            a.real_outgoing, a.limit_date, a.owner, a.remark2, a.finance_type_1, a.finance_type_2, a.finance_type_3, a.going_type, a.income-a.outgoing real_rest,
            a.real_income+a.real_outgoing rest_not_back,
            b.name finance_type_1_str, c.name finance_type_2_str, d.name finance_type_3_str, e.p_name going_type_str, f.s_name owner_str, a.picture_name, a.status, SUBSTR(a.deal_time, 1, 10) deal_time
            from jxc_finance_cashflow a, jxc_finance_type b, jxc_finance_type c, jxc_finance_type d,
                (select * from jxc_static where p_type='GOING_TYPE') e,
                jxc_staff f
            where del_flag=0
              and a.finance_type_1 = b.id
              and a.finance_type_2 = c.id
              and a.finance_type_3 = d.id
              and a.going_type = e.p_value
              and a.owner = f.id
              and (owner ={$userID} or (select count(0) from jxc_menu where id=190 and INSTR(rank, '|{$userID}|') > 0))";

        if($_REQUEST["going_type"] > 0) {
            $query .= " and a.going_type =".$_REQUEST["going_type"];
        }
        if(!empty($_REQUEST["s_atime"])){
           $s_atime = $_REQUEST["s_atime"];
           $query .= " and limit_date >='{$s_atime}'";
        }
        if(!empty($_REQUEST["e_atime"])){
           $e_atime = $_REQUEST["e_atime"];
           $query .= " and limit_date <='{$e_atime}'";
        } else {
           $e_atime = date("Y-m-d");
           $query .= " and limit_date <='{$e_atime}'";
        }
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (id like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        if (!empty($_REQUEST["orderBy"])) {
            switch ($_REQUEST["orderBy"]) {
                case 1:
                    $query .= "   order by atime ";
                break;
                case 2:
                    $query .= "   order by atime desc ";
                break;
                case 3:
                    $query .= "   order by status ";
                break;
                case 4:
                    $query .= "   order by status desc";
                break;
                case 5:
                    $query .= "   order by deal_time ";
                break;
                case 6:
                    $query .= "   order by deal_time desc";
                break;
                case 7:
                    $query .= "   order by going_type";
                break;
                case 8:
                    $query .= "   order by going_type desc";
                break;
            }
        } else {
            $query .= "   order by  atime desc";
        }
        $query .= " limit ".($pageIndex-1)*$pageCount.",".$pageCount;
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        if(!isset($_REQUEST["pageIndex"])) {
            return $results;
        }
		$total = initPageCount();
		return "{\"totalcount\":".json_encode($total, JSON_FORCE_OBJECT).",\"results\":".json_encode($results, JSON_FORCE_OBJECT)."}";
    }

//         一笑而过  17:54:11
//实际结余 = 实际收入 -实际支出 + 初始金额 求和 
//  7 = sum(1 - 2)+ 资金动向 1 求和
//     THE OTHER WAY  17:52:50
//     就是 把 每个帐号的 初始金额 求和
//     THE OTHER WAY  17:53:16
//     然后 算到 实时流水中的 实际收入中

//     一笑而过  09:40:28
//     所以现在的公式
//     实际挂账之和 = 实际结余+应收+应付
//     要修改为
//     实际挂账之和 = 实际结余+应收-应付
//     对不对
//     THE OTHER WAY  09:41:02
//     是的

        //实际挂账之和 =实际收入- 实际支出+(应收 -应付)   8 = 1 - 2 + (3 - 4)
    function initPageCount() {
        $userID = $_COOKIE['userID'];
        $query = "select count(0) c, sum(income) s_income, sum(outgoing) s_outgoing,
            sum(real_income) s_real_income, sum(real_outgoing) s_real_outgoing,
            sum(income-outgoing)+(select sum(initial) from jxc_finance_cash where del_flag=0 
            and (owner ={$userID} or (select count(0) from jxc_menu where id=190 and INSTR(rank, '|{$userID}|') > 0))";
        if($_REQUEST["going_type"] > 0) {
            $query .= " and going_type =".$_REQUEST["going_type"];
        }
        $query .= " ) s_real_rest, 
            sum(income-outgoing+(real_income-real_outgoing))+(select sum(initial) from jxc_finance_cash where del_flag=0 ";
        if($_REQUEST["going_type"] > 0) {
            $query .= " and going_type =".$_REQUEST["going_type"];
        }
        $query .= " ) s_rest_not_back
        from jxc_finance_cashflow
            where  del_flag=0";
        if($_REQUEST["going_type"] > 0) {
            $query .= " and going_type =".$_REQUEST["going_type"];
        }
        if(!empty($_REQUEST["s_atime"])){
           $s_atime = $_REQUEST["s_atime"];
           $query .= " and limit_date >='{$s_atime}'";
        }
        if(!empty($_REQUEST["e_atime"])){
           $e_atime = $_REQUEST["e_atime"];
           $query .= " and limit_date <='{$e_atime}'";
        } else {
           $e_atime = date("Y-m-d");
           $query .= " and limit_date <='{$e_atime}'";
        }
        if(!empty($_REQUEST["s_text"])){
           $s_text = $_REQUEST["s_text"];
           $query .= " and (id like '%{$s_text}%'
                          or remark1     like '%{$s_text}%'
                          or remark2     like '%{$s_text}%')";
        }
        if(!empty($_REQUEST["strChk"])) {
            $strChk = $_REQUEST["strChk"];
            if($strChk != "") {
                $ids = array();
                foreach ($strChk as $id) {
                    array_push($ids, $id);
                }
                $query .= " and a.id in ('" . implode("','", $ids) . "')";
            }
        }
        $newsql = new ezSQL_mysql();
        return $newsql->get_row($query);
    }
    
    /**
     * 上传文件
     * @param unknown $pageId
     * @param unknown $file
     * @param unknown $filetempname
     * @return multitype:number string
     */
    function uploadFile($pageId, $file, $filetempname) {
        $importStat=array("n"=>0,"u"=>0,"d"=>0,"e"=>0,"m"=>0,"filename"=>0);
        // 自己设置的上传文件存放路径
        $filePath = '/home/p-mon/tousho.co.jp/public_html/kura2014/upload/';
        $str = "";
        // 下面的路径按照你PHPExcel的路径来修改
        set_include_path('/home/p-mon/tousho.co.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
    
        $filename = explode(".", $file); // 把上传的文件名以“.”好为准做一个数组。
        $time = date("Ymd-H_i_s"); // 去当前上传的时间
        $filename[0] .= $time; // 取文件名t替换
        $name = implode(".", $filename); // 上传后的文件名
        $uploadfile = $filePath . $name; // 上传后的文件名地址
        // move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
        $result = move_uploaded_file($filetempname, $uploadfile); // 假如上传到当前目录下
        if ($result) {// 如果上传文件成功，就执行导入excel操作
            if($filename[1]=="xlsx") {
                $objReader = PHPExcel_IOFactory::createReader('Excel2007'); // use excel2007 for 2007 format
            } else {
                $objReader = PHPExcel_IOFactory::createReader('Excel5'); // use excel2007 for 2007 format
            }
            $objPHPExcel = $objReader->load($uploadfile);
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());; // 取得总列数
    
            $nsql= new ezSQL_mysql();
            // 循环读取excel文件,读取一条,插入一条,行号j没有0,从1开始
            for ($j = 2; $j <= $highestRow; $j ++) {
                $strs = array();
                for ($k = 0; $k < $highestColumn; $k ++) {
                    $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                    $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
                    //                 echo "$columnName$j"."----".$cellValue."----";
                    array_push($strs, $cellValue); // 读取单元格
                }
                switch ($strs[0]) {
                    case "n":
                        $index = 1;
//                         if(checkExists($strs[1], $strs[2]) > 0) {
//                             $importStat["m"] = 1;
//                             echo date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2];
//                             error_log(date("Ymd-H:i:s")."----".$j."出现错误[该记录已存在]商品CODE:".$strs[1]."---BarCode:".$strs[2]."\n", 3, "../logs/upload.log");
//                             break 2;
//                         }
                        $total = intval($strs[4])*intval($strs[5]);
                        $finance_type_3 = $strs[10];
                        $notesql="insert into `jxc_finance_cashflow`(`type`, `remark1`, `income`, `outgoing`, `real_income`, 
            `real_outgoing`, `limit_date`, `owner`, `remark2`, `going_type`, `finance_type_1`, `finance_type_2`, `finance_type_3`, deal_time)
                        values('".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                '".$_COOKIE['userID']."',
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."',
                                (select p_id from `jxc_finance_type` where id =(select p_id from `jxc_finance_type` where id ={$finance_type_3})),
                                (select id from `jxc_finance_type` where id =(select p_id from `jxc_finance_type` where id ={$finance_type_3})),
                                '".trim($strs[$index++])."',
                                '".trim($strs[$index++])."')";
                        $importStat['n']++;
                        break;
//                     case "d":
//                         $notesql="delete from `jxc_finance_cashflow`
//                                      where id = '".trim($strs[1])."'";
//                         $importStat['d']++;
//                         break;
                    default:
                        $notesql = "select 1 from dual";
                        break;
                }
                $b1 = $nsql->query($notesql);
                echo mysql_error();
            }
            $importStat["filename"] = $time;
        } else {
            $importStat["m"] = 5;
        }
    
        return $importStat;
    }
    
    /**
     * 插入一行记录
     * @return boolean|number|mixed
     */
    function insert() {
        $picture_name        =$_REQUEST["picture_name"];
        $type         =$_REQUEST["type"];
        $remark1      =$_REQUEST["remark1"];
        $remark2      =$_REQUEST["remark2"];
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
        $real_income  =$_REQUEST["real_income"];
        $real_outgoing=$_REQUEST["real_outgoing"];
        $limit_date   =$_REQUEST["limit_date"];
        $deal_time   =$_REQUEST["deal_time"];
        $owner        =$_COOKIE['userID'];
        $finance_type_1     =$_REQUEST["finance_type_1"];
        $finance_type_2     =$_REQUEST["finance_type_2"];
        $finance_type_3     =$_REQUEST["finance_type_3"];
        $going_type   =$_REQUEST["going_type"];
        
        //默认$status=3
        $status=3;
        if(!empty($real_income) || !empty($real_outgoing)) {
            $status=0;
        }
        if(!empty($income) || !empty($outgoing)) {
            $limit_date = date("Y-m-d H:i:s");
        }
        
        $newsql = new ezSQL_mysql();
        $query = "INSERT INTO `jxc_finance_cashflow` (`type`, `remark1`, `income`, `outgoing`, `real_income`, 
            `real_outgoing`, `limit_date`, `owner`, `remark2`, `finance_type_1`, `finance_type_2`, `finance_type_3`, `going_type`, `picture_name`, deal_time, status)
            values('{$type}', '{$remark1}', '{$income}', '{$outgoing}', '{$real_income}', 
            '{$real_outgoing}', '{$limit_date}', '{$owner}', '{$remark2}', '{$finance_type_1}', '{$finance_type_2}', '{$finance_type_3}', '{$going_type}', '{$picture_name}', '{$deal_time}', '{$status}')";
        $result = $newsql->query($query);
//实时流水 输入1条信息， 3，借贷流水 页面也生成1条信息，从而 验证 会计5要素 恒等式
        $query = "INSERT INTO `jxc_finance_drcrflow` (`income`, `outgoing`, `owner`,
        `remark2`, `finance_type_1`, `finance_type_2`, `finance_type_3`, `picture_name`)
        values('{$income}', '{$outgoing}', '{$owner}', '{$remark2}', '{$finance_type_1}', '{$finance_type_2}', '{$finance_type_3}',
        '{$picture_name}')";
        $result += $newsql->query($query);
        return $result;
    }
    /**
     * 删除一行记录
     * @return boolean|number|mixed
     */
    function delete() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];
        $query = "update jxc_finance_cashflow set del_flag = 1 where id = ".$id."
                and (select count(0) from jxc_menu where id=190 and INSTR(rank, '|".$userID."|') > 0)";
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    /**
     * THE OTHER WAY 09:36:00
     * 这个地方需要加入个判断 ，就是当日内容 有重复金额 计入，提交的 时候 仓库提醒 当日有重复金额计入 是否 计入 如果点击 确认 则计入 ，点击取消怎不计入。
     * @return boolean|number|mixed
     */
    function checkExists() {
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
        $real_income  =$_REQUEST["real_income"];
        $real_outgoing=$_REQUEST["real_outgoing"];
        
        $query = "select ifnull(max(id), 0) from jxc_finance_cashflow 
            where del_flag = 0
                and substr(atime, 1, 10) = '".date("Y-m-d")."'
                and income        = '{$income}'
                and outgoing      = '{$outgoing}'
                and real_income   = '{$real_income}'
                and real_outgoing = '{$real_outgoing}'";
        $newsql = new ezSQL_mysql();
        return $newsql->get_var($query);
    }
    
    /**
     * 更新记录
     * @return boolean|number|mixed
     */
    function update() {
        $userID = $_COOKIE['userID'];
        $id = $_REQUEST["id"];

        $picture_name        =$_REQUEST["picture_name"];
        $deal_time    =$_REQUEST["deal_time"];
        $type         =$_REQUEST["type"];
        $remark1      =$_REQUEST["remark1"];
        $remark2      =$_REQUEST["remark2"];
        $income       =$_REQUEST["income"];
        $outgoing     =$_REQUEST["outgoing"];
        $real_income  =$_REQUEST["real_income"];
        $real_outgoing=$_REQUEST["real_outgoing"];
        $limit_date   =$_REQUEST["limit_date"];
//         $owner        =$_REQUEST["owner"];
//         $finance_type_1     =$_REQUEST["finance_type_1"];
//         $finance_type_2     =$_REQUEST["finance_type_2"];
        $finance_type_3     =$_REQUEST["finance_type_3"];
        $going_type   =$_REQUEST["going_type"];
        
//         $atime       = $_REQUEST["atime"];
//         $insert_date = $_REQUEST["insert_date"];

        $newsql = new ezSQL_mysql();
        $query = "update jxc_finance_cashflow 
                set 
                type          ='".$type."',
                deal_time     ='".$deal_time."',
                remark1       ='".$remark1."',
                remark2       ='".$remark2."',
                income        ='".$income."',
                outgoing      ='".$outgoing."',
                real_income   ='".$real_income."',
                real_outgoing ='".$real_outgoing."',
                limit_date ='".$limit_date."',
                
                finance_type_1      =(select p_id from `jxc_finance_type` where id =(select p_id from `jxc_finance_type` where id ={$finance_type_3})),
                finance_type_2      =(select id from `jxc_finance_type` where id =(select p_id from `jxc_finance_type` where id ={$finance_type_3})),
                finance_type_3      ='".$finance_type_3."',
                going_type    ='".$going_type."',
                picture_name ='".$picture_name."'
                where id = ".$id."
                and (select count(0) from jxc_menu where id=190 and INSTR(rank, '|".$userID."|') > 0)";
        return $newsql->query($query);
    }
    
    /**
     * 初始化财务类型选项
     */
    function initFinanceType() {
        $p_id  =$_REQUEST["p_id"];
        $selectHtml="<option value='0'>请选择</option>";
        $query = "select id, name from `jxc_finance_type` where p_id = '".$p_id."'";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->id}'>{$result->name}</option>";
        }
        return $selectHtml;
    }
    
    /**
     * 初始化第3级财务类型选项
     */
    function initFinance3Type() {
        $selectHtml="";
        $query = "select id, name from `jxc_finance_type` where id not in (select distinct p_id from jxc_finance_type) and p_id not in (select id from jxc_finance_type where p_id =1)  and p_id <>1";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->id}'>{$result->name}</option>";
        }
        return $selectHtml;
    }
    
    /**
     * 根据选择的第3级菜单自动选择前两级菜单
     */
    function findFinanceType() {
        $p_id  =$_REQUEST["p_id"];
        $newsql = new ezSQL_mysql();
        $query = "select id, name, p_id from `jxc_finance_type` where id =(select p_id from `jxc_finance_type` where id ={$p_id})";
        $result = $newsql->get_row($query);
        return json_encode($result, JSON_FORCE_OBJECT);
    }
    
    /**
     * 从static表读取内容
     */
    function initStatic() {
        $type  =$_REQUEST["p_type"];
        $selectHtml="";
        $query = "select p_name, p_value from `jxc_static` where p_type = '".$type."'";
        $newsql = new ezSQL_mysql();
        $results = $newsql->get_results($query);
        foreach ($results as $result) {
            $selectHtml .= "<option value='{$result->p_value}'>{$result->p_name}</option>";
        }
        return $selectHtml;
    }
    
    /**
     * THE OTHER WAY  17:21:32
     * 应收 和应付 到指定的 时间后，会自动转成 实际收入 或实际支出
     * 状态改为"自动转入"
     */
    function updateOverTimeRecords() {
        $query = "update jxc_finance_cashflow 
            set income=income+real_income,
                real_income=0,
                outgoing=outgoing+real_outgoing,
                real_outgoing=0,
                status=1
            where now() >= limit_date and limit_date is not null and status = 0";
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    /**
     * 批量更新状态
     * @return Ambigous <boolean, number, mixed>
     */
    function updateStatus() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $count = 0;
        $newsql = new ezSQL_mysql();
        for($i=0;$i<count($kids);$i++){
            $query = "update jxc_finance_cashflow
                set status = 2,
                    income = ".$_REQUEST["real_income"][$i].",
                    outgoing = ".$_REQUEST["real_outgoing"][$i].",
                    deal_time = '".$_REQUEST["deal_time"][$i]."'
                    where id = ".$kids[$i]."
					and limit_date < now()
                    and (owner ={$userID} or (select count(0) from jxc_menu where id=190 and INSTR(rank, '|".$userID."|') > 0))";
            $count += $newsql->query($query);
        }
        return $count;
    }
    /**
     * 批量更新备注3
     * @return Ambigous <boolean, number, mixed>
     */
    function updateRemark3() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $remark3Input = $_REQUEST["remark3Input"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_finance_cashflow
            set remark3 = '{$remark3Input}'
                where l_id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=190 and INSTR(rank, '|".$userID."|') > 0))";
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 批量删除
     * @return Ambigous <boolean, number, mixed>
     */
    function deleteChked() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $newsql = new ezSQL_mysql();
        $count = 0;
        $query = "update jxc_finance_cashflow
            set del_flag = 1 
                where id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=190 and INSTR(rank, '|".$userID."|') > 0))";
        $count += $newsql->query($query);
        return $count;
    }
	
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "实时流水";
		$headers = array('ID','日期','截止修改日期','科目','摘要','实际收入','实际支出','应收','应付',
		    '应收付日期','記入者ID','款项用途说明','会計要素ID','科目ID','科目明細ID','资金动向ID','实际结余',
		    '挂账结余','会計要素','科目','科目明細','资金动向','記入者','領収書','审核状态','发生日期');
        $results = initPage();
          
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('PRC');
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
        ->setLastModifiedBy("Maarten Balliauw")
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");
        
        // Add some data
        $workSheet = $objPHPExcel->setActiveSheetIndex(0);
    
        $COLUMNINDEXS = unserialize(COLUMNINDEXS);
        for ($i=0; $i<sizeof($headers); $i++) {
            $workSheet->setCellValue($COLUMNINDEXS[$i].'1', $headers[$i]);
        }
        
        $i = 2;
        if($results) {
            foreach ($results as $result) {
                $j = 0;
                foreach ($result as $key=> $value){
                    $workSheet->setCellValue($COLUMNINDEXS[$j++].$i, $value);
                }
                $i++;
            }
        }
     
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($title);
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $time = date("Ymd-H_i_s");
        header('Content-Disposition: attachment;filename="'.$title.$time.'.xlsx"');
        header('Cache-Control: max-age=0');
        
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
?>  