<?php
include "../commonfile/config.inc.php";
include "../03lineNotify.php";
$token = "XXXXXXXXXXXXXXXX";

//產生字串
function get3000UpOrderDetails(){
    $now = date("Y-m-d H:i:s", strtotime("now"));
    $halfAgo = date("Y-m-d H:i:s", strtotime( "now"."-15 minutes"));
    
    //訂單大概
    $strSql_big = " SELECT orderid, ord_price, gcname, pay_typestr, tran_typestr 
                    FROM order_list 
                    WHERE order_list.process_type NOT IN ('X1', 'X2')
                    AND order_list.ord_price >= 3000
                    AND order_list.add_date >= '".$halfAgo."' AND order_list.add_date <= '".$now."'
                    ";
    $res_big = SqlQuery1($strSql_big);                
    //商品細節
    $strSql_detail =   "SELECT 
                    order_list.orderid, 
                    order_list.ord_price, 
                    order_list.gcname, 
                    order_list.pay_typestr, 
                    order_list.tran_typestr,
                    order_listdetail.prod_name AS prod_name,
                    order_listdetail.prod_namesub AS prod_namesub,
                    order_listdetail.prod_num AS prod_num,
                    order_listdetail.ord_qty AS prod_qty
                FROM order_list
                JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
                WHERE order_list.process_type NOT IN ('X1', 'X2')
                AND order_list.ord_price >= 3000
                AND order_list.add_date >= '".$halfAgo."' AND order_list.add_date <= '".$now."'
                AND order_listdetail.ord_type NOT IN ('C01', 'T01', 'G01')
                ";
    $res_detail = SqlQuery1($strSql_detail);
    //雙層迴圈產字串
    $msg = "\n【超過三千元訂單】";
    while(!$res_big->EOF){
        $orderid = $res_big->fields["orderid"];
        $ord_price = $res_big->fields["ord_price"];
        $gcname = $res_big->fields["gcname"];
        $pay_type = $res_big->fields["pay_typestr"];
        $tran_type = $res_big->fields["tran_typestr"];
        $msg .= "\n訂單編號：".$orderid."\n訂單金額：".$ord_price."\n收件姓名：".$gcname."\n付款方式：".$pay_type."\n配送方式：".$tran_type;
        $msg .= "\n購買商品：(數量 書編 名稱)";
        while(!$res_detail->EOF){
            $prod_name = strip_tags($res_detail->fields["prod_name"]);
            $prod_namesub = strip_tags($res_detail->fields["prod_namesub"]);
            $prod_num = $res_detail->fields["prod_num"];
            $prod_qty = $res_detail->fields["prod_qty"];
            $msg .= "\n".$prod_qty."\t\t".$prod_num."\t\t".$prod_name." ".$prod_namesub;
            $res_detail->MoveNext();
        }
        $msg .= "\n--------";
        $res_big->MoveNext();
    }
    return $msg;
}
//呼叫函式產生字串
$resultStr = get3000UpOrderDetails();
//利用LINE NOTIFY發信
if(strpos($resultStr, "訂單編號")){
    post_message_curl_token($resultStr, $token);
}else{
    //不做事
}



/*  利用狗狗發信(尚未使用)
*   $ch = 呼叫的LINE API
*   $response = 陣列，函式裡面會自動包裝成json
*   函式裡面存有token
*/
function sendMessageByDog($ch, $response){
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //error_log(json_encode($response));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json; charser=UTF-8', 'Authorization: Bearer ' . $accessToken )); 
    $result = curl_exec($ch); 
    //error_log($result); 
    curl_close($ch);
}
?>