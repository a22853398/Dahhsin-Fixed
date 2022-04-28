<?php

include "../03lineNotify.php";
include "../commonfile/config.inc.php";

/* LINE TOKEN */
$token = "XXXXXXXXXXXXXXXX";//測試
$token_pikachu = "XXXXXXXXXXXXXXXX";//大新官網推播營業額

class Sales{
    function getSalesDailyMessage(){
        /* 日期處理 */
        $today = date("Y-m-d");
        $today_sql = date("Y-m-d", strtotime("+1 day"));
        $month_first_day = date("Y-m-01");
        
        //如果今天是一號，回上一個月，截止日變今天(因為SQL不包含後者)
        if($today === $month_first_day){
            $month_first_day = date("Y-m-01", strtotime("-1 month"));
            $today_sql = date("Y-m-d");
        }
        //echo "今天：".$today."<br>";
        //echo "SQL用今天：".$today_sql."<br>";
        //echo "月第一天：".$month_first_day."<br>";
        
        /* 日報SQL */
        $strSql = "SELECT DATE_FORMAT(add_date, '%Y-%m-%d') AS date, SUM(ord_price-tran_price) AS amount
                    FROM order_list
                    WHERE 1=1
                    AND process_type NOT IN ('X1','X2')
                    AND (add_date BETWEEN '".$month_first_day."' AND '".$today_sql."')
                    GROUP BY date
                    ORDER BY date
                    ";
        //echo "日報SQL：".$strSql."<br>";
        
        $msg = "\n日期 日營業額";
        $sum = 0;
        $resDailySales = SqlQuery1($strSql);
        while(!$resDailySales->EOF){
            $date = $resDailySales->fields["date"];
            $day_amount = intval($resDailySales->fields["amount"]);
            $sum += $day_amount;
            $msg .= "\n".$date."\t\t".$day_amount;
            $resDailySales->MoveNext();
        }
        $msg .= "\n\n合計\t\t".$sum;
        //echo "SQL結果訊息：".$msg."<br>";
        return $msg;
    }
    
}


$tmpClass = new Sales();
$message = $tmpClass->getSalesDailyMessage();
//post_message_curl_token($message, $token);
//post_message_curl_token($message, $token_pikachu);
?>