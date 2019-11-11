<?php
require_once(dirname(__FILE__)."/include/config_base.php");
require_once(dirname(__FILE__)."/include/fix_mysql.inc.php");
// 导入Excel文件
function uploadFile($file, $filetempname) {
    $importStat=array("n"=>0,"u"=>0,"r"=>0,"t"=>0,"a"=>0,"d"=>0,"e"=>0,"m"=>0);
    // 自己设置的上传文件存放路径
    $filePath = '/home/p-mon/tousho.co.jp/public_html/kura2014/upload/';
    $str = "";
    
    // 下面的路径按照你PHPExcel的路径来修改
    set_include_path('/home/p-mon/tousho.co.jp/public_html/kura2014/PHPExcel' . PATH_SEPARATOR . get_include_path());
    
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
        //取第一行的内容，放入map中。记录每列对应的内容("表头"=>"列号")
        //CONTROL	P_CODE	BAR_CODE	PARENT	MAINKC_NUMBER	MAKER	TITLE	EXAMPLE	DETAIL	BULLET1	BULLET2	BULLET3	BULLET4	BULLET5	BULLET6	
        //CATOGRY	DW	TYPE	PRICE1	PRICE2	PRICE3	PRICE4	S_DATE	E_DATE	JJ	URL	URL1	URL2	URL3	URL4	BROWSE1	BROWSE2	KEYWORD1	KEYWORD2	KEYWORD3	KEYWORD4
        //KEYWORD5	KEYWORD6	KEYWORD7	KEYWORD8   KEYWORD9	KEYWORD10  KID
        //	L_ID	POSITION	NUMBER	STATE1	STATE2	STATE3	STATE4	STATE5	STATE6	STATE7	STATE8	STATE9	STATE10	NOTE	BZ
        $colHead = array();
        for ($k = 0; $k < $highestColumn; $k ++) {
            $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
            $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName"."1")->getValue();
            $colHead[$cellValue] = $k;
        }
        // 循环读取excel文件,读取一条,插入一条
        for ($j = 3; $j <= $highestRow; $j ++) {
            for ($k = 0; $k < $highestColumn; $k ++) {
                $columnName = PHPExcel_Cell::stringFromColumnIndex($k);
                $cellValue=$objPHPExcel->getActiveSheet()->getCell("$columnName$j")->getValue();
//                 echo "$columnName$j"."----".$cellValue."----";
                $str .= $cellValue.'\\'; // 读取单元格
            }
//                 echo "<br/>";
            // explode:函数把字符串分割为数组。
            $strs = explode("\\", $str);
            $strs[$colHead["P_CODE"]]=trim($strs[$colHead["P_CODE"]]);
            $strs[$colHead["BAR_CODE"]]=trim($strs[$colHead["BAR_CODE"]]);
            $strs[$colHead["KID"]]=trim($strs[$colHead["KID"]]);
            $strs[$colHead["L_ID"]]=trim($strs[$colHead["L_ID"]]);
            if(($strs[$colHead["P_CODE"]] =="" || $strs[$colHead["L_ID"]] == "")  || $strs[$colHead["KID"]] == "") {
                $importStat["m"] = 1;
                echo date("Ymd-H:i:s")."----B|".$j."出现错误 商品CODE 或 仓库号或 main_kc ID 为空";;
                error_log(date("Ymd-H:i:s")."----B|".$j."出现错误 商品CODE 或 仓库号或  main_kc ID 为空\n", 3, "logs/upload.log");
                break;
            }
            $b1 = true;
            $b2 = true;
            $b3 = true;
	        $cdh = str_replace('-','',GetDateMk(date(time())))."-ID".substr(GetCookie('VioomaUserID'), -3);
            switch ($strs[0]) {
                case "n":
                    $res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".$strs[$colHead["P_CODE"]]."' and l_id='".$strs[$colHead["L_ID"]]."'") or die(mysql_errno());
                    if(mysql_fetch_array($res)) {
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 1;
                        echo date("Ymd-H:i:s")."----B|".$j."出现错误[该记录已存在]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]];
                        error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[该记录已存在]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]]."\n", 3, "logs/upload.log");
                        break 2;
                    }
                    if($strs[$colHead["BAR_CODE"]] != "") {
                        $res = $nsql->ExecuteNoneQuery("select * from #@__basic where cp_tm='".$strs[$colHead["BAR_CODE"]]."'") or die(mysql_errno());
                        if(mysql_fetch_array($res)) {
                            $nsql->rollback();
                            echo mysql_error();
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----B|".$j."出现错误[BAR_CODE已存在]BAR_CODE:".$strs[$colHead["BAR_CODE"]];
                            error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[BAR_CODE已存在]BAR_CODE:".$strs[$colHead["BAR_CODE"]]."\n", 3, "logs/upload.log");
                            break 2;
                        }
                    }
                    if($strs[$colHead["PRICE1"]] == "") {
                        $importStat["m"] = 1;
                        echo date("Ymd-H:i:s")."----B|".$j."出现错误[仕入れ価格]不能空:".$strs[$colHead["BAR_CODE"]];
                        error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[仕入れ価格]不能空:".$strs[$colHead["BAR_CODE"]]."\n", 3, "logs/upload.log");
                        break 2;
                    }
                    $res = $nsql->ExecuteNoneQuery("select * from #@__basic where cp_number = '".$strs[$colHead["P_CODE"]]."'") or die(mysql_errno());
                    if(!mysql_fetch_array($res)) {
//                         $nsql->rollback();
//                         echo mysql_error();
//                         $importStat["m"] = 1;
//                         echo date("Ymd-H:i:s")."----B|".$j."出现错误[该记录已存在]".$strs[$colHead["P_CODE"]]."---".$strs[BAR_CODE"]];
//                         error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[该记录已存在]\n", 3, "logs/upload.log");
//                         break 2;
//                     }
                        $catilogs = explode("/", trim($strs[$colHead["CATOGRY"]]));
    //                     $res = $nsql->ExecuteNoneQuery("SELECT id FROM `#@__categories` where categories = '{$catilogs[0]}'");
    //                     $cat1 = mysql_fetch_row($res)[0];
    //                     $res = $nsql->ExecuteNoneQuery("SELECT id FROM `#@__categories` where reid = $cat1 and categories = '{$catilogs[1]}'") or die(mysql_errno());
    //                     $cat2 = mysql_fetch_row($res)[0];
                        						
                        $notesql="INSERT INTO `#@__basic` (`cp_number`, `cp_tm`, `cp_parent`, `cp_name`, `cp_title`, `cp_detail`,
                        `cp_gg`, cp_bullet_1,cp_bullet_2,cp_bullet_3,cp_bullet_4,cp_bullet_5,cp_bullet_6,`cp_categories`, `cp_categories_down`, `cp_dwname`, `cp_style`, `cp_jj`, `cp_sale`, `cp_saleall`,
                        `cp_sale1`, `cp_sdate`, `cp_edate`, `cp_gys`, `cp_url`, `cp_url_1`, `cp_url_2`, `cp_url_3`, `cp_url_4`,
                        `cp_browse_node_1`, `cp_browse_node_2`, `cp_helpword`, `cp_helpword_1`, `cp_helpword_2`, `cp_helpword_3`, 
                        `cp_helpword_4`, `cp_helpword_5`, `cp_helpword_6`, `cp_helpword_7`, `cp_helpword_8`, `cp_helpword_9`, `cp_bz`, `cp_dtime`
                        ) VALUES ('".trim($strs[$colHead["P_CODE"]])."','".trim($strs[$colHead["BAR_CODE"]])."','".trim($strs[$colHead["PARENT"]])."','";
//                        $colHead["MAINKC_NUMBER"];//跳过在库数更新列
                        $notesql.=trim($strs[$colHead["MAKER"]])."','".trim($strs[$colHead["TITLE"]])
                        ."','".trim($strs[$colHead["EXAMPLE"]])."\n商品コード：".trim($strs[$colHead["P_CODE"]])."','".trim($strs[$colHead["DETAIL"]])."','".trim($strs[$colHead["BULLET1"]])
						."','".trim($strs[$colHead["BULLET2"]])."','".trim($strs[$colHead["BULLET3"]])."','".trim($strs[$colHead["BULLET4"]])."','".trim($strs[$colHead["BULLET5"]])
						."','".trim($strs[$colHead["BULLET6"]])."','{$catilogs[0]}','{$catilogs[1]}','";
                        $notesql.= trim($strs[$colHead["DW"]])."', '".trim($strs[$colHead["TYPE"]])."', '".trim($strs[$colHead["PRICE1"]])."', '".trim($strs[$colHead["PRICE2"]])."', '".trim($strs[$colHead["PRICE3"]])."', '".trim($strs[$colHead["PRICE4"]])
                        ."',STR_TO_DATE('".trim($strs[$colHead["S_DATE"]])."','%Y%m%d'), STR_TO_DATE('".trim($strs[$colHead["E_DATE"]])."','%Y%m%d'),'".trim($strs[$colHead["JJ"]])
                        ."','".trim($strs[$colHead["URL"]])."','".trim($strs[$colHead["URL1"]])."','".trim($strs[$colHead["URL2"]])."','".trim($strs[$colHead["URL3"]])
                        ."','".trim($strs[$colHead["URL4"]])."','".trim($strs[$colHead["BROWSE1"]])."','".trim($strs[$colHead["BROWSE2"]])."','".trim($strs[$colHead["KEYWORD1"]])
                        ."','".trim($strs[$colHead["KEYWORD2"]])."','".trim($strs[$colHead["KEYWORD3"]])."','".trim($strs[$colHead["KEYWORD4"]])."','".trim($strs[$colHead["KEYWORD5"]])
                        ."','".trim($strs[$colHead["KEYWORD6"]])."','".trim($strs[$colHead["KEYWORD7"]])
                        ."','".trim($strs[$colHead["KEYWORD8"]])."','".trim($strs[$colHead["KEYWORD9"]])."','".trim($strs[$colHead["KEYWORD10"]])."','".trim($strs[$colHead["BZ"]])."',now()) ";
//     	                die();
						//替换所有的 _x000D_
						$notesql = str_replace("_x000D_","", $notesql);
						//替换所有的 全角空格
						$notesql = str_replace(" "," ", $notesql);
						//替换所有的 全角'～'
						$notesql = str_replace("～","~", $notesql);
                        $b1 = $nsql->ExecuteNoneQuery($notesql);
                        echo mysql_error();
                    }
                    $stores = explode("-", $strs[$colHead["POSITION"]]);
                    if(count($stores) < 5) {
                    	$b2 = false;
                        echo date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]";
                        error_log(date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]\n", 3, "logs/upload.log");
                        break 2;
                    }
                    $number = trim($strs[$colHead["NUMBER"]]) == "" ? 0 : trim($strs[$colHead["NUMBER"]]);
//                     $state2 = trim($strs[$colHead["STATE2"]]) == "" ? 60 : trim($strs[$colHead["STATE2"]]);
                    $index=0;
                    $notesql="INSERT INTO `#@__mainkc` (`p_id`, `l_id`, `d_id`, `number`, `l_floor`, `l_shelf`, `l_zone`, `l_horizontal`, `l_vertical`, 
                    `l_state1`, `l_state2`, `l_state3`, `l_state4`, `l_state5`, `l_state6`, `l_state7`, `l_state8`, `l_state9`, `l_state10`, 
                    `l_note`, `dtime`
                    ) VALUES ('{$strs[$colHead["P_CODE"]]}','{$strs[$colHead["L_ID"]]}','0','{$number}','{$stores[$index++]}',
                    '{$stores[$index++]}', '{$stores[$index++]}', '{$stores[$index++]}', '{$stores[$index++]}',
                    '{$strs[$colHead["STATE1"]]}','{$state2}','{$strs[$colHead["STATE3"]]}','{$strs[$colHead["STATE4"]]}','{$strs[$colHead["STATE5"]]}',
                    '{$strs[$colHead["STATE6"]]}','{$strs[$colHead["STATE7"]]}','{$strs[$colHead["STATE8"]]}','{$strs[$colHead["STATE9"]]}','{$strs[$colHead["STATE10"]]}','{$strs[$colHead["NOTE"]]}', now()) ";
	                $b2 = $nsql->ExecuteNoneQuery($notesql);
	                echo mysql_error();
	                
                    $notesql="INSERT INTO `#@__kc` (`productid`, `number`, `labid`, `rdh`, `rk_price`, `dtime`, `bank`, `bz`, `tantousyaid`) 
                    VALUES ('{$strs[$colHead["P_CODE"]]}', {$number}, '{$strs[$colHead["L_ID"]]}', '{$cdh}', '{$strs[$colHead["PRICE4"]]}', now(), 1, '', '".GetCookie('VioomaUserID')."')";
	                $b3 = $nsql->ExecuteNoneQuery($notesql);
	                echo mysql_error();
                    if(!$b1||!$b2||!$b3) {
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 1;
                        break 2;
                    }
                    $importStat[$strs[0]]++;
                break;
                //强制更新为excel里填的任何内容(包括空白)
                case "t":
			$res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".trim($strs[$colHead["P_CODE"]])."' and l_id='".trim($strs[$colHead["L_ID"]])."'") or die(mysql_errno());
                    if(!mysql_fetch_array($res)) {
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 1;
                        echo date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]];
                        error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]]."\n", 3, "logs/upload.log");
                        break 2;
                    }
                    $notesql="update `#@__basic` set";

                    !isset($colHead["BAR_CODE"]) ? $notesql.="" : $notesql.="`cp_tm` ='".$strs[$colHead["BAR_CODE"]]."',";
                    !isset($colHead["PARENT"]) ? $notesql.="" : $notesql.="`cp_parent` ='".$strs[$colHead["PARENT"]]."',";
                    !isset($colHead["MAKER"]) ? $notesql.="" : $notesql.="`cp_name` ='".$strs[$colHead["MAKER"]]."',";
                    !isset($colHead["TITLE"]) ? $notesql.="" : $notesql.="`cp_title` ='".$strs[$colHead["TITLE"]]."',";
                    !isset($colHead["EXAMPLE"]) ? $notesql.="" : $notesql.="`cp_detail` ='".$strs[$colHead["EXAMPLE"]]."',";
                    !isset($colHead["DETAIL"]) ? $notesql.="" : $notesql.="`cp_gg` ='".$strs[$colHead["DETAIL"]]."',";
                    !isset($colHead["BULLET1"]) ? $notesql.="" : $notesql.="`cp_bullet_1` ='".$strs[$colHead["BULLET1"]]."',";
                    !isset($colHead["BULLET2"]) ? $notesql.="" : $notesql.="`cp_bullet_2` ='".$strs[$colHead["BULLET2"]]."',";
                    !isset($colHead["BULLET3"]) ? $notesql.="" : $notesql.="`cp_bullet_3` ='".$strs[$colHead["BULLET3"]]."',";
                    !isset($colHead["BULLET4"]) ? $notesql.="" : $notesql.="`cp_bullet_4` ='".$strs[$colHead["BULLET4"]]."',";
                    !isset($colHead["BULLET5"]) ? $notesql.="" : $notesql.="`cp_bullet_5` ='".$strs[$colHead["BULLET5"]]."',";
                    !isset($colHead["BULLET6"]) ? $notesql.="" : $notesql.="`cp_bullet_6` ='".$strs[$colHead["BULLET6"]]."',";
                    if(isset($colHead["CATOGRY"])) {
                    	$catilogs = explode("/", $strs[$colHead["CATOGRY"]]);
                    	$notesql.="`cp_categories` ='".$catilogs[0]."',";
                    	$notesql.="`cp_categories_down` ='".$catilogs[1]."',";
                    }
                    !isset($colHead["DW"]) ? $notesql.="" : $notesql.="`cp_dwname` ='".$strs[$colHead["DW"]]."',";
                    !isset($colHead["TYPE"]) ? $notesql.="" : $notesql.="`cp_style` ='".$strs[$colHead["TYPE"]]."',";
                    !isset($colHead["PRICE1"]) ? $notesql.="" : $notesql.="`cp_jj` ='".$strs[$colHead["PRICE1"]]."',";
                    !isset($colHead["PRICE2"]) ? $notesql.="" : $notesql.="`cp_sale` ='".$strs[$colHead["PRICE2"]]."',";
                    !isset($colHead["PRICE3"]) ? $notesql.="" : $notesql.="`cp_saleall` ='".$strs[$colHead["PRICE3"]]."',";
                    !isset($colHead["PRICE4"]) ? $notesql.="" : $notesql.="`cp_sale1` ='".$strs[$colHead["PRICE4"]]."',";
                    !isset($colHead["S_DATE"]) ? $notesql.="" : $notesql.="`cp_sdate` =STR_TO_DATE('".$strs[$colHead["S_DATE"]]."','%Y%m%d'),";
                    !isset($colHead["E_DATE"]) ? $notesql.="" : $notesql.="`cp_edate` =STR_TO_DATE('".$strs[$colHead["E_DATE"]]."','%Y%m%d'),";
                    !isset($colHead["JJ"]) ? $notesql.="" : $notesql.="`cp_gys` ='".$strs[$colHead["JJ"]]."',";
                    !isset($colHead["URL"]) ? $notesql.="" : $notesql.="`cp_url` ='".$strs[$colHead["URL"]]."',";
                    !isset($colHead["URL1"]) ? $notesql.="" : $notesql.="`cp_url_1` ='".$strs[$colHead["URL1"]]."',";
                    !isset($colHead["URL2"]) ? $notesql.="" : $notesql.="`cp_url_2` ='".$strs[$colHead["URL2"]]."',";
                    !isset($colHead["URL3"]) ? $notesql.="" : $notesql.="`cp_url_3` ='".$strs[$colHead["URL3"]]."',";
                    !isset($colHead["URL4"]) ? $notesql.="" : $notesql.="`cp_url_4` ='".$strs[$colHead["URL4"]]."',";
                    !isset($colHead["BROWSE1"]) ? $notesql.="" : $notesql.="`cp_browse_node_1` ='".$strs[$colHead["BROWSE1"]]."',";
                    !isset($colHead["BROWSE2"]) ? $notesql.="" : $notesql.="`cp_browse_node_2` ='".$strs[$colHead["BROWSE2"]]."',";
                    !isset($colHead["KEYWORD1"]) ? $notesql.="" : $notesql.="`cp_helpword` ='".$strs[$colHead["KEYWORD1"]]."',";
                    !isset($colHead["KEYWORD2"]) ? $notesql.="" : $notesql.="`cp_helpword_1` ='".$strs[$colHead["KEYWORD2"]]."',";
                    !isset($colHead["KEYWORD3"]) ? $notesql.="" : $notesql.="`cp_helpword_2` ='".$strs[$colHead["KEYWORD3"]]."',";
                    !isset($colHead["KEYWORD4"]) ? $notesql.="" : $notesql.="`cp_helpword_3` ='".$strs[$colHead["KEYWORD4"]]."',";
                    !isset($colHead["KEYWORD5"]) ? $notesql.="" : $notesql.="`cp_helpword_4` ='".$strs[$colHead["KEYWORD5"]]."',";
                    !isset($colHead["KEYWORD6"]) ? $notesql.="" : $notesql.="`cp_helpword_5` ='".$strs[$colHead["KEYWORD6"]]."',";
                    !isset($colHead["KEYWORD7"]) ? $notesql.="" : $notesql.="`cp_helpword_6` ='".$strs[$colHead["KEYWORD7"]]."',";
                    !isset($colHead["KEYWORD8"]) ? $notesql.="" : $notesql.="`cp_helpword_7` ='".$strs[$colHead["KEYWORD8"]]."',";
                    !isset($colHead["KEYWORD9"]) ? $notesql.="" : $notesql.="`cp_helpword_8` ='".$strs[$colHead["KEYWORD9"]]."',";
                    !isset($colHead["KEYWORD10"]) ? $notesql.="" : $notesql.="`cp_helpword_9` ='".$strs[$colHead["KEYWORD10"]]."',";
                    !isset($colHead["BZ"]) ? $notesql.="" : $notesql.="`cp_bz` ='".$strs[$colHead["BZ"]]."',";
                    $notesql.=" cp_dtime = cp_dtime where `cp_number` = '".$strs[$colHead["P_CODE"]]."'";
					//替换所有的 _x000D_
					$notesql = str_replace("_x000D_","", $notesql);
					//替换所有的 全角空格
					$notesql = str_replace(" "," ", $notesql);
					//替换所有的 全角'～'
					$notesql = str_replace("～","~", $notesql);
                    $b1 = $nsql->ExecuteNoneQuery($notesql);
                    echo mysql_error();
                    $b2 = true;
                $importStat[$strs[0]]++;
                break;
                //更新为excel里填的任何内容(空白不更新)
                case "u":
                case "a":
                    $number = trim($strs[$colHead["NUMBER"]]);
                    if(trim($strs[$colHead["KID"]])) {
    			        $res = $nsql->ExecuteNoneQuery("select `number` + ".($number == "" ? 0 : $number)." from #@__mainkc where kid = '".trim($strs[$colHead["KID"]])."' and p_id = '".$strs[$colHead["P_CODE"]]."'") or die(mysql_errno());
                        if(!mysql_fetch_array($res)) {
                            $nsql->rollback();
                            echo mysql_error();
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."--- main_kc ID:".$strs[$colHead["KID"]];
                            error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---main_kc ID:".$strs[$colHead["KID"]]."\n", 3, "logs/upload.log");
                            break 2;
                        } else if(mysql_fetch_array($res)[0] < 0) {
                            $nsql->rollback();
                            echo mysql_error();
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----B|".$j."出现错误[在库数是负数]商品CODE:".$strs[$colHead["P_CODE"]]."--- main_kc ID:".$strs[$colHead["KID"]];
                            error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，在库数是负数]商品CODE:".$strs[$colHead["P_CODE"]]."---main_kc ID:".$strs[$colHead["KID"]]."\n", 3, "logs/upload.log");
                            break 2;
                        }
                    } else {
                        $res = $nsql->ExecuteNoneQuery("select `number` + ".($number == "" ? 0 : $number)." from #@__mainkc where p_id = '".trim($strs[$colHead["P_CODE"]])."' and l_id='".trim($strs[$colHead["L_ID"]])."'") or die(mysql_errno());
                        if(!mysql_fetch_array($res)) {
                            $nsql->rollback();
                            echo mysql_error();
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]];
                            error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]]."\n", 3, "logs/upload.log");
                            break 2;
                        } else if(mysql_fetch_array($res)[0] < 0) {
                            $nsql->rollback();
                            echo mysql_error();
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----B|".$j."出现错误[在库数是负数]商品CODE:".$strs[$colHead["P_CODE"]]."--- main_kc ID:".$strs[$colHead["KID"]];
                            error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，在库数是负数]商品CODE:".$strs[$colHead["P_CODE"]]."---main_kc ID:".$strs[$colHead["KID"]]."\n", 3, "logs/upload.log");
                            break 2;
                        }
                    }
                    $notesql="update `#@__basic` set";

                    ($strs[$colHead["PARENT"]] = trim($strs[$colHead["PARENT"]])) == "" ? $notesql.="" : $notesql.="`cp_parent` ='".$strs[$colHead["PARENT"]]."',";
                    ($strs[$colHead["MAKER"]] = trim($strs[$colHead["MAKER"]])) == "" ? $notesql.="" : $notesql.="`cp_name` ='".$strs[$colHead["MAKER"]]."',";
                    ($strs[$colHead["TITLE"]] = trim($strs[$colHead["TITLE"]])) == "" ? $notesql.="" : $notesql.="`cp_title` ='".$strs[$colHead["TITLE"]]."',";
                    ($strs[$colHead["EXAMPLE"]] = trim($strs[$colHead["EXAMPLE"]])) == "" ? $notesql.="" : $notesql.="`cp_detail` ='".$strs[$colHead["EXAMPLE"]]."',";
                    ($strs[$colHead["DETAIL"]] = trim($strs[$colHead["DETAIL"]])) == "" ? $notesql.="" : $notesql.="`cp_gg` ='".$strs[$colHead["DETAIL"]]."',";
                    ($strs[$colHead["BULLET1"]] = trim($strs[$colHead["BULLET1"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_1` ='".$strs[$colHead["BULLET1"]]."',";
                    ($strs[$colHead["BULLET2"]] = trim($strs[$colHead["BULLET2"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_2` ='".$strs[$colHead["BULLET2"]]."',";
                    ($strs[$colHead["BULLET3"]] = trim($strs[$colHead["BULLET3"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_3` ='".$strs[$colHead["BULLET3"]]."',";
                    ($strs[$colHead["BULLET4"]] = trim($strs[$colHead["BULLET4"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_4` ='".$strs[$colHead["BULLET4"]]."',";
                    ($strs[$colHead["BULLET5"]] = trim($strs[$colHead["BULLET5"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_5` ='".$strs[$colHead["BULLET5"]]."',";
                    ($strs[$colHead["BULLET6"]] = trim($strs[$colHead["BULLET6"]])) == "" ? $notesql.="" : $notesql.="`cp_bullet_6` ='".$strs[$colHead["BULLET6"]]."',";
                    if(($strs[$colHead["CATOGRY"]] = trim($strs[$colHead["CATOGRY"]])) != null) {
                    	$catilogs = explode("/", $strs[$colHead["CATOGRY"]]);
                    	$notesql.="`cp_categories` ='".$catilogs[0]."',";
                    	$notesql.="`cp_categories_down` ='".$catilogs[1]."',";
                    }
                    ($strs[$colHead["DW"]] = trim($strs[$colHead["DW"]])) == "" ? $notesql.="" : $notesql.="`cp_dwname` ='".$strs[$colHead["DW"]]."',";
                    ($strs[$colHead["TYPE"]] = trim($strs[$colHead["TYPE"]])) == "" ? $notesql.="" : $notesql.="`cp_style` ='".$strs[$colHead["TYPE"]]."',";
                    ($strs[$colHead["PRICE1"]] = trim($strs[$colHead["PRICE1"]])) == "" ? $notesql.="" : $notesql.="`cp_jj` ='".$strs[$colHead["PRICE1"]]."',";
                    ($strs[$colHead["PRICE2"]] = trim($strs[$colHead["PRICE2"]])) == "" ? $notesql.="" : $notesql.="`cp_sale` ='".$strs[$colHead["PRICE2"]]."',";
                    ($strs[$colHead["PRICE3"]] = trim($strs[$colHead["PRICE3"]])) == "" ? $notesql.="" : $notesql.="`cp_saleall` ='".$strs[$colHead["PRICE3"]]."',";
                    ($strs[$colHead["PRICE4"]] = trim($strs[$colHead["PRICE4"]])) == "" ? $notesql.="" : $notesql.="`cp_sale1` ='".$strs[$colHead["PRICE4"]]."',";
                    ($strs[$colHead["S_DATE"]] = trim($strs[$colHead["S_DATE"]])) == "" ? $notesql.="" : $notesql.="`cp_sdate` =STR_TO_DATE('".$strs[$colHead["S_DATE"]]."','%Y%m%d'),";
                    ($strs[$colHead["E_DATE"]] = trim($strs[$colHead["E_DATE"]])) == "" ? $notesql.="" : $notesql.="`cp_edate` =STR_TO_DATE('".$strs[$colHead["E_DATE"]]."','%Y%m%d'),";
                    ($strs[$colHead["JJ"]] = trim($strs[$colHead["JJ"]])) == "" ? $notesql.="" : $notesql.="`cp_gys` ='".$strs[$colHead["JJ"]]."',";
                    ($strs[$colHead["URL"]] = trim($strs[$colHead["URL"]])) == "" ? $notesql.="" : $notesql.="`cp_url` ='".$strs[$colHead["URL"]]."',";
                    ($strs[$colHead["URL1"]] = trim($strs[$colHead["URL1"]])) == "" ? $notesql.="" : $notesql.="`cp_url_1` ='".$strs[$colHead["URL1"]]."',";
                    ($strs[$colHead["URL2"]] = trim($strs[$colHead["URL2"]])) == "" ? $notesql.="" : $notesql.="`cp_url_2` ='".$strs[$colHead["URL2"]]."',";
                    ($strs[$colHead["URL3"]] = trim($strs[$colHead["URL3"]])) == "" ? $notesql.="" : $notesql.="`cp_url_3` ='".$strs[$colHead["URL3"]]."',";
                    ($strs[$colHead["URL4"]] = trim($strs[$colHead["URL4"]])) == "" ? $notesql.="" : $notesql.="`cp_url_4` ='".$strs[$colHead["URL4"]]."',";
                    ($strs[$colHead["BROWSE1"]] = trim($strs[$colHead["BROWSE1"]])) == "" ? $notesql.="" : $notesql.="`cp_browse_node_1` ='".$strs[$colHead["BROWSE1"]]."',";
                    ($strs[$colHead["BROWSE2"]] = trim($strs[$colHead["BROWSE2"]])) == "" ? $notesql.="" : $notesql.="`cp_browse_node_2` ='".$strs[$colHead["BROWSE2"]]."',";
                    ($strs[$colHead["KEYWORD1"]] = trim($strs[$colHead["KEYWORD1"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword` ='".$strs[$colHead["KEYWORD1"]]."',";
                    ($strs[$colHead["KEYWORD2"]] = trim($strs[$colHead["KEYWORD2"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_1` ='".$strs[$colHead["KEYWORD2"]]."',";
                    ($strs[$colHead["KEYWORD3"]] = trim($strs[$colHead["KEYWORD3"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_2` ='".$strs[$colHead["KEYWORD3"]]."',";
                    ($strs[$colHead["KEYWORD4"]] = trim($strs[$colHead["KEYWORD4"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_3` ='".$strs[$colHead["KEYWORD4"]]."',";
                    ($strs[$colHead["KEYWORD5"]] = trim($strs[$colHead["KEYWORD5"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_4` ='".$strs[$colHead["KEYWORD5"]]."',";
                    ($strs[$colHead["KEYWORD6"]] = trim($strs[$colHead["KEYWORD6"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_5` ='".$strs[$colHead["KEYWORD6"]]."',";
                    ($strs[$colHead["KEYWORD7"]] = trim($strs[$colHead["KEYWORD7"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_6` ='".$strs[$colHead["KEYWORD7"]]."',";
                    ($strs[$colHead["KEYWORD8"]] = trim($strs[$colHead["KEYWORD8"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_7` ='".$strs[$colHead["KEYWORD8"]]."',";
                    ($strs[$colHead["KEYWORD9"]] = trim($strs[$colHead["KEYWORD9"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_8` ='".$strs[$colHead["KEYWORD9"]]."',";
                    ($strs[$colHead["KEYWORD10"]] = trim($strs[$colHead["KEYWORD10"]])) == "" ? $notesql.="" : $notesql.="`cp_helpword_9` ='".$strs[$colHead["KEYWORD10"]]."',";
                    ($strs[$colHead["BZ"]] = trim($strs[$colHead["BZ"]])) == "" ? $notesql.="" : $notesql.="`cp_bz` ='".$strs[$colHead["BZ"]]."',";
                    $notesql.=" cp_dtime = cp_dtime where `cp_number` = '".$strs[$colHead["P_CODE"]]."'";
					//替换所有的 _x000D_
					$notesql = str_replace("_x000D_","", $notesql);
					//替换所有的 全角空格
					$notesql = str_replace(" "," ", $notesql);
					//替换所有的 全角'～'
					$notesql = str_replace("～","~", $notesql);
                    $b1 = $nsql->ExecuteNoneQuery($notesql);
                    echo mysql_error();
                    $b2 = true;
                    if(trim($strs[$colHead["POSITION"]]) != "") {
                        $stores = explode("-", trim($strs[$colHead["POSITION"]]));
                        if(count($stores) < 5) {
                   		    $b2 = false;
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]";
                            error_log(date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]\n", 3, "logs/upload.log");
                            break 2;
                        }
                        $index=0;
                        $notesql="update `#@__mainkc` set ";
//                         `l_floor` = '".trim($stores[$index++])."',
//                         `l_shelf` ='".trim($stores[$index++])."',
//                         `l_zone` ='".trim($stores[$index++])."',
//                         `l_horizontal` ='".trim($stores[$index++])."', 
//                         `l_vertical` ='".trim($stores[$index++])."',";
                        $notesql .= (trim($strs[$colHead["STATE1"]]) == "" ? "" : "`l_state1` ='".trim($strs[$colHead["STATE1"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE2"]]) == "" ? "" : "`l_state2` ='".trim($strs[$colHead["STATE2"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE3"]]) == "" ? "" : "`l_state3` ='".trim($strs[$colHead["STATE3"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE4"]]) == "" ? "" : "`l_state4` ='".trim($strs[$colHead["STATE4"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE5"]]) == "" ? "" : "`l_state5` ='".trim($strs[$colHead["STATE5"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE6"]]) == "" ? "" : "`l_state6` ='".trim($strs[$colHead["STATE6"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE7"]]) == "" ? "" : "`l_state7` ='".trim($strs[$colHead["STATE7"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE8"]]) == "" ? "" : "`l_state8` ='".trim($strs[$colHead["STATE8"]])."',");
                        $notesql .= (trim($strs[$colHead["STATE9"]]) == "" ? "" : "`l_state9` ='".trim($strs[$colHead["STATE9"]])."',");
                        $notesql .= (trim($strs[$colHead["NOTE"]]) == "" ? "" : "`l_note` ='".trim($strs[$colHead["NOTE"]])."', ");
                        
                        $notesql .= "`number` = `number` + ".(trim($number) == "" ? 0 : trim($number)).",".
                        " `dtime` = ".(trim($number) > 0 ? '`dtime`' : 'now()');
                        if(trim($strs[$colHead["KID"]])) {
                            $notesql .= "where `kid` = '".trim($strs[$colHead["KID"]])."'";
                        } else {
                            $notesql .= " where `l_id` = '".trim($strs[$colHead["L_ID"]])."' and  p_id = '".trim($strs[$colHead["P_CODE"]])."'";
                        }
                        $b2 = $nsql->ExecuteNoneQuery($notesql);
                        echo mysql_error();
                        
                        //根据子商品id 更新父商品数量也做相应的加减
                        if(trim($number) < 0) {
                            $notesql="update `#@__mainkc` set ";
                            $notesql .= "`number` = `number` + ".trim($number).",".
                            " `dtime` = now()
                            where p_id = (select cp_parent from #@__basic where cp_number = '".trim($strs[$colHead["P_CODE"]])."')";
                            $b2 = $nsql->ExecuteNoneQuery($notesql);
                            echo mysql_error();
                        }
						
                        $index=0;
					    //根据操作类型更新s_type, 0=正常贩卖报告,1=amazon
					    $s_type = ($strs[0] == "u" ? 0 : 1);
						if($number > 0) {
							$notesql="INSERT INTO `#@__kc` (`productid`, `number`, `labid`, `rdh`, `rk_price`, `dtime`, `bank`, `labfloor`, `labshelf`, `labzone`, `labhorizontal`, `labvertical`, `tantousyaid`, `s_type`) 
							VALUES ('{$strs[$colHead["P_CODE"]]}', {$number}, '{$strs[$colHead["L_ID"]]}', '{$cdh}', '{$strs[$colHead["PRICE4"]]}', now(), 1, '".trim($stores[$index++])."', '".trim($stores[$index++])."','".trim($stores[$index++])."','".trim($stores[$index++])."','".trim($stores[$index++])."','".GetCookie('VioomaUserID')."', '".$s_type."')";
							$b3 = $nsql->ExecuteNoneQuery($notesql);
							echo mysql_error();
						} else if($number < 0) {
							$notesql="INSERT INTO `#@__sale` (`productid`, `number`, `salelab`, `rdh`, `sale`, `dtime`, `labfloor`, `labshelf`, `labzone`, `labhorizontal`, `labvertical`, `tantousyaid`, `s_type`, member) 
							VALUES ('{$strs[$colHead["P_CODE"]]}', -{$number}, '{$strs[$colHead["L_ID"]]}', '{$cdh}', (select cp_sale1 from #@__basic where cp_number='{$strs[$colHead["P_CODE"]]}'), now(),
							'".trim($stores[$index++])."', '".trim($stores[$index++])."','".trim($stores[$index++])."','".trim($stores[$index++])."','".trim($stores[$index++])."', '".GetCookie('VioomaUserID')."', '".$s_type."', '{$strs[$colHead["STATE8"]]}')";
							$b3 = $nsql->ExecuteNoneQuery($notesql);
							echo mysql_error();
						}
						if(trim($strs[$colHead["MAINKC_NUMBER"]]) != "" && trim($strs[$colHead["MAINKC_NUMBER"]])  >= 0) {
						    $index=0;
						    $notesql="update `#@__mainkc` set
						          `number` = ".trim($strs[$colHead["MAINKC_NUMBER"]]).",".
						        " `dtime` = now()";
						    if(trim($strs[$colHead["KID"]])) {
                                $notesql .= " where `kid` = '".trim($strs[$colHead["KID"]])."'";
						    } else {
                                $notesql .= " where `l_id` = '".trim($strs[$colHead["L_ID"]])."' and  p_id = '".trim($strs[$colHead["P_CODE"]])."'";
						    }
						    $nsql->ExecuteNoneQuery($notesql);
						    echo mysql_error();
						}
					}
                $importStat[$strs[0]]++;
                break;
                case "r":
			     $res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".trim($strs[$colHead["P_CODE"]])."' and l_id='".trim($strs[$colHead["L_ID"]])."'") or die(mysql_errno());
                    if(!mysql_fetch_array($res)) {
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 1;
                        echo date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]];
                        error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]]."\n", 3, "logs/upload.log");
                        break 2;
                    }
                    $b2 = true;
                    if(trim($strs[$colHead["POSITION"]]) != "") {
                        $stores = explode("-", trim($strs[$colHead["POSITION"]]));
                        if(count($stores) < 5) {
                   		    $b2 = false;
                            $importStat["m"] = 1;
                            echo date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]";
                            error_log(date("Ymd-H:i:s")."----".PHPExcel_Cell::stringFromColumnIndex($colHead["POSITION"])."|".$j."出现错误[库存位置格式错误]\n", 3, "logs/upload.log");
                            break 2;
                        }
                        $number = trim($strs[$colHead["NUMBER"]]);
                        $index=0;
                        $notesql="update `#@__mainkc` set 
                        `l_floor` = '".trim($stores[$index++])."',
                        `l_shelf` ='".trim($stores[$index++])."',
                        `l_zone` ='".trim($stores[$index++])."',
                        `l_horizontal` ='".trim($stores[$index++])."', 
                        `l_vertical` ='".trim($stores[$index++])."'
                        where `l_id` = '".trim($strs[$colHead["L_ID"]])."' and  p_id = '".trim($strs[$colHead["P_CODE"]])."'";
                        $b2 = $nsql->ExecuteNoneQuery($notesql);
                        echo mysql_error();
					}
                    if(!$b2) {
                        $nsql->rollback();
                        echo mysql_error();
                        echo date("Ymd-H:i:s")."----".$j."行出现错误";
                        error_log(date("Ymd-H:i:s")."----".$j."行出现错误\n", 3, "logs/upload.log");
                        $importStat["m"] = 2;
                        break 2;
                    }
                $importStat[$strs[0]]++;
                break;
                case "d":
                    $res = $nsql->ExecuteNoneQuery("select * from #@__mainkc where p_id = '".$strs[$colHead["P_CODE"]]."' and l_id='".$strs[$colHead["L_ID"]]."'") or die(mysql_errno());
                    if(!mysql_fetch_array($res)) {
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 1;
                        echo date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]];
                        error_log(date("Ymd-H:i:s")."----B|".$j."出现错误[记录无效，无法完成操作]商品CODE:".$strs[$colHead["P_CODE"]]."---仓库号:".$strs[$colHead["L_ID"]]."\n", 3, "logs/upload.log");
                        break 2;
                    }
                    $notesql="delete from `#@__basic` where `cp_number` = '{$strs[$colHead["P_CODE"]]}'";
                    $b1 = $nsql->ExecuteNoneQuery($notesql);
                    $notesql="delete from  `#@__mainkc` where p_id = '{$strs[$colHead["P_CODE"]]}'";
                    $b2 = $nsql->ExecuteNoneQuery($notesql);
                    echo mysql_error();
                    if(!$b2) {
                        echo date("Ymd-H:i:s")."----".$j."行出现错误";
                        error_log(date("Ymd-H:i:s")."----".$j."行出现错误\n", 3, "logs/upload.log");
                        $nsql->rollback();
                        echo mysql_error();
                        $importStat["m"] = 3;
                        break 2;
                    }
                $importStat[$strs[0]]++;
                break;
                
                default:
                    $importStat["m"] = 4;
                    $nsql->rollback();
                    echo mysql_error();
                    echo "A|".$j."出现错误[无此操作类型]";
                    error_log("A|".$j."出现错误[无此操作类型]", 3, "logs/upload.log\n");
                $importStat["e"]++;
                break;
            }
            $str = "";
        }
		//将在库数=0 的parent, 子类数量更新为0
//	    $notesql="update `#@__mainkc` set
//	          `number` = 0
//             where p_id in (
//	        select cp_number from (
//                select cp_number from jxc_basic 
//                where cp_parent in 
//                ( select p_id from jxc_mainkc  where number <= 0 
//                and  p_id in(select distinct cp_parent from jxc_basic)
//                )) a)";
//	    $nsql->ExecuteNoneQuery($notesql);
//	    echo mysql_error();
        //根据价格系数更新状态10
	    $notesql="SELECT p_type, func_multiple (GROUP_CONCAT(p_value)) AS multiple_num
                FROM jxc_static WHERE
                p_name in ('price_1'
                            ,'price_2'
                            ,'price_3'
                            ,'price_4'
                            ,'price_5'
                            ,'price_6'
                            ,'price_7'
                            ,'price_8'
                            ,'price_9'
                            'price_10' )
                GROUP BY p_type;";
        $nsql->setquery($notesql);
        $nsql->execute();
	    while ($row = $nsql->GetAssoc()) {
	        $notesql="update jxc_mainkc a, jxc_basic b set a.l_state10=b.cp_jj*{$row["multiple_num"]}
	        where a.p_id=b.cp_number and b.cp_jj >= (select p_value from jxc_static where p_type = '{$row["p_type"]}' and p_name = 'price_from')
	         and  b.cp_jj <(select p_value from jxc_static where p_type = '{$row["p_type"]}' and p_name = 'price_to')";
	       $nsql->ExecuteNoneQuery($notesql);
	    }
	    echo mysql_error();
	    //如果在库位置是6-6-6-6-6，就把数量更新成与父类一样(THE OTHER WAY 2017.6.19 17:52:15能判断 子号等于父号 的在库数就行, 把66666这个条件删除 )
	    $notesql=
	    " update jxc_mainkc x, (".
	    " SELECT cp_number, c.number FROM `jxc_basic` a, jxc_mainkc b, jxc_mainkc c".
	    "  where a.cp_parent = c.p_id and  a.cp_number = b.p_id".
//	    " and CONCAT(b.l_floor, b.l_horizontal, b.l_zone, b.l_shelf, b.l_vertical) ='66666'".
	    " and IFNULL(cp_parent,'') <> '') y set x.number = y.number".
	    " where x.p_id = y.cp_number";
	    $b4 = $nsql->ExecuteNoneQuery($notesql);
	    echo mysql_error();
//         unlink($uploadfile); // 删除上传的excel文件
        $nsql->commit();
        $nsql->Close();
    } else {
        $importStat["m"] = 5;
    }
    
    return $importStat;
}
?>
