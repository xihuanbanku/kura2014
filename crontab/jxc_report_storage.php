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
		a.number,
	sum(b.number),
	sum(c.number),
								'{$time}'
							FROM
								jxc_mainkc a
							LEFT JOIN jxc_kc b ON a.p_id = b.productid
							AND SUBSTR(b.dtime FROM 1 FOR 10) = '{$time}'
							LEFT JOIN jxc_sale c ON a.p_id = c.productid
							AND SUBSTR(c.dtime FROM 1 FOR 10) = '{$time}'
							group by a.p_id
						";
        $res = "jxc_report_storage:".$newsql->query($insert);
		
        $insert = "INSERT INTO `jxc_report_sale_sum` ( state_id, count_sum, sale_sum, dtime )
                        SELECT
                        	x.s_value,
                        	IFNULL( y.s_count, 0 ),
                        	IFNULL( y.s_sale, 0 ),
                        	IFNULL( dt, '{$time}' ) 
                        FROM
                        	( SELECT s_name, s_value FROM jxc_state WHERE parent_id = 8 ) x
                        	LEFT JOIN (
                        	SELECT
                        		a.member,
                        		sum( a.number ) s_count,
                        		sum( a.number * a.sale ) s_sale,
                        		'{$time}' dt 
                        	FROM
                        		jxc_sale a 
                        	WHERE
                        		SUBSTR( a.dtime FROM 1 FOR 10 ) = '{$time}' 
                        	GROUP BY
                        	a.member 
                        	) y ON x.s_value = y.member
						";
        $res .= "jxc_report_sale_sum:".$newsql->query($insert);
        return $res;
        
    }
    
?>  
