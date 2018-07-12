<?php
/** Include PHPExcel */
require_once '../include/ez_sql_core.php';
require_once '../include/ez_sql_mysql.php';
    $result = "";
    switch ($_REQUEST["flag"]) {
        case "insert":
            $result = insert();
        break;
        default:
            $result = "error";
        break;
    }
    echo "jxc_salary:[".$result."]\n";

    function insert() {
        $salary_date = date("Ym");
        $today = date("d");
        if($today == 16) {
            $newsql = new ezSQL_mysql();
            $insert = "INSERT INTO jxc_salary (`p_type`, `p_name`, `p_func`, `p_value`, `mod_value`, `sort`, `user_id`, `salary_date`)
                            			SELECT `p_type`, `p_name`, `p_func`, `p_value`, `p_value`, `sort`, `user_id`, '".$salary_date."'
                                            FROM jxc_salary_config
                                            WHERE del_flag = 0)";
            return $newsql->query($insert);
        } else {
            return $today." 未到发工资日期";
        }

    }

?>
