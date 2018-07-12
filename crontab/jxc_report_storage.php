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
    echo "jxc_report_storage:[".$result."]\n";
    
    function insert() {
        $userID = $_REQUEST["userID"];
		$time = date("Y-m-d", strtotime('-1 day'));
		$newsql = new ezSQL_mysql();

        $insert = "INSERT INTO jxc_report_storage (
						productid,
						s_count,
						in_count,
						out_count,
						dtime
					)
							SELECT
								a.p_id,
		sum(a.number),
	sum(b.number),
	sum(c.number),
								'{$time}'
							FROM
								jxc_mainkc a
							LEFT JOIN jxc_kc b ON a.p_id = b.productid
							AND SUBSTR(b.dtime FROM 1 FOR 10) = '{$time}'
							LEFT JOIN jxc_sale c ON a.p_id = c.productid
							AND SUBSTR(b.dtime FROM 1 FOR 10) = SUBSTR(c.dtime FROM 1 FOR 10)
							group by a.p_id
						";
        return $newsql->query($insert);
        
    }
    
?>  
