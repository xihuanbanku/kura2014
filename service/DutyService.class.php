<?php
require_once '../PHPExcel.php';
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
date_default_timezone_set('Asia/Tokyo');
session_start();
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "initPage":
            $result = initPage();
        break;
        case "check_in":
            $result = updateColumn("check_in");
            break;
        case "snooze_start":
            $result = updateColumn("snooze_start");
            break;
        case "snooze_end":
            $result = updateColumn("snooze_end");
            break;
        case "check_out":
            $result = updateColumn("check_out");
            break;
        case "remark":
            $result = remark();
            break;
        case "updateHoliday":
            $result = updateHoliday();
            break;
        case "checkTodayStatus":
            $result = checkTodayStatus();
            break;
        case "exportExcel":
            exportExcel();
            break;
        default:
            return "error";
        break;
    }
    echo $result;  
    
    /**
     * 勤務時間的 计算方法
                 退勤时间-出勤时间
                     普通残業の計算方法
                     退勤时间-出勤时间超过 9小时的 是为普通加班
                     时给 *1.25  	 每个人的时给 是从 給料設定里面读取
                     深夜残業の計算方法	半夜 0：00　开始的时间 为 深夜加班
                     时给 *1.35  	 每个人的时给 是从 給料設定里面读取
                     周6 如果上班的 话 算是 ，普通残業。
                case when check_in <= '9:00:00' and check_out >= '18:00:00' then 9
                 when check_in > '9:00:00' and check_out >= '18:00:00' then (TIME_TO_SEC('18:00:00') - TIME_TO_SEC(check_in))/3600
                 when check_in <= '9:00:00' and check_out < '18:00:00' then (TIME_TO_SEC(check_out) - TIME_TO_SEC('9:00:00'))/3600
                 when check_in > '9:00:00' and check_out < '18:00:00' then (TIME_TO_SEC(check_out) - TIME_TO_SEC(check_in))/3600 end work_time,
                case when 
                
        THE OTHER WAY 2017-12-12 08:07:22
            这个日期的地方我忘记说了 ，我们是  每个月的 15号截至。 25发工资。  例如 10月16-11月15 算一个月。12月16到1月15 算一个月。是这么计算的
     */
    function initPage($exclelFlag=false) {
        $dutyYear = $_REQUEST["dutyYear"];
        $dutyMonth = $_REQUEST["dutyMonth"];
        $owner = $_COOKIE['userID'];

        //如果是从管理界面查询,用户id 从select中获取
        if(isset($_REQUEST["fromPage"]) && $_REQUEST["fromPage"]=="admin") {
            $owner = $_REQUEST["users"];
        }
        //先获取时间展示方式
        $newsql = new ezSQL_mysql();
        $query = "select p_value from jxc_salary_config
                where user_id = ".$owner."
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
        $query = "select *, concat(floor(floor(x.work_time_x/{$param_a})*{$param_b}/60), ':', floor(floor(x.work_time_x/{$param_a})*{$param_b}%60)) work_time,
                            concat(floor(floor(x.over_time1_x/{$param_a})*{$param_b}/60), ':', floor(floor(x.over_time1_x/{$param_a})*{$param_b}%60)) over_time1,
                            concat(floor(floor(x.over_time2_x/{$param_a})*{$param_b}/60), ':', floor(floor(x.over_time2_x/{$param_a})*{$param_b}%60)) over_time2 from 
                    (select id, atime, DATE_FORMAT(atime, '%a') wk, ifnull(check_in_on_time, '') check_in_on_time, ifnull(check_out_on_time, '') check_out_on_time, ifnull(check_in, '') check_in, ifnull(snooze_start, '') snooze_start,
                ifnull(snooze_end, '') snooze_end, ifnull(check_out, '') check_out, 
                ifnull(case when  TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then 8 else TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 end, '0') work_time_x,
                ifnull(case when TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600 -9 else 0 end, '0') over_time1_x,
                ifnull(case when (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 <8 then (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 else 0 end, '0') over_time2_x,
                b.p_name is_holiday,
                remark
                from jxc_duty a, (select p_name, p_value from jxc_static where p_type='DUTY_HOLIDAY_TYPE') b
            where a.is_holiday = b.p_value
                and atime > date_add('{$dutyYear}-{$dutyMonth}-15', interval -1 month)
                and atime <= '{$dutyYear}-{$dutyMonth}-15'
                and owner = {$owner}) x
            order by atime";
        $results = $newsql->get_results($query);
        if($exclelFlag) {
            return $results;
        }
        $query = "select sum(floor(floor(x.work_time_x/{$param_a})*{$param_b})) work_time_sum,
                            sum(floor(floor(x.over_time1_x/{$param_a})*{$param_b})) over_time1_sum,
                            sum(floor(floor(x.over_time2_x/{$param_a})*{$param_b})) over_time2_sum from 
                    (select 
                ifnull(case when  TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then 8 else TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 end, '0') work_time_x,
                ifnull(case when TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600-1 >=8 then TIME_TO_SEC(timediff(check_out, (case when check_in < check_in_on_time then check_in_on_time else check_in end)))/3600 -9 else 0 end, '0') over_time1_x,
                ifnull(case when (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 <8 then (TIME_TO_SEC(check_out) - TIME_TO_SEC('00:00:00'))/3600 else 0 end, '0') over_time2_x
                from jxc_duty a
            where atime > date_add('{$dutyYear}-{$dutyMonth}-15', interval -1 month)
                and atime <= '{$dutyYear}-{$dutyMonth}-15'
                and owner = {$owner}) x";
        $results_sum = $newsql->get_results($query);
        return "{\"results_sum\":".json_encode($results_sum, JSON_FORCE_OBJECT).",\"results\":".json_encode($results, JSON_FORCE_OBJECT).",\"timeType\":{$timeType}}";
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
        set_include_path('/home/p-mon/pmon.jp/public_html/kura2014/PHPExcel'.PATH_SEPARATOR.get_include_path());
    
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
                if($strs[2] != "") {
                    if($pageId == "admin") {
                        //当前要修改哪个user的数据
                        $user = $_REQUEST["user"];
                        $notesql="update jxc_duty set 
                                    check_in_on_time = '".gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP(trim($strs[3])))."',
                                    check_out_on_time = '".gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP(trim($strs[4])))."'
                                where atime = '{$strs[1]}'
                                    and owner = {$user}";
                    } else if($pageId == "staff") {
                        $notesql="update jxc_duty set 
                                    check_in_on_time = '".gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP(trim($strs[3])))."',
                                    check_out_on_time = '".gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP(trim($strs[4])))."'
                                where atime = '{$strs[1]}'
                                    and owner = {$_COOKIE['userID']}
                                    and 1 = (select p_value from jxc_salary_config where p_type=4 and p_name ='職位' and user_id = ".$_COOKIE['userID'].")";
                    }
                    $importStat['u']++;
                    $b1 = $nsql->query($notesql);
                    echo mysql_error();
                }
            }
            $importStat["filename"] = $time;
        } else {
            $importStat["m"] = 5;
        }
    
        return $importStat;
    }
        
    /**
     * 出勤, 休憩开始, 休憩終了, 退勤
     * @return boolean|number|mixed
     */
    function updateColumn($col) {
        $owner = $_COOKIE['userID'];
        $atime = date("Y-m-d");
//         $time = date("H:i:s");
        $newsql = new ezSQL_mysql();
        $query = "update `jxc_duty` set {$col}=now() where owner={$owner} and atime='{$atime}' and {$col} is null";
        switch ($col) {
            case "check_in":
                $action = "出勤";
                break;
            case "snooze_start":
                $action = "休憩开始";
                break;
            case "snooze_end":
                $action = "休憩終了";
                $query .= " and snooze_start is not null";
                break;
            case "check_out":
                $action = "退勤";
                $query = "update `jxc_duty` set {$col}= (case when curtime() > (select p_value from jxc_salary_config where p_name = '強制退勤時間' and user_id = {$owner})
                    then (select CONCAT(curdate(), ' ',  p_value) from jxc_salary_config where p_name = '強制退勤時間' and user_id = {$owner}) else now() end) where owner={$owner} and atime='{$atime}' and {$col} is null and check_in is not null";
                break;
            default:
                $action = "异常操作";
                break;
        }
        $count = $newsql->query($query);
        if($count > 0) {
            error_log(date("Y.m.d H:i:s")."[".$_COOKIE["VioomaUserID"]."]{$action}\n", 3, "../duty_bak/duty_".date("Ymd").".txt");
        }
        return $count;
    }
    /**
     * 备注
     * @return boolean|number|mixed
     */
    function remark() {
        $userID = $_COOKIE['userID'];
        $remark = $_REQUEST['remark'];
        $check_in_on_time = $_REQUEST['check_in_on_time'];
        $check_out_on_time = $_REQUEST['check_out_on_time'];
        $check_in   = $_REQUEST['check_in'];
        $snooze_start = $_REQUEST['snooze_start'];
        $snooze_end = $_REQUEST['snooze_end'];
        $check_out  = $_REQUEST['check_out'];
        $id = $_REQUEST['id'];
        $newsql = new ezSQL_mysql();
        $query = "update `jxc_duty` set
                    remark='{$remark}',
                    check_in_on_time    = (case when '{$check_in_on_time  }' <> '' then '{$check_in_on_time  }' else null end ),
                    check_out_on_time    = (case when '{$check_out_on_time  }' <> '' then '{$check_out_on_time  }' else null end ),
                    check_in    =(case when '{$check_in    }' <> '' then '{$check_in    }' else null end ),
                    snooze_start=(case when '{$snooze_start}' <> '' then '{$snooze_start}' else null end ),
                    snooze_end  =(case when '{$snooze_end  }' <> '' then '{$snooze_end  }' else null end ),
                    check_out   =(case when '{$check_out   }' <> '' then '{$check_out   }' else null end )
                where id={$id} and (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0)";
        return $newsql->query($query);
    }
    
    /**
     * 批量更新休假
     * @return Ambigous <boolean, number, mixed>
     */
    function updateHoliday() {
        $userID = $_COOKIE['userID'];
        $kids = $_REQUEST["strChk"];
        $isAll = $_REQUEST["isAll"];
        $holidayType = $_REQUEST["holiday_type"];
        $count = 0;
        $newsql = new ezSQL_mysql();
        for($i=0;$i<count($kids);$i++){
            if($isAll > 0) {
                $query = "update jxc_duty
                    set is_holiday = {$holidayType}
                        where atime = (select atime from (select atime from jxc_duty where id =".$kids[$i].") x)
                        and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
                $count += $newsql->query($query);
            } else {
                $query = "update jxc_duty
                    set is_holiday = {$holidayType}
                        where id = ".$kids[$i]."
                        and (owner ={$userID} or (select count(0) from jxc_menu where id=76 and INSTR(rank, '|".$userID."|') > 0))";
                $count += $newsql->query($query);
            }
        }
        return $count;
    }
    
    /**
     * 确认今天是否已经打卡
     * @return Ambigous <boolean, number, mixed>
     */
    function checkTodayStatus() {
        $userID = $_COOKIE['userID'];
        $atime = date("Y-m-d");
        
        $newsql = new ezSQL_mysql();
        $query = "select count(0) from jxc_duty where owner={$userID} and atime='{$atime}' and check_in is null and owner not in (24, 51)";
        return $newsql->get_var($query);
    }
    
    /**
     * 导出excel
     * @return Ambigous <boolean, number, mixed>
     */
	
    function exportExcel() {
        $title = "考勤";
		$headers = array('ID','日付','星期','规定出勤時刻','规定退勤时间','出勤時刻','開始時間','終了時間','退勤時刻','勤務時間(小时)','普通残業(小时)','深夜残業(小时)','休假','備考','勤務時間','普通残業','深夜残業');
        $results = initPage(true);
          
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
