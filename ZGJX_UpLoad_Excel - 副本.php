<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
// 导入Excel文件
function uploadFile($file, $filetempname)
{
    
    // 自己设置的上传文件存放路径
    $filePath = 'upload/';
    $str = "";
    
    // 下面的路径按照你PHPExcel的路径来修改
    set_include_path('.' . PATH_SEPARATOR . 'D:\Apache2.2\htdocs\kura2014\PHPExcel' . PATH_SEPARATOR . get_include_path());
    
    require_once 'PHPExcel.php';
    require_once 'PHPExcel\IOFactory.php';
    require_once 'PHPExcel\Reader\Excel5.php';
    date_default_timezone_set("PRC");
    $filename = explode(".", $file); // 把上传的文件名以“.”好为准做一个数组。
    $time = date("Ymd-H_i_s"); // 去当前上传的时间
    $filename[0] .= $time; // 取文件名t替换
    $name = implode(".", $filename); // 上传后的文件名
    $uploadfile = $filePath . $name; // 上传后的文件名地址
                                 
    // move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    $result = move_uploaded_file($filetempname, $uploadfile); // 假如上传到当前目录下
    if ($result) // 如果上传文件成功，就执行导入excel操作
{
    if($filename[1]=="xlsx") {
        $objReader = PHPExcel_IOFactory::createReader('excel2007'); // use excel2007 for 2007 format
    } else {
        $objReader = PHPExcel_IOFactory::createReader('Excel5'); // use excel2007 for 2007 format
    }
        $objPHPExcel = $objReader->load($uploadfile);
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());; // 取得总列数

//         d 代表删除 u代表更新 n
        $insertsql="INSERT INTO `kura`.`jxc_basic_copy` (`cp_number`, `cp_tm`, `cp_name`, `cp_title`, `cp_detail`,
            `cp_gg`, `cp_categories`, `cp_categories_down`, `cp_dwname`, `cp_jj`, `cp_sale`, `cp_saleall`, 
            `cp_sale1`, `cp_sdate`, `cp_edate`, `cp_gys`, `cp_helpword`, `cp_helpword_1`, `cp_helpword_2`, 
            `cp_helpword_3`, `cp_bz`, `cp_style`, `cp_url`, `cp_url_1`, `cp_url_2`, `cp_url_3`, `cp_url_4`,
            `cp_browse_node_1`, `cp_browse_node_2`, `cp_dtime`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $deletesql="delete from #@__basic_copy where cp_number = ? and cp_tm=?";
        $updatesql="update #@__basic_copy set cp_name=? cp_title=? cp_detail=? cp_gg=? cp_categories=? 
            cp_categories_down=? cp_dwname=? cp_jj=? cp_sale=? cp_saleall=? cp_sale1=? cp_sdate=?
            cp_edate=? cp_gys=? cp_helpword=? cp_helpword_1=? cp_helpword_2=? cp_helpword_3=?
            cp_bz=? cp_style=? cp_url=? cp_url_1=? cp_url_2=? cp_url_3=? cp_url_4=? 
            cp_browse_node_1=? cp_browse_node_2=? cp_dtime=? where cp_number = ? and cp_tm=?";

        $nsql=New Dedesql();
        $mysqli = new mysqli("localhost", "root", "root", "kura", 3306);
        $mysqli->autocommit(false);
//         $nsql->ExecNoneQuery("set autocommit false;");
        for ($j = 2; $j <= $highestRow; $j ++) {
            for ($k = 0; $k < $highestColumn; $k ++) {
                $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                $cellValue=$sheet->getCell("$columnName$j")->getValue();
                $str .= $cellValue.'\\'; // 读取单元格
            }
            $strs = explode("\\", $str);
            switch ($strs[0]) {
                case "n":
                    $nsql->SetParameter("cp_number", $strs[1]);
                    $nsql->SetParameter("cp_tm", $strs[2]);
                    $res = $nsql->ExecuteNoneQuery("select count(0) from #@__basic_copy where cp_number = @cp_number and cp_tm=@cp_tm");
                    if(mysql_fetch_array($res)[0]) {
                        $mysqli->rollback();
                        echo $columnName."|".$j."出现错误";
                        error_log($columnName."|".$j."出现错误", 3, "logs/upload.log\n");
                        break 2;
                    }
                $mysqli_stmt=$mysqli->prepare($insertsql) or die($mysqli->error);
                $index = 1;
                $mysqli_stmt->bind_param("s", $strs[$index++]);
                $b = $mysqli_stmt->execute() or die($mysqli->error);
                break;
                case "u":
                $mysqli_stmt=$mysqli->prepare($updatesql);
                $index = 3;
                $mysqli_stmt->bind_param("ssssssssss", $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++]
                    , $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++]
                    , $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++], $strs[$index++]
                    , $strs[$index++], $strs[$index++], $strs[$index++], $strs[1], $strs[2]);
                $b = $mysqli_stmt->execute();
                break;
                case "d":
                $mysqli_stmt=$mysqli->prepare($deletesql);
                $index = 3;
                $mysqli_stmt->bind_param("ss", $strs[$index++], $strs[$index++]);
                $b = $mysqli_stmt->execute();
                break;
                
                default:
                    echo $j."行出现错误";
                        error_log($j."行出现错误", 3, "logs/upload.log\n");
                break;
            }
            if($b== false) {
                $mysqli->rollback();
//                 $nsql->ExecNoneQuery("roolback;");
            }
            $str = "";
        }
//         $nsql->ExecNoneQuery("commit;");
        $mysqli->commit();
//         $mysqli_stmt->close();
        $mysqli->close();
        
        unlink($uploadfile); // 删除上传的excel文件
        $nsql->Close();
        $msg = "导入成功！";
    } else {
        $msg = "导入失败！";
    }
    
    return $msg;
}
?>