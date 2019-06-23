<?php
$inputFunc = "when 5#51<=162500 then round(15#5*0.05105,-1) else 0";
        $pattern = '/[-\+\*\/=><]+/';
        $pattern1 = '/(\d+#\d+)+/';
        //解析参数
        $params = preg_split($pattern, $inputFunc, -1, PREG_SPLIT_NO_EMPTY);
        //解析运算符号
        $signs = preg_split($pattern1, $inputFunc, -1, PREG_SPLIT_NO_EMPTY);
        //提取匹配到的内容
        preg_match_all($pattern1, $inputFunc, $tempRes);
        var_dump($params);
        echo "---------</br>" ;
        var_dump($signs);
        echo "---------</br>" ;
        var_dump($tempRes);
        echo "---------</br>" ;

        $results = $tempRes[0];
        $query ="";
        for ($i = 0; $i < count($results); $i ++) {
            $query .= $signs[$i];
            $items = explode("#", $results[$i]);
            $query .= "(select p_value from jxc_salary_config where p_type = {$items[0]} and sort = {$items[1]} and user_id = 53)";
        }
        $query.=end($signs);
        echo $query;
        echo "---------</br>" ;
        echo $inputFunc;
            
?> 