<?php
require_once(dirname(__FILE__)."/include/config_base.php");
// 导入Excel文件
function uploadFile($file, $filetempname) {
    $importStat=array("succ"=>0,"msg"=>0);
    // 自己设置的上传文件存放路径
    $filePath = '/home/p-mon/pmon.jp/public_html/kura2014/upload/';
    $str = "";
    //因为是用的数组， 所以列数-1
    define("P_CODE_COLUMN", 0);
    define("FROM_LAB_COLUMN", 1);
    define("TO_LAB_COLUMN", 2);
    define("NUMBER_COLUMN", 3);
    
    
    
 // 下面的路径按照你PHPExcel的路径来修改
    set_include_path('/home/p-mon/pmon.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());

    require_once 'PHPExcel.php';
    require_once 'PHPExcel/IOFactory.php';
    require_once 'PHPExcel/Reader/Excel5.php';
    date_default_timezone_set("PRC");
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
        
        $nsql=New Dedesql();
        $nsql->ExecNoneQuery("BEGIN") or die(mysql_error());
        $nsql->setAutocommit(false);
        echo mysql_error();
        // 循环读取excel文件,读取一条,插入一条
        for ($j = 2; $j <= $highestRow; $j ++) {
            for ($k = 0; $k < $highestColumn; $k ++) {
                $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
//                 echo "$columnName$j"."----".$cellValue."----";
                $str .= $cellValue.'\\'; // 读取单元格
            }
//                 echo "<br/>";
            // explode:函数把字符串分割为数组。
            $strs = explode("\\", $str);
            $strs[P_CODE_COLUMN]=trim($strs[P_CODE_COLUMN]);
            $strs[FROM_LAB_COLUMN]=trim($strs[FROM_LAB_COLUMN]);
            $strs[TO_LAB_COLUMN]=trim($strs[TO_LAB_COLUMN]);
            $strs[NUMBER_COLUMN]=trim($strs[NUMBER_COLUMN]);
            if($strs[P_CODE_COLUMN] =="" || $strs[FROM_LAB_COLUMN] == "" || $strs[TO_LAB_COLUMN] == "" || $strs[NUMBER_COLUMN] == "") {
                $importStat["msg"] = 1;
                echo date("Ymd-H:i:s")."----A|".$j."出现错误,信息不完整";;
                error_log(date("Ymd-H:i:s")."----A|".$j."出现错误,信息不完整\n", 3, "logs/upload.log");
                break;
            }
            if($strs[FROM_LAB_COLUMN] == $strs[TO_LAB_COLUMN]) {
                $importStat["msg"] = 1;
                echo date("Ymd-H:i:s")."----A|".$j."出现错误,仓库号相同";;
                error_log(date("Ymd-H:i:s")."----A|".$j."出现错误,仓库号相同\n", 3, "logs/upload.log");
                break;
            }
            
            //检查数量是否符合逻辑
            $res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".$strs[P_CODE_COLUMN]."' and l_id='".$strs[FROM_LAB_COLUMN]."' and number >= '".$strs[NUMBER_COLUMN]."'") or die(mysql_errno());
            if(!mysql_fetch_array($res)) {
                $nsql->rollback();
                echo mysql_error();
                $importStat["msg"] = 1;
                echo date("Ymd-H:i:s")."----B|".$j."出现错误[该记录不存在或数量不足]商品CODE:".$strs[P_CODE_COLUMN]."---仓库号:".$strs[FROM_LAB_COLUMN];
                error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[该记录不存在或数量不足]商品CODE:".$strs[P_CODE_COLUMN]."---仓库号:".$strs[FROM_LAB_COLUMN]."\n", 3, "logs/upload.log");
                break;
            }
            $res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".$strs[P_CODE_COLUMN]."' and l_id='".$strs[TO_LAB_COLUMN]."'") or die(mysql_errno());
            if(!mysql_fetch_array($res)) {
                 $nsql->ExecuteNoneQuery("insert into #@__mainkc(`p_id`, `l_id`, `d_id`, `number`, `l_floor`, `l_shelf`, `l_zone`, `l_horizontal`, `l_vertical`, `dtime`) 
                     values ('".$strs[P_CODE_COLUMN]."', '".$strs[TO_LAB_COLUMN]."', '0', '0', '0', '0', '0', '0', '0', now())") or die(mysql_errno());
            }
            
            //开始更新
            $res = $nsql->ExecuteNoneQuery("update #@__mainkc set number = number - '".$strs[NUMBER_COLUMN]."'  where p_id = '".$strs[P_CODE_COLUMN]."' and l_id = '".$strs[FROM_LAB_COLUMN]."'") or die(mysql_errno());
            $res = $nsql->ExecuteNoneQuery("update #@__mainkc set number = number + '".$strs[NUMBER_COLUMN]."'  where p_id = '".$strs[P_CODE_COLUMN]."' and l_id = '".$strs[TO_LAB_COLUMN]."'") or die(mysql_errno());
            $importStat["succ"]++;
            $str = "";
        }
//         unlink($uploadfile); // 删除上传的excel文件
        $nsql->commit();
        $nsql->Close();
    } else {
        $importStat["msg"] = 5;
    }
    
    return $importStat;
}
?>