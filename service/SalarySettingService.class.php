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
        case "delete":
            $result = delete();
            break;
        case "update":
            $result = update();
            break;
        case "exportExcel":
            exportExcel();
            break;
        case "deleteChked":
            $result = deleteChked();
            break;
        case "updateRemark3":
            $result = updateRemark3();
            break;
        case "updateUserType":
            $result = updateUserType();
            break;
        case "updateDelFlag":
            $result = updateDelFlag();
            break;
        case "updateTimeType":
            $result = updateTimeType();
            break;
        case "timeType":
            $result = timeType();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    function initPage() {
        $user = $_REQUEST["user"];

        //先自动更新考勤相关的参数， 应到x天
        //当月出勤見込み
        $newsql = new ezSQL_mysql();
        $query = "update jxc_salary_config set p_value = 
                (select count(0) from jxc_duty 
                    where owner = {$user}
                    and atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
                    and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else date_format(now(), '%Y-%m') end),'-15')
                    and weekday(atime) not in (5, 6))
                where del_flag <> 1 
                    and user_id={$user} 
                    and p_name = '当月出勤見込み'";
        $newsql->get_results($query);
        //欠勤日数, 没有check_in 
        $query = "update jxc_salary_config set p_value = 
                (select count(0) from jxc_duty 
                    where owner = {$user}
                    and atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
                    and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15')
                    and is_holiday = 0
                    and check_in is null
                    and weekday(atime) not in (5, 6))
                where del_flag <> 1
                    and user_id={$user} 
                    and p_name = '欠勤日数'";
        $newsql->get_results($query);
        //勤務日数
        $query = "update jxc_salary_config set p_value = 
                (select * from (select p_value from jxc_salary_config 
                    where user_id = {$user}
                    and p_name = '当月出勤見込み'
                    and del_flag <> 1) x) -
                (select * from (select p_value from jxc_salary_config 
                    where user_id = {$user}
                    and p_name = '欠勤日数'
                    and del_flag <> 1) y) 
                where del_flag <> 1 
                    and user_id={$user} 
                    and p_name = '勤務日数'";
        $newsql->get_results($query);
        //早退回数
        $query = "update jxc_salary_config set p_value = 
                (select * from (select count(0) from jxc_duty 
                    where owner = {$user}
                    and atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
                    and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15')
                    and is_holiday = 0
                    and atime < now()
                    and IfNULL(check_in, '9') > concat(atime, ' ', (select p_value from jxc_salary_config where p_name = '出勤时间' and user_id={$user} and del_flag <>1 ))
                    and weekday(atime) not in (5, 6)) x)
                where del_flag <> 1 
                    and user_id={$user} 
                    and p_name = '早退回数'";
        $newsql->get_results($query);
        //遅刻回数
        $query = "update jxc_salary_config set p_value = 
                (select * from (select count(0) from jxc_duty 
                    where owner = {$user}
                    and atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
                    and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15')
                    and is_holiday = 0
                    and atime < now()
                    and IfNULL(check_out, '0') < concat(atime, ' ', (select p_value from jxc_salary_config where p_name = '退勤时间' and user_id={$user} and del_flag <>1 ))
                    and weekday(atime) not in (5, 6)) x)
                where del_flag <> 1 
                    and user_id={$user} 
                    and p_name = '遅刻回数'";
        $newsql->get_results($query);
        //勤務時間 + 普通残業
        
        //读取考勤表
        //先获取时间展示方式
        $query = "select p_value from jxc_salary_config
                where user_id = ".$user."
                    and p_name='时间展示方式'";
        $timeType = $newsql->get_var($query);
        $param_a = 0.01;
        $param_b = 0.6;
        switch ($timeType) {
            case "2":
                $param_a = 0.25;
                $param_b = 15;
                break;
            case "3":
                $param_a = 0.5;
                $param_b = 30;
                break;
            default:
                break;
        }
        $query = "select sum(floor(floor(x.work_time_x/{$param_a})*{$param_b})) work_time_sum,
        sum(floor(floor(x.over_time1_x/{$param_a})*{$param_b})) over_time1_sum,
        sum(floor(floor(x.over_time2_x/{$param_a})*{$param_b})) over_time2_sum from
        (select
        ifnull(case when  TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then 8 else TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 end, '0') work_time_x,
        ifnull(case when TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600 -9 else 0 end, '0') over_time1_x,
        ifnull(case when (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 <8 then (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 else 0 end, '0') over_time2_x
        from jxc_duty a
        where atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
        and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15')
        and owner = {$user}) x";
        $results_sum = $newsql->get_results($query);
        //更新工资表中的 勤務时间 + 普通残業
        $query = "update jxc_salary_config set p_value =  {$results_sum[0]->work_time_sum}/60
                where del_flag <> 1 
                    and user_id={$user} 
                    and p_name = '勤務時間'";
        $newsql->get_results($query);
        $query = "update jxc_salary_config set p_value =  {$results_sum[0]->over_time1_sum}/60
                where del_flag <> 1
                    and user_id={$user} 
                    and p_name = '普通残業'";
        $newsql->get_results($query);
        //有給休暇日総数
        $query = "update jxc_salary_config set p_value = 
                    (select count(0) from jxc_duty 
                    where owner = {$user}
                    and  atime > date_add(CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15'),INTERVAL -1 MONTH)
                    and atime <= CONCAT((select case  when (select DAY(now()))>15 then date_format(date_add(now(), INTERVAL 1 month), '%Y-%m') else  date_format(now(), '%Y-%m') end),'-15')
                    and is_holiday = 1
                    and weekday(atime) not in (5, 6))
                where del_flag <> 1
                    and user_id={$user} 
                    and p_name = '当月有給使用日数'";
        $newsql->get_results($query);

        //按照顺序计算所有的公式
        $query = "select id, p_func from jxc_salary_config where del_flag <> 1 and user_id={$user} and func_order > 0 order by func_order ";
        $results = $newsql->get_results($query);
		if($results) {
			foreach ($results as $inputFunc) {
				caculateInput(" ".$inputFunc->p_func." ", $user, $inputFunc->id);
			}
        }
        $query = "select * from jxc_salary_config where del_flag <> 1 and user_id={$user} order by  p_type, sort ";
        $results = $newsql->get_results($query);
		return json_encode($results, JSON_FORCE_OBJECT);
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
        $filePath = '/home/p-mon/pmon.jp/public_html/kura2014/upload/';
        $str = "";
        // 下面的路径按照你PHPExcel的路径来修改
        set_include_path('/home/p-mon/pmon.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
    
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
                        $notesql="insert into `jxc_salary_config`(`type`, `remark1`, `income`, `outgoing`, `real_income`, 
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
//                         $notesql="delete from `jxc_salary_config`
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
        $sort      =$_REQUEST["sort"];
        $p_name    =$_REQUEST["p_name"];
        $p_value   =$_REQUEST["p_value"];
        $inputFunc   =$_REQUEST["p_func"];
        $func_order   =$_REQUEST["func_order"];
        $p_type   =$_REQUEST["p_type"];
        $user      =$_REQUEST["user"];
        if(isset($_REQUEST["del_flag"])) {
            $del_flag      =$_REQUEST["del_flag"];
            $newsql = new ezSQL_mysql();
            $query = "INSERT INTO `jxc_salary_config` (`p_type`, `p_name`, `p_value`, p_func, func_order, `sort`, del_flag, `user_id`)
                select '{$p_type}', '{$p_name}', '{$p_value}', '{$inputFunc}', '{$func_order}', '{$sort}', '{$del_flag}', id from jxc_staff";
            return $newsql->query($query);
        }
        return 0;
    }
    /**
     * 删除一行记录
     * @return boolean|number|mixed
     */
    function delete() {
        $id = $_REQUEST["id"];
        $query = "update jxc_salary_config set del_flag = 1 where id = ".$id;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    /**
     * 切换显示状态
     * @return boolean|number|mixed
     */
    function updateDelFlag() {
        $id = $_REQUEST["id"];
        $query = "update jxc_salary_config set del_flag = (case del_flag when 0 then 2 when 2 then 0 end) where id = ".$id;
        $newsql = new ezSQL_mysql();
        return $newsql->query($query);
    }
    
    /**
     * 更新记录
     * @return boolean|number|mixed
     */
    function update() {
        $id = $_REQUEST["id"];
        $user = $_REQUEST["user"];

        $sort        =$_REQUEST["sort"];
        $p_name    =$_REQUEST["p_name"];
        $p_value     =$_REQUEST["p_value"];
        $inputFunc   =$_REQUEST["p_func"];
        $func_order   =$_REQUEST["func_order"];
        $all   =$_REQUEST["all"];

        $newsql = new ezSQL_mysql();
        //如果更新出勤时间， 需要同步更新考勤表(正式员工)
        if($p_name == "出勤时间") {
            $query = "update jxc_duty 
                    set 
                    check_in_on_time = concat(atime, ' ', '".$p_value."')
                    where owner = '".$user."'
                      and 1 = (select p_value from jxc_salary_config where p_type=4 and p_name ='職位' and user_id = ".$user.")";
            $newsql->query($query);
        }
        //如果更新退勤时间， 需要同步更新考勤表(正式员工)
        if($p_name == "退勤时间") {
            $query = "update jxc_duty 
                    set 
                    check_out_on_time = concat(atime, ' ', '".$p_value."')
                    where owner = '".$user."'
                      and 1 = (select p_value from jxc_salary_config where p_type=4 and p_name ='職位' and user_id = ".$user.")";
            $newsql->query($query);
        }
        if($all == 0) {
            //正常更新记录
            $query = "update jxc_salary_config
                    set 
                    sort    ='".$sort."',
                    p_name  ='".$p_name."',
                    p_func  ='".$inputFunc."',
                    func_order  ='".$func_order."',
                    p_value ='".$p_value."'
                    where id = ".$id;
            $count = $newsql->query($query);
        } else {
            //全局更新记录
            $query = "update jxc_salary_config
                    set 
                    sort    ='".$sort."',
                    p_name  ='".$p_name."',
                    p_func  ='".$inputFunc."',
                    func_order  ='".$func_order."',
                    p_value ='".$p_value."'
                    where p_name = (select * from (select p_name from jxc_salary_config where id = ".$id.") x)";
            $count = $newsql->query($query);
        }
        //如果是需要公式计算, 将结果按照公式计算出来
        if($inputFunc) {
            caculateInput(" ".$inputFunc." ", $user, $id);
        }
        return $count;
    }
    
    function caculateInput($inputFunc, $user, $id) {
        $newsql = new ezSQL_mysql();
        
            $pattern = '/(\d+#\d+)+/';
            //解析case when 分段
            $others = preg_split($pattern, $inputFunc, -1, PREG_SPLIT_NO_EMPTY);
            //提取匹配到的内容, case when 的条件
            preg_match_all($pattern, $inputFunc, $tempRes);
            $results = $tempRes[0];
            $query= "update jxc_salary_config set p_value = (select * from (select ";
            for ($i = 0; $i < count($results); $i ++) {
                $query .= $others[$i];
                $items = explode("#", $results[$i]);
                $query .= "(select p_value from jxc_salary_config where p_type = {$items[0]} and sort = {$items[1]} and user_id = {$user} and del_flag<>1)";
            }
            $query.=end($others);
//             $query .= " x ) where p_name = (select * from (select p_name from jxc_salary_config where id = ".$id.") x) and user_id = {$user}";
            $query .= ") x ) where  id = ".$id;
            $newsql->query($query);
            
            
//         } else { //否则正常计算
//             //解析参数
//             $params = preg_split($pattern, $inputFunc, -1, PREG_SPLIT_NO_EMPTY);
//             //解析运算符号
//             $signs = preg_split($pattern1, $inputFunc, -1, PREG_SPLIT_NO_EMPTY);
//             array_push($signs, ")");
            
//             $query= "update jxc_salary_config set p_value = (select * from (select ";
//             for ($i = 1; $i < count($params); $i ++) {
//                 $items = explode("#", $params[$i]);
//                 $query .= "(select p_value from jxc_salary_config where p_type = {$items[0]} and sort = {$items[1]} and user_id = {$user})";
//                 $query .= $signs[$i];
//             }
//             $items = explode("#", $params[0]);
// //             $query .= " x ) where p_name = (select * from (select p_name from jxc_salary_config where id = ".$id.") x) and user_id = {$user}";
//             $query .= " x ) where  id = ".$id;
//             $newsql->query($query);
//         }
    }
    
    /**
     * 更新人员类型
     * @return boolean|number|mixed
     */
    function updateUserType() {
        $id = $_REQUEST["id"];
        $p_value         =$_REQUEST["userType"];

        $newsql = new ezSQL_mysql();
        $query = "update jxc_salary_config
                set 
                p_value ='".$p_value."'
                where id = ".$id;
        return $newsql->query($query);
    }

    /**
     * 获取时间展示方式类型
     * @return boolean|number|mixed
     */
    function timeType() {
        $user = $_REQUEST["user"];
        $newsql = new ezSQL_mysql();
        $query = "select p_value from jxc_salary_config
                where user_id = ".$user."
                    and p_name='时间展示方式'";
        $results = $newsql->get_results($query);
		return json_encode($results, JSON_FORCE_OBJECT);
    }
    
    /**
     * 更新时间展示方式类型
     * @return boolean|number|mixed
     */
    function updateTimeType() {
        $id = $_REQUEST["id"];
        $p_value         =$_REQUEST["timeType"];
        $newsql = new ezSQL_mysql();
        $query = "update jxc_salary_config
                set 
                p_value ='{$p_value}'
                where id = ".$id;
        return $newsql->query($query);
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
        $query = "update jxc_salary_config
            set remark3 = '{$remark3Input}'
                where l_id in ('" . implode("','", $kids) . "')
                and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
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