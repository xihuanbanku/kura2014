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
    echo "jxc_analyze:[".$result."]\n";

    function insert() {
        $newsql = new ezSQL_mysql();

        $insert = "INSERT INTO jxc_analyze (
									cp_number,
									number,
									cp_categories,
									cp_categories_down,
									mindate,
									maxdate
							) SELECT
									*
							FROM
			(SELECT
                        b.productid,
                        a.number,
                        c.cp_categories,
                        c.cp_categories_down,
                        datediff(min(b.dtime), c.cp_dtime) mindtime,
                        datediff(now(), max(b.dtime)) maxdtime
                FROM
                        jxc_mainkc a,
                        jxc_sale b,
                        jxc_basic c
                WHERE
                        a.p_id = b.productid
                AND a.p_id = c.cp_number
                AND ifnull(b.productid, '') <> ''
                GROUP BY
                        b.productid
        ) x ON DUPLICATE KEY UPDATE mindate = mindtime,
        maxdate = maxdtime,
        number = x.number";
        return $newsql->query($insert);

    }

?>
